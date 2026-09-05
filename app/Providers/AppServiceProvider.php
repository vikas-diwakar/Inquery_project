<?php

namespace App\Providers;

use App\Models\Brochure;
use App\Models\Company;
use App\Models\Inquiry;
use App\Models\Project;
use App\Models\User;
use App\Policies\BrochurePolicy;
use App\Policies\InquiryPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\SubscriptionPolicy;
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
        Company::class => SubscriptionPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $publicHtml = base_path('../public_html');
        if (is_dir($publicHtml) && realpath($publicHtml) !== realpath(base_path())) {
            $this->app->usePublicPath($publicHtml);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
