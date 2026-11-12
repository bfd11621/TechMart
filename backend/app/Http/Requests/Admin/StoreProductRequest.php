<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('products.create') ?? true;
    }

    public function rules(): array
    {
        $imageRule = $this->hasFile('image')
            ? ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120']
            : ['nullable', 'string'];

        $datasheetRule = $this->hasFile('datasheet')
            ? ['nullable', 'file', 'mimes:pdf', 'max:15360']
            : ['nullable', 'string'];

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['required', 'exists:brands,id'],
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug'],
            'model_number' => ['nullable', 'string', 'max:100'],
            'short_description' => ['nullable', 'string'],
            'full_description' => ['nullable', 'string'],
            'key_features' => ['nullable'],
            'key_features.*' => ['nullable', 'string'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'warranty_info' => ['nullable', 'string', 'max:150'],
            'is_featured' => ['nullable'],
            'is_published' => ['nullable'],
            'availability_status' => ['nullable', 'string'],
            'image' => $imageRule,
            'datasheet' => $datasheetRule,
            'datasheet_url' => ['nullable', 'string'],
            'datasheet_filename' => ['nullable', 'string'],
            'specifications' => ['nullable'],
            'images' => ['nullable'],
            'image_urls' => ['nullable'],
            'primary_image_index' => ['nullable'],
            'primary_image_url' => ['nullable', 'string'],
        ];
    }
}
