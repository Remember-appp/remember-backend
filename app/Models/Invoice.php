<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'subscription_id','amount_cents','currency','status','issued_at','paid_at'
    ];
    protected $casts = ['issued_at'=>'datetime','paid_at'=>'datetime'];

    public function subscription(): BelongsTo { return $this->belongsTo(Subscription::class); }
}
