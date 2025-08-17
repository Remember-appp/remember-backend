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
        Schema::create('delivery_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('job_id');
            $table->string('vendor')->nullable();
            $table->string('status')->nullable();    // ok/fail/...
            $table->string('error_code')->nullable();
            $table->json('response')->default(new Expression("'{}'::json"));
            $table->timestampTz('created_at')->useCurrent();

            $table->foreign('job_id')->references('id')->on('delivery_jobs')->cascadeOnDelete();

            $table->index('job_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_attempt');
    }
};
