<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Project;
use App\Models\ProjectUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectUnitTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private User $user;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create([
            'name' => 'Acme Real Estate',
            'email' => 'acme@example.com',
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addYear(),
        ]);

        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'email_verified_at' => now(),
        ]);

        $this->project = Project::create([
            'company_id' => $this->company->id,
            'name' => 'Grand Residency',
            'status' => 'ongoing',
        ]);

        $this->user->projects()->attach($this->project->id);
    }

    public function test_user_can_view_project_stacking_units_page(): void
    {
        $this->actingAs($this->user);

        ProjectUnit::create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'tower_name' => 'Tower A',
            'unit_number' => 'A-101',
            'floor_number' => 1,
            'unit_type' => '2 BHK',
            'status' => 'available',
            'price' => 5000000,
        ]);

        $response = $this->get(route('projects.units.index', $this->project));

        $response->assertStatus(200);
        $response->assertSee('A-101');
        $response->assertSee('Tower A');
        $response->assertSee('Edit Unit');
        $response->assertSee('Delete Unit');
    }

    public function test_user_can_update_a_stacking_unit(): void
    {
        $this->actingAs($this->user);

        $unit = ProjectUnit::create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'tower_name' => 'Tower B',
            'unit_number' => 'B-501',
            'floor_number' => 5,
            'unit_type' => '2 BHK',
            'status' => 'available',
            'price' => 4500000,
            'notes' => 'Original note',
        ]);

        $response = $this->put(route('units.update', $unit), [
            'tower_name' => 'Tower B',
            'unit_number' => 'B-501-MODIFIED',
            'floor_number' => 5,
            'unit_type' => '3 BHK Luxury',
            'status' => 'on_hold',
            'price' => 6200000,
            'notes' => 'Reserved for VIP client',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $unit->refresh();
        $this->assertEquals('B-501-MODIFIED', $unit->unit_number);
        $this->assertEquals('3 BHK Luxury', $unit->unit_type);
        $this->assertEquals('on_hold', $unit->status);
        $this->assertEquals('6200000.00', $unit->price);
        $this->assertEquals('Reserved for VIP client', $unit->notes);
    }

    public function test_user_can_delete_a_stacking_unit(): void
    {
        $this->actingAs($this->user);

        $unit = ProjectUnit::create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'tower_name' => 'Tower B',
            'unit_number' => 'B-502',
            'floor_number' => 5,
            'unit_type' => '2 BHK',
            'status' => 'available',
        ]);

        $this->assertDatabaseHas('project_units', [
            'id' => $unit->id,
            'unit_number' => 'B-502',
        ]);

        $response = $this->delete(route('units.destroy', $unit));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('project_units', [
            'id' => $unit->id,
        ]);
    }

    public function test_user_cannot_update_or_delete_unit_from_another_company(): void
    {
        $otherCompany = Company::create([
            'name' => 'Competitor Co',
            'email' => 'competitor@example.com',
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addYear(),
        ]);

        $otherProject = Project::create([
            'company_id' => $otherCompany->id,
            'name' => 'Competitor Heights',
        ]);

        $otherUnit = ProjectUnit::create([
            'company_id' => $otherCompany->id,
            'project_id' => $otherProject->id,
            'tower_name' => 'Tower X',
            'unit_number' => 'X-101',
            'floor_number' => 1,
            'status' => 'available',
        ]);

        $this->actingAs($this->user);

        // Attempting to update another company's unit should fail with 404 (due to HasTenant global scope)
        $updateResponse = $this->put(route('units.update', $otherUnit), [
            'tower_name' => 'Hacked Tower',
            'unit_number' => 'HACKED',
            'floor_number' => 1,
            'status' => 'sold',
        ]);

        $updateResponse->assertStatus(404);

        // Attempting to delete another company's unit should fail with 404
        $deleteResponse = $this->delete(route('units.destroy', $otherUnit));

        $deleteResponse->assertStatus(404);

        $this->assertDatabaseHas('project_units', [
            'id' => $otherUnit->id,
            'unit_number' => 'X-101',
        ]);
    }
}
