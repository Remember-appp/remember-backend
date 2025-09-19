<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSession extends Model
{
    public $timestamps = false;

    protected $table = 'user_sessions';

    protected $fillable = [
        'uuid',
        'user_id',
        'user_agent',
        'ip_address',
        'created_at',
        'ended_at',
    ];

    protected $casts = [
        'uuid' => 'string',
        'created_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
