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

        Schema::create('transcript_segments', function (Blueprint $table) {
            $table->bigIncrements('id');          // internal PK
            $table->uuid('uuid')->unique();       // public id

            $table->foreignId('transcript_id')    // BIGINT FK -> transcripts.id
            ->constrained('transcripts')
                ->cascadeOnDelete();

            $table->decimal('t_start', 10, 3);
            $table->decimal('t_end', 10, 3);
            $table->text('text')->nullable();
            $table->string('topic_tag')->nullable();

            $table->index('transcript_id');
            $table->index(['t_start','t_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transcript_segments');
    }
};
