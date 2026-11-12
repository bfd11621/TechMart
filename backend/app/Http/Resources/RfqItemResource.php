<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RfqItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_name' => $this->product?->name ?? 'Custom Equipment',
            'product_sku' => $this->product?->sku ?? 'N/A',
            'model_number' => $this->product?->model_number ?? 'N/A',
            'product_slug' => $this->product?->slug ?? '',
            'primary_image_url' => $this->product?->primaryImage?->image_url ?? $this->product?->primary_image_url,
            'brand_name' => $this->product?->brand?->name ?? 'Hardware',
            'category_name' => $this->product?->category?->name ?? 'IT Solutions',
            'quantity' => (int) $this->quantity,
            'target_price' => $this->target_price ? (float) $this->target_price : null,
            'notes' => $this->notes,
        ];
    }
}
