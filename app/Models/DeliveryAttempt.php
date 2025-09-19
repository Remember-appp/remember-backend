<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryAttempt extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'job_id',
        'vendor',
        'status',
        'error_code',
        'response',
        'created_at',
    ];

    protected $casts = [
        'uuid' => 'string',
        'response' => 'array',
        'created_at' => 'datetime',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(DeliveryJob::class, 'job_id');
    }
}
