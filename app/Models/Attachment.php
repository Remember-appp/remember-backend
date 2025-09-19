<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attachment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'message_id',
        'asset_id',
        'kind',
        'position',
    ];

    protected $casts = [
        'uuid' => 'string',
        'position' => 'integer',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
