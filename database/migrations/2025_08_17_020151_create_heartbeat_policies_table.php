<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('heartbeat_policies', function (Blueprint $table) {
            // PK = user_id referencing users.id (BIGINT)
            $table->foreignId('user_id')
                ->primary()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->integer('cadence_days')->default(14);     // days between pings
            $table->integer('escalation_days')->default(30);  // days to escalate
            $table->integer('grace_days')->default(14);       // grace window
            $table->json('channels')->nullable();             // ["email","push"]

            // Optional: indexes/checks if needed
            // DB::statement("ALTER TABLE heartbeat_policies ADD CHECK (cadence_days > 0 AND escalation_days > 0 AND grace_days >= 0)");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('heartbeat_policies');
    }
};
