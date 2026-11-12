<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RfqItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'rfq_request_id',
        'product_id',
        'quantity',
        'target_price',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'target_price' => 'decimal:2',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(RfqRequest::class, 'rfq_request_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
