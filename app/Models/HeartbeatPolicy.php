<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HeartbeatPolicy extends Model
{
    protected $table = 'heartbeat_policies';
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'cadence_days',
        'escalation_days',
        'grace_days',
        'channels',
    ];

    protected $casts = [
        'channels' => 'array',
        'cadence_days' => 'integer',
        'escalation_days' => 'integer',
        'grace_days' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
