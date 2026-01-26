<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$company = App\Models\Company::where('name', 'mohan_sangali')->first();

if ($company) {
    echo "Company found.\n";
    echo "Can use trial: " . ($company->canUseTrial() ? 'YES' : 'NO') . "\n";
    echo "Trial used: " . ($company->trial_used ? 'YES' : 'NO') . "\n";
    echo "Subscription count: " . $company->subscriptions()->count() . "\n";
    echo "Subscription status: " . $company->subscription_status . "\n";

    $lastTrial = $company->subscriptions()->where('status', 'trial')->latest()->first();
    if ($lastTrial) {
        echo "Last trial found:\n";
        echo "  Status: " . $lastTrial->status . "\n";
        echo "  End date: " . $lastTrial->end_date . "\n";
        echo "  Is expired: " . ($lastTrial->isExpired() ? 'YES' : 'NO') . "\n";
        echo "  End date is past: " . ($lastTrial->end_date->isPast() ? 'YES' : 'NO') . "\n";
    } else {
        echo "No trial subscription found\n";
    }

    // Check all subscriptions
    echo "\nAll subscriptions:\n";
    foreach ($company->subscriptions as $sub) {
        echo "  ID: {$sub->id}, Status: {$sub->status}, End: {$sub->end_date}, Expired: " . ($sub->isExpired() ? 'YES' : 'NO') . "\n";
    }
} else {
    echo "Company not found\n";
}