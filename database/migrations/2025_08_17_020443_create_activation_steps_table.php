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
        Schema::create('activation_steps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('case_id');
            $table->string('step');
            $table->string('actor_type'); // user/trusted/admin/system
            $table->uuid('actor_id')->nullable();     // FK → users.id
            $table->string('result')->nullable();     // ok/fail/...
            $table->text('notes')->nullable();
            $table->timestampTz('created_at')->useCurrent();

            $table->foreign('case_id')->references('id')->on('death_activation_cases')->cascadeOnDelete();
            $table->foreign('actor_id')->references('id')->on('users')->nullOnDelete();
            $table->index('case_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activation_steps');
    }
};
