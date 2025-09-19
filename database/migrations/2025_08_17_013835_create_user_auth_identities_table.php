<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_auth_identities', function (Blueprint $table) {
            $table->bigIncrements('id'); // internal PK
            $table->uuid('uuid')->unique(); // optional public identifier

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('type'); // password / oauth / phone
            $table->text('secret_hash')->nullable();
            $table->string('provider')->nullable();
            $table->string('provider_uid')->nullable();
            $table->timestampTz('created_at')->useCurrent();

            $table->unique(['user_id','type','provider','provider_uid'], 'uniq_user_provider');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_auth_identities');
    }
};
