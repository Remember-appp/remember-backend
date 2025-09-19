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

        Schema::create('ai_embeddings', function (Blueprint $table) {
            $table->bigIncrements('id');         // internal PK
            $table->uuid('uuid')->unique();      // public identifier

            $table->foreignId('dataset_item_id') // BIGINT FK -> ai_dataset_items.id
            ->constrained('ai_dataset_items')
                ->cascadeOnDelete();

            $table->string('model_name');
            $table->json('embedding');           // array<float> stored in JSON
            $table->timestampTz('created_at')->useCurrent();

            $table->index('dataset_item_id');
            $table->index('model_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_embeddings');
    }
};
