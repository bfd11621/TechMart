<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuotationItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quotation_id' => $this->quotation_id,
            'product_id' => $this->product_id,
            'product_name' => $this->product?->name ?? 'Enterprise Product',
            'product_model' => $this->product?->model_number ?? 'N/A',
            'product_sku' => $this->product?->sku ?? 'N/A',
            'product_image' => $this->product?->primary_image_url ?? null,
            'product_brand' => $this->product?->brand?->name ?? null,
            'quantity' => (int) $this->quantity,
            'unit_price' => (float) $this->unit_price,
            'discount_percent' => (float) $this->discount_percent,
            'total_price' => (float) $this->total_price,
            'notes' => $this->notes,
        ];
    }
}
