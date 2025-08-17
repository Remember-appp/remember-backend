<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrustedContact extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'name',
        'relation',
        'email',
        'phone',
        'quorum_group',
        'created_at',
        ];
    protected $casts = [
        'created_at'=>'datetime',
        ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
