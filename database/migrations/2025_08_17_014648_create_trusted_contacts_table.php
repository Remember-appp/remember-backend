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

        Schema::create('trusted_contacts', function (Blueprint $table) {
            $table->bigIncrements('id');                 // internal PK
            $table->uuid('uuid')->unique();              // optional public id

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('relation')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->unsignedInteger('quorum_group')->default(1);
            $table->timestampTz('created_at')->useCurrent();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trusted_contacts');
    }
};
