<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MessageRecipient extends Model
{
    use HasUuids;

    public $timestamps = false;
    protected $fillable = ['message_id','kind','user_id','contact_id','visibility_scope'];

    public function message(): BelongsTo { return $this->belongsTo(Message::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function contact(): BelongsTo { return $this->belongsTo(Contact::class); }
    public function jobs(): HasMany { return $this->hasMany(DeliveryJob::class, 'recipient_id'); }
}
