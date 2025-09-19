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

        Schema::create('relationships', function (Blueprint $table) {
            $table->bigIncrements('id');       // internal PK
            $table->uuid('uuid')->unique();    // public id

            $table->foreignId('person_id')      // BIGINT FK -> persons.id
            ->constrained('persons')
                ->cascadeOnDelete();

            $table->foreignId('other_person_id')// BIGINT FK -> persons.id
            ->constrained('persons')
                ->cascadeOnDelete();

            $table->string('type');             // parent/child/partner
            $table->date('since')->nullable();
            $table->date('until')->nullable();

            $table->unique(['person_id','other_person_id','type'], 'uniq_rel_direct');

            // індекси для пошуку
            $table->index('person_id');
            $table->index('other_person_id');
            $table->index(['type','person_id']);
        });

        // Заборона self-зв’язку
        DB::statement("ALTER TABLE relationships
                       ADD CONSTRAINT relationships_no_self
                       CHECK (person_id <> other_person_id)");

        // Узгодженість дат
        DB::statement("ALTER TABLE relationships
                       ADD CONSTRAINT relationships_dates_ok
                       CHECK (since IS NULL OR until IS NULL OR since <= until)");

        // (опційно) суворі типи
        // DB::statement(\"ALTER TABLE relationships
        //                ADD CONSTRAINT relationships_type_chk
        //                CHECK (type IN ('parent','child','partner'))\");

        // (опційно) для симетричного типу 'partner' заборонити дубль у зворотному порядку
        DB::statement("
          CREATE UNIQUE INDEX relationships_partner_pair_once
          ON relationships (LEAST(person_id, other_person_id), GREATEST(person_id, other_person_id))
          WHERE type = 'partner'
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('relationships');
    }
};
