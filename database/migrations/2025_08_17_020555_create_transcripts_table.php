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

        Schema::create('transcripts', function (Blueprint $table) {
            $table->bigIncrements('id');       // internal PK
            $table->uuid('uuid')->unique();    // public identifier

            $table->foreignId('asset_id')      // BIGINT FK -> assets.id
            ->constrained('assets')
                ->cascadeOnDelete();

            $table->string('lang')->nullable();
            $table->longText('text')->nullable();
            $table->timestampTz('created_at')->useCurrent();

            $table->index('asset_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transcripts');
    }
};
