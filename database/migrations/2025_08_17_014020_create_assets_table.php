<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Keep pgcrypto for gen_random_uuid() if you want public UUIDs
        DB::statement('CREATE EXTENSION IF NOT EXISTS "pgcrypto";');

        Schema::create('assets', function (Blueprint $table) {
            $table->bigIncrements('id');                 // internal PK (fast joins)
            $table->uuid('uuid')->unique();              // public identifier (optional but recommended)

            $table->foreignId('owner_user_id')           // bigint FK -> users.id
            ->constrained('users')
                ->cascadeOnDelete();

            $table->string('storage_key');
            // Optional: if you store on multiple disks, uncomment next line:
            // $table->string('disk')->default('assets');
            $table->string('mime');
            $table->unsignedBigInteger('size_bytes');
            $table->string('content_hash');
            $table->json('meta')->default(DB::raw("'{}'::json"));
            $table->timestampTz('created_at')->useCurrent();

            $table->index('owner_user_id');
            $table->index('created_at');
            $table->unique(['owner_user_id', 'content_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
