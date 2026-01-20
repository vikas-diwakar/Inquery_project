<?php

namespace App\Providers;

use App\Models\Brochure;
use App\Models\Inquiry;
use App\Models\Project;
use App\Models\User;
use App\Policies\BrochurePolicy;
use App\Policies\InquiryPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Project::class => ProjectPolicy::class,
        Inquiry::class => InquiryPolicy::class,
        Brochure::class => BrochurePolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
