<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreInventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'product_id',
        'quantity',
        'reserved_quantity',
        'min_alert_quantity',
        'shelf_location',
        'last_movement_at',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'reserved_quantity' => 'integer',
        'min_alert_quantity' => 'integer',
        'last_movement_at' => 'datetime',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function availableQuantity(): int
    {
        return max(0, $this->quantity - $this->reserved_quantity);
    }

    public function isLowStock(): bool
    {
        return $this->quantity > 0 && $this->quantity <= $this->min_alert_quantity;
    }

    public function isOutOfStock(): bool
    {
        return $this->quantity <= 0;
    }

    public function stockStatus(): string
    {
        if ($this->quantity <= 0) {
            return 'OUT_OF_STOCK';
        }
        if ($this->quantity <= $this->min_alert_quantity) {
            return 'LOW_STOCK';
        }
        return 'IN_STOCK';
    }

    public function stockStatusLabel(): string
    {
        return match ($this->stockStatus()) {
            'OUT_OF_STOCK' => 'Out of Stock',
            'LOW_STOCK' => 'Low Stock Alert',
            'IN_STOCK' => 'In Stock',
        };
    }

    public function stockStatusBadge(): string
    {
        return match ($this->stockStatus()) {
            'OUT_OF_STOCK' => 'danger',
            'LOW_STOCK' => 'warning',
            'IN_STOCK' => 'success',
        };
    }
}
