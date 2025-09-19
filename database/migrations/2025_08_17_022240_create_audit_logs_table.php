<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS "pgcrypto";');

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->bigIncrements('id');       // internal PK
            $table->uuid('uuid')->unique();    // public identifier

            $table->foreignId('actor_user_id') // BIGINT FK -> users.id
            ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('scope');           // profile/message/case/...
            $table->string('action');          // create/update/delete/view/...
            $table->string('entity_table');

            // для entity_id залишаємо UUID (воно може вказувати на будь-яку сутність)
            $table->uuid('entity_uuid')->nullable();

            $table->json('meta')->default(DB::raw("'{}'::json"));
            $table->timestampTz('created_at')->useCurrent();

            $table->index('created_at');
            $table->index('scope');
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
