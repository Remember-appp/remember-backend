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

        Schema::create('death_activation_cases', function (Blueprint $table) {
            $table->bigIncrements('id');         // internal PK
            $table->uuid('uuid')->unique();      // public identifier

            $table->foreignId('user_id')         // BIGINT FK -> users.id
            ->constrained('users')
                ->cascadeOnDelete();

            $table->string('state')->default('pre'); // pre/triggered/grace/activated/halted/restored
            $table->timestampTz('opened_at')->useCurrent();
            $table->timestampTz('closed_at')->nullable();

            $table->index('user_id');
            $table->index('state');
        });

        // Optional strictness:
        // DB::statement("ALTER TABLE death_activation_cases ADD CHECK (state IN ('pre','triggered','grace','activated','halted','restored'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('death_activation_cases');
    }
};
