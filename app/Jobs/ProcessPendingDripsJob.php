<?php

namespace App\Jobs;

use App\Services\DripNurtureService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPendingDripsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public ?int $companyId = null,
        public bool $forceNow = false
    ) {}

    public function handle(DripNurtureService $service): void
    {
        Log::info("Processing queued pending drips for Company ID: " . ($this->companyId ?? 'All Companies'));
        $result = $service->processPendingDrips($this->companyId, $this->forceNow);
        Log::info("Queued drips processed: {$result['total_processed']} total, {$result['sent']} sent, {$result['failed']} failed.");
    }
}
