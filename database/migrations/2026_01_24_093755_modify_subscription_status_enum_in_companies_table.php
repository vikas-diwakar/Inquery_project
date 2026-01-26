<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the enum to include 'pending'
        DB::statement("ALTER TABLE companies MODIFY COLUMN subscription_status ENUM('pending', 'trial', 'active', 'expired', 'cancelled') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum
        DB::statement("ALTER TABLE companies MODIFY COLUMN subscription_status ENUM('trial', 'active', 'expired', 'cancelled') DEFAULT 'trial'");
    }
};
