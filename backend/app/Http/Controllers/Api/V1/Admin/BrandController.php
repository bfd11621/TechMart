<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBrandRequest;
use App\Http\Requests\Admin\UpdateBrandRequest;
use App\Http\Resources\BrandResource;
use App\Models\Brand;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    /**
     * List all partner brands with product counts.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Brand::withCount('products');

        if ($search = $request->query('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->has('is_active') && $request->query('is_active') !== '') {
            $query->where('is_active', filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        $brands = $query->orderBy('name')->get();

        return $this->successResponse(
            BrandResource::collection($brands),
            'Brands retrieved successfully.'
        );
    }

    /**
     * Store new brand.
     */
    public function store(StoreBrandRequest $request): JsonResponse
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '_' . Str::slug($data['name']) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('brands', $filename, 'public');
            $data['logo_url'] = '/storage/' . $path;
        }

        $brand = Brand::create($data);
        $brand->loadCount('products');

        AuditService::log('BRAND_CREATED', $brand, null, $brand->toArray());

        return $this->successResponse(
            new BrandResource($brand),
            'Brand registered successfully.',
            201
        );
    }

    /**
     * Show brand details.
     */
    public function show(Brand $brand): JsonResponse
    {
        return $this->successResponse(
            new BrandResource($brand->loadCount('products')),
            'Brand details retrieved.'
        );
    }

    /**
     * Update brand.
     */
    public function update(UpdateBrandRequest $request, Brand $brand): JsonResponse
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        if ($request->hasFile('logo')) {
            if ($brand->logo_url && str_contains($brand->logo_url, '/storage/brands/')) {
                $oldPath = str_replace('/storage/', '', $brand->logo_url);
                Storage::disk('public')->delete($oldPath);
            }
            $file = $request->file('logo');
            $filename = time() . '_' . Str::slug($data['name']) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('brands', $filename, 'public');
            $data['logo_url'] = '/storage/' . $path;
        }

        $old = $brand->toArray();
        $brand->update($data);
        $brand->loadCount('products');

        AuditService::log('BRAND_UPDATED', $brand, $old, $brand->toArray());

        return $this->successResponse(
            new BrandResource($brand),
            'Brand updated successfully.'
        );
    }

    /**
     * Toggle brand active status.
     */
    public function toggleStatus(Brand $brand): JsonResponse
    {
        $brand->update(['is_active' => !$brand->is_active]);

        AuditService::log(
            $brand->is_active ? 'BRAND_ACTIVATED' : 'BRAND_DEACTIVATED',
            $brand
        );

        return $this->successResponse(
            new BrandResource($brand->loadCount('products')),
            "Brand has been " . ($brand->is_active ? 'activated.' : 'archived.')
        );
    }

    /**
     * Delete brand if no products attached.
     */
    public function destroy(Brand $brand): JsonResponse
    {
        if ($brand->products()->count() > 0) {
            return $this->errorResponse('Cannot delete brand with associated products in catalog. Please reassign products first.', 422);
        }

        $name = $brand->name;
        $brand->delete();

        AuditService::log('BRAND_DELETED', null, ['name' => $name]);

        return $this->successResponse(null, "Brand '{$name}' has been deleted.");
    }
}
