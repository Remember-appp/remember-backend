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

        Schema::create('attachments', function (Blueprint $table) {
            $table->bigIncrements('id');            // internal PK
            $table->uuid('uuid')->unique();         // public identifier

            $table->foreignId('message_id')         // BIGINT FK -> messages.id
            ->constrained('messages')
                ->cascadeOnDelete();

            $table->foreignId('asset_id')           // BIGINT FK -> assets.id
            ->constrained('assets')
                ->restrictOnDelete();

            $table->string('kind');                 // text/photo/video/audio/doc
            $table->integer('position')->default(0);

            $table->index('message_id');
            $table->index(['message_id','position']); // common ordering query
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
