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
        Schema::create('api_keys', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('owner_user_id');
            $table->string('label')->nullable();
            $table->text('hash');
            $table->json('scopes')->nullable();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('revoked_at')->nullable();

            $table->foreign('owner_user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index('owner_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_keys');
    }
};
