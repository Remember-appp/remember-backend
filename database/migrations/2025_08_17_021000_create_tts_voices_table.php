<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tts_voices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('vendor');
            $table->string('model');
            $table->string('voice_ref');
            $table->uuid('consent_id')->nullable(); // FK -> user_consents.id

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('consent_id')->references('id')->on('user_consents')->nullOnDelete();
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tts_voices');
    }
};
