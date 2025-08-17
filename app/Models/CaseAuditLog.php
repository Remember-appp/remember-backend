<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseAuditLog extends Model
{
    use HasUuids;

    public $timestamps = false;
    protected $table = 'case_audit_logs';

    protected $fillable = ['case_id','actor_id','action','meta','created_at'];
    protected $casts = ['meta'=>'array','created_at'=>'datetime'];

    public function case(): BelongsTo { return $this->belongsTo(DeathActivationCase::class, 'case_id'); }
    public function actor(): BelongsTo { return $this->belongsTo(User::class, 'actor_id'); }
}
