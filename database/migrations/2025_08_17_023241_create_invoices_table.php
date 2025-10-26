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

        Schema::create('invoices', function (Blueprint $table) {
            $table->bigIncrements('id');       // internal PK
            $table->uuid('uuid')->unique();    // public identifier

            $table->foreignId('subscription_id')  // BIGINT FK -> subscriptions.id
            ->constrained('subscriptions')
                ->cascadeOnDelete();

            $table->bigInteger('amount_cents');
            $table->string('currency')->default('USD');
            $table->string('status'); // open/paid/void/uncollectible
            $table->timestampTz('issued_at')->useCurrent();
            $table->timestampTz('paid_at')->nullable();

            $table->index('subscription_id');
            $table->index('status');
        });

        // optional strictness
        DB::statement("ALTER TABLE invoices
                       ADD CONSTRAINT invoices_status_chk
                       CHECK (status IN ('open','paid','void','uncollectible'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
