<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsageCounter extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = ['user_id','period_start','period_end','metric','value'];
    protected $casts = ['period_start'=>'date','period_end'=>'date','value'=>'integer'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
