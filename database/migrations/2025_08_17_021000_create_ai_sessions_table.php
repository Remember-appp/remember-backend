<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS "pgcrypto";');

        Schema::create('ai_sessions', function (Blueprint $table) {
            $table->bigIncrements('id');            // internal PK
            $table->uuid('uuid')->unique();         // public identifier (optional but useful)

            $table->foreignId('user_id')            // BIGINT FK -> users.id
            ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('relative_user_id')   // BIGINT FK -> users.id
            ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestampTz('started_at')->useCurrent();
            $table->timestampTz('ended_at')->nullable();
            $table->json('meta')->default(DB::raw("'{}'::json"));

            $table->index('user_id');
            $table->index('started_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_sessions');
    }
};
