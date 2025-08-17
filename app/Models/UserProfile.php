<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasUuids;

    public $timestamps = false;
    protected $primaryKey = 'user_id';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'display_name',
        'bio',
        'photo_asset_id',
        'birth_date',
        'favorite_phrases',
        ];
    protected $casts = [
        'birth_date'=>'date',
        'favorite_phrases'=>'array',
        ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function photo(): BelongsTo { return $this->belongsTo(Asset::class, 'photo_asset_id'); }
}
