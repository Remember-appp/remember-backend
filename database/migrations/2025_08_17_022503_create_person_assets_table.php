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

        Schema::create('person_assets', function (Blueprint $table) {
            $table->bigIncrements('id');       // internal PK
            $table->uuid('uuid')->unique();    // public identifier

            $table->foreignId('person_id')     // BIGINT FK -> persons.id
            ->constrained('persons')
                ->cascadeOnDelete();

            $table->foreignId('asset_id')      // BIGINT FK -> assets.id
            ->constrained('assets')
                ->restrictOnDelete();

            $table->string('kind');            // photo/video/doc

            $table->index('person_id');
            $table->index(['person_id','kind']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('person_assets');
    }
};
