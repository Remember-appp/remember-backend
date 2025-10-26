<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OutboundWebhook extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'topic',
        'target_url',
        'secret',
        'active',
        'created_at',
    ];

    protected $casts = [
        'uuid' => 'string',
        'active' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(WebhookEvent::class, 'webhook_id');
    }
}
