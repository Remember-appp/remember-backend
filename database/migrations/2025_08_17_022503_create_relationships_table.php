<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('relationships', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('person_id');
            $table->uuid('other_person_id');
            $table->string('type');          // parent/child/partner
            $table->date('since')->nullable();
            $table->date('until')->nullable();

            $table->foreign('person_id')->references('id')->on('persons')->cascadeOnDelete();
            $table->foreign('other_person_id')->references('id')->on('persons')->cascadeOnDelete();

            $table->unique(['person_id','other_person_id','type']);
        });

        DB::statement("
          ALTER TABLE relationships
          ADD CONSTRAINT relationships_no_self CHECK (person_id <> other_person_id)
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relationships');
    }
};
