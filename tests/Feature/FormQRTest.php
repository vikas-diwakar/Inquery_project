<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Project;
use App\Models\ProjectUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormQRTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_can_toggle_stacking_chart_on_and_off_when_generating_inquiry_qr(): void
    {
        $company = Company::create([
            'name' => 'QR Co',
            'email' => 'qr@co.com',
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addYear(),
        ]);
        $user = User::factory()->create([
            'company_id' => $company->id,
            'email_verified_at' => now(),
        ]);
        $project = Project::create([
            'company_id' => $company->id,
            'name' => 'Skyline Tower',
            'show_stacking_chart' => true,
        ]);
        $user->projects()->attach($project->id);

        $this->actingAs($user);

        // Toggle OFF (uncheck show_stacking_chart)
        $response = $this->withSession(['selected_project_id' => $project->id])
            ->post(route('forms-qr.generate-inquiry-qr'), []);

        $response->assertRedirect();
        $this->assertFalse($project->fresh()->show_stacking_chart);

        // Toggle ON (check show_stacking_chart)
        $response2 = $this->withSession(['selected_project_id' => $project->id])
            ->post(route('forms-qr.generate-inquiry-qr'), [
                'show_stacking_chart' => 1,
            ]);

        $response2->assertRedirect();
        $this->assertTrue($project->fresh()->show_stacking_chart);
    }

    public function test_public_inquiry_form_hides_stacking_chart_when_turned_off(): void
    {
        $company = Company::create([
            'name' => 'QR Co 2',
            'email' => 'qr2@co.com',
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addYear(),
        ]);
        $project = Project::create([
            'company_id' => $company->id,
            'name' => 'Grand Residency',
            'show_stacking_chart' => false, // Turned OFF
        ]);

        ProjectUnit::create([
            'company_id' => $company->id,
            'project_id' => $project->id,
            'tower_name' => 'Tower A',
            'unit_number' => 'A-101',
            'floor_number' => 1,
            'unit_type' => '2BHK',
            'status' => 'available',
        ]);

        $response = $this->get(route('public.inquiry.form', $project));

        $response->assertOk();
        $response->assertDontSee('Live Unit Availability Map');

        // Turn ON
        $project->update(['show_stacking_chart' => true]);

        $response2 = $this->get(route('public.inquiry.form', $project));
        $response2->assertOk();
        $response2->assertSee('Live Unit Availability Map');
    }
}
