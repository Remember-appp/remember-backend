<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonAsset extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = ['person_id','asset_id','kind'];

    public function person(): BelongsTo { return $this->belongsTo(Person::class, 'person_id'); }
    public function asset(): BelongsTo { return $this->belongsTo(Asset::class, 'asset_id'); }
}
