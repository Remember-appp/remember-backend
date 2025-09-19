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

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->bigIncrements('id');          // internal PK
            $table->uuid('uuid')->unique();       // public id

            $table->foreignId('user_id')          // BIGINT FK -> users.id
            ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('plan_id')          // BIGINT FK -> plans.id
            ->constrained('plans');

            $table->string('status')->default('active'); // active/paused/cancelled/expired
            $table->timestampTz('current_period_start');
            $table->timestampTz('current_period_end');
            $table->timestampTz('cancel_at')->nullable();

            $table->index('user_id');
            $table->index('plan_id');
            $table->index(['status', 'current_period_end']);
        });

        // (опційно) одна активна підписка на користувача:
        DB::statement("
          CREATE UNIQUE INDEX subscriptions_one_active_per_user
          ON subscriptions (user_id)
          WHERE status = 'active'
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
