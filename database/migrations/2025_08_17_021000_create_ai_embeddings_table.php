<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ai_embeddings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('dataset_item_id');
            $table->string('model_name');
            $table->json('embedding'); // array float у JSON
            $table->timestampTz('created_at')->useCurrent();

            $table->foreign('dataset_item_id')->references('id')->on('ai_dataset_items')->cascadeOnDelete();
            $table->index('dataset_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_embeddings');
    }
};
