<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeathActivationCase extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = ['user_id','state','opened_at','closed_at'];
    protected $casts = ['opened_at'=>'datetime','closed_at'=>'datetime'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function steps(): HasMany { return $this->hasMany(ActivationStep::class, 'case_id'); }
    public function stopRequests(): HasMany { return $this->hasMany(StopRequest::class, 'case_id'); }
    public function auditLogs(): HasMany { return $this->hasMany(CaseAuditLog::class, 'case_id'); }
}
