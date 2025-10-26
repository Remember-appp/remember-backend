<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiKey extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'owner_user_id',
        'label',
        'hash',
        'scopes',
        'created_at',
        'revoked_at',
    ];

    protected $casts = [
        'uuid' => 'string',
        'scopes' => 'array',
        'created_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }
}
