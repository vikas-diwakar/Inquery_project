<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Inquiry;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class UniqueInquiryMobileTest extends TestCase
{
    use RefreshDatabase;

    public function test_first_public_inquiry_with_mobile_number_succeeds(): void
    {
        Queue::fake();

        $company = Company::create(['name' => 'Unique Test Co', 'email' => 'unique@co.com']);
        $project = Project::create(['company_id' => $company->id, 'name' => 'Project Alpha']);

        $response = $this->post(route('public.inquiry.store', $project), [
            'customer_name' => 'John Doe',
            'phone' => '9876543210',
            'email' => 'john@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('inquiries', [
            'project_id' => $project->id,
            'phone' => '9876543210',
        ]);
    }

    public function test_duplicate_mobile_number_for_same_project_is_rejected(): void
    {
        Queue::fake();

        $company = Company::create(['name' => 'Unique Test Co', 'email' => 'unique@co.com']);
        $project = Project::create(['company_id' => $company->id, 'name' => 'Project Alpha']);

        // First submission succeeds
        Inquiry::create([
            'company_id' => $company->id,
            'project_id' => $project->id,
            'customer_name' => 'John Doe',
            'phone' => '9876543210',
            'status' => 'new',
        ]);

        // Second submission with exact same phone number must fail validation
        $response = $this->from(route('public.inquiry.form', $project))
            ->post(route('public.inquiry.store', $project), [
                'customer_name' => 'Jane Doe',
                'phone' => '9876543210',
                'email' => 'jane@example.com',
            ]);

        $response->assertRedirect(route('public.inquiry.form', $project));
        $response->assertSessionHasErrors(['phone']);
        
        $this->assertEquals(1, Inquiry::where('project_id', $project->id)->count());
    }

    public function test_duplicate_mobile_number_with_different_formatting_is_rejected(): void
    {
        Queue::fake();

        $company = Company::create(['name' => 'Unique Test Co', 'email' => 'unique@co.com']);
        $project = Project::create(['company_id' => $company->id, 'name' => 'Project Alpha']);

        // Existing lead with formatted phone (+91 98765 43210)
        Inquiry::create([
            'company_id' => $company->id,
            'project_id' => $project->id,
            'customer_name' => 'John Doe',
            'phone' => '+91 98765 43210',
            'status' => 'new',
        ]);

        // Submission with raw number (9876543210)
        $response = $this->from(route('public.inquiry.form', $project))
            ->post(route('public.inquiry.store', $project), [
                'customer_name' => 'Duplicate Attempt',
                'phone' => '9876543210',
            ]);

        $response->assertSessionHasErrors(['phone']);
        $this->assertEquals(1, Inquiry::where('project_id', $project->id)->count());
    }

    public function test_same_mobile_number_for_different_project_is_allowed(): void
    {
        Queue::fake();

        $company = Company::create(['name' => 'Unique Test Co', 'email' => 'unique@co.com']);
        $project1 = Project::create(['company_id' => $company->id, 'name' => 'Project One']);
        $project2 = Project::create(['company_id' => $company->id, 'name' => 'Project Two']);

        // Existing lead in Project 1
        Inquiry::create([
            'company_id' => $company->id,
            'project_id' => $project1->id,
            'customer_name' => 'John Doe',
            'phone' => '9876543210',
            'status' => 'new',
        ]);

        // Same phone submitted for Project 2 should succeed
        $response = $this->post(route('public.inquiry.store', $project2), [
            'customer_name' => 'John Doe',
            'phone' => '9876543210',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertEquals(1, Inquiry::where('project_id', $project2->id)->count());
    }
}
