<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
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
            'base_price' => (float) $this->base_price,
            'cost_price' => $request->user() ? (float) $this->cost_price : null,
            'is_featured' => (bool) $this->is_featured,
            'is_published' => (bool) $this->is_published,
            'availability_status' => $this->availability_status ?? $this->computedAvailability(),
            'warranty_info' => $this->warranty_info,
            'primary_image' => $primaryImg,
            'datasheet_url' => $datasheet,
            'datasheet_filename' => $this->datasheet_filename,
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'slug' => $this->category->slug,
                    'parent_id' => $this->category->parent_id,
                ];
            }),
            'brand' => $this->whenLoaded('brand', function () {
                return [
                    'id' => $this->brand->id,
                    'name' => $this->brand->name,
                    'slug' => $this->brand->slug,
                    'logo_url' => $this->brand->logo_url,
                ];
            }),
            'total_stock' => $this->totalStock(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
