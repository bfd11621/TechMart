<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RfqRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'rfq_number' => $this->rfq_number,
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_phone' => $this->customer_phone,
            'company_name' => $this->company_name,
            'tax_number' => $this->tax_number,
            'address' => $this->address,
            'city' => $this->city,
            'location' => $this->location ?: ($this->city ?: $this->address),
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'badge_variant' => $this->status->badgeVariant(),
            'customer_notes' => $this->customer_notes,
            'admin_notes' => $this->admin_notes,
            'preferred_store' => $this->preferredStore ? [
                'id' => $this->preferredStore->id,
                'name' => $this->preferredStore->name,
                'code' => $this->preferredStore->code,
            ] : null,
            'assigned_user' => $this->assignedUser ? [
                'id' => $this->assignedUser->id,
                'name' => $this->assignedUser->name,
                'email' => $this->assignedUser->email,
            ] : null,
            'items_count' => $this->items()->count(),
            'total_quantity' => (int) $this->items()->sum('quantity'),
            'items' => RfqItemResource::collection($this->whenLoaded('items')),
            'status_histories' => RfqStatusHistoryResource::collection($this->whenLoaded('statusHistories')),
            'submitted_at' => $this->submitted_at?->toIso8601String(),
            'formatted_submitted_at' => $this->submitted_at?->format('M d, Y H:i'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
