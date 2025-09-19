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

        Schema::create('user_sessions', function (Blueprint $table) {
            $table->bigIncrements('id');        // internal PK
            $table->uuid('uuid')->unique();     // public identifier

            $table->foreignId('user_id')        // BIGINT FK -> users.id
            ->constrained('users')
                ->cascadeOnDelete();

            $table->text('user_agent')->nullable();
            $table->string('ip', 45)->nullable(); // IPv4/IPv6
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('ended_at')->nullable();

            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
    }
};
