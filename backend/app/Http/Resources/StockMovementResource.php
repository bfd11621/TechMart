<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockMovementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'movement_number' => $this->movement_number,
            'type' => $this->type->value,
            'type_label' => $this->type->label(),
            'badge_variant' => $this->type->badgeVariant(),
            'direction' => $this->type->direction(),
            'supplier' => $this->supplier,
            'external_reference' => $this->external_reference,
            'status' => $this->status,
            'source_store' => $this->sourceStore ? [
                'id' => $this->sourceStore->id,
                'name' => $this->sourceStore->name,
                'code' => $this->sourceStore->code,
            ] : null,
            'destination_store' => $this->destinationStore ? [
                'id' => $this->destinationStore->id,
                'name' => $this->destinationStore->name,
                'code' => $this->destinationStore->code,
            ] : null,
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ] : null,
            'reason' => $this->reason,
            'notes' => $this->notes,
            'items_count' => $this->items()->count(),
            'total_quantity' => (int) $this->items()->sum('quantity'),
            'total_valuation' => (float) $this->totalValuation(),
            'items' => StockMovementItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at?->toIso8601String(),
            'formatted_created_at' => $this->created_at?->format('M d, Y H:i'),
        ];
    }
}
