<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('products.update') ?? true;
    }

    public function rules(): array
    {
        $productId = $this->route('product') instanceof \App\Models\Product 
            ? $this->route('product')->id 
            : $this->route('product');

        // Validation rule accepts either an uploaded file (mimes:jpg,jpeg,png,webp) OR a nullable string (existing URL)
        $imageRule = $this->hasFile('image')
            ? ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120']
            : ['nullable', 'string'];

        // Validation rule accepts either an uploaded PDF file (mimes:pdf) OR a nullable string (existing URL)
        $datasheetRule = $this->hasFile('datasheet')
            ? ['nullable', 'file', 'mimes:pdf', 'max:15360']
            : ['nullable', 'string'];

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id' => ['required', 'exists:brands,id'],
            'sku' => ['required', 'string', 'max:100', Rule::unique('products')->ignore($productId)],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('products')->ignore($productId)],
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
            'images.*.image_url' => ['nullable', 'string'],
            'images.*.is_primary' => ['nullable'],
            'images.*.sort_order' => ['nullable', 'integer'],
        ];
    }
}
