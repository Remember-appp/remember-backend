<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'actor_user_id','scope','action','entity_table','entity_id','meta','created_at'
    ];
    protected $casts = ['meta'=>'array','created_at'=>'datetime'];

    public function actor(): BelongsTo { return $this->belongsTo(User::class, 'actor_user_id'); }
}
