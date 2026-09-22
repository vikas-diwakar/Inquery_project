<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Project;
use App\Models\Role;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\RazorpayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class SubscriptionExpirationLockdownTest extends TestCase
{
    use RefreshDatabase;

    private Company $expiredCompany;
    private User $adminUser;
    private User $employeeUser;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        SubscriptionPlan::ensureDefaultPlansExist();

        // Expired company
        $this->expiredCompany = Company::create([
            'name' => 'Expired Realty Corp',
            'email' => 'contact@expiredrealty.com',
            'subscription_status' => 'expired',
            'subscription_ends_at' => now()->subDays(2),
        ]);

        $paidPlan = SubscriptionPlan::where('type', 'paid')->first();
        $this->expiredCompany->subscriptions()->create([
            'subscription_plan_id' => $paidPlan->id,
            'start_date' => now()->subMonths(6),
            'end_date' => now()->subDays(2),
            'status' => 'expired',
        ]);

        $adminRole = Role::create([
            'company_id' => $this->expiredCompany->id,
            'name' => 'Admin',
            'permissions' => ['*'],
        ]);

        $employeeRole = Role::create([
            'company_id' => $this->expiredCompany->id,
            'name' => 'Sales Executive',
            'permissions' => ['inquiries.view'],
        ]);

        $this->adminUser = User::factory()->create([
            'company_id' => $this->expiredCompany->id,
            'role_id' => $adminRole->id,
            'email_verified_at' => now(),
        ]);

        $this->employeeUser = User::factory()->create([
            'company_id' => $this->expiredCompany->id,
            'role_id' => $employeeRole->id,
            'email_verified_at' => now(),
        ]);

        $this->project = Project::create([
            'company_id' => $this->expiredCompany->id,
            'name' => 'Sunset Heights',
            'location' => 'Downtown',
            'status' => 'ongoing',
        ]);
    }

    public function test_expired_company_admin_is_redirected_to_subscription_required_from_dashboard(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('subscription.required'));
        $response->assertSessionHas('error');
    }

    public function test_expired_company_admin_is_redirected_to_subscription_required_from_projects(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('projects.index'));

        $response->assertRedirect(route('subscription.required'));
        $response->assertSessionHas('error');
    }

    public function test_expired_company_admin_is_redirected_to_subscription_required_from_users(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('users.index'));

        $response->assertRedirect(route('subscription.required'));
        $response->assertSessionHas('error');
    }

    public function test_expired_company_employee_is_redirected_to_subscription_required_from_inquiries(): void
    {
        $this->actingAs($this->employeeUser);

        $response = $this->withSession(['selected_project_id' => $this->project->id])
            ->get(route('inquiries.index'));

        $response->assertRedirect(route('subscription.required'));
        $response->assertSessionHas('error');
    }

    public function test_expired_company_user_cannot_see_app_navigation_links_on_subscription_required_page(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->get(route('subscription.required'));

        $response->assertOk();
        $response->assertSee('Subscription Expired');
        // Check that internal app links are NOT present in the navigation
        $response->assertDontSee('href="' . route('dashboard') . '"', false);
        $response->assertDontSee('href="' . route('projects.index') . '"', false);
        $response->assertDontSee('href="' . route('users.index') . '"', false);
    }

    public function test_expired_company_employee_sees_admin_authorization_notice_on_subscription_required_page(): void
    {
        $this->actingAs($this->employeeUser);

        $response = $this->get(route('subscription.required'));

        $response->assertOk();
        $response->assertSee('Administrator Authorization Required');
        $response->assertSee('Admin Only');
    }

    public function test_active_company_user_retains_full_access_and_sees_navigation_links(): void
    {
        $activeCompany = Company::create([
            'name' => 'Active Skyline Developers',
            'email' => 'info@activeskyline.com',
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addMonths(3),
        ]);

        $adminRole = Role::create([
            'company_id' => $activeCompany->id,
            'name' => 'Admin',
            'permissions' => ['*'],
        ]);

        $activeAdmin = User::factory()->create([
            'company_id' => $activeCompany->id,
            'role_id' => $adminRole->id,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($activeAdmin);

        $response = $this->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('href="' . route('dashboard') . '"', false);
        $response->assertSee('href="' . route('projects.index') . '"', false);
    }

    public function test_expired_admin_can_access_checkout_and_create_order_to_renew(): void
    {
        $paidPlan = SubscriptionPlan::where('type', 'paid')->first();

        $mockRazorpay = Mockery::mock(RazorpayService::class);
        $mockRazorpay->shouldReceive('isConfigured')->andReturn(true);
        $mockRazorpay->shouldReceive('getKey')->andReturn('rzp_test_mockkey');
        $mockRazorpay->shouldReceive('isTestMode')->andReturn(true);
        $mockRazorpay->shouldReceive('createOrder')->once()->andReturn([
            'id' => 'order_renew_123',
            'amount' => $paidPlan->price * 100,
            'currency' => 'INR',
            'status' => 'created',
        ]);

        $this->app->instance(RazorpayService::class, $mockRazorpay);

        $this->actingAs($this->adminUser);

        // Checkout page is accessible
        $checkoutResponse = $this->get(route('subscription.checkout', $paidPlan));
        $checkoutResponse->assertOk();

        // Create order is accessible
        $orderResponse = $this->postJson(route('subscription.create-order', $paidPlan));
        $orderResponse->assertOk();
        $orderResponse->assertJson([
            'success' => true,
            'order_id' => 'order_renew_123',
        ]);
    }

    public function test_ajax_request_returns_json_subscription_required_when_expired(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->getJson(route('dashboard'));

        $response->assertStatus(403);
        $response->assertJson([
            'subscription_required' => true,
        ]);
    }
}
