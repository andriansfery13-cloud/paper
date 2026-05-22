<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Make started_at nullable using raw SQL to avoid doctrine/dbal dependency issues
        DB::statement("ALTER TABLE subscription_histories MODIFY COLUMN started_at TIMESTAMP NULL DEFAULT NULL");

        // Update status enum to include pending and failed
        DB::statement("ALTER TABLE subscription_histories MODIFY COLUMN status ENUM('active', 'expired', 'cancelled', 'upgraded', 'pending', 'failed') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting enum strictly is hard if we have data, but we can try
        // DB::statement("ALTER TABLE subscription_histories MODIFY COLUMN status ENUM('active', 'expired', 'cancelled', 'upgraded') NOT NULL DEFAULT 'active'");

        // We generally don't revert nullable to not-null as it might fail if nulls exist
    }
};
