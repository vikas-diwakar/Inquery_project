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
            $table->integer('lead_score')->default(0)->after('status');
            $table->enum('lead_grade', ['hot', 'warm', 'cold'])->default('cold')->after('lead_score');
            $table->json('score_breakdown')->nullable()->after('lead_grade');
            $table->dateTime('allocated_at')->nullable()->after('assigned_to');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->enum('lead_allocation_method', ['manual', 'round_robin'])->default('round_robin')->after('whatsapp_welcome_template');
            $table->foreignId('last_allocated_user_id')->nullable()->after('lead_allocation_method')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['last_allocated_user_id']);
            $table->dropColumn(['lead_allocation_method', 'last_allocated_user_id']);
        });

        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropColumn(['lead_score', 'lead_grade', 'score_breakdown', 'allocated_at']);
        });
    }
};
