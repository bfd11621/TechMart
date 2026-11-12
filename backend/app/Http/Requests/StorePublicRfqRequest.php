<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePublicRfqRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:150',
            'customer_email' => 'required|email|max:150',
            'customer_phone' => 'required|string|max:50',
            'company_name' => 'nullable|string|max:150',
            'location' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'preferred_store_id' => 'nullable|exists:stores,id',
            'customer_notes' => 'nullable|string|max:3000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1|max:9999',
            'items.*.target_price' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Please provide your full contact name.',
            'customer_email.required' => 'A valid corporate email address is required.',
            'customer_phone.required' => 'A contact telephone number is required for quote verification.',
            'items.required' => 'Your quotation basket cannot be empty.',
            'items.min' => 'Please add at least one IT hardware product to the quotation basket.',
            'items.*.product_id.exists' => 'One or more selected equipment items are invalid or unavailable.',
            'items.*.quantity.min' => 'Quantity must be at least 1 unit.',
        ];
    }
}
