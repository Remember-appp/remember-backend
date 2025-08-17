<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attachment extends Model
{
    use HasUuids;

    public $timestamps = false;
    protected $fillable = ['message_id','asset_id','kind','position'];

    public function message(): BelongsTo { return $this->belongsTo(Message::class); }
    public function asset(): BelongsTo { return $this->belongsTo(Asset::class); }
}
