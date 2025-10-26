<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiDataset extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'user_id',
        'title',
        'description',
        'created_at',
    ];

    protected $casts = [
        'uuid' => 'string',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(AiDatasetItem::class, 'dataset_id');
    }
}
