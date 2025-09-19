<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserConsent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'user_id',
        'kind',
        'version',
        'granted_at',
        'revoked_at',
    ];

    protected $casts = [
        'uuid' => 'string',
        'granted_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
