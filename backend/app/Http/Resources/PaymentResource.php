<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sales_order_id' => $this->sales_order_id,
            'receipt_number' => $this->receipt_number,
            'amount' => (float) $this->amount,
            'payment_method' => $this->payment_method,
            'payment_reference' => $this->payment_reference,
            'payment_date' => $this->payment_date?->format('Y-m-d'),
            'formatted_payment_date' => $this->payment_date?->format('M d, Y'),
            'notes' => $this->notes,
            'received_by' => $this->receivedBy ? [
                'id' => $this->receivedBy->id,
                'name' => $this->receivedBy->name,
            ] : null,
            'created_at' => $this->created_at?->toIso8601String(),
            'formatted_created_at' => $this->created_at?->format('M d, Y H:i'),
        ];
    }
}
