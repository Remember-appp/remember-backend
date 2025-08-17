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
        Schema::create('attachments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('message_id');
            $table->uuid('asset_id');
            $table->string('kind');     // text/photo/video/audio/doc
            $table->integer('position')->default(0);

            $table->foreign('message_id')->references('id')->on('messages')->cascadeOnDelete();
            $table->foreign('asset_id')->references('id')->on('assets')->restrictOnDelete();
            $table->index('message_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
