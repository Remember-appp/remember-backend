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

        Schema::create('activation_steps', function (Blueprint $table) {
            $table->bigIncrements('id');       // internal PK
            $table->uuid('uuid')->unique();    // public id

            // BIGINT FK -> death_activation_cases.id
            $table->foreignId('case_id')
                ->constrained('death_activation_cases')
                ->cascadeOnDelete();

            $table->string('step');

            // Polymorphic-by-check (як у message_recipients)
            $table->string('actor_type'); // user/trusted/admin/system
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('actor_trusted_id')->nullable()->constrained('trusted_contacts')->nullOnDelete();

            $table->string('result')->nullable(); // ok/fail/...
            $table->text('notes')->nullable();
            $table->timestampTz('created_at')->useCurrent();

            $table->index('case_id');
            $table->index(['case_id','step']);
            $table->index('actor_user_id');
            $table->index('actor_trusted_id');
            $table->index('created_at');
        });

        DB::statement("
          ALTER TABLE activation_steps
          ADD CONSTRAINT activation_steps_actor_check
          CHECK (
            (actor_type = 'user'    AND actor_user_id    IS NOT NULL AND actor_trusted_id IS NULL) OR
            (actor_type = 'trusted' AND actor_trusted_id IS NOT NULL AND actor_user_id    IS NULL) OR
            (actor_type = 'admin'   AND actor_user_id    IS NOT NULL AND actor_trusted_id IS NULL) OR
            (actor_type = 'system'  AND actor_user_id    IS NULL     AND actor_trusted_id IS NULL)
          )
        ");


        // DB::statement("ALTER TABLE activation_steps ADD CHECK (actor_type IN ('user','trusted','admin','system'))");
        // DB::statement("ALTER TABLE activation_steps ADD CHECK (result IN ('ok','fail'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('activation_steps');
    }
};
