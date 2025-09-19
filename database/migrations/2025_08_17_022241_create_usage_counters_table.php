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

        Schema::create('usage_counters', function (Blueprint $table) {
            $table->bigIncrements('id');        // internal PK
            $table->uuid('uuid')->unique();     // public identifier

            $table->foreignId('user_id')        // BIGINT FK -> users.id
            ->constrained('users')
                ->cascadeOnDelete();

            $table->date('period_start');
            $table->date('period_end');
            $table->string('metric');           // storage_gb/recipients/jobs_month ...
            $table->bigInteger('value')->default(0);

            $table->unique(['user_id','period_start','period_end','metric'], 'uniq_usage_period_metric');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usage_counters');
    }
};
