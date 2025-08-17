<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MergeRequest extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = ['initiator_user_id','person_a_id','person_b_id','state','evidence_assets','created_at'];
    protected $casts = ['evidence_assets'=>'array','created_at'=>'datetime'];

    public function initiator(): BelongsTo { return $this->belongsTo(User::class, 'initiator_user_id'); }
    public function personA(): BelongsTo { return $this->belongsTo(Person::class, 'person_a_id'); }
    public function personB(): BelongsTo { return $this->belongsTo(Person::class, 'person_b_id'); }
}
