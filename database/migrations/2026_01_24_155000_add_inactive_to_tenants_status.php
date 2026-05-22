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
        // Modify ENUM to include 'inactive'
        DB::statement("ALTER TABLE tenants MODIFY COLUMN status ENUM('active', 'suspended', 'expired', 'cancelled', 'inactive') DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original ENUM (Warning: this might fail if there are 'inactive' rows, so usually we don't strict revert enums with data loss risks in simple apps)
        // But for completeness:
        // DB::statement("ALTER TABLE tenants MODIFY COLUMN status ENUM('active', 'suspended', 'expired', 'cancelled') DEFAULT 'active'");
    }
};
