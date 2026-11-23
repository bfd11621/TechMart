<?php

namespace App\Http\Controllers;

use App\Http\Resources\BrandResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShowroomController extends Controller
{
    public function home(): Response
    {
        $featured = Product::where('is_published', true)
            ->with(['category', 'brand', 'primaryImage', 'inventories'])
            ->orderByDesc('is_featured')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        return Inertia::render('Public/Home', [
            'categories' => $this->categoryTree(),
            'brands' => $this->brandDirectory(),
            'products' => $this->cards($featured),
            'counts' => [
                'categories' => Category::where('is_active', true)->whereNull('parent_id')->count(),
                'products' => Product::where('is_published', true)->count(),
                'brands' => Brand::where('is_active', true)->count(),
            ],
        ]);
    }

    public function catalog(Request $request): Response
    {
        $products = $this->productQuery($request)->paginate(12)->withQueryString();

        return Inertia::render('Public/Catalog', [
            'products' => $this->cards($products->getCollection()),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'total' => $products->total(),
            ],
            'filters' => [
                'search' => (string) $request->query('search', ''),
                'category' => (string) $request->query('category', ''),
                'brand' => (string) $request->query('brand', ''),
                'sort' => (string) $request->query('sort', 'newest'),
            ],
            'categories' => $this->categoryTree(),
            'brands' => $this->brandDirectory(),
        ]);
    }

    public function categories(): Response
    {
        return Inertia::render('Public/Categories', [
            'categories' => $this->categoryTree(),
        ]);
    }

    public function brands(): Response
    {
        return Inertia::render('Public/Brands', [
            'brands' => $this->brandDirectory(),
        ]);
    }

    public function product(string $slug): Response
    {
        $product = Product::where('slug', $slug)
            ->where('is_published', true)
            ->with(['category.parent', 'brand', 'images', 'specifications', 'inventories.store'])
            ->firstOrFail();

        $related = Product::where('is_published', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['category', 'brand', 'primaryImage', 'inventories'])
            ->limit(4)
            ->get();

        return Inertia::render('Public/Product', [
            'product' => (new ProductDetailResource($product))->resolve(),
            'related' => $this->cards($related),
        ]);
    }

    public function about(): Response
    {
        return Inertia::render('Public/About');
    }

    public function services(): Response
    {
        return Inertia::render('Public/Services');
    }

    public function contact(): Response
    {
        return Inertia::render('Public/Contact');
    }

    public function register(): Response
    {
        return Inertia::render('Public/Register');
    }

    public function privacy(): Response
    {
        return Inertia::render('Public/Privacy');
    }

    public function terms(): Response
    {
        return Inertia::render('Public/Terms');
    }

    public function cart(): Response
    {
        return Inertia::render('Public/Cart');
    }

    public function compare(): Response
    {
        return Inertia::render('Public/Compare');
    }

    private function productQuery(Request $request): Builder
    {
        $query = Product::where('is_published', true)
            ->with(['category', 'brand', 'primaryImage', 'inventories']);

        if ($search = trim((string) $request->query('search', ''))) {
            $query->where(function ($inner) use ($search) {
                $inner->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('model_number', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        if ($category = $request->query('category')) {
            $query->whereHas('category', function ($inner) use ($category) {
                $inner->where('slug', $category)
                    ->orWhere('id', $category)
                    ->orWhereHas('parent', function ($parent) use ($category) {
                        $parent->where('slug', $category)->orWhere('id', $category);
                    });
            });
        }

        if ($brand = $request->query('brand')) {
            $query->whereHas('brand', function ($inner) use ($brand) {
                $inner->where('slug', $brand)->orWhere('id', $brand);
            });
        }

        match ($request->query('sort', 'newest')) {
            'price_asc' => $query->orderBy('base_price'),
            'price_desc' => $query->orderByDesc('base_price'),
            'name_asc' => $query->orderBy('name'),
            default => $query->orderByDesc('id'),
        };

        return $query;
    }

    private function categoryTree(): array
    {
        $categories = Category::roots()
            ->where('is_active', true)
            ->with(['children' => function ($query) {
                $query->where('is_active', true)
                    ->withCount(['products' => fn ($products) => $products->where('is_published', true)])
                    ->orderBy('sort_order');
            }])
            ->withCount(['products' => fn ($products) => $products->where('is_published', true)])
            ->orderBy('sort_order')
            ->get();

        Category::attachInventoryTotals($categories, true);

        return collect(CategoryResource::collection($categories)->resolve())
            ->map(function (array $category) {
                $children = $category['children'] ?? [];
                if ($children instanceof \Illuminate\Http\Resources\Json\JsonResource) {
                    $children = $children->resolve();
                }
                if (isset($children['data']) && is_array($children['data'])) {
                    $children = $children['data'];
                }
                $category['children'] = array_values(is_array($children) ? $children : []);
                $category['products_count'] = (int) ($category['total_products_count']
                    ?? ((int) ($category['products_count'] ?? 0) + (int) collect($category['children'])->sum('products_count')));
                $category['stock_quantity'] = (int) ($category['total_stock_quantity']
                    ?? ((int) ($category['stock_quantity'] ?? 0) + (int) collect($category['children'])->sum('stock_quantity')));

                return $category;
            })
            ->all();
    }

    private function brandDirectory(): array
    {
        $brands = Brand::where('is_active', true)
            ->withCount(['products' => fn ($products) => $products->where('is_published', true)])
            ->orderBy('name')
            ->get();

        return BrandResource::collection($brands)->resolve();
    }

    private function cards(iterable $products): array
    {
        return collect(ProductResource::collection($products)->resolve())
            ->map(function (array $product) {
                $product['primary_image_url'] = $product['primary_image'] ?? null;

                return $product;
            })
            ->values()
            ->all();
    }
}
