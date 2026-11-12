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
            'payment_method' => ['nullable', 'string', 'in:CASH,BANK_TRANSFER,CHEQUE,CREDIT_CARD,LETTER_OF_CREDIT'],
            'initial_payment_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
