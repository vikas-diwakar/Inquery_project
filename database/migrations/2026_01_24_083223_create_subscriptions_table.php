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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['trial', 'active', 'expired', 'cancelled'])->default('trial');
            $table->decimal('amount_paid', 10, 2)->nullable();
            $table->string('currency', 3)->default('INR');
            $table->string('payment_reference')->nullable();
            $table->json('payment_details')->nullable();
            $table->boolean('auto_renew')->default(false);
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index(['end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
