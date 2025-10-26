<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'user_id',
        'plan_id',
        'status',
        'current_period_start',
        'current_period_end',
        'cancel_at',
    ];

    protected $casts = [
        'uuid' => 'string',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'cancel_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
