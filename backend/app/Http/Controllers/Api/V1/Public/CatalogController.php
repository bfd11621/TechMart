<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CatalogController extends Controller
{
    /**
     * Public Category Tree for showroom navigation.
     */
    public function categories(): JsonResponse
    {
        $categories = Category::roots()
            ->where('is_active', true)
            ->with(['children' => function ($q) {
                $q->where('is_active', true)
                  ->withCount(['products' => function ($pQ) {
                      $pQ->where('is_published', true);
                  }])
                  ->orderBy('sort_order');
            }])
            ->withCount(['products' => function ($pQ) {
                $pQ->where('is_published', true);
            }])
            ->orderBy('sort_order')
            ->get();

        return $this->successResponse(
            CategoryResource::collection($categories),
            'Public categories retrieved.'
        );
    }

    /**
     * Public Brands Directory.
     */
    public function brands(): JsonResponse
    {
        $brands = Brand::where('is_active', true)
            ->withCount(['products' => function ($q) {
                $q->where('is_published', true);
            }])
            ->orderBy('name')
            ->get();

        return $this->successResponse(
            BrandResource::collection($brands),
            'Public brands retrieved.'
        );
    }

    /**
     * Browse published products with faceted search and filters.
     */
    public function products(Request $request): JsonResponse
    {
        $query = Product::where('is_published', true)
            ->with(['category', 'brand', 'primaryImage', 'inventories']);

        // Search
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('model_number', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($cat = $request->query('category')) {
            $query->whereHas('category', function ($q) use ($cat) {
                $q->where('slug', $cat)
                  ->orWhere('id', $cat)
                  ->orWhereHas('parent', function ($pQ) use ($cat) {
                      $pQ->where('slug', $cat)->orWhere('id', $cat);
                  });
            });
        }

        // Brand filter
        if ($brand = $request->query('brand')) {
            $query->whereHas('brand', function ($q) use ($brand) {
                $q->where('slug', $brand)->orWhere('id', $brand);
            });
        }

        // Price range
        if ($minPrice = $request->query('min_price')) {
            $query->where('base_price', '>=', (float) $minPrice);
        }
        if ($maxPrice = $request->query('max_price')) {
            $query->where('base_price', '<=', (float) $maxPrice);
        }

        // Featured only
        if ($request->query('featured')) {
            $query->where('is_featured', true);
        }

        // Sorting
        $sort = $request->query('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('base_price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('base_price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->orderBy('id', 'desc');
                break;
        }

        $perPage = max(6, min(48, (int) $request->query('per_page', 12)));
        $products = $query->paginate($perPage);

        return $this->paginatedResponse($products, 'Showroom products retrieved.');
    }

    /**
     * Show single product details for public showroom.
     */
    public function showProduct(string $slug): JsonResponse
    {
        $product = Product::where('slug', $slug)
            ->where('is_published', true)
            ->with(['category.parent', 'brand', 'images', 'specifications', 'inventories.store'])
            ->firstOrFail();

        return $this->successResponse(
            new ProductDetailResource($product),
            'Product details retrieved.'
        );
    }

    /**
     * Download manufacturer datasheet PDF.
     */
    public function downloadDatasheet(Product $product): BinaryFileResponse|JsonResponse
    {
        if (!$product->datasheet_url) {
            return $this->errorResponse('Datasheet not available for this product.', 404);
        }

        $relativePath = str_replace('/storage/', '', $product->datasheet_url);

        if (!Storage::disk('public')->exists($relativePath)) {
            return $this->errorResponse('Datasheet document file was not found on storage.', 404);
        }

        $fullPath = Storage::disk('public')->path($relativePath);
        $filename = $product->datasheet_filename ?: "datasheet_{$product->sku}.pdf";

        return response()->download($fullPath, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
