<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'owner_user_id',
        'storage_key',
        'mime',
        'size_bytes',
        'content_hash',
        'meta',
        'created_at',
    ];

    protected $casts = [
        'uuid' => 'string',
        'meta' => 'array',
        'created_at' => 'datetime',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }
}
