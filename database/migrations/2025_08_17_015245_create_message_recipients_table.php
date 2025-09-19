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

        Schema::create('message_recipients', function (Blueprint $table) {
            $table->bigIncrements('id');          // internal PK
            $table->uuid('uuid')->unique();       // public identifier

            // BIGINT FKs -> messages/users/contacts
            $table->foreignId('message_id')->constrained('messages')->cascadeOnDelete();
            $table->string('kind');               // 'user' | 'contact'
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->string('visibility_scope')->default('private');

            // Helpful indexes
            $table->index('message_id');
            $table->index('user_id');
            $table->index('contact_id');
            $table->index(['message_id','kind']);
        });

        // CHECK: exactly one of user_id/contact_id depending on kind
        DB::statement("
          ALTER TABLE message_recipients
          ADD CONSTRAINT message_recipients_kind_check
          CHECK (
            (kind = 'user'    AND user_id    IS NOT NULL AND contact_id IS NULL) OR
            (kind = 'contact' AND contact_id IS NOT NULL AND user_id    IS NULL)
          )
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('message_recipients');
    }
};
