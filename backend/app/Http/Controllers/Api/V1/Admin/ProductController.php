<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSpecification;
use App\Models\Store;
use App\Models\StoreInventory;
use App\Services\AuditService;
use App\Services\FileUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * List products with multi-facet filtering and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['category', 'brand', 'primaryImage', 'inventories']);

        // Search: Name, SKU, Model Number
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('model_number', 'like', "%{$search}%");
            });
        }

        // Category Filter (including subcategories)
        if ($categoryId = $request->query('category_id')) {
            $query->where(function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId)
                  ->orWhereHas('category', function ($subQ) use ($categoryId) {
                      $subQ->where('parent_id', $categoryId);
                  });
            });
        }

        // Brand Filter
        if ($brandId = $request->query('brand_id')) {
            $query->where('brand_id', $brandId);
        }

        // Availability Filter
        if ($avail = $request->query('availability_status')) {
            $query->where('availability_status', $avail);
        }

        // Published Filter
        if ($request->has('is_published') && $request->query('is_published') !== '') {
            $query->where('is_published', filter_var($request->query('is_published'), FILTER_VALIDATE_BOOLEAN));
        }

        // Featured Filter
        if ($request->has('is_featured') && $request->query('is_featured') !== '') {
            $query->where('is_featured', filter_var($request->query('is_featured'), FILTER_VALIDATE_BOOLEAN));
        }

        $perPage = max(5, min(100, (int) $request->query('per_page', 15)));
        $products = $query->orderBy('id', 'desc')->paginate($perPage);

        return $this->paginatedResponse($products, 'Products retrieved successfully.');
    }

    /**
     * Store new product along with dynamic specifications and images.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']) . '-' . Str::lower($data['sku']);
        }

        // Process datasheet: local file upload OR remote external URL
        if ($request->hasFile('datasheet')) {
            $datasheetFile = $request->file('datasheet');
            $originalFilename = $datasheetFile->getClientOriginalName();
            $datasheetFilename = 'datasheet_' . Str::slug($data['sku']) . '_' . time() . '.pdf';
            $datasheetPath = 'products/datasheets/' . $datasheetFilename;

            Storage::disk('public')->put($datasheetPath, file_get_contents($datasheetFile));
            $data['datasheet_url'] = Storage::url($datasheetPath);
            $data['datasheet_filename'] = $originalFilename;
        } elseif ($request->filled('datasheet') || $request->filled('datasheet_url')) {
            $rawDs = $request->input('datasheet_url') ?: $request->input('datasheet');
            if (is_string($rawDs) && !empty($rawDs)) {
                $data['datasheet_url'] = $rawDs;
                $data['datasheet_filename'] = $request->input('datasheet_filename') ?: (basename(parse_url($rawDs, PHP_URL_PATH)) ?: 'Technical_Datasheet.pdf');
            }
        }

        // Process images: handle multiple files, single file, or remote external URLs
        $imagesToCreate = [];
        $primaryIndex = (int) $request->input('primary_image_index', 0);

        if ($request->hasFile('images')) {
            $files = $request->file('images');
            if (!is_array($files)) $files = [$files];
            foreach ($files as $idx => $imgFile) {
                if ($imgFile && $imgFile->isValid()) {
                    $ext = $imgFile->getClientOriginalExtension();
                    $filename = 'img_' . Str::slug($data['sku']) . '_' . time() . '_' . $idx . '_' . Str::random(4) . '.' . $ext;
                    $path = 'products/images/' . $filename;
                    Storage::disk('public')->put($path, file_get_contents($imgFile));
                    $imagesToCreate[] = [
                        'image_url' => Storage::url($path),
                        'is_primary' => ($idx === $primaryIndex),
                        'sort_order' => $idx + 1,
                    ];
                }
            }
        } elseif ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imageExt = $imageFile->getClientOriginalExtension();
            $imageFilename = 'img_' . Str::slug($data['sku']) . '_' . time() . '_' . Str::random(6) . '.' . $imageExt;
            $imagePath = 'products/images/' . $imageFilename;

            Storage::disk('public')->put($imagePath, file_get_contents($imageFile));
            $imagesToCreate[] = [
                'image_url' => Storage::url($imagePath),
                'is_primary' => true,
                'sort_order' => 1,
            ];
        }

        // Process external image URLs if passed
        $rawImages = $request->input('images') ?: $request->input('image_urls');
        if (is_string($rawImages)) {
            $rawImages = json_decode($rawImages, true) ?? [];
        }
        if (is_array($rawImages) && !empty($rawImages)) {
            $startIndex = count($imagesToCreate);
            foreach ($rawImages as $idx => $imgItem) {
                $url = is_array($imgItem) ? ($imgItem['image_url'] ?? $imgItem['url'] ?? null) : $imgItem;
                if ($url && is_string($url)) {
                    $isPrim = is_array($imgItem) ? (!empty($imgItem['is_primary'])) : ($idx === $primaryIndex && empty($imagesToCreate));
                    $imagesToCreate[] = [
                        'image_url' => $url,
                        'is_primary' => $isPrim,
                        'sort_order' => $startIndex + $idx + 1,
                    ];
                }
            }
        } elseif (empty($imagesToCreate) && $request->filled('image') && is_string($request->input('image'))) {
            $imagesToCreate[] = [
                'image_url' => $request->input('image'),
                'is_primary' => true,
                'sort_order' => 1,
            ];
        }

        // Ensure at least one image is marked primary
        if (!empty($imagesToCreate)) {
            $hasPrimary = false;
            foreach ($imagesToCreate as $img) {
                if (!empty($img['is_primary'])) { $hasPrimary = true; break; }
            }
            if (!$hasPrimary) {
                $imagesToCreate[0]['is_primary'] = true;
            }
        }

        // Process key_features & specifications if passed as JSON strings from FormData
        if (isset($data['key_features']) && is_string($data['key_features'])) {
            $data['key_features'] = json_decode($data['key_features'], true) ?? [];
        }

        if (isset($data['specifications']) && is_string($data['specifications'])) {
            $data['specifications'] = json_decode($data['specifications'], true) ?? [];
        }

        $product = DB::transaction(function () use ($data, $imagesToCreate) {
            $product = Product::create([
                'category_id' => $data['category_id'],
                'brand_id' => $data['brand_id'],
                'sku' => strtoupper($data['sku']),
                'name' => $data['name'],
                'slug' => $data['slug'],
                'model_number' => $data['model_number'] ?? null,
                'short_description' => $data['short_description'] ?? null,
                'full_description' => $data['full_description'] ?? null,
                'key_features' => $data['key_features'] ?? [],
                'base_price' => $data['base_price'],
                'cost_price' => $data['cost_price'] ?? 0.00,
                'warranty_info' => $data['warranty_info'] ?? null,
                'is_featured' => filter_var($data['is_featured'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'is_published' => filter_var($data['is_published'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'availability_status' => $data['availability_status'] ?? 'IN_STOCK',
                'datasheet_url' => $data['datasheet_url'] ?? null,
                'datasheet_filename' => $data['datasheet_filename'] ?? null,
            ]);

            // 1. Sync Dynamic Technical Specifications
            if (!empty($data['specifications']) && is_array($data['specifications'])) {
                $sort = 1;
                foreach ($data['specifications'] as $spec) {
                    if (!empty($spec['spec_name'])) {
                        ProductSpecification::create([
                            'product_id' => $product->id,
                            'spec_group' => $spec['spec_group'] ?? 'General',
                            'spec_name' => $spec['spec_name'],
                            'spec_value' => $spec['spec_value'] ?? '',
                            'sort_order' => $spec['sort_order'] ?? $sort++,
                        ]);
                    }
                }
            }

            // 2. Sync Images
            if (!empty($imagesToCreate)) {
                foreach ($imagesToCreate as $img) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_url' => $img['image_url'],
                        'is_primary' => (bool) $img['is_primary'],
                        'sort_order' => $img['sort_order'],
                    ]);
                }
            }

            // 3. Initialize inventory slots across all active stores (default 0 stock)
            $stores = Store::where('is_active', true)->get();
            foreach ($stores as $store) {
                StoreInventory::firstOrCreate([
                    'store_id' => $store->id,
                    'product_id' => $product->id,
                ], [
                    'quantity' => 0,
                    'reserved_quantity' => 0,
                    'min_alert_quantity' => 5,
                    'shelf_location' => null,
                ]);
            }

            AuditService::log('PRODUCT_CREATED', $product, null, $product->toArray());

            return $product;
        });

        $product->load(['category', 'brand', 'images', 'specifications', 'inventories.store']);

        return $this->successResponse(
            new ProductDetailResource($product),
            'Product registered in catalogue successfully.',
            201
        );
    }

    /**
     * Show full product details.
     */
    public function show(Product $product): JsonResponse
    {
        $product->load(['category.parent', 'brand', 'images', 'specifications', 'inventories.store']);

        return $this->successResponse(
            new ProductDetailResource($product),
            'Product details retrieved.'
        );
    }

    /**
     * Update product details, specifications, and images.
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $data = $request->validated();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']) . '-' . Str::lower($data['sku']);
        }

        $old = $product->toArray();

        // 1. Backend Storage Logic: Check and store new product image file(s) or remote external URLs
        $newUploadedImages = [];
        $primaryIndex = (int) $request->input('primary_image_index', 0);

        if ($request->hasFile('images')) {
            $files = $request->file('images');
            if (!is_array($files)) $files = [$files];
            foreach ($files as $idx => $imgFile) {
                if ($imgFile && $imgFile->isValid()) {
                    $ext = $imgFile->getClientOriginalExtension();
                    $filename = 'img_' . Str::slug($product->sku) . '_' . time() . '_' . $idx . '_' . Str::random(4) . '.' . $ext;
                    $path = 'products/images/' . $filename;
                    Storage::disk('public')->put($path, file_get_contents($imgFile));
                    $newUploadedImages[] = [
                        'image_url' => Storage::url($path),
                        'is_primary' => ($idx === $primaryIndex),
                        'sort_order' => $idx + 1,
                    ];
                }
            }
        } elseif ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imageExt = $imageFile->getClientOriginalExtension();
            $imageFilename = 'img_' . Str::slug($product->sku) . '_' . time() . '_' . Str::random(6) . '.' . $imageExt;
            $imagePath = 'products/images/' . $imageFilename;

            Storage::disk('public')->put($imagePath, file_get_contents($imageFile));
            $newImageUrl = Storage::url($imagePath);

            $newUploadedImages[] = [
                'image_url' => $newImageUrl,
                'is_primary' => true,
                'sort_order' => 1,
            ];
        }

        // Parse external / existing images list if passed
        $rawImages = $request->input('images') ?: $request->input('image_urls');
        if (is_string($rawImages)) {
            $rawImages = json_decode($rawImages, true) ?? [];
        }
        $syncedImages = $newUploadedImages;
        if (is_array($rawImages) && !empty($rawImages)) {
            $offset = count($syncedImages);
            foreach ($rawImages as $idx => $imgItem) {
                $url = is_array($imgItem) ? ($imgItem['image_url'] ?? $imgItem['url'] ?? null) : $imgItem;
                if ($url && is_string($url)) {
                    $isPrim = is_array($imgItem) ? (!empty($imgItem['is_primary'])) : ($idx === $primaryIndex && empty($syncedImages));
                    $syncedImages[] = [
                        'image_url' => $url,
                        'is_primary' => $isPrim,
                        'sort_order' => $offset + $idx + 1,
                    ];
                }
            }
        }

        // 2. Backend Storage Logic: Check and store new datasheet PDF file or remote external URL
        if ($request->hasFile('datasheet')) {
            $datasheetFile = $request->file('datasheet');
            $originalFilename = $datasheetFile->getClientOriginalName();
            $datasheetFilename = 'datasheet_' . Str::slug($product->sku) . '_' . time() . '.pdf';
            $datasheetPath = 'products/datasheets/' . $datasheetFilename;

            // Store using Storage::disk('public')->put()
            Storage::disk('public')->put($datasheetPath, file_get_contents($datasheetFile));
            $newDatasheetUrl = Storage::url($datasheetPath);

            // Delete old datasheet file from storage if local
            if ($product->datasheet_url && str_contains($product->datasheet_url, '/storage/')) {
                $oldDsPath = str_replace('/storage/', '', $product->datasheet_url);
                if (Storage::disk('public')->exists($oldDsPath)) {
                    Storage::disk('public')->delete($oldDsPath);
                }
            }

            // Save new path and original filename to database
            $data['datasheet_url'] = $newDatasheetUrl;
            $data['datasheet_filename'] = $originalFilename;
        } elseif ($request->filled('datasheet') || $request->filled('datasheet_url')) {
            $rawDs = $request->input('datasheet_url') ?: $request->input('datasheet');
            if (is_string($rawDs) && !empty($rawDs)) {
                $data['datasheet_url'] = $rawDs;
                $data['datasheet_filename'] = $request->input('datasheet_filename') ?: ($product->datasheet_filename ?: 'Technical_Datasheet.pdf');
            }
        }

        // Process key_features & specifications if passed as JSON strings from FormData
        if (isset($data['key_features']) && is_string($data['key_features'])) {
            $data['key_features'] = json_decode($data['key_features'], true) ?? [];
        }

        if (isset($data['specifications']) && is_string($data['specifications'])) {
            $data['specifications'] = json_decode($data['specifications'], true) ?? [];
        }

        DB::transaction(function () use ($product, $data, $request, $syncedImages) {
            $product->update([
                'category_id' => $data['category_id'],
                'brand_id' => $data['brand_id'],
                'sku' => strtoupper($data['sku']),
                'name' => $data['name'],
                'slug' => $data['slug'],
                'model_number' => $data['model_number'] ?? null,
                'short_description' => $data['short_description'] ?? null,
                'full_description' => $data['full_description'] ?? null,
                'key_features' => $data['key_features'] ?? [],
                'base_price' => $data['base_price'],
                'cost_price' => $data['cost_price'] ?? 0.00,
                'warranty_info' => $data['warranty_info'] ?? null,
                'is_featured' => filter_var($data['is_featured'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'is_published' => filter_var($data['is_published'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'availability_status' => $data['availability_status'] ?? $product->availability_status,
                'datasheet_url' => $data['datasheet_url'] ?? $product->datasheet_url,
                'datasheet_filename' => $data['datasheet_filename'] ?? $product->datasheet_filename,
            ]);

            // Sync Specifications
            if (isset($data['specifications']) && is_array($data['specifications'])) {
                $product->specifications()->delete();
                $sort = 1;
                foreach ($data['specifications'] as $spec) {
                    if (!empty($spec['spec_name'])) {
                        ProductSpecification::create([
                            'product_id' => $product->id,
                            'spec_group' => $spec['spec_group'] ?? 'General',
                            'spec_name' => $spec['spec_name'],
                            'spec_value' => $spec['spec_value'] ?? '',
                            'sort_order' => $spec['sort_order'] ?? $sort++,
                        ]);
                    }
                }
            }

            // Sync Images if any new images uploaded or images list explicitly passed
            if (!empty($syncedImages)) {
                $product->images()->delete();
                $hasPrimary = false;
                foreach ($syncedImages as $img) {
                    if (!empty($img['is_primary'])) { $hasPrimary = true; break; }
                }
                if (!$hasPrimary && count($syncedImages) > 0) {
                    $syncedImages[0]['is_primary'] = true;
                }
                foreach ($syncedImages as $img) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_url' => $img['image_url'],
                        'is_primary' => (bool) $img['is_primary'],
                        'sort_order' => $img['sort_order'],
                    ]);
                }
            }
        });

        AuditService::log('PRODUCT_UPDATED', $product, $old, $product->toArray());

        $product->load(['category.parent', 'brand', 'images', 'specifications', 'inventories.store']);

        return $this->successResponse(
            new ProductDetailResource($product),
            'Product catalogue updated successfully.'
        );
    }

    /**
     * Toggle publication status.
     */
    public function togglePublish(Product $product): JsonResponse
    {
        $product->update(['is_published' => !$product->is_published]);

        AuditService::log(
            $product->is_published ? 'PRODUCT_PUBLISHED' : 'PRODUCT_UNPUBLISHED',
            $product
        );

        return $this->successResponse([
            'id' => $product->id,
            'is_published' => $product->is_published,
        ], "Product publication status set to " . ($product->is_published ? 'Published.' : 'Unpublished.'));
    }

    /**
     * Toggle featured status.
     */
    public function toggleFeatured(Product $product): JsonResponse
    {
        $product->update(['is_featured' => !$product->is_featured]);

        return $this->successResponse([
            'id' => $product->id,
            'is_featured' => $product->is_featured,
        ], "Product " . ($product->is_featured ? 'marked as featured.' : 'removed from featured.'));
    }

    /**
     * Upload an image for the product gallery.
     */
    public function uploadImage(Request $request, Product $product, FileUploadService $uploader): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,webp,gif', 'max:5120'],
        ]);

        $url = $uploader->uploadProductImage($request->file('image'), $product->sku);

        $isFirst = $product->images()->count() === 0;

        $img = ProductImage::create([
            'product_id' => $product->id,
            'image_url' => $url,
            'is_primary' => $isFirst,
            'sort_order' => $product->images()->count() + 1,
        ]);

        AuditService::log('PRODUCT_IMAGE_UPLOADED', $product, null, ['image_url' => $url]);

        return $this->successResponse([
            'id' => $img->id,
            'url' => $img->image_url,
            'is_primary' => (bool) $img->is_primary,
            'sort_order' => $img->sort_order,
        ], 'Image uploaded successfully.', 201);
    }

    /**
     * Set a product image as the primary cover image.
     */
    public function setPrimaryImage(Product $product, ProductImage $image): JsonResponse
    {
        if ($image->product_id !== $product->id) {
            return $this->errorResponse('Image does not belong to this product.', 400);
        }

        $product->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return $this->successResponse(null, 'Primary product image updated.');
    }

    /**
     * Delete an image from product gallery.
     */
    public function deleteImage(Product $product, ProductImage $image, FileUploadService $uploader): JsonResponse
    {
        if ($image->product_id !== $product->id) {
            return $this->errorResponse('Image does not belong to this product.', 400);
        }

        $wasPrimary = $image->is_primary;
        $uploader->deleteFile($image->image_url);
        $image->delete();

        // If primary was deleted, set next image as primary
        if ($wasPrimary) {
            $next = $product->images()->first();
            if ($next) {
                $next->update(['is_primary' => true]);
            }
        }

        AuditService::log('PRODUCT_IMAGE_DELETED', $product);

        return $this->successResponse(null, 'Image deleted successfully.');
    }

    /**
     * Upload manufacturer technical PDF datasheet.
     */
    public function uploadDatasheet(Request $request, Product $product, FileUploadService $uploader): JsonResponse
    {
        $request->validate([
            'datasheet' => ['required', 'file', 'mimes:pdf', 'max:15360'],
        ]);

        // Clean prior file if exists
        if ($product->datasheet_url) {
            $uploader->deleteFile($product->datasheet_url);
        }

        $uploaded = $uploader->uploadDatasheet($request->file('datasheet'), $product->sku);

        $product->update([
            'datasheet_url' => $uploaded['url'],
            'datasheet_filename' => $uploaded['filename'],
        ]);

        AuditService::log('PRODUCT_DATASHEET_UPLOADED', $product, null, $uploaded);

        return $this->successResponse([
            'datasheet_url' => $product->datasheet_url,
            'datasheet_filename' => $product->datasheet_filename,
        ], 'Manufacturer datasheet uploaded successfully.');
    }

    /**
     * Delete datasheet.
     */
    public function deleteDatasheet(Product $product, FileUploadService $uploader): JsonResponse
    {
        if ($product->datasheet_url) {
            $uploader->deleteFile($product->datasheet_url);
            $product->update(['datasheet_url' => null, 'datasheet_filename' => null]);
            AuditService::log('PRODUCT_DATASHEET_DELETED', $product);
        }

        return $this->successResponse(null, 'Datasheet removed.');
    }

    /**
     * Delete product.
     */
    public function destroy(Product $product, FileUploadService $uploader): JsonResponse
    {
        // Check if product exists in confirmed sales orders
        if (DB::table('sales_order_items')->where('product_id', $product->id)->exists()) {
            return $this->errorResponse('Cannot delete product with historical sales orders. You may unpublish it instead.', 422);
        }

        // Delete images from disk
        foreach ($product->images as $img) {
            $uploader->deleteFile($img->image_url);
        }

        if ($product->datasheet_url) {
            $uploader->deleteFile($product->datasheet_url);
        }

        $name = $product->name;
        $product->delete();

        AuditService::log('PRODUCT_DELETED', null, ['name' => $name]);

        return $this->successResponse(null, "Product '{$name}' deleted from catalogue.");
    }
}
