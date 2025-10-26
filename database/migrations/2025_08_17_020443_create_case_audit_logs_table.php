<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS "pgcrypto";');

        Schema::create('case_audit_logs', function (Blueprint $table) {
            $table->bigIncrements('id');          // internal PK
            $table->uuid('uuid')->unique();       // optional public id

            $table->foreignId('case_id')          // BIGINT FK -> death_activation_cases.id
            ->constrained('death_activation_cases')
                ->cascadeOnDelete();

            $table->foreignId('actor_id')         // BIGINT FK -> users.id (nullable)
            ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('action');
            $table->json('meta')->default(DB::raw("'{}'::json"));
            $table->timestampTz('created_at')->useCurrent();

            $table->index('case_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_audit_logs');
    }
};
