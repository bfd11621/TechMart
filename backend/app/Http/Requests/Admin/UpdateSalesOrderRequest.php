<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSalesOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('sales.update')
            || $this->user()?->hasPermission('sales.confirm')
            || $this->user()?->hasPermission('sales.create');
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', 'string', 'in:CONFIRMED,PROCESSING,READY_FOR_DISPATCH,COMPLETED,CANCELLED'],
            'payment_status' => ['nullable', 'string', 'in:UNPAID,PARTIAL,PAID'],
            'payment_reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
};
