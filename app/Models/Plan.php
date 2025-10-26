<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'code',
        'name',
        'price_cents',
        'currency',
        'quotas',
    ];

    protected $casts = [
        'uuid' => 'string',
        'price_cents' => 'integer',
        'quotas' => 'array',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
