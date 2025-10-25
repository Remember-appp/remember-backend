<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Asset;
use App\Models\UserProfile;
use App\Models\UserSetting;
use App\Services\FileStorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct(private FileStorageService $files) {}

    public function get(Request $request): JsonResponse
    {
        $u = $request->user();

        $targetId = $u->hasRole('admin')
            ? ($request->query('user_id') ?: $u->id)
            : $u->id;

        $profile = UserProfile::firstOrCreate(['user_id' => $targetId]);
        $this->authorize('view', $profile);

        return response()->json([
            'profile' => $this->decorateProfile($profile),
        ]);
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $u = $request->user();

        $targetId = $u->hasRole('admin')
            ? ($request->query('user_id') ?: $u->id)
            : $u->id;

        $profile = UserProfile::firstOrCreate(['user_id' => $targetId]);
        $this->authorize('update', $profile);

        $data = $request->validated();

        if (!empty($data['photo_asset_uuid']) && empty($data['photo_asset_id'])) {
            $asset = \App\Models\Asset::where('uuid', $data['photo_asset_uuid'])->first();
            $data['photo_asset_id'] = $asset?->id;
            unset($data['photo_asset_uuid']);
        }

        $profile->fill($data)->save();

        return response()->json($this->decorateProfile($profile));
    }

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

    /**
     * Add photo_url to payload if photo_asset_id is set.
     */
    private function decorateProfile(UserProfile $profile): array
    {
        $out = $profile->toArray();
        $out['photo_url'] = null;

        $asset = null;

        // 1) якщо прив'язано явно — беремо його
        if ($profile->photo_asset_id) {
            $asset = Asset::find($profile->photo_asset_id);
        }

        // 2) fallback: якщо не прив'язано, шукаємо останній asset з meta.alt = "avatar" цього юзера
        if (!$asset) {
            $asset = Asset::where('owner_user_id', $profile->user_id)
                ->where('meta->alt', 'avatar')        // Postgres JSONB оператор
                ->orderByDesc('created_at')
                ->first();
            // (опційно) запам'ятати знайдений id у профілі, щоб наступного разу не шукати
            if ($asset) {
                $profile->forceFill(['photo_asset_id' => $asset->id])->save();
            }
        }

        if ($asset) {
            // пробуємо підписаний URL (S3), або публічний (local)
            $disk = config('filesystems.default', env('FILESYSTEM_DISK', 'public'));
            $url  = app(FileStorageService::class)->temporaryUrl($asset->storage_key, $disk, 3600);
            if (!$url) {
                try { $url = Storage::disk($disk)->url($asset->storage_key); } catch (\Throwable) {}
            }
            $out['photo_url'] = $url;
        }

        return $out;
    }
}
