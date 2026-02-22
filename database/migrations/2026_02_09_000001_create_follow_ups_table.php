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
        Schema::create('follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inquiry_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('follow_up_by')->constrained('users')->onDelete('cascade')->comment('User who performed the follow-up');
            $table->enum('type', ['call', 'email', 'sms', 'visit', 'message'])->default('call')->comment('Type of follow-up');
            $table->text('notes')->nullable()->comment('Follow-up notes and observations');
            $table->enum('outcome', ['interested', 'not_interested', 'no_response', 'callback_requested', 'other'])->nullable()->comment('Outcome of the follow-up');
            $table->dateTime('scheduled_date')->nullable()->comment('When this follow-up was scheduled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follow_ups');
    }
};
