<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'actor_user_id',
        'scope',
        'action',
        'entity_table',
        'entity_id',
        'meta',
        'created_at',
    ];

    protected $casts = [
        'uuid' => 'string',
        'meta' => 'array',
        'created_at' => 'datetime',
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}
