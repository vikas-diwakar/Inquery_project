<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Trial Plan
        SubscriptionPlan::create([
            'name' => 'Free Trial',
            'type' => 'trial',
            'duration_months' => 3,
            'price' => null,
            'currency' => 'INR',
            'features' => [
                'projects' => true,
                'inquiries' => true,
                'qr_codes' => true,
                'brochures' => true,
                'users' => true,
                'support' => 'basic',
            ],
            'is_active' => true,
        ]);

        // 6-Month Plan
        SubscriptionPlan::create([
            'name' => '6-Month Plan',
            'type' => 'paid',
            'duration_months' => 6,
            'price' => 2999.00, // Configurable pricing
            'currency' => 'INR',
            'features' => [
                'projects' => true,
                'inquiries' => true,
                'qr_codes' => true,
                'brochures' => true,
                'users' => true,
                'support' => 'priority',
                'analytics' => true,
            ],
            'is_active' => true,
        ]);

        // 1-Year Plan
        SubscriptionPlan::create([
            'name' => '1-Year Plan',
            'type' => 'paid',
            'duration_months' => 12,
            'price' => 4999.00, // Configurable pricing
            'currency' => 'INR',
            'features' => [
                'projects' => true,
                'inquiries' => true,
                'qr_codes' => true,
                'brochures' => true,
                'users' => true,
                'support' => 'premium',
                'analytics' => true,
                'api_access' => true,
            ],
            'is_active' => true,
        ]);
    }
}
