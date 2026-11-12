<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StockReceivingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('inventory.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'destination_store_id' => ['required', 'integer', 'exists:stores,id'],
            'product_id' => ['required_without:items', 'integer', 'exists:products,id'],
            'quantity' => ['required_without:items', 'integer', 'min:1'],
            'supplier' => ['nullable', 'string', 'max:150'],
            'purchase_reference' => ['nullable', 'string', 'max:100'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'receiving_date' => ['nullable', 'date'],
            'remarks' => ['nullable', 'string', 'max:1000'],
            'shelf_location' => ['nullable', 'string', 'max:100'],
            // Support multi-line items receiving
            'items' => ['nullable', 'array', 'min:1'],
            'items.*.product_id' => ['required_with:items', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1'],
            'items.*.unit_cost' => ['nullable', 'numeric', 'min:0'],
            'items.*.shelf_location' => ['nullable', 'string', 'max:100'],
            'items.*.notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
