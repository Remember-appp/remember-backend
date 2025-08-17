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
        Schema::create('message_recipients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('message_id');
            $table->string('kind'); // user/contact
            $table->uuid('user_id')->nullable();
            $table->uuid('contact_id')->nullable();
            $table->string('visibility_scope')->default('private');

            $table->foreign('message_id')->references('id')->on('messages')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('contact_id')->references('id')->on('contacts')->nullOnDelete();

            $table->index('message_id');
            $table->index('user_id');
            $table->index('contact_id');
        });

        DB::statement("
          ALTER TABLE message_recipients
          ADD CONSTRAINT message_recipients_kind_check
          CHECK (
            (kind='user' AND user_id IS NOT NULL AND contact_id IS NULL)
            OR
            (kind='contact' AND contact_id IS NOT NULL AND user_id IS NULL)
          )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_recipients');
    }
};
