<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StockIssuingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('inventory.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'source_store_id' => ['required', 'integer', 'exists:stores,id'],
            'product_id' => ['required_without:items', 'integer', 'exists:products,id'],
            'quantity' => ['required_without:items', 'integer', 'min:1'],
            'issue_reason' => ['required', 'string', 'max:255'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'issuing_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            // Support multi-line items issuing
            'items' => ['nullable', 'array', 'min:1'],
            'items.*.product_id' => ['required_with:items', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1'],
            'items.*.notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
