<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiSession extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'user_id',
        'relative_user_id',
        'started_at',
        'ended_at',
        'meta',
    ];

    protected $casts = [
        'uuid' => 'string',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function relative(): BelongsTo
    {
        return $this->belongsTo(User::class, 'relative_user_id');
    }
}
