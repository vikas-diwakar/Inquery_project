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
        $response->assertSee('1. Welcome Greeting');
        $response->assertSee('2. New Project Detail');
        $response->assertSee('3. Dynamic Offer');
        $response->assertSee('{offer_event}');
        $response->assertSee('{offer_percentage}');
        $response->assertSee('{new_project_name}');
    }

    public function test_user_can_send_dynamic_offer_template_with_festival_and_percentage(): void
    {
        $inquiry = Inquiry::create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'customer_name' => 'Siddharth Rao',
            'phone' => '+919876543290',
            'status' => 'new',
        ]);

        $offerTemplate = 'Greetings {customer_name}! Celebrate this {offer_event} with {offer_percentage} on {project_name}!';

        $response = $this->actingAs($this->user)
            ->withSession(['selected_project_id' => $this->project->id])
            ->postJson(route('inquiries.send-custom-drip'), [
                'inquiry_ids' => [$inquiry->id],
                'message_content' => $offerTemplate,
                'offer_event' => 'Diwali Mega Dhamaka',
                'offer_percentage' => '15% Spot Discount',
                'offer_validity' => 'Valid till Sunday',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'sent_count' => 1,
            'failed_count' => 0,
        ]);

        $log = InquiryDripLog::where('inquiry_id', $inquiry->id)->first();
        $this->assertNotNull($log);
        $this->assertEquals('sent', $log->status);
        $this->assertStringContainsString('Siddharth Rao', $log->sent_message);
        $this->assertStringContainsString('Diwali Mega Dhamaka', $log->sent_message);
        $this->assertStringContainsString('15% Spot Discount', $log->sent_message);
        $this->assertStringContainsString('Crown Luxury Residences', $log->sent_message);
    }

    public function test_user_can_send_new_project_detail_template_to_existing_leads(): void
    {
        $inquiry = Inquiry::create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'customer_name' => 'Kavita Verma',
            'phone' => '+919876543291',
            'status' => 'contacted',
        ]);

        $newProjectTemplate = 'Hello {customer_name}! Exclusive pre-launch access to our new development {new_project_name} located at {new_project_location}!';

        $response = $this->actingAs($this->user)
            ->withSession(['selected_project_id' => $this->project->id])
            ->postJson(route('inquiries.send-custom-drip'), [
                'inquiry_ids' => [$inquiry->id],
                'message_content' => $newProjectTemplate,
                'new_project_name' => 'Crown Signature Towers',
                'new_project_location' => 'Golf Course Road Extension',
                'new_project_pricing' => '3 & 4 BHK starting ₹1.5 Cr',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'sent_count' => 1,
            'failed_count' => 0,
        ]);

        $log = InquiryDripLog::where('inquiry_id', $inquiry->id)->first();
        $this->assertNotNull($log);
        $this->assertEquals('sent', $log->status);
        $this->assertStringContainsString('Kavita Verma', $log->sent_message);
        $this->assertStringContainsString('Crown Signature Towers', $log->sent_message);
        $this->assertStringContainsString('Golf Course Road Extension', $log->sent_message);
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
