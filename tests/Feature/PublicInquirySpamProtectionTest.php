<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Inquiry;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PublicInquirySpamProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_honeypot_silently_discards_bot_submissions(): void
    {
        Queue::fake();

        $company = Company::create(['name' => 'Spam Test Co', 'email' => 'spam@co.com']);
        $project = Project::create(['company_id' => $company->id, 'name' => 'Project Guard']);

        // A bot fills the invisible honeypot field 'company_website'
        $response = $this->post(route('public.inquiry.store', ['project' => $project->getEncryptedKey()]), [
            'customer_name' => 'Spam Bot',
            'phone' => '9876543211',
            'company_website' => 'https://spam-site.com', // Honeypot filled!
            '_rendered_at' => encrypt(time() - 10),
        ]);

        $response->assertRedirect();
        // Inquiry must NOT be saved in database
        $this->assertDatabaseMissing('inquiries', [
            'project_id' => $project->id,
            'phone' => '9876543211',
        ]);
    }

    public function test_time_trap_discards_submissions_under_3_seconds(): void
    {
        Queue::fake();

        $company = Company::create(['name' => 'Spam Test Co', 'email' => 'spam2@co.com']);
        $project = Project::create(['company_id' => $company->id, 'name' => 'Project Guard']);

        // A bot submits in under 1 second
        $response = $this->post(route('public.inquiry.store', ['project' => $project->getEncryptedKey()]), [
            'customer_name' => 'Fast Bot',
            'phone' => '9876543212',
            '_rendered_at' => encrypt(time()), // Submitted at exact same second
        ]);

        $response->assertRedirect();
        // Inquiry must NOT be saved in database
        $this->assertDatabaseMissing('inquiries', [
            'project_id' => $project->id,
            'phone' => '9876543212',
        ]);
    }

    public function test_genuine_human_submission_succeeds(): void
    {
        Queue::fake();

        $company = Company::create(['name' => 'Human Test Co', 'email' => 'human@co.com']);
        $project = Project::create(['company_id' => $company->id, 'name' => 'Project Guard']);

        // Human filled properly (time elapsed >= 3 seconds, honeypot empty)
        $response = $this->post(route('public.inquiry.store', ['project' => $project->getEncryptedKey()]), [
            'customer_name' => 'Jane Human',
            'phone' => '9876543213',
            'company_website' => '',
            '_rendered_at' => encrypt(time() - 8),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('inquiries', [
            'project_id' => $project->id,
            'phone' => '9876543213',
            'customer_name' => 'Jane Human',
        ]);
    }

    public function test_repeated_identical_digits_dummy_phone_is_rejected(): void
    {
        Queue::fake();

        $company = Company::create(['name' => 'Dummy Test Co', 'email' => 'dummy@co.com']);
        $project = Project::create(['company_id' => $company->id, 'name' => 'Project Guard']);

        // Dummy number like 0000000000 or 9999999999
        $response = $this->post(route('public.inquiry.store', ['project' => $project->getEncryptedKey()]), [
            'customer_name' => 'Fake User',
            'phone' => '0000000000',
            '_rendered_at' => encrypt(time() - 8),
        ]);

        $response->assertSessionHasErrors(['phone']);
        $this->assertDatabaseMissing('inquiries', [
            'project_id' => $project->id,
            'phone' => '0000000000',
        ]);
    }

    public function test_rate_limiting_throttles_excessive_submissions(): void
    {
        Queue::fake();

        $company = Company::create(['name' => 'Throttle Test Co', 'email' => 'throttle@co.com']);
        $project = Project::create(['company_id' => $company->id, 'name' => 'Project Guard']);

        // Submit 5 requests (up to the limit)
        for ($i = 1; $i <= 5; $i++) {
            $this->post(route('public.inquiry.store', ['project' => $project->getEncryptedKey()]), [
                'customer_name' => "User {$i}",
                'phone' => "98111222{$i}0",
                '_rendered_at' => encrypt(time() - 8),
            ]);
        }

        // 6th request must be throttled
        $response = $this->post(route('public.inquiry.store', ['project' => $project->getEncryptedKey()]), [
            'customer_name' => 'User 6',
            'phone' => '9811122260',
            '_rendered_at' => encrypt(time() - 8),
        ]);

        $response->assertSessionHasErrors(['phone']);
        $this->assertDatabaseMissing('inquiries', [
            'project_id' => $project->id,
            'phone' => '9811122260',
        ]);
    }
}
