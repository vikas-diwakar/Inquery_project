<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Inquiry;
use App\Models\InquiryDripLog;
use App\Models\LeadDripStep;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomDripTest extends TestCase
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
            'name' => 'Elite Realty Promoters',
            'email' => 'elite@realty.com',
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
            'name' => 'Crown Luxury Residences',
            'status' => 'ongoing',
        ]);

        $this->user->projects()->attach($this->project->id);

        $this->step = LeadDripStep::create([
            'company_id' => $this->company->id,
            'day_offset' => 1,
            'step_title' => 'Day 1: Welcome & Brochure',
            'channel' => 'whatsapp',
            'message_template' => 'Hello {customer_name}! Thank you for your inquiry about {project_name}.',
            'is_active' => true,
        ]);
    }

    public function test_user_can_view_inquiries_listing_with_drip_templates(): void
    {
        $response = $this->actingAs($this->user)
            ->withSession(['selected_project_id' => $this->project->id])
            ->get(route('inquiries.index'));

        $response->assertStatus(200);
        $response->assertSee('Send WhatsApp Message');
        $response->assertSee('Crown Luxury Residences');
        $response->assertSee('{customer_name}');
    }

    public function test_user_can_send_custom_drip_to_single_inquiry(): void
    {
        $inquiry = Inquiry::create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'customer_name' => 'Vikram Singhania',
            'phone' => '+919876543210',
            'status' => 'new',
        ]);

        $customMessage = 'Hi {customer_name}, we have an exclusive festival offer on {project_name}!';

        $response = $this->actingAs($this->user)
            ->withSession(['selected_project_id' => $this->project->id])
            ->postJson(route('inquiries.send-custom-drip'), [
                'inquiry_ids' => [$inquiry->id],
                'message_content' => $customMessage,
                'lead_drip_step_id' => $this->step->id,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'sent_count' => 1,
            'failed_count' => 0,
        ]);

        // Check Inquiry Drip Log creation
        $log = InquiryDripLog::where('inquiry_id', $inquiry->id)->first();
        $this->assertNotNull($log);
        $this->assertEquals('sent', $log->status);
        $this->assertStringContainsString('Vikram Singhania', $log->sent_message);
        $this->assertStringContainsString('Crown Luxury Residences', $log->sent_message);

        // Check inquiry WhatsApp last message
        $inquiry->refresh();
        $this->assertEquals('sent', $inquiry->whatsapp_status);
        $this->assertStringContainsString('Vikram Singhania', $inquiry->whatsapp_last_message);
    }

    public function test_user_can_bulk_send_custom_drip_to_multiple_inquiries(): void
    {
        $inquiry1 = Inquiry::create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'customer_name' => 'Rajesh Sharma',
            'phone' => '+919876543211',
            'status' => 'contacted',
        ]);

        $inquiry2 = Inquiry::create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'customer_name' => 'Neha Patel',
            'phone' => '+919876543212',
            'status' => 'interested',
        ]);

        $response = $this->actingAs($this->user)
            ->withSession(['selected_project_id' => $this->project->id])
            ->postJson(route('inquiries.send-custom-drip'), [
                'inquiry_ids' => [$inquiry1->id, $inquiry2->id],
                'message_content' => 'Dear {customer_name}, your site visit to {project_name} can be scheduled this Saturday!',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'sent_count' => 2,
        ]);

        $log1 = InquiryDripLog::where('inquiry_id', $inquiry1->id)->first();
        $this->assertStringContainsString('Rajesh Sharma', $log1->sent_message);

        $log2 = InquiryDripLog::where('inquiry_id', $inquiry2->id)->first();
        $this->assertStringContainsString('Neha Patel', $log2->sent_message);
    }

    public function test_user_cannot_send_drip_to_inquiry_of_another_company(): void
    {
        $otherCompany = Company::create([
            'name' => 'Other Builder',
            'email' => 'other@builder.com',
            'subscription_status' => 'active',
        ]);

        $otherInquiry = Inquiry::create([
            'company_id' => $otherCompany->id,
            'project_id' => $this->project->id,
            'customer_name' => 'Intruder Target',
            'phone' => '+919876543299',
            'status' => 'new',
        ]);

        $response = $this->actingAs($this->user)
            ->withSession(['selected_project_id' => $this->project->id])
            ->postJson(route('inquiries.send-custom-drip'), [
                'inquiry_ids' => [$otherInquiry->id],
                'message_content' => 'Hello test',
            ]);

        $response->assertStatus(404);
        $this->assertDatabaseMissing('inquiry_drip_logs', [
            'inquiry_id' => $otherInquiry->id,
        ]);
    }

    public function test_validation_requires_inquiry_ids_and_message_content(): void
    {
        $response = $this->actingAs($this->user)
            ->withSession(['selected_project_id' => $this->project->id])
            ->postJson(route('inquiries.send-custom-drip'), [
                'inquiry_ids' => [],
                'message_content' => '',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['inquiry_ids', 'message_content']);
    }
}
