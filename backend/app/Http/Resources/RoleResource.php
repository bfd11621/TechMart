<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'is_system' => (bool) $this->is_system,
            'users_count' => $this->whenCounted('users'),
            'permissions' => PermissionResource::collection($this->whenLoaded('permissions')),
            'permission_ids' => $this->whenLoaded('permissions', function () {
                return $this->permissions->pluck('id');
            }),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
