<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'subscription_id',
        'amount_cents',
        'currency',
        'status',
        'issued_at',
        'paid_at',
    ];

    protected $casts = [
        'uuid' => 'string',
        'amount_cents' => 'integer',
        'issued_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
