<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Inquiry;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IntegrationMenuAndAccessTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private Role $adminRole;
    private Role $salesRole;
    private User $adminUser;
    private User $salesUser;
    private Project $project1;
    private Project $project2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create([
            'name' => 'Skyline Developers',
            'email' => 'admin@skylinedev.test',
            'is_active' => true,
        ]);

        $this->adminRole = Role::create([
            'company_id' => $this->company->id,
            'name' => 'Admin',
            'permissions' => ['*'],
        ]);

        $this->salesRole = Role::create([
            'company_id' => $this->company->id,
            'name' => 'Sales Executive',
            'permissions' => ['inquiries.view', 'inquiries.create', 'inquiries.edit'],
        ]);

        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@skylinedev.test',
            'password' => bcrypt('password'),
            'company_id' => $this->company->id,
            'role_id' => $this->adminRole->id,
            'email_verified_at' => now(),
        ]);

        $this->salesUser = User::create([
            'name' => 'Sales Executive',
            'email' => 'sales@skylinedev.test',
            'password' => bcrypt('password'),
            'company_id' => $this->company->id,
            'role_id' => $this->salesRole->id,
            'email_verified_at' => now(),
        ]);

        $this->project1 = Project::create([
            'company_id' => $this->company->id,
            'name' => 'Skyline Heights',
            'location' => 'Downtown',
            'status' => 'ongoing',
            'lead_token' => 'token_skyline_heights_12345',
        ]);

        $this->project2 = Project::create([
            'company_id' => $this->company->id,
            'name' => 'Green Valley Villas',
            'location' => 'Suburbs',
            'status' => 'ongoing',
            'lead_token' => 'token_green_valley_67890',
        ]);

        // Assign sales user to project 1
        $this->salesUser->projects()->attach($this->project1->id);
    }

    public function test_admin_can_access_social_media_page_and_see_project_details(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->withSession(['selected_project_id' => $this->project1->id])
            ->get(route('social-media.index'));

        $response->assertStatus(200);
        $response->assertSee('Social Media Leads');
        $response->assertSee('Skyline Heights');
        $response->assertSee($this->project1->lead_token);
        $response->assertSee(route('api.leads.webhook', ['token' => $this->project1->lead_token]));
    }

    public function test_admin_can_switch_to_another_project_on_social_media_page(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('social-media.project', $this->project2));

        $response->assertRedirect(route('social-media.index'));
        $this->assertEquals($this->project2->id, session('selected_project_id'));

        $followUp = $this->actingAs($this->adminUser)
            ->get(route('social-media.index'));

        $followUp->assertStatus(200);
        $followUp->assertSee('Green Valley Villas');
        $followUp->assertSee($this->project2->lead_token);
    }

    public function test_admin_can_switch_project_via_query_parameter(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('social-media.index', ['project_id' => $this->project2->id]));

        $response->assertStatus(200);
        $response->assertSee('Green Valley Villas');
        $response->assertSee($this->project2->lead_token);
        $this->assertEquals($this->project2->id, session('selected_project_id'));
    }

    public function test_admin_can_regenerate_social_media_token(): void
    {
        $oldToken = $this->project1->lead_token;

        $response = $this->actingAs($this->adminUser)
            ->post(route('projects.regenerate-token', $this->project1));

        $response->assertRedirect(route('social-media.index'));
        $response->assertSessionHas('success');

        $this->project1->refresh();
        $this->assertNotEmpty($this->project1->lead_token);
        $this->assertNotEquals($oldToken, $this->project1->lead_token);
    }

    public function test_non_admin_cannot_access_social_media_page(): void
    {
        $response = $this->actingAs($this->salesUser)
            ->withSession(['selected_project_id' => $this->project1->id])
            ->get(route('social-media.index'));

        // Middleware CheckRole redirects to dashboard with error message
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_non_admin_cannot_access_project_social_media_switch_route(): void
    {
        $response = $this->actingAs($this->salesUser)
            ->get(route('social-media.project', $this->project1));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_non_admin_cannot_regenerate_lead_token(): void
    {
        $oldToken = $this->project1->lead_token;

        $response = $this->actingAs($this->salesUser)
            ->post(route('projects.regenerate-token', $this->project1));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');

        $this->project1->refresh();
        $this->assertEquals($oldToken, $this->project1->lead_token);
    }

    public function test_admin_sees_social_media_in_navigation_only_within_project_scope(): void
    {
        // When in project scope: Admin sees Social Media menu
        $response = $this->actingAs($this->adminUser)
            ->withSession(['selected_project_id' => $this->project1->id])
            ->get(route('inquiries.index'));

        $response->assertStatus(200);
        $response->assertSee(route('social-media.index'));
        $response->assertSee('Social Media');

        // When in global scope (e.g. Projects listing): Social Media should NOT appear in navbar or on project cards
        $responseProjects = $this->actingAs($this->adminUser)
            ->get(route('projects.index'));

        $responseProjects->assertStatus(200);
        $responseProjects->assertDontSee(route('social-media.index'));
        $responseProjects->assertDontSee(route('social-media.project', $this->project1));
    }

    public function test_non_admin_does_not_see_social_media_in_navigation(): void
    {
        $response = $this->actingAs($this->salesUser)
            ->withSession(['selected_project_id' => $this->project1->id])
            ->get(route('inquiries.index'));

        $response->assertStatus(200);
        $response->assertDontSee(route('social-media.index'));
    }

    public function test_public_webhook_works_for_each_project_without_auth(): void
    {
        // Project 1 submission
        $response1 = $this->postJson(route('api.leads.webhook', ['token' => $this->project1->lead_token]), [
            'name' => 'Alice Skyline',
            'phone' => '9876543210',
            'email' => 'alice@test.com',
            'source' => 'Facebook Ads',
        ]);

        $response1->assertStatus(201);
        $response1->assertJson(['success' => true]);

        $inquiry1 = Inquiry::where('phone', '9876543210')->first();
        $this->assertNotNull($inquiry1);
        $this->assertEquals($this->project1->id, $inquiry1->project_id);

        // Project 2 submission
        $response2 = $this->postJson(route('api.leads.webhook', ['token' => $this->project2->lead_token]), [
            'name' => 'Bob Valley',
            'phone' => '9876543211',
            'email' => 'bob@test.com',
            'source' => 'Google Form',
        ]);

        $response2->assertStatus(201);
        $response2->assertJson(['success' => true]);

        $inquiry2 = Inquiry::where('phone', '9876543211')->first();
        $this->assertNotNull($inquiry2);
        $this->assertEquals($this->project2->id, $inquiry2->project_id);
    }
}
