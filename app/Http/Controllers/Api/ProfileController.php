<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdateSettingsRequest;
use App\Models\UserProfile;
use App\Models\UserSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function get(): JsonResponse
    {
        $u = Auth::user();
        abort_unless($u, 401);

        // admin може підглянути інший профіль: /me/profile?user_id=UUID
        $targetId = $u->hasRole('admin') ? (request('user_id') ?: $u->id) : $u->id;

        $profile = UserProfile::firstOrCreate(['user_id' => $targetId]);
        $this->authorize('view', $profile);

        return response()->json(['profile' => $profile]);
    }

    public function update(UpdateProfileRequest $req): JsonResponse
    {
        $u = Auth::user(); abort_unless($u, 401);

        $targetId = $u->hasRole('admin') ? (request('user_id') ?: $u->id) : $u->id;

        $profile = UserProfile::firstOrCreate(['user_id' => $targetId]);
        $this->authorize('update', $profile);

        $profile->fill($req->validated())->save();

        return response()->json(['profile' => $profile]);
    }

    public function getSettings(): JsonResponse
    {
        $u = Auth::user(); abort_unless($u, 401);

        $targetId = $u->hasRole('admin') ? (request('user_id') ?: $u->id) : $u->id;

        $settings = UserSetting::firstOrCreate(['user_id' => $targetId], ['tz' => 'UTC', 'locale' => 'en']);
        $this->authorize('view', $settings);

        return response()->json(['settings' => $settings]);
    }

    public function updateSettings(UpdateSettingsRequest $req): JsonResponse
    {
        $u = Auth::user(); abort_unless($u, 401);

        $targetId = $u->hasRole('admin') ? (request('user_id') ?: $u->id) : $u->id;

        $settings = UserSetting::firstOrCreate(['user_id' => $targetId]);
        $this->authorize('update', $settings);

        $settings->fill($req->validated())->save();

        return response()->json(['settings' => $settings]);
    }
}
