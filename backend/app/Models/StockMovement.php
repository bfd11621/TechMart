<?php

namespace App\Models;

use App\Enums\StockMovementType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'movement_number',
        'type',
        'supplier',
        'external_reference',
        'status',
        'source_store_id',
        'destination_store_id',
        'reference_type',
        'reference_id',
        'user_id',
        'reason',
        'notes',
    ];

    protected $casts = [
        'type' => StockMovementType::class,
    ];

    public function sourceStore(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'source_store_id');
    }

    public function destinationStore(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'destination_store_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockMovementItem::class);
    }

    public function totalQuantity(): int
    {
        return (int) $this->items()->sum('quantity');
    }

    public function totalValuation(): float
    {
        return (float) $this->items()->sum(\DB::raw('quantity * unit_cost'));
    }
}
