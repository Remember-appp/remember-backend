<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    // PK now auto-incrementing bigint (Laravel defaults)
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'status',
         'uuid'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $user) {
            if (empty($user->uuid)) {
                $user->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function authIdentities(): HasMany { return $this->hasMany(UserAuthIdentity::class); }
    public function profile(): HasOne { return $this->hasOne(UserProfile::class, 'user_id'); }
    public function settings(): HasOne { return $this->hasOne(UserSetting::class, 'user_id'); }
    public function consents(): HasMany { return $this->hasMany(UserConsent::class); }
    public function assets(): HasMany { return $this->hasMany(Asset::class, 'owner_user_id'); }
    public function trustedContacts(): HasMany { return $this->hasMany(TrustedContact::class); }
}
