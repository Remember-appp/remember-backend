<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiEmbedding extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = ['dataset_item_id','model_name','embedding','created_at'];
    protected $casts = ['embedding'=>'array','created_at'=>'datetime'];

    public function item(): BelongsTo { return $this->belongsTo(AiDatasetItem::class, 'dataset_item_id'); }
}
