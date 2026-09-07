<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicMarketingPagesTest extends TestCase
{
    public function test_home_page_can_be_rendered(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('PropDrip');
        $response->assertSee('Home');
        $response->assertSee('About');
        $response->assertSee('Contact Us');
        $response->assertSee(route('login'));
    }

    public function test_about_page_can_be_rendered(): void
    {
        $response = $this->get(route('about'));
        $response->assertStatus(200);
        $response->assertSee('About PropDrip');
        $response->assertSee('Log In');
    }

    public function test_contact_page_can_be_rendered_and_submitted(): void
    {
        $response = $this->get(route('contact'));
        $response->assertStatus(200);
        $response->assertSee('Contact Information');

        $postResponse = $this->post(route('contact.store'), [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'subject' => 'Real Estate Inquiry Automation',
            'message' => 'Hello, I want to learn more about setting up PropDrip for my real estate project.',
        ]);

        $postResponse->assertRedirect();
        $postResponse->assertSessionHas('success');
    }

    public function test_login_and_register_pages_contain_public_navigation_menu(): void
    {
        $loginResponse = $this->get(route('login'));
        $loginResponse->assertStatus(200);
        $loginResponse->assertSee('Home');
        $loginResponse->assertSee('About');
        $loginResponse->assertSee('Contact Us');

        $registerResponse = $this->get(route('company.register'));
        $registerResponse->assertStatus(200);
        $registerResponse->assertSee('Home');
        $registerResponse->assertSee('About');
        $registerResponse->assertSee('Contact Us');
    }
}
