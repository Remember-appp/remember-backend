<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Person extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'owner_user_id',
        'full_name',
        'birth_date',
        'death_date',
        'bio',
        'photo_asset_id',
        'is_user_linked',
        'linked_user_id',
    ];

    protected $casts = [
        'uuid' => 'string',
        'birth_date' => 'date',
        'death_date' => 'date',
        'is_user_linked' => 'boolean',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function photo(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'photo_asset_id');
    }

    public function linkedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'linked_user_id');
    }

    public function links(): HasMany
    {
        return $this->hasMany(Relationship::class, 'person_id');
    }

    public function reverseLinks(): HasMany
    {
        return $this->hasMany(Relationship::class, 'other_person_id');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(PersonAsset::class, 'person_id');
    }
}
