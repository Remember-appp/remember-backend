<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Asset extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uuid',          // public id
        'owner_user_id',
        'disk',
        'storage_key',
        'mime',
        'size_bytes',
        'content_hash',
        'meta',
        'created_at',
    ];

    protected $casts = [
        'uuid'       => 'string',
        'meta'       => 'array',
        'created_at' => 'datetime',
    ];

    // Автогенерація uuid, якщо не передано
    protected static function booted(): void
    {
        static::creating(function (self $m) {
            if (empty($m->uuid)) {
                $m->uuid = (string) Str::uuid();
            }
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }
}
