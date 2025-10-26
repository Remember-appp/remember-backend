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

        Schema::create('message_schedules', function (Blueprint $table) {
            $table->bigIncrements('id');            // internal PK
            $table->uuid('uuid')->unique();         // optional public id

            $table->foreignId('message_id')         // BIGINT FK -> messages.id
            ->constrained('messages')
                ->cascadeOnDelete();

            $table->string('trigger_type');         // e.g., date | event
            $table->text('cron_expr')->nullable();  // or RRULE string (use text for safety)
            $table->date('start_date')->nullable();
            $table->boolean('repeat_yearly')->default(false);

            $table->index('message_id');
            $table->index(['message_id', 'trigger_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_schedules');
    }
};
