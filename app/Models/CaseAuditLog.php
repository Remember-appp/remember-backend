<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseAuditLog extends Model
{
    public $timestamps = false;
    protected $table = 'case_audit_logs';

    protected $fillable = [
        'uuid',
        'case_id',
        'actor_id',
        'action',
        'meta',
        'created_at',
    ];

    protected $casts = [
        'uuid' => 'string',
        'meta' => 'array',
        'created_at' => 'datetime',
    ];

    public function case(): BelongsTo
    {
        return $this->belongsTo(DeathActivationCase::class, 'case_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
