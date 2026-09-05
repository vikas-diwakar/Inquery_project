<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Role;
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

    public function test_selecting_paid_plan_redirects_to_checkout(): void
    {
        $company = Company::create([
            'name' => 'Test Builder 2',
            'email' => 'builder2@example.com',
            'phone' => '9876543210',
            'address' => 'Test Address',
            'subscription_status' => 'expired',
        ]);

        $adminRole = Role::create([
            'company_id' => $company->id,
            'name' => 'Admin',
            'permissions' => ['*'],
        ]);

        $user = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $adminRole->id,
        ]);

        SubscriptionPlan::ensureDefaultPlansExist();
        $paidPlan = SubscriptionPlan::where('type', 'paid')->first();

        $this->actingAs($user);

        $response = $this->post(route('subscription.activate-plan'), [
            'plan_id' => $paidPlan->id,
        ]);

        $response->assertRedirect(route('subscription.checkout', $paidPlan));
    }

    public function test_realtime_expired_subscription_is_detected(): void
    {
        $company = Company::create([
            'name' => 'Test Builder 3',
            'email' => 'builder3@example.com',
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->subDay(), // Expired yesterday
        ]);

        $this->assertFalse($company->hasActiveSubscription());
        $this->assertEquals('expired', $company->subscription_status);
        $this->assertTrue($company->subscriptionExpired());
    }

    public function test_renew_subscription_redirects_to_checkout(): void
    {
        $company = Company::create([
            'name' => 'Test Builder 4',
            'email' => 'builder4@example.com',
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addDays(5),
        ]);

        $adminRole = Role::create([
            'company_id' => $company->id,
            'name' => 'Admin',
            'permissions' => ['*'],
        ]);

        $user = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $adminRole->id,
        ]);

        SubscriptionPlan::ensureDefaultPlansExist();
        $paidPlan = SubscriptionPlan::where('type', 'paid')->first();

        // Create an active subscription record
        $company->subscriptions()->create([
            'subscription_plan_id' => $paidPlan->id,
            'start_date' => now()->subMonth(),
            'end_date' => now()->addDays(5),
            'status' => 'active',
        ]);

        $this->actingAs($user);

        $response = $this->post(route('subscription.renew'));

        $response->assertRedirect(route('subscription.checkout', $paidPlan));
    }
}
