<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TtsVoice extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = ['user_id','vendor','model','voice_ref','consent_id'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function consent(): BelongsTo { return $this->belongsTo(UserConsent::class, 'consent_id'); }
}
