<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * List categories, optionally formatted as hierarchical tree.
     */
    public function index(Request $request): JsonResponse
    {
        if ($request->query('tree')) {
            $categories = Category::roots()
                ->with(['children' => function ($q) {
                    $q->withCount('products')->orderBy('sort_order');
                }])
                ->withCount('products')
                ->orderBy('sort_order')
                ->get();

            return $this->successResponse(
                CategoryResource::collection($categories),
                'Category hierarchy tree retrieved.'
            );
        }

        $query = Category::with(['parent'])->withCount(['products', 'children']);

        if ($search = $request->query('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->has('parent_id')) {
            $query->where('parent_id', $request->query('parent_id'));
        }

        if ($request->has('is_active') && $request->query('is_active') !== '') {
            $query->where('is_active', filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        $categories = $query->orderBy('sort_order')->orderBy('name')->get();

        return $this->successResponse(
            CategoryResource::collection($categories),
            'Categories retrieved successfully.'
        );
    }

    /**
     * Store new category or subcategory.
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $category = Category::create($data);
        $category->load(['parent'])->loadCount(['products', 'children']);

        AuditService::log('CATEGORY_CREATED', $category, null, $category->toArray());

        return $this->successResponse(
            new CategoryResource($category),
            'Category created successfully.',
            201
        );
    }

    /**
     * Show category details.
     */
    public function show(Category $category): JsonResponse
    {
        return $this->successResponse(
            new CategoryResource($category->load(['parent', 'children'])->loadCount(['products', 'children'])),
            'Category details retrieved.'
        );
    }

    /**
     * Update category.
     */
    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $old = $category->toArray();
        $category->update($data);
        $category->load(['parent', 'children'])->loadCount(['products', 'children']);

        AuditService::log('CATEGORY_UPDATED', $category, $old, $category->toArray());

        return $this->successResponse(
            new CategoryResource($category),
            'Category updated successfully.'
        );
    }

    /**
     * Toggle category active status.
     */
    public function toggleStatus(Category $category): JsonResponse
    {
        $category->update(['is_active' => !$category->is_active]);

        AuditService::log(
            $category->is_active ? 'CATEGORY_ACTIVATED' : 'CATEGORY_DEACTIVATED',
            $category
        );

        return $this->successResponse(
            new CategoryResource($category->loadCount('products')),
            "Category has been " . ($category->is_active ? 'activated.' : 'archived.')
        );
    }

    /**
     * Delete category if safe.
     */
    public function destroy(Category $category): JsonResponse
    {
        if ($category->children()->count() > 0) {
            return $this->errorResponse('Cannot delete category with active subcategories. Please reassign or delete subcategories first.', 422);
        }

        if ($category->products()->count() > 0) {
            return $this->errorResponse('Cannot delete category with associated hardware products. Please reassign products first.', 422);
        }

        $name = $category->name;
        $category->delete();

        AuditService::log('CATEGORY_DELETED', null, ['name' => $name]);

        return $this->successResponse(null, "Category '{$name}' has been deleted.");
    }
}
