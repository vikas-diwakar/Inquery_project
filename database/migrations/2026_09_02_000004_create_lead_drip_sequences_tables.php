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
        Schema::create('lead_drip_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('day_offset'); // 1, 3, 7, 14
            $table->string('step_title'); // e.g. "Day 3: Video Walkthrough & Location Advantages"
            $table->enum('channel', ['whatsapp', 'email', 'both'])->default('whatsapp');
            $table->text('message_template');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['company_id', 'day_offset']);
        });

        Schema::create('inquiry_drip_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('inquiry_id')->constrained()->onDelete('cascade');
            $table->foreignId('lead_drip_step_id')->constrained('lead_drip_steps')->onDelete('cascade');
            $table->dateTime('scheduled_for');
            $table->enum('status', ['pending', 'sent', 'failed', 'skipped'])->default('pending');
            $table->dateTime('sent_at')->nullable();
            $table->text('sent_message')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'status', 'scheduled_for']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiry_drip_logs');
        Schema::dropIfExists('lead_drip_steps');
    }
};
