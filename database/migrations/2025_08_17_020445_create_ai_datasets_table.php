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

        Schema::create('ai_datasets', function (Blueprint $table) {
            $table->bigIncrements('id');       // internal PK
            $table->uuid('uuid')->unique();    // public identifier

            $table->foreignId('user_id')       // BIGINT FK -> users.id
            ->constrained('users')
                ->cascadeOnDelete();

            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->timestampTz('created_at')->useCurrent();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_datasets');
    }
};
