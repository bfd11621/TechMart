<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'brand_id',
        'sku',
        'name',
        'slug',
        'model_number',
        'short_description',
        'full_description',
        'key_features',
        'base_price',
        'cost_price',
        'datasheet_url',
        'datasheet_filename',
        'is_featured',
        'is_published',
        'availability_status',
        'warranty_info',
    ];

    protected $casts = [
        'key_features' => 'array',
        'base_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('is_primary', 'desc')->orderBy('sort_order');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function specifications(): HasMany
    {
        return $this->hasMany(ProductSpecification::class)->orderBy('spec_group')->orderBy('sort_order');
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(StoreInventory::class);
    }

    public function totalStock(): int
    {
        return (int) $this->inventories()->sum('quantity');
    }

    public function availableStock(): int
    {
        return (int) $this->inventories()->sum(function ($inv) {
            return max(0, $inv->quantity - $inv->reserved_quantity);
        });
    }

    /**
     * Compute dynamic availability status based on inventory and rules.
     */
    public function computedAvailability(): string
    {
        if (!$this->is_published) {
            return 'UNPUBLISHED';
        }

        $stock = $this->totalStock();
        if ($stock <= 0) {
            return 'OUT_OF_STOCK';
        }
        if ($stock <= 5) {
            return 'LOW_STOCK';
        }
        return 'IN_STOCK';
    }
}
