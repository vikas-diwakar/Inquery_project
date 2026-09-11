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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('subdomain', 50)->nullable()->unique()->after('name');
        });

        // Auto-generate subdomains for existing companies
        $companies = DB::table('companies')->select('id', 'name')->get();
        $usedSlugs = [];

        foreach ($companies as $company) {
            $baseSlug = Str::slug($company->name);
            if (empty($baseSlug)) {
                $baseSlug = 'company';
            }

            $slug = $baseSlug;
            $counter = 1;
            while (in_array($slug, $usedSlugs) || DB::table('companies')->where('subdomain', $slug)->exists()) {
                $slug = $baseSlug . '-' . ($company->id ?? $counter);
                $counter++;
            }

            $usedSlugs[] = $slug;
            DB::table('companies')->where('id', $company->id)->update(['subdomain' => $slug]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('subdomain');
        });
    }
};
