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

        Schema::create('plans', function (Blueprint $table) {
            $table->bigIncrements('id');       // internal PK
            $table->uuid('uuid')->unique();    // public identifier

            $table->string('code')->unique();  // план по коду
            $table->string('name');
            $table->bigInteger('price_cents');
            $table->string('currency')->default('USD');
            $table->json('quotas')->default(DB::raw("'{}'::json"));
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
