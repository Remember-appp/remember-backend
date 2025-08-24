<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdateSettingsRequest;
use App\Models\UserProfile;
use App\Models\UserSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Me
 */
class ProfileController extends Controller
{
    /**
     * Get my profile (owner or admin with ?user_id=UUID).
     *
     * @authenticated
     * @queryParam user_id string UUID of another user (admin only).
     * @response 200 {"profile":{"user_id":"0198...","display_name":"Andrii","bio":null,"photo_asset_id":null,"birth_date":null,"favorite_phrases":[]}}
     * @response 401 {"message":"Unauthenticated."}
     * @response 403 {"message":"Forbidden."}
     */
    public function get(Request $request): JsonResponse
    {
        $u = $request->user();

        $targetId = $u->hasRole('admin')
            ? ($request->query('user_id') ?: $u->id)
            : $u->id;

        $profile = UserProfile::firstOrCreate(['user_id' => $targetId]);
        $this->authorize('view', $profile);

        return response()->json(['profile' => $profile]);
    }

    /**
     * Update my profile (owner or admin with ?user_id=UUID).
     *
     * @authenticated
     * @queryParam user_id string UUID of another user (admin only).
     * @bodyParam display_name string max:255
     * @bodyParam bio string max:2000
     * @bodyParam photo_asset_id string uuid
     * @bodyParam birth_date date
     * @bodyParam favorite_phrases array of strings
     * @response 200 {"profile":{"user_id":"0198...","display_name":"Andrii","bio":"hello","photo_asset_id":null,"birth_date":"1990-01-01","favorite_phrases":["..."]}}
     * @response 422 {"message":"The given data was invalid.","errors":{"display_name":["The display name may not be greater than 255 characters."]}}
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $u = $request->user();

        $targetId = $u->hasRole('admin')
            ? ($request->query('user_id') ?: $u->id)
            : $u->id;

        $profile = UserProfile::firstOrCreate(['user_id' => $targetId]);
        $this->authorize('update', $profile);

        $profile->fill($request->validated())->save();

        return response()->json(['profile' => $profile]);
    }

    /**
     * Get my settings (owner or admin with ?user_id=UUID).
     *
     * @authenticated
     * @queryParam user_id string UUID of another user (admin only).
     * @response 200 {"settings":{"user_id":"0198...","tz":"America/Edmonton","locale":"uk","privacy":{},"notifications":{}}}
     */
    public function getSettings(Request $request): JsonResponse
    {
        $u = $request->user();

        $targetId = $u->hasRole('admin')
            ? ($request->query('user_id') ?: $u->id)
            : $u->id;

        $settings = UserSetting::firstOrCreate(['user_id' => $targetId], [
            'tz' => 'UTC',
            'locale' => 'en',
        ]);

        $this->authorize('view', $settings);

        return response()->json(['settings' => $settings]);
    }

    /**
     * Update my settings (owner or admin with ?user_id=UUID).
     *
     * @authenticated
     * @queryParam user_id string UUID of another user (admin only).
     * @bodyParam tz string required Example: America/Edmonton
     * @bodyParam locale string required Example: uk
     * @bodyParam privacy object
     * @bodyParam notifications object
     * @response 200 {"settings":{"user_id":"0198...","tz":"America/Edmonton","locale":"uk","privacy":{},"notifications":{}}}
     * @response 422 {"message":"The given data was invalid.","errors":{"tz":["The tz field is required."]}}
     */
    public function updateSettings(UpdateSettingsRequest $request): JsonResponse
    {
        $u = $request->user();

        $targetId = $u->hasRole('admin')
            ? ($request->query('user_id') ?: $u->id)
            : $u->id;

        $settings = UserSetting::firstOrCreate(['user_id' => $targetId]);
        $this->authorize('update', $settings);

        $settings->fill($request->validated())->save();

        return response()->json(['settings' => $settings]);
    }
}
