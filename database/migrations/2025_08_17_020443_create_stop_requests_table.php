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
        Schema::create('stop_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('case_id');
            $table->uuid('requester_id')->nullable(); // FK → users.id
            $table->text('reason_text')->nullable();
            $table->string('state')->default('pending'); // pending/accepted/rejected
            $table->json('evidence_assets')->nullable(); // array asset_id
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('decided_at')->nullable();

            $table->foreign('case_id')->references('id')->on('death_activation_cases')->cascadeOnDelete();
            $table->foreign('requester_id')->references('id')->on('users')->nullOnDelete();
            $table->index('case_id');
            $table->index('state');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stop_requests');
    }
};
