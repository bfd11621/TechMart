<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $logo = $this->logo_url;
        if ($logo && !str_starts_with($logo, 'http://') && !str_starts_with($logo, 'https://')) {
            $logo = url($logo);
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'logo_url' => $logo,
            'website' => $this->website,
            'description' => $this->description,
            'is_active' => (bool) $this->is_active,
            'products_count' => $this->whenCounted('products'),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
