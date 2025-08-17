<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ai_dataset_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('dataset_id');
            $table->string('source_type');      // transcript | text
            $table->uuid('source_id')->nullable();
            $table->longText('text')->nullable();
            $table->json('meta')->default(new Expression("'{}'::json"));
            $table->timestampTz('created_at')->useCurrent();

            $table->foreign('dataset_id')->references('id')->on('ai_datasets')->cascadeOnDelete();
            $table->index('dataset_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_dataset_items');
    }
};
