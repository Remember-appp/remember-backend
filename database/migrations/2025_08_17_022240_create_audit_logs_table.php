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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('actor_user_id')->nullable();
            $table->string('scope');         // profile/message/case/...
            $table->string('action');        // create/update/delete/view/...
            $table->string('entity_table');
            $table->uuid('entity_id')->nullable();
            $table->json('meta')->default(new Expression("'{}'::json"));
            $table->timestampTz('created_at')->useCurrent();

            $table->foreign('actor_user_id')->references('id')->on('users')->nullOnDelete();
            $table->index('created_at');
            $table->index('scope');
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
