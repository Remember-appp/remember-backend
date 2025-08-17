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
        Schema::create('heartbeat_policies', function (Blueprint $table) {
            $table->uuid('user_id')->primary();
            $table->integer('cadence_days')->default(14);
            $table->integer('escalation_days')->default(30);
            $table->integer('grace_days')->default(14);
            $table->json('channels')->nullable(); //  ["email","push"]

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('heartbeat_policies');
    }
};
