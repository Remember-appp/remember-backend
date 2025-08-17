<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserConsent extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'kind',
        'version',
        'granted_at',
        'revoked_at',
        ];
    protected $casts = [
        'granted_at'=>'datetime',
        'revoked_at'=>'datetime',
        ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
