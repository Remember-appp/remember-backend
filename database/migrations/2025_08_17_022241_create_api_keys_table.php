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

        Schema::create('api_keys', function (Blueprint $table) {
            $table->bigIncrements('id');        // internal PK
            $table->uuid('uuid')->unique();     // public identifier

            $table->foreignId('owner_user_id')  // BIGINT FK -> users.id
            ->constrained('users')
                ->cascadeOnDelete();

            $table->string('label')->nullable();
            $table->text('hash');
            $table->json('scopes')->nullable();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('revoked_at')->nullable();

            $table->index('owner_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_keys');
    }
};
