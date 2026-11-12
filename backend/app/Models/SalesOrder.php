<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'quotation_id',
        'store_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'company_name',
        'billing_address',
        'shipping_address',
        'status',
        'payment_status',
        'payment_method',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'paid_amount',
        'created_by_user_id',
        'confirmed_by_user_id',
        'confirmed_at',
        'notes',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'payment_status' => PaymentStatus::class,
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'confirmed_at' => 'datetime',
    ];

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by_user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function balanceDue(): float
    {
        return max(0.00, (float)$this->total_amount - (float)$this->paid_amount);
    }

    public function isConfirmed(): bool
    {
        return $this->status !== OrderStatus::PENDING && $this->status !== OrderStatus::CANCELLED;
    }

    public function totalItemsQuantity(): int
    {
        return (int) $this->items()->sum('quantity');
    }

    /**
     * Scope for orders that qualify as recognized business revenue.
     * Excludes PENDING and CANCELLED orders.
     */
    public function scopeRecognizedRevenue(Builder $query): Builder
    {
        return $query->whereIn('status', [
            OrderStatus::CONFIRMED->value,
            OrderStatus::PROCESSING->value,
            OrderStatus::COMPLETED->value,
        ]);
    }
}
