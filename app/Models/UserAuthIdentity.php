<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAuthIdentity extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'type',
        'secret_hash',
        'provider',
        'provider_uid',
        ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
