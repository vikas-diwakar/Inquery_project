<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicMarketingPagesTest extends TestCase
{
    use RefreshDatabase;
    public function test_home_page_can_be_rendered(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('PropDrip');
        $response->assertSee('Home');
        $response->assertSee('About');
        $response->assertSee('Contact Us');
        $response->assertSee(route('login'));
    }

    public function test_about_page_can_be_rendered(): void
    {
        $response = $this->get(route('about'));
        $response->assertStatus(200);
        $response->assertSee('About PropDrip');
        $response->assertSee('Log In');
    }

    public function test_contact_page_can_be_rendered_and_submitted(): void
    {
        $response = $this->get(route('contact'));
        $response->assertStatus(200);
        $response->assertSee('Contact Information');

        $postResponse = $this->post(route('contact.store'), [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'subject' => 'Real Estate Inquiry Automation',
            'message' => 'Hello, I want to learn more about setting up PropDrip for my real estate project.',
        ]);

        $postResponse->assertRedirect();
        $postResponse->assertSessionHas('success');
    }

    public function test_login_and_register_pages_contain_public_navigation_menu(): void
    {
        $loginResponse = $this->get(route('login'));
        $loginResponse->assertStatus(200);
        $loginResponse->assertSee('Home');
        $loginResponse->assertSee('About');
        $loginResponse->assertSee('Contact Us');

        $registerResponse = $this->get(route('company.register'));
        $registerResponse->assertStatus(200);
        $registerResponse->assertSee('Home');
        $registerResponse->assertSee('About');
        $registerResponse->assertSee('Contact Us');
    }

    public function test_nonexistent_workspace_subdomain_links_to_root_domain(): void
    {
        $response = $this->withServerVariables([
            'HTTP_HOST' => 'vikas.localhost:8000',
            'SERVER_NAME' => 'vikas.localhost',
            'SERVER_PORT' => '8000',
        ])->get('http://vikas.localhost:8000/');

        $response->assertStatus(404);
        $response->assertSee('No company workspace found for');
        $response->assertSee('vikas');

        // Verify "Go to PropDrip Home" links to the root localhost:8000 rather than vikas.localhost:8000
        $response->assertSee('href="http://localhost:8000/"', false);
        // Verify "Find Your Organization" links to root login
        $response->assertSee('href="http://localhost:8000/login"', false);
        // Verify "Register Company" links to root register
        $response->assertSee('href="http://localhost:8000/register"', false);
    }

    public function test_tenant_subdomain_login_page_links_main_portal_to_root_domain_and_hides_registration(): void
    {
        $company = \App\Models\Company::create([
            'name' => 'Vikas Builders',
            'email' => 'vikas@test.com',
            'subdomain' => 'vikas',
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addYear(),
        ]);

        $response = $this->withServerVariables([
            'HTTP_HOST' => 'vikas.localhost:8000',
            'SERVER_NAME' => 'vikas.localhost',
            'SERVER_PORT' => '8000',
        ])->get('http://vikas.localhost:8000/login');

        $response->assertStatus(200);
        $response->assertSee('Go to Main Portal');
        // Ensure "Go to Main Portal" links to root domain, not the subdomain!
        $response->assertSee('href="http://localhost:8000/"', false);
        // Ensure "Start Free Trial" or "Register company" are hidden on the tenant subdomain
        $response->assertDontSee('Start Free Trial');
        $response->assertDontSee('Register company');
    }

    public function test_tenant_subdomain_root_redirects_to_login_instead_of_marketing_page(): void
    {
        $company = \App\Models\Company::create([
            'name' => 'Vikas Builders',
            'email' => 'vikas2@test.com',
            'subdomain' => 'vikas-portal',
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addYear(),
        ]);

        $response = $this->withServerVariables([
            'HTTP_HOST' => 'vikas-portal.localhost:8000',
            'SERVER_NAME' => 'vikas-portal.localhost',
            'SERVER_PORT' => '8000',
        ])->get('http://vikas-portal.localhost:8000/');

        $response->assertRedirect('http://vikas-portal.localhost:8000/login');
    }

    public function test_tenant_subdomain_register_redirects_to_root_domain_register(): void
    {
        $company = \App\Models\Company::create([
            'name' => 'Vikas Builders',
            'email' => 'vikas3@test.com',
            'subdomain' => 'vikas-portal-reg',
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addYear(),
        ]);

        $response = $this->withServerVariables([
            'HTTP_HOST' => 'vikas-portal-reg.localhost:8000',
            'SERVER_NAME' => 'vikas-portal-reg.localhost',
            'SERVER_PORT' => '8000',
        ])->get('http://vikas-portal-reg.localhost:8000/register');

        $response->assertRedirect('http://localhost:8000/register');
    }

    public function test_tenant_user_visiting_root_domain_does_not_see_dashboard_button(): void
    {
        $company = \App\Models\Company::create([
            'name' => 'Apex Realty',
            'email' => 'apex@test.com',
            'subdomain' => 'apex',
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addYear(),
        ]);

        $user = \App\Models\User::factory()->create([
            'company_id' => $company->id,
            'email' => 'admin@apex.test',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        // If a request hits the root domain while authenticated as a tenant user
        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        // The navbar must show guest links, NEVER "Dashboard →"
        $response->assertDontSee('Dashboard →');
        $response->assertSee('Log In');
        $response->assertSee('Start Free Trial');
    }

    public function test_root_domain_login_hands_off_and_cleans_root_session(): void
    {
        $company = \App\Models\Company::create([
            'name' => 'Metro Group',
            'email' => 'metro@test.com',
            'subdomain' => 'metro',
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addYear(),
        ]);

        $user = \App\Models\User::factory()->create([
            'company_id' => $company->id,
            'email' => 'agent@metro.test',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'agent@metro.test',
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $redirectUrl = $response->headers->get('Location');
        $this->assertStringStartsWith($company->workspace_url . '/workspace/sso-handoff', $redirectUrl);

        // Root domain session must be logged out
        $this->assertGuest();

        // Visiting root domain main portal afterwards must display guest navbar
        $homeResponse = $this->get('/');
        $homeResponse->assertDontSee('Dashboard →');
        $homeResponse->assertSee('Log In');
        $homeResponse->assertSee('Start Free Trial');
    }

    public function test_tenant_user_accessing_dashboard_on_root_domain_does_not_auto_login(): void
    {
        $company = \App\Models\Company::create([
            'name' => 'Tower Real Estate',
            'email' => 'tower@test.com',
            'subdomain' => 'tower',
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addYear(),
        ]);

        $user = \App\Models\User::factory()->create([
            'company_id' => $company->id,
            'email' => 'admin@tower.test',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);

        // Attempting to access dashboard on root domain must NOT generate signed SSO handoff
        $response = $this->actingAs($user)->get('/dashboard');

        // It must redirect to login or workspace login, never a signed SSO URL
        $response->assertRedirect();
        $redirectUrl = $response->headers->get('Location');
        $this->assertStringNotContainsString('/workspace/sso-handoff', $redirectUrl);
    }
}
