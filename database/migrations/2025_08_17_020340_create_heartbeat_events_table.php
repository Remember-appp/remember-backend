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

        Schema::create('heartbeat_events', function (Blueprint $table) {
            $table->bigIncrements('id');          // internal PK
            $table->uuid('uuid')->unique();       // optional public identifier

            $table->foreignId('user_id')          // BIGINT FK -> users.id
            ->constrained('users')
                ->cascadeOnDelete();

            $table->string('kind');               // poll | ping | confirm
            $table->string('status');             // sent | responded | timeout
            $table->timestampTz('sent_at')->nullable();
            $table->timestampTz('responded_at')->nullable();

            $table->index('user_id');
            $table->index('status');
            $table->index(['user_id','status']);
        });

        // Optional strictness:
        // DB::statement("ALTER TABLE heartbeat_events ADD CHECK (kind IN ('poll','ping','confirm'))");
        // DB::statement("ALTER TABLE heartbeat_events ADD CHECK (status IN ('sent','responded','timeout'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('heartbeat_events');
    }
};
