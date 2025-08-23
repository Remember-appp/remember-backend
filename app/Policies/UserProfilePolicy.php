<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserProfile;

class UserProfilePolicy
{
    public function view(User $auth, UserProfile $profile): bool
    {
        return $auth->id === $profile->user_id || $auth->hasRole('admin');
    }

    public function update(User $auth, UserProfile $profile): bool
    {
        return $auth->id === $profile->user_id || $auth->hasRole('admin');
    }
}
