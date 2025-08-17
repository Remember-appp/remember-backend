<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class RetentionPolicy extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = ['subject','rule','ttl_days'];
}
