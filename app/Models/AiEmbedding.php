<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiEmbedding extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'uuid',
        'dataset_item_id',
        'model_name',
        'embedding',
        'created_at',
    ];

    protected $casts = [
        'uuid' => 'string',
        'embedding' => 'array',
        'created_at' => 'datetime',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(AiDatasetItem::class, 'dataset_item_id');
    }
}
