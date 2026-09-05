<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_screen_can_be_rendered(): void
    {
        $response = $this->get(route('password.request'));
        $response->assertOk();
    }

    public function test_password_reset_link_can_be_requested(): void
    {
        Notification::fake();

        $company = Company::create(['name' => 'Reset Co', 'email' => 'reset@co.com']);
        $user = User::factory()->create([
            'company_id' => $company->id,
            'email' => 'resetuser@example.com',
        ]);

        $response = $this->post(route('password.email'), [
            'email' => 'resetuser@example.com',
        ]);

        $response->assertSessionHas('status');
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $company = Company::create(['name' => 'Reset Co 2', 'email' => 'reset2@co.com']);
        $user = User::factory()->create([
            'company_id' => $company->id,
            'email' => 'user2@example.com',
            'password' => Hash::make('old-password'),
        ]);

        $token = Password::createToken($user);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'user2@example.com',
            'password' => 'NewSecurePassword123!',
            'password_confirmation' => 'NewSecurePassword123!',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertTrue(Hash::check('NewSecurePassword123!', $user->fresh()->password));
    }
}
