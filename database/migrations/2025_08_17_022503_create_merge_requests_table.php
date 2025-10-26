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

        Schema::create('merge_requests', function (Blueprint $table) {
            $table->bigIncrements('id');          // internal PK
            $table->uuid('uuid')->unique();       // public id

            $table->foreignId('initiator_user_id')  // BIGINT FK -> users.id
            ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('person_a_id')        // BIGINT FK -> persons.id
            ->constrained('persons')
                ->cascadeOnDelete();

            $table->foreignId('person_b_id')        // BIGINT FK -> persons.id
            ->constrained('persons')
                ->cascadeOnDelete();

            $table->string('state')->default('pending'); // pending/accepted/rejected
            $table->json('evidence_assets')->nullable(); // array of asset ids (BIGINTs у JSON)
            $table->timestampTz('created_at')->useCurrent();

            $table->index('state');
        });

        // Забороняємо злиття однієї й тієї ж особи
        DB::statement("
          ALTER TABLE merge_requests
          ADD CONSTRAINT merge_requests_distinct_persons
          CHECK (person_a_id <> person_b_id)
        ");

        // (опційно) унікальність пари незалежно від порядку для активних заявок
        DB::statement("
          CREATE UNIQUE INDEX merge_requests_pair_once_pending
          ON merge_requests (LEAST(person_a_id, person_b_id), GREATEST(person_a_id, person_b_id))
          WHERE state = 'pending'
        ");

        // (опційно) суворі стани
        // DB::statement(\"ALTER TABLE merge_requests ADD CHECK (state IN ('pending','accepted','rejected'))\");
    }

    public function down(): void
    {
        Schema::dropIfExists('merge_requests');
    }
};
