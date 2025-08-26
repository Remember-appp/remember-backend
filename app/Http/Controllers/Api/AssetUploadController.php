<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
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
    /**
     * Upload asset (multipart/form-data).
     *
     * Stores the file on the filesystem and creates an asset record.
     *
     * @authenticated
     * @bodyParam file file required The binary file to upload.
     * @bodyParam meta string JSON string with optional metadata. Example: {"alt":"avatar"}
     * @response 201 scenario="Created" {"asset":{"id":"0198...","owner_user_id":"0198...","storage_key":"assets/0198.../photo.png","mime":"image/png","size_bytes":12345,"meta":{"alt":"avatar"},"created_at":"2025-08-23T20:44:00Z"},"public_url":null}
     * @response 422 {"message":"The given data was invalid.","errors":{"file":["The file field is required."]}}
     */
    public function upload(Request $request): JsonResponse
    {
        $u = $request->user();

        $data = $request->validate([
            'file' => ['required','file','max:51200'], // 50MB default
            'meta' => ['nullable','string'],
        ]);

        $file = $data['file'];
        $disk = Storage::disk('assets'); // ensure 'assets' disk exists in filesystems.php

        // Build a stable storage path: assets/{userId}/{uuid}/{originalName}
        $uuid = (string) Str::uuid();
        $original = trim($file->getClientOriginalName()) ?: ($uuid.'.bin');
        $dir = 'assets/'.$u->id.'/'.$uuid;
        $storageKey = $dir.'/'.$original;

        // Pre-calc metadata before moving the file
        $mime = $file->getClientMimeType();
        $size = $file->getSize();
        $hash = hash_file('sha256', $file->getRealPath());

        // Store the file
        $disk->putFileAs($dir, $file, $original);

        $meta = null;
        if (!empty($data['meta'])) {
            try {
                $meta = json_decode($data['meta'], true, 512, JSON_THROW_ON_ERROR);
            } catch (\Throwable $e) {
                return response()->json([
                    'message' => 'Invalid meta JSON string.',
                ], 422);
            }
        }

        $asset = Asset::create([
            'owner_user_id' => $u->id,
            'storage_key'   => $storageKey,
            'mime'          => $mime,
            'size_bytes'    => $size,
            'content_hash'  => $hash,
            'meta'          => $meta,
        ]);

        return response()->json([
            'asset' => $asset,
            'public_url' => null, // set if you expose public storage
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
        $asset = Asset::findOrFail($id);

        // Authorization: owner or admin (handled by AssetPolicy@view)
        $this->authorize('view', $asset);

        $disk = Storage::disk('assets');
        abort_unless($disk->exists($asset->storage_key), 404);

        return $disk->download($asset->storage_key, basename($asset->storage_key));
    }
}
