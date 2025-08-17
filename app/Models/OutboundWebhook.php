<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OutboundWebhook extends Model
{
    use HasUuids;

    public $timestamps = false;
    protected $fillable = ['topic','target_url','secret','active','created_at'];
    protected $casts = ['active'=>'boolean','created_at'=>'datetime'];

    public function events(): HasMany { return $this->hasMany(WebhookEvent::class, 'webhook_id'); }
}
