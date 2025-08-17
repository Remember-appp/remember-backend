<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TranscriptSegment extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = ['transcript_id','t_start','t_end','text','topic_tag'];
    protected $casts = ['t_start'=>'decimal:3','t_end'=>'decimal:3'];

    public function transcript(): BelongsTo { return $this->belongsTo(Transcript::class); }
}
