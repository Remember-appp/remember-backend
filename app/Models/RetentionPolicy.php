<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RetentionPolicy extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'subject',
        'rule',
        'ttl_days',
    ];

    protected $casts = [
        'uuid' => 'string',
        'ttl_days' => 'integer',
    ];
}
