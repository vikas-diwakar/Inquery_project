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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('whatsapp_waba_id')->nullable()->after('whatsapp_phone_number_id');
            $table->string('whatsapp_connected_phone')->nullable()->after('whatsapp_waba_id');
            $table->string('whatsapp_account_status')->default('disconnected')->after('whatsapp_connected_phone');
            $table->timestamp('whatsapp_connected_at')->nullable()->after('whatsapp_account_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'whatsapp_waba_id',
                'whatsapp_connected_phone',
                'whatsapp_account_status',
                'whatsapp_connected_at',
            ]);
        });
    }
};
