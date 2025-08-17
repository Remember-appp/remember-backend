<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StopRequest extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = ['case_id','requester_id','reason_text','state','evidence_assets','created_at','decided_at'];
    protected $casts = ['evidence_assets'=>'array','created_at'=>'datetime','decided_at'=>'datetime'];

    public function case(): BelongsTo { return $this->belongsTo(DeathActivationCase::class, 'case_id'); }
    public function requester(): BelongsTo { return $this->belongsTo(User::class, 'requester_id'); }
}
