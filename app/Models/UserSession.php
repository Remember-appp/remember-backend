<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSession extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $table = 'user_sessions';

    protected $fillable = ['user_id','user_agent','ip_address','created_at','ended_at'];
    protected $casts = ['created_at'=>'datetime','ended_at'=>'datetime'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
