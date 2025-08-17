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
        Schema::create('persons', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('owner_user_id');
            $table->string('full_name');
            $table->date('birth_date')->nullable();
            $table->date('death_date')->nullable();
            $table->text('bio')->nullable();
            $table->uuid('photo_asset_id')->nullable();
            $table->boolean('is_user_linked')->default(false);
            $table->uuid('linked_user_id')->nullable();

            $table->foreign('owner_user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('photo_asset_id')->references('id')->on('assets')->nullOnDelete();
            $table->foreign('linked_user_id')->references('id')->on('users')->nullOnDelete();

            $table->index('owner_user_id');
            $table->index('full_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persons');
    }
};
