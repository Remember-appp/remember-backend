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
        Schema::create('person_assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('person_id');
            $table->uuid('asset_id');
            $table->string('kind'); // photo/video/doc

            $table->foreign('person_id')->references('id')->on('persons')->cascadeOnDelete();
            $table->foreign('asset_id')->references('id')->on('assets')->restrictOnDelete();

            $table->index('person_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person_assets');
    }
};
