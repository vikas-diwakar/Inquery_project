<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('inquiry_custom_fields')) {
            Schema::create('inquiry_custom_fields', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->foreignId('project_id')->constrained()->onDelete('cascade');
                $table->string('field_label');
                $table->string('field_name');
                $table->string('field_type')->default('text'); // text, number, select, textarea
                $table->json('field_options')->nullable();
                $table->string('placeholder')->nullable();
                $table->boolean('is_required')->default(false);
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasColumn('inquiries', 'custom_fields')) {
            Schema::table('inquiries', function (Blueprint $table) {
                $table->json('custom_fields')->nullable()->after('selected_unit_option_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiry_custom_fields');
        if (Schema::hasColumn('inquiries', 'custom_fields')) {
            Schema::table('inquiries', function (Blueprint $table) {
                $table->dropColumn('custom_fields');
            });
        }
    }
};
