<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Relationship extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = ['person_id','other_person_id','type','since','until'];
    protected $casts = ['since'=>'date','until'=>'date'];

    public function person(): BelongsTo { return $this->belongsTo(Person::class, 'person_id'); }
    public function other(): BelongsTo { return $this->belongsTo(Person::class, 'other_person_id'); }
}
