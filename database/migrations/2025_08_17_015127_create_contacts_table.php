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

        Schema::create('contacts', function (Blueprint $table) {
            $table->bigIncrements('id');       // internal PK
            $table->uuid('uuid')->unique();    // public identifier (optional, якщо треба)

            $table->foreignId('user_id_owner') // BIGINT FK -> users.id
            ->constrained('users')
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->index('user_id_owner');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
