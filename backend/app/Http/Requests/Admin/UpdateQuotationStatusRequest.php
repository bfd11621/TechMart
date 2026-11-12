<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuotationStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('quotations.approve') ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:DRAFT,PREPARED,SENT,APPROVED,REJECTED,EXPIRED,CONVERTED'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
