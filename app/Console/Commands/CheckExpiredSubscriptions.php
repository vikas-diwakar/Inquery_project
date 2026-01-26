<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\Subscription;
use Illuminate\Console\Command;

class CheckExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expired subscriptions and update company status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired subscriptions...');

        // Find subscriptions that have expired
        $expiredSubscriptions = Subscription::where('end_date', '<', now())
            ->whereIn('status', ['active', 'trial'])
            ->with('company')
            ->get();

        $expiredCount = 0;

        foreach ($expiredSubscriptions as $subscription) {
            // Update subscription status
            $subscription->update(['status' => 'expired']);

            // Update company status if this was their active subscription
            if ($subscription->company->activeSubscription() === null) {
                $subscription->company->expireSubscription();
                $expiredCount++;
            }

            $this->line("Expired subscription for company: {$subscription->company->name}");
        }

        $this->info("Found and expired {$expiredCount} company subscriptions.");
        $this->info('Expired subscriptions check completed.');
    }
}
