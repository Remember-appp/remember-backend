<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserSetting;

class UserSettingPolicy
{
    public function view(User $auth, UserSetting $setting): bool
    {
        return $auth->id === $setting->user_id || $auth->hasRole('admin');
    }

    public function update(User $auth, UserSetting $setting): bool
    {
        return $auth->id === $setting->user_id || $auth->hasRole('admin');
    }
}
