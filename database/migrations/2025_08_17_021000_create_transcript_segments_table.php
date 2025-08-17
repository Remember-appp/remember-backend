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
        Schema::create('transcript_segments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('transcript_id');
            $table->decimal('t_start', 10, 3);
            $table->decimal('t_end', 10, 3);
            $table->text('text')->nullable();
            $table->string('topic_tag')->nullable();

            $table->foreign('transcript_id')->references('id')->on('transcripts')->cascadeOnDelete();
            $table->index('transcript_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transcript_segments');
    }
};
