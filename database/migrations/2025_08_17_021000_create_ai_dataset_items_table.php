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

        Schema::create('ai_dataset_items', function (Blueprint $table) {
            $table->bigIncrements('id');          // internal PK
            $table->uuid('uuid')->unique();       // public id

            $table->foreignId('dataset_id')       // -> ai_datasets.id (BIGINT)
            ->constrained('ai_datasets')
                ->cascadeOnDelete();

            $table->string('source_type');        // transcript | text
            // For transcript items we reference transcripts.id (BIGINT)
            $table->foreignId('source_transcript_id')
                ->nullable()
                ->constrained('transcripts')
                ->cascadeOnDelete();

            $table->longText('text')->nullable(); // used when source_type = 'text'
            $table->json('meta')->default(DB::raw("'{}'::json"));
            $table->timestampTz('created_at')->useCurrent();

            $table->index('dataset_id');
            $table->index(['dataset_id','source_type']);
            $table->index('source_transcript_id');
        });

        // Enforce consistency: exactly one mode
        DB::statement("
          ALTER TABLE ai_dataset_items
          ADD CONSTRAINT ai_dataset_items_source_check
          CHECK (
            (source_type = 'transcript' AND source_transcript_id IS NOT NULL AND text IS NULL)
            OR
            (source_type = 'text'       AND source_transcript_id IS NULL     AND text IS NOT NULL)
          )
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_dataset_items');
    }
};
