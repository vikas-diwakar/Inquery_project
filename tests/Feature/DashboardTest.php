<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $company = Company::create([
            'name' => 'Dashboard Test Co',
            'email' => 'dashboard@co.com',
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addMonth(),
        ]);

        $user = User::factory()->create([
            'company_id' => $company->id,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user);

        $response = $this->get(route('dashboard'));

        $response->assertOk();
    }

    public function test_dashboard_handles_invalid_selected_project_without_404(): void
    {
        $company = Company::create([
            'name' => 'Dashboard Test Co 2',
            'email' => 'dashboard2@co.com',
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addMonth(),
        ]);

        $user = User::factory()->create([
            'company_id' => $company->id,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user);
        
        // Put non-existent project ID in session
        session(['selected_project_id' => 99999]);

        $response = $this->get(route('dashboard'));

        // Should clear invalid project from session and redirect or show ok
        $response->assertRedirect(route('dashboard'));
        $this->assertFalse(session()->has('selected_project_id'));
    }
}
