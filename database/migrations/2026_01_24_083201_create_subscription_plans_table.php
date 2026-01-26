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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "6-Month Plan", "1-Year Plan"
            $table->string('type'); // 'trial', 'paid'
            $table->integer('duration_months'); // 3 for trial, 6 or 12 for paid
            $table->decimal('price', 10, 2)->nullable(); // null for trial, amount for paid
            $table->string('currency', 3)->default('INR');
            $table->json('features')->nullable(); // Store features as JSON
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
