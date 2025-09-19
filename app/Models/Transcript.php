<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transcript extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'asset_id',
        'lang',
        'text',
        'created_at',
    ];

    protected $casts = [
        'uuid' => 'string',
        'created_at' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function segments(): HasMany
    {
        return $this->hasMany(TranscriptSegment::class);
    }
}
