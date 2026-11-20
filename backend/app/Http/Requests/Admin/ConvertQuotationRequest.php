<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ConvertQuotationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('sales.confirm') ?? false;
    }

    public function rules(): array
    {
        return [
            'store_id' => ['nullable', 'integer', 'exists:stores,id'],
            'shipping_address' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['nullable', 'string', 'in:CASH,BANK_TRANSFER,CHECK,CHEQUE,CPO,NET_30,CREDIT_CARD,LETTER_OF_CREDIT'],
            'payment_status' => ['nullable', 'string', 'in:UNPAID,PARTIAL,PAID'],
            'initial_payment_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_reference' => ['nullable', 'string', 'max:100'],
            'client_po_number' => ['nullable', 'string', 'max:100'],
            'client_po_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
            'expected_delivery_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
