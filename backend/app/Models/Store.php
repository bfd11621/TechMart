<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'postal_code',
        'is_active',
        'is_primary',
        'manager_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_primary' => 'boolean',
    ];

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(User::class, 'store_id');
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(StoreInventory::class);
    }

    public function sourceMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'source_store_id');
    }

    public function destinationMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'destination_store_id');
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    public function salesOrders(): HasMany
    {
        return $this->hasMany(SalesOrder::class);
    }

    public function totalStockQuantity(): int
    {
        return (int) $this->inventories()->sum('quantity');
    }

    public function lowStockCount(): int
    {
        return (int) $this->inventories()
            ->where('quantity', '>', 0)
            ->whereColumn('quantity', '<=', 'min_alert_quantity')
            ->count();
    }

    public function outOfStockCount(): int
    {
        return (int) $this->inventories()
            ->where('quantity', '<=', 0)
            ->count();
    }
}
