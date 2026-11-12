<?php

namespace App\Models;

use App\Enums\RfqStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RfqRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'rfq_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'company_name',
        'tax_number',
        'address',
        'city',
        'location',
        'preferred_store_id',
        'status',
        'customer_notes',
        'admin_notes',
        'assigned_to_user_id',
        'submitted_at',
    ];

    protected $casts = [
        'status' => RfqStatus::class,
        'submitted_at' => 'datetime',
    ];

    public function preferredStore(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'preferred_store_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(RfqItem::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(RfqStatusHistory::class)->orderBy('created_at', 'desc');
    }

    public function quotation(): HasOne
    {
        return $this->hasOne(Quotation::class);
    }

    /**
     * Record a status transition in the audit history.
     */
    public function logStatusChange(?User $user, RfqStatus|string $newStatus, ?string $notes = null): RfqStatusHistory
    {
        $statusEnum = $newStatus instanceof RfqStatus ? $newStatus : RfqStatus::from($newStatus);
        $prev = $this->status;

        $this->update(['status' => $statusEnum]);

        return $this->statusHistories()->create([
            'status' => $statusEnum,
            'previous_status' => $prev,
            'user_id' => $user?->id,
            'notes' => $notes,
        ]);
    }
}
