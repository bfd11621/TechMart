<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'is_active' => (bool) $this->is_active,
            'is_primary' => (bool) $this->is_primary,
            'manager_id' => $this->manager_id,
            'manager' => $this->manager ? [
                'id' => $this->manager->id,
                'name' => $this->manager->name,
                'email' => $this->manager->email,
            ] : null,
            'users_count' => $this->users()->count(),
            'total_stock_quantity' => $this->totalStockQuantity(),
            'low_stock_count' => $this->lowStockCount(),
            'out_of_stock_count' => $this->outOfStockCount(),
            'employees' => $this->whenLoaded('users', function () {
                return $this->users->map(fn($u) => [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'role_name' => $u->role?->name,
                ]);
            }),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
