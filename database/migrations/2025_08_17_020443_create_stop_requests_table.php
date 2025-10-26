<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS "pgcrypto";');

        Schema::create('stop_requests', function (Blueprint $table) {
            $table->bigIncrements('id');       // internal PK
            $table->uuid('uuid')->unique();    // public identifier

            $table->foreignId('case_id')       // BIGINT FK -> death_activation_cases.id
            ->constrained('death_activation_cases')
                ->cascadeOnDelete();

            $table->foreignId('requester_id')  // BIGINT FK -> users.id
            ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('reason_text')->nullable();
            $table->string('state')->default('pending'); // pending/accepted/rejected
            $table->json('evidence_assets')->nullable(); // array of asset_id
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('decided_at')->nullable();

            $table->index('case_id');
            $table->index('state');
        });

        // Optional strictness
        // DB::statement("ALTER TABLE stop_requests ADD CHECK (state IN ('pending','accepted','rejected'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('stop_requests');
    }
};
