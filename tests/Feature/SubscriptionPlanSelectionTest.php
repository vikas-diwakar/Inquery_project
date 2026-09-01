<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionPlanSelectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_first_login_users_can_see_default_subscription_plans_when_none_exist(): void
    {
        $company = Company::create([
            'name' => 'Test Builder',
            'email' => 'builder@example.com',
            'phone' => '9876543210',
            'address' => 'Test Address',
            'subscription_status' => 'pending',
        ]);

        $user = User::factory()->create([
            'company_id' => $company->id,
        ]);

        SubscriptionPlan::query()->delete();

        $this->actingAs($user);

        $response = $this->get(route('subscription.choose-plan'));

        $response->assertOk();
        $response->assertSee('Free Trial');
        $response->assertSee('6-Month Plan');
        $response->assertSee('1-Year Plan');

        $this->assertDatabaseHas('subscription_plans', ['type' => 'trial']);
        $this->assertDatabaseHas('subscription_plans', ['type' => 'paid', 'duration_months' => 6]);
        $this->assertDatabaseHas('subscription_plans', ['type' => 'paid', 'duration_months' => 12]);
    }
}
