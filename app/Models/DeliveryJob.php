<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryJob extends Model
{
    use HasUuids;

    public $timestamps = false;
    protected $fillable = ['message_id','recipient_id','channel','scheduled_at','state','created_at'];
    protected $casts = ['scheduled_at'=>'datetime','created_at'=>'datetime'];

    public function message(): BelongsTo { return $this->belongsTo(Message::class); }
    public function recipient(): BelongsTo { return $this->belongsTo(MessageRecipient::class, 'recipient_id'); }
    public function attempts(): HasMany { return $this->hasMany(DeliveryAttempt::class, 'job_id'); }
}
