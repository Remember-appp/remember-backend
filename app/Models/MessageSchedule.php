<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageSchedule extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'message_id',
        'trigger_type',
        'cron_expr',
        'start_date',
        'repeat_yearly',
    ];

    protected $casts = [
        'uuid' => 'string',
        'start_date' => 'date',
        'repeat_yearly' => 'boolean',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }
}
