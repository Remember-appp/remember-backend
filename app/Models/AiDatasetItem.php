<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AiDatasetItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'dataset_id',
        'source_type',
        'source_id',
        'text',
        'meta',
        'created_at',
    ];

    protected $casts = [
        'uuid' => 'string',
        'meta' => 'array',
        'created_at' => 'datetime',
    ];

    public function dataset(): BelongsTo
    {
        return $this->belongsTo(AiDataset::class, 'dataset_id');
    }

    public function embedding(): HasOne
    {
        return $this->hasOne(AiEmbedding::class, 'dataset_item_id');
    }
}
