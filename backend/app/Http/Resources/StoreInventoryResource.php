<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreInventoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $product = $this->product;
        $store = $this->store;
        $costPrice = (float) ($product->cost_price ?? 0.00);
        $totalValuation = round($this->quantity * $costPrice, 2);

        $lastMovementDate = $this->last_movement_at ?? $this->updated_at ?? $this->created_at;
        $agingDays = $lastMovementDate ? now()->diffInDays($lastMovementDate) : 0;

        return [
            'id' => $this->id,
            'store_id' => $this->store_id,
            'store_name' => $store?->name,
            'store_code' => $store?->code,
            'product_id' => $this->product_id,
            'product_name' => $product?->name,
            'sku' => $product?->sku,
            'model_number' => $product?->model_number,
            'product_slug' => $product?->slug,
            'brand_name' => $product?->brand?->name,
            'category_name' => $product?->category?->name,
            'primary_image_url' => $product?->primaryImage?->image_url ?? $product?->images?->first()?->image_url,
            'quantity' => (int) $this->quantity,
            'reserved_quantity' => (int) $this->reserved_quantity,
            'available_quantity' => (int) max(0, $this->quantity - $this->reserved_quantity),
            'min_alert_quantity' => (int) $this->min_alert_quantity,
            'shelf_location' => $this->shelf_location,
            'stock_status' => $this->stockStatus(),
            'stock_status_label' => $this->stockStatusLabel(),
            'badge_variant' => $this->stockStatusBadge(),
            'cost_price' => $costPrice,
            'base_price' => (float) ($product->base_price ?? 0.00),
            'stock_valuation' => $totalValuation,
            'last_movement_at' => $this->last_movement_at?->toIso8601String(),
            'formatted_last_movement' => $this->last_movement_at?->format('M d, Y H:i') ?? 'No movement logged',
            'aging_days' => $agingDays,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
