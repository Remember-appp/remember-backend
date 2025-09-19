<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_consents', function (Blueprint $table) {
            $table->bigIncrements('id'); // internal PK
            $table->uuid('uuid')->unique(); // optional public id

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('kind');     // tos/privacy/digital_will/...
            $table->string('version');
            $table->timestampTz('granted_at');
            $table->timestampTz('revoked_at')->nullable();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_consents');
    }
};
