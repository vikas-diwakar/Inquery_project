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
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dateTime('next_follow_up_date')->nullable()->after('status')->comment('Scheduled date for next follow-up');
            $table->dateTime('last_follow_up_date')->nullable()->after('next_follow_up_date')->comment('Date of last follow-up');
            $table->text('follow_up_notes')->nullable()->after('last_follow_up_date')->comment('Notes from follow-ups');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropColumn(['next_follow_up_date', 'last_follow_up_date', 'follow_up_notes']);
        });
    }
};
