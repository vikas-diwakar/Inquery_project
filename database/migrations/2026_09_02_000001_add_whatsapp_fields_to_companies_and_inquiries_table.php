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
            $table->string('whatsapp_provider')->default('simulated')->after('subscription_ends_at'); // simulated, twilio, ultramsg, meta_cloud
            $table->string('whatsapp_api_key')->nullable()->after('whatsapp_provider');
            $table->string('whatsapp_phone_number_id')->nullable()->after('whatsapp_api_key');
            $table->string('whatsapp_instance_id')->nullable()->after('whatsapp_phone_number_id');
            $table->boolean('whatsapp_auto_send')->default(true)->after('whatsapp_instance_id');
            $table->text('whatsapp_welcome_template')->nullable()->after('whatsapp_auto_send');
        });

        Schema::table('inquiries', function (Blueprint $table) {
            $table->timestamp('whatsapp_sent_at')->nullable()->after('status');
            $table->string('whatsapp_status')->nullable()->after('whatsapp_sent_at'); // sent, failed, pending
            $table->text('whatsapp_last_message')->nullable()->after('whatsapp_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'whatsapp_provider',
                'whatsapp_api_key',
                'whatsapp_phone_number_id',
                'whatsapp_instance_id',
                'whatsapp_auto_send',
                'whatsapp_welcome_template',
            ]);
        });

        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropColumn([
                'whatsapp_sent_at',
                'whatsapp_status',
                'whatsapp_last_message',
            ]);
        });
    }
};
