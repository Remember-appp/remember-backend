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

        Schema::create('tts_voices', function (Blueprint $table) {
            $table->bigIncrements('id');        // internal PK
            $table->uuid('uuid')->unique();     // public identifier

            $table->foreignId('user_id')        // BIGINT FK -> users.id
            ->constrained('users')
                ->cascadeOnDelete();

            $table->string('vendor');
            $table->string('model');
            $table->string('voice_ref');

            $table->foreignId('consent_id')     // BIGINT FK -> user_consents.id
            ->nullable()
                ->constrained('user_consents')
                ->nullOnDelete();

            $table->index('user_id');
            // Часто корисно уникалізувати голос в межах юзера:
            $table->unique(['user_id','vendor','model','voice_ref'], 'uniq_user_vendor_model_voice');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tts_voices');
    }
};
