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
        Schema::create('merge_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('initiator_user_id');
            $table->uuid('person_a_id');
            $table->uuid('person_b_id');
            $table->string('state')->default('pending'); // pending/accepted/rejected
            $table->json('evidence_assets')->nullable(); // array asset_id
            $table->timestampTz('created_at')->useCurrent();

            $table->foreign('initiator_user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('person_a_id')->references('id')->on('persons')->cascadeOnDelete();
            $table->foreign('person_b_id')->references('id')->on('persons')->cascadeOnDelete();

            $table->index('state');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merge_requests');
    }
};
