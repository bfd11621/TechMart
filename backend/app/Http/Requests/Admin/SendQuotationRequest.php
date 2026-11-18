<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SendQuotationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('quotations.update') ?? false;
    }

    public function rules(): array
    {
        return [
            'email' => ['nullable', 'email', 'max:255'],
            'message' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
