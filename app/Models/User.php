<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function authIdentities(): HasMany { return $this->hasMany(UserAuthIdentity::class); }
    public function profile(): HasOne { return $this->hasOne(UserProfile::class, 'user_id'); }
    public function settings(): HasOne { return $this->hasOne(UserSetting::class, 'user_id'); }
    public function consents(): HasMany { return $this->hasMany(UserConsent::class); }
    public function assets(): HasMany { return $this->hasMany(Asset::class, 'owner_user_id'); }
    public function trustedContacts(): HasMany { return $this->hasMany(TrustedContact::class); }
}
