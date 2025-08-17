<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageSchedule extends Model
{
    use HasUuids;

    public $timestamps = false;
    protected $fillable = ['message_id','trigger_type','cron_expr','start_date','repeat_yearly'];
    protected $casts = ['start_date'=>'date','repeat_yearly'=>'boolean'];

    public function message(): BelongsTo { return $this->belongsTo(Message::class); }
}
