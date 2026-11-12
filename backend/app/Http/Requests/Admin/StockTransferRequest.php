<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StockTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('stock_transfers.create') ?? $this->user()?->hasPermission('inventory.transfer') ?? false;
    }

    public function rules(): array
    {
        return [
            'source_store_id' => ['required', 'integer', 'exists:stores,id', 'different:destination_store_id'],
            'destination_store_id' => ['required', 'integer', 'exists:stores,id', 'different:source_store_id'],
            'product_id' => ['required_without:items', 'integer', 'exists:products,id'],
            'quantity' => ['required_without:items', 'integer', 'min:1'],
            'transfer_reference' => ['nullable', 'string', 'max:100'],
            'transfer_date' => ['nullable', 'date'],
            'transfer_notes' => ['nullable', 'string', 'max:1000'],
            'reason' => ['nullable', 'string', 'max:255'],
            // Support multi-line transfer
            'items' => ['nullable', 'array', 'min:1'],
            'items.*.product_id' => ['required_with:items', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1'],
            'items.*.notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
