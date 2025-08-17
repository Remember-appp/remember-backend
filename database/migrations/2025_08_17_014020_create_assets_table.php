<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('owner_user_id');
            $table->string('storage_key');
            $table->string('mime');
            $table->unsignedBigInteger('size_bytes');
            $table->string('content_hash');
            $table->json('meta')->default(new Expression("'{}'::json"));
            $table->timestampTz('created_at')->useCurrent();

            $table->foreign('owner_user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index('owner_user_id');
            $table->index('created_at');
            $table->unique(['owner_user_id','content_hash']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
