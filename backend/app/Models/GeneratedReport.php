<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneratedReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_number',
        'type',
        'title',
        'period_label',
        'from_date',
        'to_date',
        'store_id',
        'filters',
        'summary_metrics',
        'format',
        'generated_by_user_id',
    ];

    protected $casts = [
        'filters' => 'array',
        'summary_metrics' => 'array',
        'from_date' => 'date',
        'to_date' => 'date',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by_user_id');
    }
}
