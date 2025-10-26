<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HeartbeatEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'user_id',
        'kind',
        'status',
        'sent_at',
        'responded_at',
    ];

    protected $casts = [
        'uuid' => 'string',
        'sent_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
