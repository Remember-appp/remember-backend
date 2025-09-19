<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->primary()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('display_name')->nullable();
            $table->text('bio')->nullable();

            $table->foreignId('photo_asset_id')
                ->nullable()
                ->constrained('assets')
                ->nullOnDelete();

            $table->date('birth_date')->nullable();
            $table->json('favorite_phrases')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
