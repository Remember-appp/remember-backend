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
        Schema::create('delivery_jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('message_id');
            $table->uuid('recipient_id');
            $table->string('channel'); // email/sms/inapp
            $table->timestampTz('scheduled_at');
            $table->string('state')->default('pending'); // pending/processing/sent/failed/cancelled
            $table->timestampTz('created_at')->useCurrent();

            $table->foreign('message_id')->references('id')->on('messages')->cascadeOnDelete();
            $table->foreign('recipient_id')->references('id')->on('message_recipients')->cascadeOnDelete();

            $table->index(['message_id','state']);
            $table->index('recipient_id');
            $table->index(['state','scheduled_at']);
            $table->index('scheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_jobs');
    }
};
