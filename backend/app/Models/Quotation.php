<?php

namespace App\Models;

use App\Enums\QuotationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_number',
        'rfq_request_id',
        'store_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'company_name',
        'billing_address',
        'status',
        'valid_until',
        'subtotal',
        'discount_type',
        'discount_value',
        'discount_amount',
        'tax_rate',
        'tax_amount',
        'total_amount',
        'terms_conditions',
        'notes',
        'client_notes',
        'prepared_by_user_id',
        'approved_at',
        'converted_at',
    ];

    protected $casts = [
        'status' => QuotationStatus::class,
        'valid_until' => 'date',
        'subtotal' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'converted_at' => 'datetime',
    ];

    public function rfqRequest(): BelongsTo
    {
        return $this->belongsTo(RfqRequest::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function preparedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by_user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(QuotationStatusHistory::class)->latest();
    }

    public function salesOrder(): HasOne
    {
        return $this->hasOne(SalesOrder::class);
    }

    public function canBeConverted(): bool
    {
        return $this->status === QuotationStatus::APPROVED &&
               is_null($this->converted_at) &&
               !$this->salesOrder()->exists();
    }

    public function logStatusChange(QuotationStatus|string $newStatus, ?User $user = null, ?string $notes = null): void
    {
        $statusEnum = $newStatus instanceof QuotationStatus ? $newStatus : QuotationStatus::from($newStatus);
        $previous = $this->status?->value ?? null;

        $this->update(['status' => $statusEnum]);

        $this->statusHistories()->create([
            'status' => $statusEnum->value,
            'previous_status' => $previous,
            'user_id' => $user?->id,
            'notes' => $notes ?? "Quotation transitioned to {$statusEnum->label()}.",
        ]);
    }
}
