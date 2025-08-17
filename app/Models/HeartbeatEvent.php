<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HeartbeatEvent extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = ['user_id','kind','status','sent_at','responded_at'];
    protected $casts = ['sent_at'=>'datetime','responded_at'=>'datetime'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
