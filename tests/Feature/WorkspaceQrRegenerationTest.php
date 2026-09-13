<?php

namespace Tests\Feature;

use App\Models\Brochure;
use App\Models\Company;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WorkspaceQrRegenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_workspace_change_regenerates_form_and_brochure_qr_codes(): void
    {
        Storage::fake('public');

        $company = Company::create([
            'name' => 'Acme Real Estate',
            'subdomain' => 'acme-test',
            'email' => 'acme@test.com',
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addYear(),
        ]);

        $role = \App\Models\Role::create([
            'company_id' => $company->id,
            'name' => 'Admin',
            'permissions' => ['*'],
        ]);

        $user = User::factory()->create([
            'company_id' => $company->id,
            'role_id' => $role->id,
            'email_verified_at' => now(),
        ]);

        $project = Project::create([
            'company_id' => $company->id,
            'name' => 'Acme Heights',
        ]);

        // Upload fake brochure file
        $brochureFile = 'brochures/test.pdf';
        Storage::disk('public')->put($brochureFile, 'fake-content');

        $brochure = Brochure::create([
            'company_id' => $company->id,
            'project_id' => $project->id,
            'file_path' => $brochureFile,
            'file_name' => 'test.pdf',
        ]);

        // Generate initial QR codes
        $project->generateQrCode();
        $brochure->generateQrCode();

        // Verify initial URLs and SVG exist
        $initialInquirySvg = Storage::disk('public')->get($project->inquiry_qr_code);
        $initialBrochureSvg = Storage::disk('public')->get('qrcodes/brochure_' . $brochure->id . '.svg');

        $this->assertNotNull($initialInquirySvg);
        $this->assertNotNull($initialBrochureSvg);
        $this->assertStringContainsString('acme-test', $project->getInquiryFormUrl());
        $this->assertStringContainsString('acme-test', $brochure->qr_code);
        $this->assertStringContainsString('acme-test', $brochure->getDownloadUrl());

        // Now update workspace subdomain from 'acme-test' to 'summit-test'
        $response = $this->actingAs($user)->put(route('settings.domain.update'), [
            'name' => 'Summit Real Estate',
            'subdomain' => 'summit-test',
        ]);

        $response->assertRedirect();

        $company->refresh();
        $project->refresh();
        $brochure->refresh();

        // 1. Verify company subdomain and previous subdomains
        $this->assertEquals('summit-test', $company->subdomain);
        $this->assertEquals('Summit Real Estate', $company->name);
        $this->assertIsArray($company->previous_subdomains);
        $this->assertContains('acme-test', $company->previous_subdomains);

        // 2. Verify project inquiry QR code URL and SVG were regenerated for new subdomain 'summit-test'
        $this->assertStringContainsString('summit-test', $project->getInquiryFormUrl());
        $this->assertStringNotContainsString('acme-test', $project->getInquiryFormUrl());

        $updatedInquirySvg = Storage::disk('public')->get($project->inquiry_qr_code);
        $this->assertNotNull($updatedInquirySvg);
        $this->assertNotEquals($initialInquirySvg, $updatedInquirySvg);

        // 3. Verify brochure QR code URL and SVG were regenerated with new subdomain 'summit-test'
        $this->assertStringContainsString('summit-test', $brochure->qr_code);
        $this->assertStringNotContainsString('acme-test', $brochure->qr_code);
        $this->assertStringContainsString('summit-test', $brochure->getDownloadUrl());

        $updatedBrochureSvg = Storage::disk('public')->get('qrcodes/brochure_' . $brochure->id . '.svg');
        $this->assertNotNull($updatedBrochureSvg);
        $this->assertNotEquals($initialBrochureSvg, $updatedBrochureSvg);
    }

    public function test_old_workspace_subdomain_301_redirects_to_new_workspace_url(): void
    {
        $company = Company::create([
            'name' => 'Summit Real Estate',
            'subdomain' => 'summit-live',
            'previous_subdomains' => ['acme-old'],
            'email' => 'summit@test.com',
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addYear(),
        ]);

        $project = Project::create([
            'company_id' => $company->id,
            'name' => 'Summit Plaza',
        ]);

        // Access inquiry route via old subdomain 'acme-old.localhost:8000'
        $response = $this->withServerVariables([
            'HTTP_HOST' => 'acme-old.localhost:8000',
            'SERVER_NAME' => 'acme-old.localhost',
            'SERVER_PORT' => '8000',
        ])->get("http://acme-old.localhost:8000/inquiry/{$project->id}");

        // Must 301 permanently redirect to new subdomain
        $response->assertStatus(301);
        $response->assertRedirect("http://summit-live.localhost:8000/inquiry/{$project->id}");
    }
}
