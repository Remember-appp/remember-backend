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

        Schema::create('delivery_attempts', function (Blueprint $table) {
            $table->bigIncrements('id');          // internal PK
            $table->uuid('uuid')->unique();       // public id

            $table->foreignId('job_id')           // BIGINT FK -> delivery_jobs.id
            ->constrained('delivery_jobs')
                ->cascadeOnDelete();

            $table->string('vendor')->nullable();
            $table->string('status')->nullable();     // ok/fail/...
            $table->string('error_code')->nullable();
            $table->json('response')->default(DB::raw("'{}'::json"));
            $table->timestampTz('created_at')->useCurrent();

            $table->index('job_id');
            $table->index('created_at');
            $table->index(['job_id','created_at']);
        });

        // (опційно) суворість статусу:
        // DB::statement("ALTER TABLE delivery_attempts ADD CHECK (status IN ('ok','fail'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_attempts');
    }
};
