<?php

namespace App\Http\Resources;

use App\Enums\QuotationStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuotationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $statusEnum = $this->status instanceof QuotationStatus
            ? $this->status
            : QuotationStatus::tryFrom($this->status);

        return [
            'id' => $this->id,
            'quotation_number' => $this->quotation_number,
            'rfq_request_id' => $this->rfq_request_id,
            'rfq_number' => $this->rfqRequest?->rfq_number,
            'store_id' => $this->store_id,
            'store_name' => $this->store?->name,
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_phone' => $this->customer_phone,
            'company_name' => $this->company_name,
            'billing_address' => $this->billing_address,
            'status' => $this->status instanceof QuotationStatus ? $this->status->value : $this->status,
            'status_label' => $statusEnum?->label() ?? $this->status,
            'badge_variant' => $statusEnum?->badgeVariant() ?? 'neutral',
            'valid_until' => $this->valid_until?->format('Y-m-d'),
            'formatted_valid_until' => $this->valid_until?->format('M d, Y'),
            'is_expired' => $this->valid_until ? $this->valid_until->isPast() && $this->status !== QuotationStatus::CONVERTED : false,
            'subtotal' => (float) $this->subtotal,
            'discount_type' => $this->discount_type,
            'discount_value' => (float) $this->discount_value,
            'discount_amount' => (float) $this->discount_amount,
            'tax_rate' => (float) $this->tax_rate,
            'tax_amount' => (float) $this->tax_amount,
            'total_amount' => (float) $this->total_amount,
            'terms_conditions' => $this->terms_conditions,
            'notes' => $this->notes,
            'client_notes' => $this->client_notes,
            'prepared_by' => $this->preparedBy ? [
                'id' => $this->preparedBy->id,
                'name' => $this->preparedBy->name,
                'email' => $this->preparedBy->email,
            ] : null,
            'can_be_converted' => $this->canBeConverted(),
            'sales_order_id' => $this->salesOrder?->id,
            'sales_order_number' => $this->salesOrder?->order_number,
            'approved_at' => $this->approved_at?->toIso8601String(),
            'converted_at' => $this->converted_at?->toIso8601String(),
            'items_count' => $this->items()->count(),
            'total_quantity' => (int) $this->items()->sum('quantity'),
            'items' => QuotationItemResource::collection($this->whenLoaded('items')),
            'status_histories' => QuotationStatusHistoryResource::collection($this->whenLoaded('statusHistories')),
            'created_at' => $this->created_at?->toIso8601String(),
            'formatted_created_at' => $this->created_at?->format('M d, Y H:i'),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
