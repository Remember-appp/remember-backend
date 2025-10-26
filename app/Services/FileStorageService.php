<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileStorageService
{

    public function store(string $domain, int|string $ownerId, UploadedFile $file, array $opts = []): array
    {
        $disk        = $opts['disk'] ?? env('FILESYSTEM_DISK', 'public'); // 's3' in prod, 'public' locally
        $subdir      = trim((string)($opts['subdir'] ?? ''), '/');
        $keepHistory = (bool)($opts['keep_history'] ?? false);
        $driver      = config("filesystems.disks.$disk.driver"); // 's3' | 'local' | ...

        $fs   = Storage::disk($disk);
        $base = trim($domain, '/').'/'.$ownerId;   // e.g. avatar/32
        $dir  = $subdir ? "$base/$subdir" : $base;

        if ($keepHistory === false) {
            try { $fs->deleteDirectory($base); } catch (\Throwable $e) {}
        }

        $origName = $file->getClientOriginalName() ?: $file->hashName();
        $ext      = strtolower($file->guessExtension() ?: pathinfo($origName, PATHINFO_EXTENSION) ?: 'bin');
        $ext      = preg_replace('/[^a-z0-9]+/i', '', $ext) ?: 'bin';

        // unguessable filename
        $name = bin2hex(random_bytes(32)).'_'.Str::random(8).'.'.$ext;

        // default: private (S3)
        $storeOpts = [];
        if (isset($opts['visibility'])) {
            $storeOpts['visibility'] = $opts['visibility']; // 'private'|'public'
        } elseif ($driver === 'local') {
            $storeOpts['visibility'] = 'public';
        }

        try {
            if ($driver === 'local') {
                // --- tiny local fallback ---
                try { $fs->makeDirectory($dir); } catch (\Throwable $e) {}
                $path = $fs->putFileAs($dir, $file, $name, $storeOpts);
            } else {
                $path = $file->storeAs($dir, $name, ['disk' => $disk] + $storeOpts);
            }
        } catch (\Throwable $e) {
            Log::error('S3/local store failed', [
                'disk'   => $disk,
                'driver' => $driver,
                'dir'    => $dir,
                'name'   => $name,
                'error'  => $e->getMessage(),
            ]);
            throw new \RuntimeException("Failed to store file: driver=$driver, disk=$disk, dir=$dir, name=$name", 0, $e);
        }

        if (!$path) {
            Log::error('Store returned empty path', ['disk'=>$disk,'driver'=>$driver,'dir'=>$dir,'name'=>$name]);
            throw new \RuntimeException('Failed to store file: empty path returned from storage driver');
        }

        // Optional SHA-256
        $hash = null;
        try { $hash = hash_file('sha256', $file->getRealPath()); } catch (\Throwable) {}

        $url = null;
        try {
            $url = method_exists($fs, 'url') ? $fs->url($path) : null;
        } catch (\Throwable) {
            $url = null;
        }

        return [
            'disk'          => $disk,
            'path'          => $path,          // relative path, e.g. "avatar/32/xxxxxxxx.png"
            'url'           => $url,           // null for private S3; filled for local public
            'name'          => $name,
            'original_name' => $origName,
            'ext'           => $ext,
            'mime'          => $file->getClientMimeType(),
            'size'          => $file->getSize(),
            'hash_sha256'   => $hash,
            'created_at'    => now()->toIso8601String(),
        ];
    }

    public function storeAndRecord(Model $owner, string $domain, UploadedFile $file, array $opts = []): StoredFile
    {
        $data = $this->store($domain, $owner->getKey(), $file, $opts);

        if (!empty($opts['unique_per_domain'])) {
            $old = $owner->files()->where('domain', $domain)->get();
            foreach ($old as $f) {
                try { Storage::disk($f->disk ?: config('filesystems.default'))->delete($f->path); } catch (\Throwable) {}
                $f->delete();
            }
        }

        return $owner->files()->create([
            'domain'        => $domain,
            'disk'          => $data['disk'],
            'path'          => $data['path'],
            'name'          => $data['name'],
            'original_name' => $data['original_name'],
            'ext'           => $data['ext'],
            'mime'          => $data['mime'],
            'size'          => $data['size'],
            'hash_sha256'   => $data['hash_sha256'],
            'visibility'    => $opts['visibility'] ?? ($data['disk'] === 'public' ? 'public' : 'private'),
            'uploaded_by'   => $opts['uploaded_by'] ?? null,
            'meta'          => $opts['meta'] ?? null,
        ]);
    }

    public function temporaryUrl(string $path, ?string $disk = null, ?int $ttlSeconds = null): ?string
    {
        $disk   = $disk ?? config('filesystems.default', 'public');
        $fs     = Storage::disk($disk);
        $driver = config("filesystems.disks.$disk.driver"); // 'local' | 's3' | ...

        if ($driver === 'local') {
            try {
                return $fs->url($path);
            } catch (\Throwable) {
                return null;
            }
        }

        $expires = now()->addSeconds($ttlSeconds ?? (int) env('SIGNED_URL_TTL', 900));

        // 1) try temporaryUrl()
        try {
            return $fs->temporaryUrl($path, $expires);
        } catch (\Throwable) {
            try {
                return $fs->url($path);
            } catch (\Throwable) {
                return null;
            }
        }
    }
}
