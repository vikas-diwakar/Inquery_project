<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;

class RegenerateQrCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qr:regenerate {company_id? : Optional specific company ID to regenerate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate all inquiry form and brochure QR codes for companies';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $companyId = $this->argument('company_id');

        $query = Company::query();
        if ($companyId) {
            $query->where('id', $companyId);
        }

        $companies = $query->get();

        if ($companies->isEmpty()) {
            $this->warn('No companies found.');
            return self::SUCCESS;
        }

        $this->info("Regenerating QR codes for {$companies->count()} company(ies)...");

        $totalProjects = 0;
        $totalBrochures = 0;

        foreach ($companies as $company) {
            $res = $company->regenerateAllQrCodes();
            $totalProjects += $res['projects_count'];
            $totalBrochures += $res['brochures_count'];

            $this->line("  ✓ Company: {$company->name} (subdomain: {$company->subdomain}) -> {$res['projects_count']} project(s), {$res['brochures_count']} brochure(s)");
        }

        $this->info("Completed! Regenerated {$totalProjects} project form QR code(s) and {$totalBrochures} brochure QR code(s).");

        return self::SUCCESS;
    }
}
