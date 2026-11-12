<?php

namespace App\Models;

use App\Enums\RfqStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RfqStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'rfq_request_id',
        'status',
        'previous_status',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'status' => RfqStatus::class,
        'previous_status' => RfqStatus::class,
    ];

    public function rfqRequest(): BelongsTo
    {
        return $this->belongsTo(RfqRequest::class, 'rfq_request_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
