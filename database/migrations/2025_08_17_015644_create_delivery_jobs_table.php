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

        Schema::create('delivery_jobs', function (Blueprint $table) {
            $table->bigIncrements('id');              // internal PK
            $table->uuid('uuid')->unique();           // public identifier

            $table->foreignId('message_id')           // BIGINT FK -> messages.id
            ->constrained('messages')
                ->cascadeOnDelete();

            $table->foreignId('recipient_id')         // BIGINT FK -> message_recipients.id
            ->constrained('message_recipients')
                ->cascadeOnDelete();

            $table->string('channel');                // email/sms/inapp
            $table->timestampTz('scheduled_at');
            $table->string('state')->default('pending'); // pending/processing/sent/failed/cancelled
            $table->timestampTz('created_at')->useCurrent();

            $table->index(['message_id','state']);
            $table->index('recipient_id');
            $table->index(['state','scheduled_at']);
            $table->index('scheduled_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_jobs');
    }
};
