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
        Schema::create('message_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('message_id');
            $table->string('trigger_type'); // date/event
            $table->string('cron_expr')->nullable(); // або RRULE
            $table->date('start_date')->nullable();
            $table->boolean('repeat_yearly')->default(false);

            $table->foreign('message_id')->references('id')->on('messages')->cascadeOnDelete();
            $table->index('message_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_schedules');
    }
};
