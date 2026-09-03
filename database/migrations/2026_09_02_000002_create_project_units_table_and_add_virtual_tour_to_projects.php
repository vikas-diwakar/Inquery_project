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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('virtual_tour_url')->nullable()->after('description');
            $table->string('master_plan_image')->nullable()->after('virtual_tour_url');
        });

        Schema::create('project_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('unit_number'); // e.g. A-101, B-402
            $table->string('tower_name')->default('Tower A'); // Tower A, Phase 1, Block B
            $table->integer('floor_number')->default(1); // 1, 2, 3...
            $table->string('unit_type')->nullable(); // 2 BHK, 3 BHK, Penthouse
            $table->enum('status', ['available', 'on_hold', 'sold'])->default('available');
            $table->decimal('price', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'project_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_units');

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['virtual_tour_url', 'master_plan_image']);
        });
    }
};
