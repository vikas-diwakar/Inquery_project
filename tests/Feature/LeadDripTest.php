<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Inquiry;
use App\Models\InquiryDripLog;
use App\Models\LeadDripStep;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadDripTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;
    private User $user;
    private Project $project;
    private LeadDripStep $step;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create([
            'name' => 'Prime Realty Group',
            'email' => 'prime@realty.com',
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addYear(),
            'whatsapp_auto_send' => true,
        ]);

        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'email_verified_at' => now(),
        ]);

        $this->project = Project::create([
            'company_id' => $this->company->id,
            'name' => 'Skyline Heights',
            'status' => 'ongoing',
        ]);

        $this->user->projects()->attach($this->project->id);

        $this->step = LeadDripStep::create([
            'company_id' => $this->company->id,
            'day_offset' => 1,
            'step_title' => 'Day 1: Welcome Brochure',
            'channel' => 'whatsapp',
            'message_template' => 'Hello {customer_name}, welcome to {project_name}!',
            'is_active' => true,
        ]);
    }

    public function test_user_can_view_drip_settings_with_selected_users_dispatch(): void
    {
        $this->actingAs($this->user);

        $response = $this->withSession(['selected_project_id' => $this->project->id])
            ->get(route('settings.drip'));

        $response->assertStatus(200);
        $response->assertSee('Dispatch Selected Users Drips');
        $response->assertSee('selectUsersDripModal');
        $response->assertSee('Automated Drip Activity Logs');
    }

    public function test_user_can_dispatch_specifically_selected_user_drips(): void
    {
        $this->actingAs($this->user);

        $inquiry1 = Inquiry::create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'customer_name' => 'Amit Verma',
            'phone' => '9876543210',
            'email' => 'amit@example.com',
            'status' => 'new',
        ]);

        $inquiry2 = Inquiry::create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'customer_name' => 'Priya Sharma',
            'phone' => '9876543211',
            'email' => 'priya@example.com',
            'status' => 'new',
        ]);

        $inquiry3 = Inquiry::create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'customer_name' => 'Rohan Mehta',
            'phone' => '9876543212',
            'email' => 'rohan@example.com',
            'status' => 'new',
        ]);

        $log1 = InquiryDripLog::create([
            'company_id' => $this->company->id,
            'inquiry_id' => $inquiry1->id,
            'lead_drip_step_id' => $this->step->id,
            'scheduled_for' => Carbon::now()->addDay(),
            'status' => 'pending',
        ]);

        $log2 = InquiryDripLog::create([
            'company_id' => $this->company->id,
            'inquiry_id' => $inquiry2->id,
            'lead_drip_step_id' => $this->step->id,
            'scheduled_for' => Carbon::now()->addDay(),
            'status' => 'pending',
        ]);

        $log3 = InquiryDripLog::create([
            'company_id' => $this->company->id,
            'inquiry_id' => $inquiry3->id,
            'lead_drip_step_id' => $this->step->id,
            'scheduled_for' => Carbon::now()->addDay(),
            'status' => 'pending',
        ]);

        // Dispatch ONLY log1 and log2
        $response = $this->withSession(['selected_project_id' => $this->project->id])
            ->post(route('settings.drip.process-selected'), [
                'selected_drip_ids' => [$log1->id, $log2->id],
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Log 1 and Log 2 should be sent
        $this->assertEquals('sent', $log1->fresh()->status);
        $this->assertEquals('sent', $log2->fresh()->status);

        // Log 3 should remain pending (not selected)
        $this->assertEquals('pending', $log3->fresh()->status);
    }

    public function test_user_can_dispatch_single_user_drip(): void
    {
        $this->actingAs($this->user);

        $inquiry = Inquiry::create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'customer_name' => 'Vikram Patel',
            'phone' => '9876543213',
            'status' => 'new',
        ]);

        $log = InquiryDripLog::create([
            'company_id' => $this->company->id,
            'inquiry_id' => $inquiry->id,
            'lead_drip_step_id' => $this->step->id,
            'scheduled_for' => Carbon::now()->addDay(),
            'status' => 'pending',
        ]);

        $response = $this->withSession(['selected_project_id' => $this->project->id])
            ->post(route('settings.drip.process-single', $log));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('sent', $log->fresh()->status);
    }

    public function test_user_cannot_dispatch_drip_belonging_to_another_company(): void
    {
        $otherCompany = Company::create([
            'name' => 'Other Builder',
            'email' => 'other@builder.com',
            'subscription_status' => 'active',
            'subscription_ends_at' => now()->addYear(),
        ]);

        $otherProject = Project::create([
            'company_id' => $otherCompany->id,
            'name' => 'Other Heights',
        ]);

        $otherInquiry = Inquiry::create([
            'company_id' => $otherCompany->id,
            'project_id' => $otherProject->id,
            'customer_name' => 'Sneha Rao',
            'phone' => '9111111111',
            'status' => 'new',
        ]);

        $otherStep = LeadDripStep::create([
            'company_id' => $otherCompany->id,
            'day_offset' => 1,
            'step_title' => 'Other Step',
            'channel' => 'whatsapp',
            'message_template' => 'Other Template',
            'is_active' => true,
        ]);

        $otherLog = InquiryDripLog::create([
            'company_id' => $otherCompany->id,
            'inquiry_id' => $otherInquiry->id,
            'lead_drip_step_id' => $otherStep->id,
            'scheduled_for' => Carbon::now()->addDay(),
            'status' => 'pending',
        ]);

        $this->actingAs($this->user);

        // Attempt single dispatch on other company's log
        $response = $this->withSession(['selected_project_id' => $this->project->id])
            ->post(route('settings.drip.process-single', $otherLog));
        $response->assertStatus(404); // Scoped by HasTenant or 403

        // Attempt bulk dispatch containing other company's log
        $bulkResponse = $this->withSession(['selected_project_id' => $this->project->id])
            ->post(route('settings.drip.process-selected'), [
                'selected_drip_ids' => [$otherLog->id],
            ]);
        // The foreign log should not be processed for current company
        $this->assertEquals('pending', $otherLog->fresh()->status);
    }

    public function test_dispatch_selected_requires_at_least_one_drip(): void
    {
        $this->actingAs($this->user);

        $response = $this->withSession(['selected_project_id' => $this->project->id])
            ->post(route('settings.drip.process-selected'), [
                'selected_drip_ids' => [],
            ]);

        $response->assertSessionHasErrors('selected_drip_ids');
    }

    public function test_user_can_discard_single_drip_and_remove_it_from_queue(): void
    {
        $this->actingAs($this->user);

        $inquiry = Inquiry::create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'customer_name' => 'Discard Test Lead',
            'phone' => '9876543299',
            'status' => 'new',
        ]);

        $log = InquiryDripLog::create([
            'company_id' => $this->company->id,
            'inquiry_id' => $inquiry->id,
            'lead_drip_step_id' => $this->step->id,
            'scheduled_for' => Carbon::now()->addDay(),
            'status' => 'pending',
        ]);

        $response = $this->withSession(['selected_project_id' => $this->project->id])
            ->post(route('settings.drip.discard-single', $log));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Status is changed to skipped
        $this->assertEquals('skipped', $log->fresh()->status);

        // Verification: Discarded log is excluded from both pendingLogs and recentLogs views
        $indexResponse = $this->withSession(['selected_project_id' => $this->project->id])
            ->get(route('settings.drip'));
        $indexResponse->assertOk();
        $this->assertFalse($indexResponse->viewData('pendingLogs')->contains('id', $log->id));
        $this->assertFalse($indexResponse->viewData('recentLogs')->contains('id', $log->id));

        // After clearing flash session, the lead name is not present in queue table
        $freshPageResponse = $this->flushSession()
            ->withSession(['selected_project_id' => $this->project->id])
            ->get(route('settings.drip'));
        $freshPageResponse->assertOk();
        $freshPageResponse->assertDontSee('Discard Test Lead');
    }

    public function test_user_can_discard_selected_drips_in_bulk(): void
    {
        $this->actingAs($this->user);

        $inquiry1 = Inquiry::create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'customer_name' => 'Bulk Discard Lead 1',
            'phone' => '9876543288',
            'status' => 'new',
        ]);

        $inquiry2 = Inquiry::create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'customer_name' => 'Keep Lead 2',
            'phone' => '9876543277',
            'status' => 'new',
        ]);

        $log1 = InquiryDripLog::create([
            'company_id' => $this->company->id,
            'inquiry_id' => $inquiry1->id,
            'lead_drip_step_id' => $this->step->id,
            'scheduled_for' => Carbon::now()->addDay(),
            'status' => 'pending',
        ]);

        $log2 = InquiryDripLog::create([
            'company_id' => $this->company->id,
            'inquiry_id' => $inquiry2->id,
            'lead_drip_step_id' => $this->step->id,
            'scheduled_for' => Carbon::now()->addDay(),
            'status' => 'pending',
        ]);

        $response = $this->withSession(['selected_project_id' => $this->project->id])
            ->post(route('settings.drip.discard-selected'), [
                'selected_drip_ids' => [$log1->id],
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('skipped', $log1->fresh()->status);
        $this->assertEquals('pending', $log2->fresh()->status);
    }
}
