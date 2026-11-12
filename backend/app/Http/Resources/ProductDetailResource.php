<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Group specifications by spec_group
        $groupedSpecs = $this->specifications->groupBy('spec_group')->map(function ($items, $group) {
            return [
                'group' => $group,
                'items' => $items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->spec_name,
                        'value' => $item->spec_value,
                        'sort_order' => $item->sort_order,
                    ];
                })->values(),
            ];
        })->values();

        $primaryImg = $this->primaryImage?->image_url ?? $this->images->first()?->image_url;
        if ($primaryImg && !str_starts_with($primaryImg, 'http://') && !str_starts_with($primaryImg, 'https://')) {
            $primaryImg = url($primaryImg);
        }

        $datasheet = $this->datasheet_url;
        if ($datasheet && !str_starts_with($datasheet, 'http://') && !str_starts_with($datasheet, 'https://')) {
            $datasheet = url($datasheet);
        }

        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'name' => $this->name,
            'slug' => $this->slug,
            'model_number' => $this->model_number,
            'short_description' => $this->short_description,
            'full_description' => $this->full_description,
            'key_features' => $this->key_features ?? [],
            'base_price' => (float) $this->base_price,
            'cost_price' => $request->user() ? (float) $this->cost_price : null,
            'is_featured' => (bool) $this->is_featured,
            'is_published' => (bool) $this->is_published,
            'availability_status' => $this->availability_status ?? $this->computedAvailability(),
            'warranty_info' => $this->warranty_info,
            'datasheet_url' => $datasheet,
            'datasheet_filename' => $this->datasheet_filename,
            'category_id' => $this->category_id,
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'slug' => $this->category->slug,
                    'parent_id' => $this->category->parent_id,
                    'parent' => $this->category->parent ? [
                        'id' => $this->category->parent->id,
                        'name' => $this->category->parent->name,
                    ] : null,
                ];
            }),
            'brand_id' => $this->brand_id,
            'brand' => $this->whenLoaded('brand', function () {
                return [
                    'id' => $this->brand->id,
                    'name' => $this->brand->name,
                    'slug' => $this->brand->slug,
                    'logo_url' => $this->brand->logo_url,
                    'website' => $this->brand->website,
                ];
            }),
            'images' => $this->images->map(function ($img) {
                $imgUrl = $img->image_url;
                if ($imgUrl && !str_starts_with($imgUrl, 'http://') && !str_starts_with($imgUrl, 'https://')) {
                    $imgUrl = url($imgUrl);
                }
                return [
                    'id' => $img->id,
                    'url' => $imgUrl,
                    'image_url' => $imgUrl,
                    'is_primary' => (bool) $img->is_primary,
                    'sort_order' => (int) $img->sort_order,
                ];
            }),
            'primary_image' => $primaryImg,
            'specifications' => $this->specifications->map(function ($s) {
                return [
                    'id' => $s->id,
                    'group' => $s->spec_group,
                    'name' => $s->spec_name,
                    'value' => $s->spec_value,
                    'sort_order' => $s->sort_order,
                ];
            }),
            'specifications_grouped' => $groupedSpecs,
            'store_inventories' => $this->whenLoaded('inventories', function () {
                return $this->inventories->map(function ($inv) {
                    return [
                        'store_id' => $inv->store_id,
                        'store_name' => $inv->store?->name,
                        'store_code' => $inv->store?->code,
                        'quantity' => $inv->quantity,
                        'available' => $inv->availableQuantity(),
                        'shelf_location' => $inv->shelf_location,
                        'is_low_stock' => $inv->isLowStock(),
                    ];
                });
            }),
            'total_stock' => $this->totalStock(),
            'available_stock' => $this->availableStock(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
