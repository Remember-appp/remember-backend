<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivationStep extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'case_id',
        'step',
        'actor_type',
        'actor_id',
        'result',
        'notes',
        'created_at',
    ];

    protected $casts = [
        'uuid' => 'string',
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
