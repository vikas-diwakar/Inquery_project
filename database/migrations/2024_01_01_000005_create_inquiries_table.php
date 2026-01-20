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
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('customer_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->decimal('budget', 15, 2)->nullable();
            $table->string('flat_type')->nullable(); // 1BHK, 2BHK, 3BHK, etc.
            $table->text('message')->nullable();
            $table->enum('status', ['new', 'contacted', 'qualified', 'booked', 'rejected'])->default('new');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
