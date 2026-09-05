<?php

namespace Tests\Feature;

use App\Jobs\SendEmailVerificationJob;
use App\Jobs\SendInquiryConfirmationEmailJob;
use App\Jobs\SendNewLeadNotificationEmailJob;
use App\Jobs\SendPasswordResetEmailJob;
use App\Jobs\SendWelcomeEmailJob;
use App\Models\Company;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class QueuedEmailSendingTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_registration_dispatches_queued_welcome_and_verification_jobs(): void
    {
        Queue::fake();

        $response = $this->post('/register', [
            'company_name' => 'Apex Realty',
            'company_email' => 'contact@apex.com',
            'admin_name' => 'John Admin',
            'admin_email' => 'john@apex.com',
            'admin_password' => 'Password123!',
            'admin_password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect('/login');

        Queue::assertPushed(SendEmailVerificationJob::class);
        Queue::assertPushed(SendWelcomeEmailJob::class);
    }

    public function test_public_inquiry_submission_dispatches_queued_email_jobs(): void
    {
        Queue::fake();

        $company = Company::create(['name' => 'Estate Co', 'email' => 'estate@co.com']);
        $project = Project::create(['company_id' => $company->id, 'name' => 'Ocean View']);

        $response = $this->post(route('public.inquiry.store', $project), [
            'customer_name' => 'Alice Smith',
            'phone' => '+19876543210',
            'email' => 'alice@example.com',
        ]);

        $response->assertRedirect();

        Queue::assertPushed(SendInquiryConfirmationEmailJob::class);
        Queue::assertPushed(SendNewLeadNotificationEmailJob::class);
    }

    public function test_password_reset_request_dispatches_queued_password_reset_job(): void
    {
        Queue::fake();

        $company = Company::create(['name' => 'Reset Co', 'email' => 'reset@co.com']);
        $user = User::factory()->create(['company_id' => $company->id, 'email' => 'user@reset.com']);

        $response = $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertSessionHas('status');

        Queue::assertPushed(SendPasswordResetEmailJob::class);
    }
}
