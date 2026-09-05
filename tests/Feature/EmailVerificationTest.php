<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_registration_sends_verification_email_and_prevents_immediate_login(): void
    {
        Notification::fake();

        $response = $this->post(route('company.register'), [
            'company_name' => 'Acme Real Estate',
            'company_email' => 'contact@acme.test',
            'company_phone' => '1234567890',
            'admin_name' => 'Admin User',
            'admin_email' => 'admin@acme.test',
            'admin_password' => 'Password123!',
            'admin_password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');

        $user = User::where('email', 'admin@acme.test')->first();
        $this->assertNotNull($user);
        $this->assertNull($user->email_verified_at);

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_unverified_user_cannot_login(): void
    {
        $company = Company::create(['name' => 'Test Co', 'email' => 'co@test.com']);
        $user = User::factory()->create([
            'company_id' => $company->id,
            'email_verified_at' => null,
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_user_can_verify_email_using_signed_url(): void
    {
        Event::fake();

        $company = Company::create(['name' => 'Test Co 2', 'email' => 'co2@test.com']);
        $user = User::factory()->create([
            'company_id' => $company->id,
            'email_verified_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        $response = $this->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(route('login'));
    }
}
