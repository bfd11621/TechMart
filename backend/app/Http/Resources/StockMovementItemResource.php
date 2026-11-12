<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockMovementItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $product = $this->product;

        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_name' => $product?->name,
            'sku' => $product?->sku,
            'model_number' => $product?->model_number,
            'brand_name' => $product?->brand?->name,
            'primary_image_url' => $product?->primaryImage?->image_url ?? $product?->images?->first()?->image_url,
            'quantity' => (int) $this->quantity,
            'previous_quantity' => $this->previous_quantity !== null ? (int) $this->previous_quantity : null,
            'new_quantity' => $this->new_quantity !== null ? (int) $this->new_quantity : null,
            'unit_cost' => (float) $this->unit_cost,
            'line_total' => round((float) ($this->quantity * $this->unit_cost), 2),
            'notes' => $this->notes,
        ];
    }
}
