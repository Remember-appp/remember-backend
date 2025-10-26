<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'user_id_owner',
        'name',
        'email',
        'phone',
    ];

    protected $casts = [
        'uuid' => 'string',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_owner');
    }
}
