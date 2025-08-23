<?php
// app/Http/Controllers/Api/AssetUploadController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AssetUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:51200', // 50MB
            'meta' => 'nullable|string',
        ]);

        $user = Auth::user();
        abort_unless($user, Response::HTTP_UNAUTHORIZED);

        $disk = Storage::disk('assets'); // config/filesystems.php -> 'assets'
        $file = $request->file('file');

        $safeName = preg_replace('~[^A-Za-z0-9_.-]~', '_', $file->getClientOriginalName() ?: 'upload.bin');
        $key = sprintf('users/%s/%s_%s', $user->id, Str::uuid(), $safeName);

        $disk->putFileAs('', $file, $key);

        $asset = Asset::create([
            'id'            => (string) Str::uuid(),
            'owner_user_id' => $user->id,
            'storage_key'   => $key,
            'mime'          => $file->getMimeType() ?: 'application/octet-stream',
            'size_bytes'    => $file->getSize(),
            'content_hash'  => null,
            'meta'          => $this->parseMeta($request->input('meta')),
            'created_at'    => now(),
        ]);

        return response()->json([
            'asset' => $asset,
            'public_url' => $disk->url($key),
        ], Response::HTTP_CREATED);
    }

    public function download(string $id)
    {
        $asset = Asset::findOrFail($id);

        $disk = Storage::disk('assets');
        abort_unless($disk->exists($asset->storage_key), 404);

        return $disk->download($asset->storage_key, basename($asset->storage_key));
    }

    private function parseMeta(?string $json): ?array
    {
        if (!$json) return null;
        try { $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR); }
        catch (\Throwable) { return null; }
        return is_array($data) ? $data : null;
    }
}
