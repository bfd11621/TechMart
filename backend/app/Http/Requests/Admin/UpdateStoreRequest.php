<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('stores.update') ?? false;
    }

    public function rules(): array
    {
        $storeId = $this->route('store') instanceof \App\Models\Store 
            ? $this->route('store')->id 
            : $this->route('store');

        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('stores')->ignore($storeId)],
            'name' => ['required', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
            'is_primary' => ['boolean'],
            'manager_id' => ['nullable', 'exists:users,id'],
        ];
    }
}
