<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Keep pgcrypto if you use gen_random_uuid() elsewhere
        DB::statement('CREATE EXTENSION IF NOT EXISTS "pgcrypto";');

        Schema::create('messages', function (Blueprint $table) {
            $table->bigIncrements('id');            // internal PK
            $table->uuid('uuid')->unique();         // public identifier

            $table->foreignId('user_id')            // bigint FK -> users.id
            ->constrained('users')
                ->cascadeOnDelete();

            $table->string('title')->nullable();
            $table->text('body_text')->nullable();

            // Consider enum in DB if you want strictness; string is fine for now
            $table->string('status')->default('draft'); // draft/scheduled/locked/queued/delivered/halted/cancelled

            $table->timestampTz('locked_at')->nullable();
            $table->timestampsTz();

            $table->index('user_id');
            $table->index('status');
            $table->index(['user_id','status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
