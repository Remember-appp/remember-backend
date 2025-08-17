<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['user_id','title','body_text','status','locked_at'];
    protected $casts = ['locked_at'=>'datetime'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function schedules(): HasMany { return $this->hasMany(MessageSchedule::class); }
    public function recipients(): HasMany { return $this->hasMany(MessageRecipient::class); }
    public function attachments(): HasMany { return $this->hasMany(Attachment::class); }
    public function deliveryJobs(): HasMany { return $this->hasMany(DeliveryJob::class); }
}
