<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AiDatasetItem extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = ['dataset_id','source_type','source_id','text','meta','created_at'];
    protected $casts = ['meta'=>'array','created_at'=>'datetime'];

    public function dataset(): BelongsTo { return $this->belongsTo(AiDataset::class, 'dataset_id'); }
    public function embedding(): HasOne { return $this->hasOne(AiEmbedding::class, 'dataset_item_id'); }
}
