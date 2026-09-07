<?php

namespace Tests\Feature;

use App\Models\Brochure;
use App\Models\Company;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BrochureDownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_brochure_downloads_with_company_and_project_name_filename(): void
    {
        Storage::fake('public');

        $company = Company::create([
            'name' => 'PropDrip Realty',
            'email' => 'contact@propdrip.in',
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addYear(),
        ]);

        $project = Project::create([
            'company_id' => $company->id,
            'name' => 'Green Valley Phase 1',
        ]);

        $fakeFilePath = 'brochures/sample-brochure.pdf';
        Storage::disk('public')->put($fakeFilePath, 'PDF content mock');

        $brochure = Brochure::create([
            'company_id' => $company->id,
            'project_id' => $project->id,
            'file_path' => $fakeFilePath,
            'file_name' => 'brochure.pdf',
        ]);

        $response = $this->get(route('public.brochure.download', $brochure));

        $response->assertOk();
        $response->assertHeader('content-disposition', 'attachment; filename=propdrip-realty-green-valley-phase-1-brochure.pdf');
    }

    public function test_brochure_downloads_retaining_custom_file_title(): void
    {
        Storage::fake('public');

        $company = Company::create([
            'name' => 'Apex Towers',
            'email' => 'info@apextowers.com',
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addYear(),
        ]);

        $project = Project::create([
            'company_id' => $company->id,
            'name' => 'Skyline Heights',
        ]);

        $fakeFilePath = 'brochures/floorplan.pdf';
        Storage::disk('public')->put($fakeFilePath, 'Floorplan mock content');

        $brochure = Brochure::create([
            'company_id' => $company->id,
            'project_id' => $project->id,
            'file_path' => $fakeFilePath,
            'file_name' => 'Floorplan_TowerA.pdf',
        ]);

        $response = $this->get(route('public.brochure.download', $brochure));

        $response->assertOk();
        $response->assertHeader('content-disposition', 'attachment; filename=apex-towers-skyline-heights-floorplan-towera.pdf');
    }
}
