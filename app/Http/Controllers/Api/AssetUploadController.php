<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Services\FileStorageService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @group Assets
 */
class AssetUploadController extends Controller
{
    public function __construct(private FileStorageService $files) {}

    /**
     * Upload asset (multipart/form-data).
     *
     * @authenticated
     * @bodyParam file file required The binary file to upload.
     * @bodyParam meta string JSON string with optional metadata. Example: {"alt":"avatar"}
     * @response 201 scenario="Created" {"asset":{"id":1,"uuid":"0198...","owner_user_id":1,"storage_key":"assets/1/xxxx.png","mime":"image/png","size_bytes":12345,"content_hash":"...","meta":{"alt":"avatar"},"created_at":"2025-08-23T20:44:00Z"},"public_url":null}
     * @response 422 {"message":"The given data was invalid.","errors":{"file":["The file field is required."]}}
     */
    public function upload(Request $request): JsonResponse
    {
        $u = $request->user();

        $data = $request->validate([
            'file' => ['required','file','max:51200'],
            'meta' => ['nullable','string'],
        ]);

        $file = $data['file'];

        // 1) Рахуємо хеш ДО збереження, щоб перевірити дубль
        $sha = null;
        try { $sha = hash_file('sha256', $file->getRealPath()); } catch (\Throwable) {}

        if ($sha) {
            $existing = \App\Models\Asset::where('owner_user_id', $u->id)
                ->where('content_hash', $sha)
                ->first();

            if ($existing) {
                // Ідемпотентна відповідь на дубль
                return response()->json([
                    'asset' => $existing,
                    'duplicate' => true,
                ], 200);
            }
        }

        // 2) Зберігаємо файл через FileStorageService
        $stored = $this->files->store('assets', $u->id, $file, [
            'disk' => 'public',
            'visibility' => 'public',
        ]);

        // meta
        $meta = null;
        if (!empty($data['meta'])) {
            try { $meta = json_decode($data['meta'], true, 512, JSON_THROW_ON_ERROR); }
            catch (\Throwable) { return response()->json(['message' => 'Invalid meta JSON string.'], 422); }
        }

        // 3) Створюємо запис; ловимо race-condition (23505)
        try {
            $asset = \App\Models\Asset::create([
                'uuid'         => \Illuminate\Support\Str::uuid()->toString(),
                'owner_user_id'=> $u->id,
                'disk'         => $stored['disk'] ?? null,        // <— важливо
                'storage_key'  => $stored['path'],
                'mime'         => $stored['mime'],
                'size_bytes'   => $stored['size'],
                'content_hash' => $sha ?: $stored['hash_sha256'],
                'meta'         => $meta,
            ]);
        } catch (QueryException $e) {
            // duplicate key -> повертаємо існуючий (перегрузка/гонка)
            if ($e->getCode() === '23505') {
                $asset = \App\Models\Asset::where('owner_user_id', $u->id)
                    ->where('content_hash', $sha ?: $stored['hash_sha256'])
                    ->firstOrFail();

                return response()->json([
                    'asset' => $asset,
                    'duplicate' => true,
                ], 200);
            }
            throw $e;
        }

        return response()->json([
            'asset' => $asset,
        ], 201);
    }

    /**
     * Download asset (owner or admin).
     *
     * @authenticated
     * @urlParam id string required Asset UUID.
     * @response 200 binary "application/octet-stream"
     * @response 403 {"message":"Forbidden"}
     * @response 404 {"message":"Not Found"}
     */
    public function download(string $id): StreamedResponse
    {
        $asset = Asset::findOrFail($id); // PK = uuid

        $this->authorize('view', $asset);

        $disk = config('filesystems.default', env('FILESYSTEM_DISK', 'public'));
        $fs   = Storage::disk($disk);

        abort_unless($fs->exists($asset->storage_key), 404);

        return $fs->download($asset->storage_key, basename($asset->storage_key));
    }
}
