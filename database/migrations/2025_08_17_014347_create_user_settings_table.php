<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_settings', function (Blueprint $table) {
            // PK = user_id (BIGINT) pointing to users.id
            $table->foreignId('user_id')
                ->primary()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('tz')->default('America/Edmonton');
            $table->string('locale')->default('uk-UA');

            // Use DB::raw for Postgres JSON default
            $table->json('privacy')->default(DB::raw("'{}'::json"));
            $table->json('notifications')->default(DB::raw("'{}'::json"));
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};
