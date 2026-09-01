<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('lead_token', 64)->nullable()->unique()->after('inquiry_qr_code');
        });

        // Generate tokens for existing projects
        $projects = DB::table('projects')->get();
        foreach ($projects as $project) {
            DB::table('projects')->where('id', $project->id)->update([
                'lead_token' => Str::random(32)
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('lead_token');
        });
    }
};
