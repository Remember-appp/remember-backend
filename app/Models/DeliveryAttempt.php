<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryAttempt extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'job_id','vendor','status','error_code','response','created_at'
    ];
    protected $casts = [
        'response' => 'array',
        'created_at' => 'datetime',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(DeliveryJob::class, 'job_id');
    }
}
