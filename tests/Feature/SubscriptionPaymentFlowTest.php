<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\RazorpayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class SubscriptionPaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private User $user;
    private SubscriptionPlan $plan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create([
            'name' => 'Acme Real Estate',
            'email' => 'acme@realestate.com',
            'phone' => '9876543210',
            'subscription_status' => 'pending',
        ]);

        $adminRole = Role::create([
            'company_id' => $this->company->id,
            'name' => 'Admin',
            'permissions' => ['*'],
        ]);

        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'role_id' => $adminRole->id,
            'email_verified_at' => now(),
        ]);

        $this->plan = SubscriptionPlan::create([
            'name' => '6-Month Growth Plan',
            'type' => 'paid',
            'price' => 6000,
            'currency' => 'INR',
            'duration_months' => 6,
            'features' => ['Unlimited Leads', 'Custom Drips'],
            'is_active' => true,
        ]);
    }

    public function test_checkout_page_renders_properly(): void
    {
        $this->actingAs($this->user);

        $response = $this->get(route('subscription.checkout', $this->plan));

        $response->assertOk();
        $response->assertSee('Checkout Order');
        $response->assertSee('6-Month Growth Plan');
        $response->assertSee('6,000');
        $response->assertSee('Official Razorpay Checkout');
    }

    public function test_create_order_returns_error_when_keys_are_missing(): void
    {
        config(['services.razorpay.key' => null]);
        config(['services.razorpay.secret' => null]);

        $this->actingAs($this->user);

        $response = $this->postJson(route('subscription.create-order', $this->plan));

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
        $response->assertJsonFragment([
            'message' => 'Razorpay API credentials (RAZORPAY_KEY and RAZORPAY_SECRET) are not configured in .env. Please configure your Razorpay keys.'
        ]);
    }

    public function test_create_order_succeeds_when_razorpay_is_configured(): void
    {
        $mockRazorpay = Mockery::mock(RazorpayService::class);
        $mockRazorpay->shouldReceive('isConfigured')->andReturn(true);
        $mockRazorpay->shouldReceive('getKey')->andReturn('rzp_test_mockkey123');
        $mockRazorpay->shouldReceive('isTestMode')->andReturn(true);
        $mockRazorpay->shouldReceive('createOrder')->once()->andReturn([
            'id' => 'order_test_123456',
            'amount' => 600000,
            'currency' => 'INR',
            'receipt' => 'sub_receipt_1',
            'status' => 'created',
        ]);

        $this->app->instance(RazorpayService::class, $mockRazorpay);

        $this->actingAs($this->user);

        $response = $this->postJson(route('subscription.create-order', $this->plan));

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'order_id' => 'order_test_123456',
            'amount' => 600000,
            'currency' => 'INR',
            'key' => 'rzp_test_mockkey123',
            'is_test_mode' => true,
        ]);
    }

    public function test_purchase_fails_when_signature_is_invalid(): void
    {
        $mockRazorpay = Mockery::mock(RazorpayService::class);
        $mockRazorpay->shouldReceive('isConfigured')->andReturn(true);
        $mockRazorpay->shouldReceive('verifyPayment')->once()->andReturn(false);

        $this->app->instance(RazorpayService::class, $mockRazorpay);

        $this->actingAs($this->user);

        $response = $this->post(route('subscription.purchase', $this->plan), [
            'razorpay_payment_id' => 'pay_invalid_123',
            'razorpay_order_id' => 'order_invalid_456',
            'razorpay_signature' => 'bad_signature_789',
        ]);

        $response->assertRedirect(route('subscription.checkout', $this->plan));
        $response->assertSessionHas('error');

        $this->assertDatabaseMissing('subscriptions', [
            'payment_reference' => 'pay_invalid_123',
        ]);
    }

    public function test_purchase_successfully_activates_subscription(): void
    {
        $mockRazorpay = Mockery::mock(RazorpayService::class);
        $mockRazorpay->shouldReceive('isConfigured')->andReturn(true);
        $mockRazorpay->shouldReceive('verifyPayment')->once()->andReturn(true);
        $mockRazorpay->shouldReceive('getPayment')->once()->andReturn([
            'id' => 'pay_valid_999',
            'amount' => 600000,
            'currency' => 'INR',
            'status' => 'captured',
            'method' => 'upi',
            'email' => 'acme@realestate.com',
            'contact' => '9876543210',
            'order_id' => 'order_valid_888',
        ]);

        $this->app->instance(RazorpayService::class, $mockRazorpay);

        $this->actingAs($this->user);

        $response = $this->post(route('subscription.purchase', $this->plan), [
            'razorpay_payment_id' => 'pay_valid_999',
            'razorpay_order_id' => 'order_valid_888',
            'razorpay_signature' => 'valid_sig_123',
        ]);

        $response->assertRedirect(route('subscription.show'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('subscriptions', [
            'company_id' => $this->company->id,
            'subscription_plan_id' => $this->plan->id,
            'payment_reference' => 'pay_valid_999',
            'status' => 'active',
            'amount_paid' => 6000,
        ]);

        // Company subscription status is now active
        $this->company->refresh();
        $this->assertEquals('active', $this->company->subscription_status);
        $this->assertTrue($this->company->hasActiveSubscription());
    }

    public function test_webhook_activates_subscription_on_order_paid(): void
    {
        $payload = [
            'event' => 'order.paid',
            'payload' => [
                'payment' => [
                    'entity' => [
                        'id' => 'pay_webhook_777',
                        'order_id' => 'order_webhook_888',
                        'amount' => 600000,
                        'currency' => 'INR',
                        'method' => 'card',
                        'notes' => [
                            'company_id' => $this->company->id,
                            'plan_id' => $this->plan->id,
                        ],
                    ],
                ],
            ],
        ];

        $mockRazorpay = Mockery::mock(RazorpayService::class);
        $mockRazorpay->shouldReceive('verifyWebhookSignature')->andReturn(true);
        $this->app->instance(RazorpayService::class, $mockRazorpay);

        $response = $this->postJson(route('webhook.razorpay'), $payload, [
            'X-Razorpay-Signature' => 'dummy_signature',
        ]);

        $response->assertOk();
        $response->assertJson([
            'status' => 'success',
            'message' => 'Subscription activated',
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'company_id' => $this->company->id,
            'payment_reference' => 'pay_webhook_777',
            'status' => 'active',
        ]);

        $this->company->refresh();
        $this->assertEquals('active', $this->company->subscription_status);
    }
}
