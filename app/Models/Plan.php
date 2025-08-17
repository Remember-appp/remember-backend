<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = ['code','name','price_cents','currency','quotas'];
    protected $casts = ['quotas'=>'array'];

    public function subscriptions(): HasMany { return $this->hasMany(Subscription::class); }
}
