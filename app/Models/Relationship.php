<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Relationship extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'person_id',
        'other_person_id',
        'type',
        'since',
        'until',
    ];

    protected $casts = [
        'uuid' => 'string',
        'since' => 'date',
        'until' => 'date',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }

    public function other(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'other_person_id');
    }
}
