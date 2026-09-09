<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProjectAssignmentAndSalesExecutivePermissionsTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private Role $adminRole;
    private Role $managerRole;
    private Role $salesExecutiveRole;
    private User $adminUser;
    private Project $project1;
    private Project $project2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create([
            'name' => 'Acme Real Estate',
            'email' => 'acme@realestate.com',
            'slug' => 'acme-real-estate',
        ]);

        $this->adminRole = Role::create([
            'company_id' => $this->company->id,
            'name' => 'Admin',
            'permissions' => ['*'],
        ]);

        $this->managerRole = Role::create([
            'company_id' => $this->company->id,
            'name' => 'Manager',
            'permissions' => [
                'projects.view',
                'projects.create',
                'projects.edit',
                'inquiries.view',
                'inquiries.edit',
            ],
        ]);

        $this->salesExecutiveRole = Role::create([
            'company_id' => $this->company->id,
            'name' => 'Sales Executive',
            'permissions' => [
                'inquiries.view',
                'inquiries.edit',
            ],
        ]);

        $this->adminUser = User::create([
            'company_id' => $this->company->id,
            'role_id' => $this->adminRole->id,
            'name' => 'Admin User',
            'email' => 'admin@acme.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);

        $this->project1 = Project::create([
            'company_id' => $this->company->id,
            'name' => 'Sunrise Heights',
            'status' => 'ongoing',
        ]);

        $this->project2 = Project::create([
            'company_id' => $this->company->id,
            'name' => 'Ocean Towers',
            'status' => 'planning',
        ]);
    }

    public function test_creating_non_admin_user_without_project_fails_validation(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('users.store'), [
                'name' => 'John Sales',
                'email' => 'john.sales@acme.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role_id' => $this->salesExecutiveRole->id,
                'project_ids' => [], // Empty project assignment
            ]);

        $response->assertSessionHasErrors(['project_ids']);
        $this->assertDatabaseMissing('users', ['email' => 'john.sales@acme.com']);
    }

    public function test_creating_non_admin_user_with_assigned_project_succeeds(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->post(route('users.store'), [
                'name' => 'John Sales',
                'email' => 'john.sales@acme.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role_id' => $this->salesExecutiveRole->id,
                'project_ids' => [$this->project1->id],
            ]);

        $response->assertRedirect(route('users.index'));
        
        $createdUser = User::where('email', 'john.sales@acme.com')->first();
        $this->assertNotNull($createdUser);
        $this->assertTrue($createdUser->projects->contains($this->project1->id));
    }

    public function test_active_session_project_is_preselected_on_create_user_page(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->withSession(['selected_project_id' => $this->project2->id])
            ->get(route('users.create'));

        $response->assertStatus(200);
        $response->assertSee('project_' . $this->project2->id);
        $response->assertSee('checked');
    }

    public function test_sales_executive_cannot_access_edit_project_page(): void
    {
        $salesUser = User::create([
            'company_id' => $this->company->id,
            'role_id' => $this->salesExecutiveRole->id,
            'name' => 'Jane Executive',
            'email' => 'jane@acme.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);
        $salesUser->projects()->attach($this->project1->id);

        $response = $this->actingAs($salesUser)
            ->get(route('projects.edit', $this->project1));

        $response->assertStatus(403);
    }

    public function test_sales_executive_cannot_update_project(): void
    {
        $salesUser = User::create([
            'company_id' => $this->company->id,
            'role_id' => $this->salesExecutiveRole->id,
            'name' => 'Jane Executive',
            'email' => 'jane@acme.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);
        $salesUser->projects()->attach($this->project1->id);

        $response = $this->actingAs($salesUser)
            ->put(route('projects.update', $this->project1), [
                'name' => 'Attempted New Name',
                'status' => 'ongoing',
            ]);

        $response->assertStatus(403);
        $this->assertEquals('Sunrise Heights', $this->project1->fresh()->name);
    }

    public function test_admin_can_edit_and_update_project(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->get(route('projects.edit', $this->project1));
        $response->assertStatus(200);

        $response = $this->actingAs($this->adminUser)
            ->put(route('projects.update', $this->project1), [
                'name' => 'Updated Sunrise Heights',
                'status' => 'ongoing',
            ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertEquals('Updated Sunrise Heights', $this->project1->fresh()->name);
    }

    public function test_non_admin_users_cannot_delete_project(): void
    {
        $salesUser = User::create([
            'company_id' => $this->company->id,
            'role_id' => $this->salesExecutiveRole->id,
            'name' => 'Jane Executive',
            'email' => 'jane.executive@acme.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($salesUser)
            ->delete(route('projects.destroy', $this->project1));

        $response->assertStatus(403);
        $this->assertDatabaseHas('projects', [
            'id' => $this->project1->id,
            'deleted_at' => null,
        ]);
    }

    public function test_admin_can_soft_delete_project_without_flushing_database_record(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->delete(route('projects.destroy', $this->project1));

        $response->assertRedirect(route('projects.index'));
        $response->assertSessionHas('success');

        // Verify row remains in database with soft delete timestamp (not flushed)
        $this->assertSoftDeleted('projects', [
            'id' => $this->project1->id,
        ]);
    }
}
