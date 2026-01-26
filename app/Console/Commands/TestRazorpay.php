<?php

namespace App\Console\Commands;

use App\Services\RazorpayService;
use Illuminate\Console\Command;

class TestRazorpay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:razorpay {--create-order : Test order creation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Razorpay integration';

    /**
     * Execute the console command.
     */
    public function handle(RazorpayService $razorpay)
    {
        $this->info('Testing Razorpay Integration...');

        // Test basic service instantiation
        $this->info('✓ Razorpay service instantiated successfully');

        // Test getting Razorpay key
        $key = $razorpay->getKey();
        if ($key) {
            $this->info('✓ Razorpay key retrieved: ' . substr($key, 0, 10) . '...');
        } else {
            $this->error('✗ Razorpay key not configured');
            return;
        }

        // Test order creation if requested
        if ($this->option('create-order')) {
            $this->info('Testing order creation...');
            try {
                $order = $razorpay->createOrder(100, 'INR', 'test_order_' . time());
                $this->info('✓ Order created successfully:');
                $this->line('  Order ID: ' . $order['id']);
                $this->line('  Amount: ' . $order['amount']);
                $this->line('  Currency: ' . $order['currency']);
            } catch (\Exception $e) {
                $this->error('✗ Order creation failed: ' . $e->getMessage());
            }
        }

        $this->info('Razorpay integration test completed!');
    }
}
