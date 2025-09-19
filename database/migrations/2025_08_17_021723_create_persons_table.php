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

        Schema::create('persons', function (Blueprint $table) {
            $table->bigIncrements('id');        // internal PK
            $table->uuid('uuid')->unique();     // public identifier

            $table->foreignId('owner_user_id')  // BIGINT FK -> users.id
            ->constrained('users')
                ->cascadeOnDelete();

            $table->string('full_name');
            $table->date('birth_date')->nullable();
            $table->date('death_date')->nullable();
            $table->text('bio')->nullable();

            $table->foreignId('photo_asset_id') // BIGINT FK -> assets.id
            ->nullable()
                ->constrained('assets')
                ->nullOnDelete();

            $table->boolean('is_user_linked')->default(false);

            $table->foreignId('linked_user_id') // BIGINT FK -> users.id
            ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->index('owner_user_id');
            $table->index('full_name');
        });

        // Опційно: гарантуємо узгодженість полів лінкування
        DB::statement("
          ALTER TABLE persons
          ADD CONSTRAINT persons_link_check
          CHECK (
            (is_user_linked = true  AND linked_user_id IS NOT NULL) OR
            (is_user_linked = false AND linked_user_id IS NULL)
          )
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('persons');
    }
};
