<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAuthIdentity extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'uuid',
        'type',
        'secret_hash',
        'provider',
        'provider_uid',
    ];

    protected $casts = [
        'uuid' => 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
