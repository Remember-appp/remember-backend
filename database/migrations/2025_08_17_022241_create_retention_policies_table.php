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

        Schema::create('retention_policies', function (Blueprint $table) {
            $table->bigIncrements('id');     // internal PK
            $table->uuid('uuid')->unique();  // public identifier

            $table->string('subject');       // user/message/asset/...
            $table->text('rule');
            $table->integer('ttl_days');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retention_policies');
    }
};
