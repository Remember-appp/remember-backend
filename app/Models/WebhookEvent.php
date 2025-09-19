<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'webhook_id',
        'payload',
        'status',
        'created_at',
    ];

    protected $casts = [
        'uuid' => 'string',
        'payload' => 'array',
        'created_at' => 'datetime',
    ];

    public function webhook(): BelongsTo
    {
        return $this->belongsTo(OutboundWebhook::class, 'webhook_id');
    }
}
