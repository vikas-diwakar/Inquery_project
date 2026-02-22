<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the enum values for inquiries.status to support lead workflow
        DB::statement("ALTER TABLE `inquiries` MODIFY `status` ENUM('new','contacted','interested','site_visit','booked','lost') NOT NULL DEFAULT 'new'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to previous enum values
        DB::statement("ALTER TABLE `inquiries` MODIFY `status` ENUM('new','contacted','qualified','booked','rejected') NOT NULL DEFAULT 'new'");
    }
};
