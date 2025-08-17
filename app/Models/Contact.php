<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    use HasUuids;

    public $timestamps = false;
    protected $fillable = ['user_id_owner','name','email','phone'];

    public function owner(): BelongsTo { return $this->belongsTo(User::class, 'user_id_owner'); }
}
