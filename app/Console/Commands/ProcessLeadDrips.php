<?php

namespace App\Console\Commands;

use App\Services\DripNurtureService;
use Illuminate\Console\Command;

class ProcessLeadDrips extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inquiries:process-drip-sequences {--company= : Optional company ID filter}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process and dispatch scheduled automated WhatsApp lead nurturing drip steps';

    /**
     * Execute the console command.
     */
    public function handle(DripNurtureService $service)
    {
        $companyId = $this->option('company') ? (int) $this->option('company') : null;

        $this->info('Starting automated lead drip sequence dispatching...');

        $result = $service->processPendingDrips($companyId);

        $this->info("Processed: {$result['total_processed']} due drip logs.");
        $this->info("Sent: {$result['sent']} messages successfully.");
        if ($result['failed'] > 0) {
            $this->error("Failed: {$result['failed']} messages.");
        }

        return Command::SUCCESS;
    }
}
