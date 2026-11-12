<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesOrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sales_order_id' => $this->sales_order_id,
            'product_id' => $this->product_id,
            'product_name' => $this->product?->name ?? 'Enterprise Product',
            'product_model' => $this->product?->model_number ?? 'N/A',
            'product_sku' => $this->product?->sku ?? 'N/A',
            'product_image' => $this->product?->primary_image_url ?? null,
            'quantity' => (int) $this->quantity,
            'unit_cost' => (float) $this->unit_cost,
            'unit_price' => (float) $this->unit_price,
            'discount_amount' => (float) $this->discount_amount,
            'total_price' => (float) $this->total_price,
        ];
    }
}
