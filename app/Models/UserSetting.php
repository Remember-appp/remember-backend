<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSetting extends Model
{
    use HasUuids;

    public $timestamps = false;
    protected $primaryKey = 'user_id';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'tz',
        'locale',
        'privacy',
        'notifications',
        ];
    protected $casts = [
        'privacy'=>'array',
        'notifications'=>'array',
        ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
