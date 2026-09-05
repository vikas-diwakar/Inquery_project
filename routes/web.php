<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrochureController;
use App\Http\Controllers\CompanyRegistrationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FacebookWebhookController;
use App\Http\Controllers\FormQRController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;
use Illuminate\Support\Facades\Route;

// SEO & Search Engine Indexing routes
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('seo.sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('seo.robots');

// Public routes
Route::get('/', function () {
    $hashed = Hash::make('12345678');
    // dd($hashed);
    return redirect()->route('login');
});

// Public inquiry form (no auth required)
Route::get('/inquiry/{project}', [InquiryController::class, 'showPublicForm'])
    ->name('public.inquiry.form');
Route::post('/inquiry/{project}', [InquiryController::class, 'storePublic'])
    ->name('public.inquiry.store');

// Public brochure download (no auth required)
Route::get('/brochure/{brochure}/download', [BrochureController::class, 'download'])
    ->name('public.brochure.download');

// Public integrations (accessible via lead_token)
Route::get('/inquiry/widget/{token}', [\App\Http\Controllers\IntegrationController::class, 'showWidget'])
    ->name('public.inquiry.widget');
Route::post('/inquiry/widget/{token}', [\App\Http\Controllers\IntegrationController::class, 'storeWidget'])
    ->name('public.inquiry.widget.store');
Route::post('/api/v1/leads/{token}', [\App\Http\Controllers\IntegrationController::class, 'handleWebhook'])
    ->name('api.leads.webhook');

Route::get('/webhook/facebook', [FacebookWebhookController::class, 'verify']);
Route::post('/webhook/facebook', [FacebookWebhookController::class, 'handle']);

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [CompanyRegistrationController::class, 'showRegistrationForm'])->name('company.register');
    Route::post('/register', [CompanyRegistrationController::class, 'register']);

    // Password Reset Routes
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Email Verification Routes
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');
Route::post('/email/resend', [EmailVerificationController::class, 'resend'])
    ->middleware('throttle:6,1')
    ->name('verification.resend');

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Subscription routes (available even when subscription expired)
    Route::get('/subscription/required', [SubscriptionController::class, 'required'])->name('subscription.required');
    Route::get('/subscription/choose-plan', [SubscriptionController::class, 'choosePlan'])->name('subscription.choose-plan');
    Route::post('/subscription/activate-plan', [SubscriptionController::class, 'activatePlan'])->name('subscription.activate-plan');
    Route::post('/subscription/create-order/{plan}', [SubscriptionController::class, 'createOrder'])->name('subscription.create-order');
    Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::get('/subscription/details', [SubscriptionController::class, 'show'])->name('subscription.show');
    Route::get('/subscription/checkout/{plan}', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    Route::post('/subscription/purchase/{plan}', [SubscriptionController::class, 'purchase'])->name('subscription.purchase');
    Route::post('/subscription/renew', [SubscriptionController::class, 'renew'])->name('subscription.renew');
    Route::post('/subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Projects (accessible without project selection)
    Route::middleware('tenant')->group(function () {
        Route::resource('projects', ProjectController::class);
        Route::get('/projects/{project}/select', [ProjectController::class, 'select'])->name('projects.select');
        Route::post('/projects/clear-selection', [ProjectController::class, 'clearSelection'])->name('projects.clear-selection');

        // Unit Inventory & Stacking Chart
        Route::get('/projects/{project}/units', [\App\Http\Controllers\ProjectUnitController::class, 'index'])->name('projects.units.index');
        Route::post('/projects/{project}/units', [\App\Http\Controllers\ProjectUnitController::class, 'store'])->name('projects.units.store');
        Route::post('/projects/{project}/units/batch', [\App\Http\Controllers\ProjectUnitController::class, 'generateBatch'])->name('projects.units.batch');
        Route::patch('/units/{unit}/status', [\App\Http\Controllers\ProjectUnitController::class, 'updateStatus'])->name('units.update-status');
        Route::delete('/units/{unit}', [\App\Http\Controllers\ProjectUnitController::class, 'destroy'])->name('units.destroy');
    });

    // Project-specific routes (require project selection and active subscription)
    Route::middleware(['tenant', 'project', 'subscription'])->group(function () {
        // Inquiries
        Route::get('/inquiries', [InquiryController::class, 'index'])->name('inquiries.index');
        Route::get('/inquiries/export', [InquiryController::class, 'export'])->name('inquiries.export');
        Route::get('/inquiries/create', [InquiryController::class, 'create'])->name('inquiries.create');
        Route::post('/inquiries', [InquiryController::class, 'store'])->name('inquiries.store');
        Route::get('/inquiries/{inquiry}', [InquiryController::class, 'show'])->name('inquiries.show');
        Route::put('/inquiries/{inquiry}', [InquiryController::class, 'update'])->name('inquiries.update');
        Route::patch('/inquiries/{inquiry}/status', [InquiryController::class, 'updateStatus'])->name('inquiries.update-status');
        Route::post('/inquiries/{inquiry}/resend-whatsapp', [InquiryController::class, 'resendWhatsApp'])->name('inquiries.resend-whatsapp');
        Route::delete('/inquiries/{inquiry}', [InquiryController::class, 'destroy'])->name('inquiries.destroy');

        // WhatsApp Integration Settings
        Route::get('/settings/whatsapp', [\App\Http\Controllers\WhatsAppSettingController::class, 'index'])->name('settings.whatsapp');
        Route::match(['post', 'put'], '/settings/whatsapp', [\App\Http\Controllers\WhatsAppSettingController::class, 'update'])->name('settings.whatsapp.update');
        Route::post('/settings/whatsapp/test', [\App\Http\Controllers\WhatsAppSettingController::class, 'testSend'])->name('settings.whatsapp.test');

        // Lead Drip Automation Sequences
        Route::get('/settings/drip', [\App\Http\Controllers\LeadDripController::class, 'index'])->name('settings.drip');
        Route::post('/settings/drip', [\App\Http\Controllers\LeadDripController::class, 'store'])->name('settings.drip.store');
        Route::delete('/settings/drip/{step}', [\App\Http\Controllers\LeadDripController::class, 'destroy'])->name('settings.drip.destroy');
        Route::post('/settings/drip/process-now', [\App\Http\Controllers\LeadDripController::class, 'processNow'])->name('settings.drip.process-now');
        Route::post('/settings/drip/enroll-past', [\App\Http\Controllers\LeadDripController::class, 'enrollPastLeads'])->name('settings.drip.enroll-past');

        // Follow-up routes
        Route::get('/follow-ups', [FollowUpController::class, 'index'])->name('follow-ups.index');
        Route::post('/inquiries/{inquiry}/follow-ups', [FollowUpController::class, 'store'])->name('follow-ups.store');
        Route::post('/inquiries/{inquiry}/follow-ups/complete', [FollowUpController::class, 'complete'])->name('follow-ups.complete');
        Route::post('/follow-ups/bulk-schedule', [FollowUpController::class, 'bulkSchedule'])->name('follow-ups.bulk-schedule');
        Route::get('/api/follow-ups/stats', [FollowUpController::class, 'getStats'])->name('follow-ups.stats');

        // Brochures
        Route::get('/brochures', [BrochureController::class, 'index'])->name('brochures.index');
        Route::get('/brochures/create', [BrochureController::class, 'create'])->name('brochures.create');
        Route::post('/brochures', [BrochureController::class, 'store'])->name('brochures.store');
        Route::delete('/brochures/{brochure}', [BrochureController::class, 'destroy'])->name('brochures.destroy');

        // Forms & QR Codes
        Route::get('/forms-qr', [FormQRController::class, 'index'])->name('forms-qr.index');
        Route::get('/forms-qr/create-inquiry-form', [FormQRController::class, 'createInquiryForm'])->name('forms-qr.create-inquiry-form');
        Route::post('/forms-qr/generate-inquiry-qr', [FormQRController::class, 'generateInquiryQR'])->name('forms-qr.generate-inquiry-qr');
        Route::get('/forms-qr/inquiry-qr', [FormQRController::class, 'showInquiryQR'])->name('forms-qr.show-inquiry-qr');
        Route::get('/forms-qr/inquiry-qr/download', [FormQRController::class, 'downloadInquiryQR'])->name('forms-qr.download-inquiry-qr');
        Route::get('/forms-qr/brochure-qr', [FormQRController::class, 'brochureQR'])->name('forms-qr.brochure-qr');
        Route::get('/forms-qr/brochure-qr/{brochure}', [FormQRController::class, 'showBrochureQR'])->name('forms-qr.show-brochure-qr');

        // Integrations panel
        Route::get('/integrations', [\App\Http\Controllers\IntegrationController::class, 'index'])->name('integrations.index');
        Route::post('/projects/{project}/regenerate-token', [\App\Http\Controllers\IntegrationController::class, 'regenerateToken'])->name('projects.regenerate-token');
    });

        // Users (Admin only)
        Route::middleware('role:Admin')->group(function () {
            Route::resource('users', UserController::class);
        });
});
