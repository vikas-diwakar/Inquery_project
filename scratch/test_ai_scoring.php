<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Inquiry;
use App\Models\Project;
use App\Services\LeadScoringService;
use App\Services\LeadAllocationService;

$project = Project::first();
if (!$project) {
    echo "No project found.\n";
    exit;
}

echo "=== Testing AI Lead Quality Scoring & Round-Robin Allocation ===\n\n";

// Re-evaluate existing inquiries
$inquiries = Inquiry::all();
$scoringService = app(LeadScoringService::class);
$allocationService = app(LeadAllocationService::class);

foreach ($inquiries as $inquiry) {
    $scoringService->evaluateAndUpdate($inquiry);
    if (!$inquiry->assigned_to) {
        $allocationService->allocateInquiry($inquiry);
    }
    $inquiry->refresh();
    echo "Lead #{$inquiry->id} ({$inquiry->customer_name}): Score={$inquiry->lead_score}/100 Grade={$inquiry->lead_grade} AssignedTo=" . ($inquiry->assignedUser->name ?? 'Unassigned') . "\n";
}

echo "\nCompleted successfully!\n";
