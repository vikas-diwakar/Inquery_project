<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inquiry_drip_logs', function (Blueprint $table) {
            $table->foreignId('lead_drip_step_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inquiry_drip_logs', function (Blueprint $table) {
            $table->foreignId('lead_drip_step_id')->nullable(false)->change();
        });
    }
};
