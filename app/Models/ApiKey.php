<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiKey extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = ['owner_user_id','label','hash','scopes','created_at','revoked_at'];
    protected $casts = ['scopes'=>'array','created_at'=>'datetime','revoked_at'=>'datetime'];

    public function owner(): BelongsTo { return $this->belongsTo(User::class, 'owner_user_id'); }
}
