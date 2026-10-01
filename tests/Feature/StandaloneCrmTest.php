<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StandaloneCrmTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private Role $adminRole;
    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create([
            'id' => 1,
            'name' => 'Prime Realty Group',
            'email' => 'admin@primerealty.test',
            'phone' => '+1 555 123 4567',
            'address' => '100 Main St',
            'is_active' => true,
        ]);

        $this->adminRole = Role::create([
            'company_id' => $this->company->id,
            'name' => 'Admin',
            'permissions' => ['*'],
        ]);

        $this->adminUser = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@primerealty.test',
            'password' => bcrypt('password'),
            'company_id' => $this->company->id,
            'role_id' => $this->adminRole->id,
            'email_verified_at' => now(),
        ]);
    }

    public function test_root_redirects_guest_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect(route('login'));
    }

    public function test_root_redirects_authenticated_user_to_dashboard(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/');
        $response->assertRedirect(route('dashboard'));
    }

    public function test_admin_can_access_and_update_company_settings(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('settings.company'));
        $response->assertStatus(200);
        $response->assertSee('Prime Realty Group');

        $updateResponse = $this->actingAs($this->adminUser)->put(route('settings.company.update'), [
            'name' => 'Summit Real Estate Holdings',
            'email' => 'contact@summit.test',
            'phone' => '+1 555 999 8888',
            'address' => '500 Executive Tower',
            'lead_allocation_method' => 'round_robin',
        ]);

        $updateResponse->assertRedirect(route('settings.company'));
        $this->assertDatabaseHas('companies', [
            'name' => 'Summit Real Estate Holdings',
            'email' => 'contact@summit.test',
            'lead_allocation_method' => 'round_robin',
        ]);
    }

    public function test_admin_can_create_user_with_automatic_company_association(): void
    {
        $salesRole = Role::create([
            'company_id' => $this->company->id,
            'name' => 'Sales Executive',
            'permissions' => ['inquiries.view'],
        ]);

        $project = Project::create([
            'company_id' => $this->company->id,
            'name' => 'Summit Towers',
            'location' => 'Downtown',
            'status' => 'ongoing',
        ]);

        $response = $this->actingAs($this->adminUser)->post(route('users.store'), [
            'name' => 'Jane Agent',
            'email' => 'jane@summit.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => $salesRole->id,
            'project_ids' => [$project->id],
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'Jane Agent',
            'email' => 'jane@summit.test',
            'company_id' => $this->company->id,
            'role_id' => $salesRole->id,
        ]);
    }

    public function test_public_inquiry_form_accessible_on_main_domain(): void
    {
        $project = Project::create([
            'company_id' => $this->company->id,
            'name' => 'Oceanview Residences',
            'location' => 'Coastline',
            'status' => 'ongoing',
        ]);

        $response = $this->get(route('public.inquiry.form', $project->getEncryptedKey()));
        $response->assertStatus(200);
        $response->assertSee('Oceanview Residences');
    }

    public function test_admin_can_upload_update_and_remove_company_logo(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        // 1. Upload new logo
        $logoFile = \Illuminate\Http\UploadedFile::fake()->image('agency-logo.png', 400, 100);

        $response = $this->actingAs($this->adminUser)->put(route('settings.company.update'), [
            'name' => 'Apex Real Estate',
            'email' => 'contact@apex.test',
            'logo' => $logoFile,
        ]);

        $response->assertRedirect(route('settings.company'));
        $this->company->refresh();
        $this->assertNotNull($this->company->logo);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($this->company->logo);

        // 2. Remove logo
        $removeResponse = $this->actingAs($this->adminUser)->put(route('settings.company.update'), [
            'name' => 'Apex Real Estate',
            'email' => 'contact@apex.test',
            'remove_logo' => '1',
        ]);

        $removeResponse->assertRedirect(route('settings.company'));
        $this->company->refresh();
        $this->assertNull($this->company->logo);
    }
}