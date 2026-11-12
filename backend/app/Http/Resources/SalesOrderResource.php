<?php

namespace App\Http\Resources;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $statusEnum = $this->status instanceof OrderStatus
            ? $this->status
            : OrderStatus::tryFrom($this->status);

        $paymentStatusEnum = $this->payment_status instanceof PaymentStatus
            ? $this->payment_status
            : PaymentStatus::tryFrom($this->payment_status);

        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'quotation_id' => $this->quotation_id,
            'quotation_number' => $this->quotation?->quotation_number,
            'store_id' => $this->store_id,
            'store_name' => $this->store?->name,
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_phone' => $this->customer_phone,
            'company_name' => $this->company_name,
            'billing_address' => $this->billing_address,
            'shipping_address' => $this->shipping_address,
            'status' => $this->status instanceof OrderStatus ? $this->status->value : $this->status,
            'status_label' => $statusEnum?->label() ?? $this->status,
            'badge_variant' => $statusEnum?->badgeVariant() ?? 'neutral',
            'payment_status' => $this->payment_status instanceof PaymentStatus ? $this->payment_status->value : $this->payment_status,
            'payment_status_label' => $paymentStatusEnum?->label() ?? $this->payment_status,
            'payment_badge_variant' => $paymentStatusEnum?->badgeVariant() ?? 'neutral',
            'payment_method' => $this->payment_method,
            'subtotal' => (float) $this->subtotal,
            'discount_amount' => (float) $this->discount_amount,
            'tax_amount' => (float) $this->tax_amount,
            'total_amount' => (float) $this->total_amount,
            'paid_amount' => (float) $this->paid_amount,
            'balance_due' => (float) $this->balanceDue(),
            'notes' => $this->notes,
            'created_by' => $this->createdBy ? [
                'id' => $this->createdBy->id,
                'name' => $this->createdBy->name,
            ] : null,
            'confirmed_by' => $this->confirmedBy ? [
                'id' => $this->confirmedBy->id,
                'name' => $this->confirmedBy->name,
            ] : null,
            'confirmed_at' => $this->confirmed_at?->toIso8601String(),
            'formatted_confirmed_at' => $this->confirmed_at?->format('M d, Y H:i'),
            'items_count' => $this->items()->count(),
            'total_quantity' => (int) $this->items()->sum('quantity'),
            'items' => SalesOrderItemResource::collection($this->whenLoaded('items')),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'created_at' => $this->created_at?->toIso8601String(),
            'formatted_created_at' => $this->created_at?->format('M d, Y H:i'),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
