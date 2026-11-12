<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('roles.update') ?? false;
    }

    public function rules(): array
    {
        $roleId = $this->route('role') instanceof \App\Models\Role 
            ? $this->route('role')->id 
            : $this->route('role');

        return [
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:100', Rule::unique('roles')->ignore($roleId)],
            'description' => ['nullable', 'string'],
        ];
    }
}
