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

        Schema::create('webhook_events', function (Blueprint $table) {
            $table->bigIncrements('id');          // internal PK
            $table->uuid('uuid')->unique();       // public identifier

            $table->foreignId('webhook_id')       // BIGINT FK -> outbound_webhooks.id
            ->constrained('outbound_webhooks')
                ->cascadeOnDelete();

            $table->json('payload');
            $table->string('status')->default('pending');
            $table->timestampTz('created_at')->useCurrent();

            $table->index('webhook_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_events');
    }
};
