<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrochureController;
use App\Http\Controllers\CompanySettingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FacebookWebhookController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\FormQRController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\IntegrationController;
use App\Http\Controllers\SocialMediaController;
use App\Http\Controllers\LeadDripController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectUnitController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WhatsAppSettingController;
use Illuminate\Support\Facades\Route;

// SEO & Search Engine Indexing routes
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('seo.sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('seo.robots');

// Root redirect: Direct CRM portal
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
})->name('home');

// Public inquiry form (no auth required)
Route::get('/inquiry/{project}', [InquiryController::class, 'showPublicForm'])
    ->name('public.inquiry.form');
Route::post('/inquiry/{project}', [InquiryController::class, 'storePublic'])
    ->name('public.inquiry.store')
    ->middleware('throttle:public-inquiry');

// Public brochure download (no auth required)
Route::get('/brochure/{brochure}/download', [BrochureController::class, 'download'])
    ->name('public.brochure.download');

// Public Social Media & Webhook leads (accessible via lead_token)
Route::get('/inquiry/widget/{token}', [SocialMediaController::class, 'showWidget'])
    ->name('public.inquiry.widget');
Route::post('/inquiry/widget/{token}', [SocialMediaController::class, 'storeWidget'])
    ->name('public.inquiry.widget.store');
Route::post('/api/v1/leads/{token}', [SocialMediaController::class, 'handleWebhook'])
    ->name('api.leads.webhook');

Route::get('/webhook/facebook', [FacebookWebhookController::class, 'verify']);
Route::post('/webhook/facebook', [FacebookWebhookController::class, 'handle']);

// Authentication routes (Guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Password Reset Request Routes
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
});

// Password Reset Action Routes
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Projects (accessible without project selection)
    Route::middleware('tenant')->group(function () {
        Route::resource('projects', ProjectController::class);
        Route::get('/projects/{project}/select', [ProjectController::class, 'select'])->name('projects.select');
        Route::post('/projects/clear-selection', [ProjectController::class, 'clearSelection'])->name('projects.clear-selection');

        // Unit Inventory & Stacking Chart
        Route::get('/projects/{project}/units', [ProjectUnitController::class, 'index'])->name('projects.units.index');
        Route::post('/projects/{project}/units', [ProjectUnitController::class, 'store'])->name('projects.units.store');
        Route::post('/projects/{project}/units/batch', [ProjectUnitController::class, 'generateBatch'])->name('projects.units.batch');
        Route::patch('/units/{unit}/status', [ProjectUnitController::class, 'updateStatus'])->name('units.update-status');
        Route::put('/units/{unit}', [ProjectUnitController::class, 'update'])->name('units.update');
        Route::delete('/units/{unit}', [ProjectUnitController::class, 'destroy'])->name('units.destroy');
    });

    // Project-specific routes (require project selection)
    Route::middleware(['tenant', 'project'])->group(function () {
        // Inquiries
        Route::get('/inquiries', [InquiryController::class, 'index'])->name('inquiries.index');
        Route::get('/inquiries/export', [InquiryController::class, 'export'])->name('inquiries.export');
        Route::get('/inquiries/create', [InquiryController::class, 'create'])->name('inquiries.create');
        Route::post('/inquiries', [InquiryController::class, 'store'])->name('inquiries.store');
        Route::get('/inquiries/{inquiry}', [InquiryController::class, 'show'])->name('inquiries.show');
        Route::put('/inquiries/{inquiry}', [InquiryController::class, 'update'])->name('inquiries.update');
        Route::patch('/inquiries/{inquiry}/status', [InquiryController::class, 'updateStatus'])->name('inquiries.update-status');
        Route::post('/inquiries/{inquiry}/resend-whatsapp', [InquiryController::class, 'resendWhatsApp'])->name('inquiries.resend-whatsapp');
        Route::post('/inquiries/send-custom-drip', [InquiryController::class, 'sendCustomDrip'])->name('inquiries.send-custom-drip');
        Route::delete('/inquiries/{inquiry}', [InquiryController::class, 'destroy'])->name('inquiries.destroy');

        // WhatsApp Integration Settings & Embedded Signup
        Route::get('/settings/whatsapp', [WhatsAppSettingController::class, 'index'])->name('settings.whatsapp');
        Route::post('/settings/whatsapp', [WhatsAppSettingController::class, 'update']);
        Route::put('/settings/whatsapp', [WhatsAppSettingController::class, 'update'])->name('settings.whatsapp.update');
        Route::post('/settings/whatsapp/test', [WhatsAppSettingController::class, 'testSend'])->name('settings.whatsapp.test');
        Route::post('/settings/whatsapp/embedded-callback', [WhatsAppSettingController::class, 'handleEmbeddedCallback'])->name('settings.whatsapp.embedded-callback');
        Route::get('/settings/whatsapp/callback', [WhatsAppSettingController::class, 'handleOAuthRedirectCallback'])->name('settings.whatsapp.oauth-callback');
        Route::post('/settings/whatsapp/disconnect', [WhatsAppSettingController::class, 'disconnect'])->name('settings.whatsapp.disconnect');
        Route::post('/settings/whatsapp/quick-demo-connect', [WhatsAppSettingController::class, 'quickDemoConnect'])->name('settings.whatsapp.demo-connect');

        // Lead Drip Automation Sequences
        Route::get('/settings/drip', [LeadDripController::class, 'index'])->name('settings.drip');
        Route::post('/settings/drip', [LeadDripController::class, 'store'])->name('settings.drip.store');
        Route::delete('/settings/drip/{step}', [LeadDripController::class, 'destroy'])->name('settings.drip.destroy');
        Route::post('/settings/drip/process-now', [LeadDripController::class, 'processNow'])->name('settings.drip.process-now');
        Route::post('/settings/drip/process-selected', [LeadDripController::class, 'processSelected'])->name('settings.drip.process-selected');
        Route::post('/settings/drip/discard-selected', [LeadDripController::class, 'discardSelected'])->name('settings.drip.discard-selected');
        Route::post('/settings/drip/{log}/process-single', [LeadDripController::class, 'processSingle'])->name('settings.drip.process-single');
        Route::post('/settings/drip/{log}/discard', [LeadDripController::class, 'discardSingle'])->name('settings.drip.discard-single');
        Route::post('/settings/drip/enroll-past', [LeadDripController::class, 'enrollPastLeads'])->name('settings.drip.enroll-past');

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
        Route::post('/forms-qr/custom-fields', [FormQRController::class, 'storeCustomField'])->name('forms-qr.custom-fields.store');
        Route::delete('/forms-qr/custom-fields/{field}', [FormQRController::class, 'deleteCustomField'])->name('forms-qr.custom-fields.delete');
        Route::post('/forms-qr/custom-fields/{field}/toggle', [FormQRController::class, 'toggleCustomField'])->name('forms-qr.custom-fields.toggle');
        Route::post('/forms-qr/generate-inquiry-qr', [FormQRController::class, 'generateInquiryQR'])->name('forms-qr.generate-inquiry-qr');
        Route::get('/forms-qr/inquiry-qr', [FormQRController::class, 'showInquiryQR'])->name('forms-qr.show-inquiry-qr');
        Route::get('/forms-qr/inquiry-qr/download', [FormQRController::class, 'downloadInquiryQR'])->name('forms-qr.download-inquiry-qr');
        Route::get('/forms-qr/brochure-qr', [FormQRController::class, 'brochureQR'])->name('forms-qr.brochure-qr');
        Route::get('/forms-qr/brochure-qr/{brochure}', [FormQRController::class, 'showBrochureQR'])->name('forms-qr.show-brochure-qr');
    });

    // Admin-only management
    Route::middleware(['tenant', 'role:Admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::get('/settings/company', [CompanySettingController::class, 'edit'])->name('settings.company');
        Route::put('/settings/company', [CompanySettingController::class, 'update'])->name('settings.company.update');

        // Social Media & Lead Capture (Admin only, project-wise)
        Route::get('/social-media', [SocialMediaController::class, 'index'])->name('social-media.index');
        Route::get('/social-media/{project}', [SocialMediaController::class, 'showProject'])->name('social-media.project');
        Route::post('/projects/{project}/regenerate-token', [SocialMediaController::class, 'regenerateToken'])->name('projects.regenerate-token');

        // Aliases for backward compatibility
        Route::get('/integrations', [SocialMediaController::class, 'index'])->name('integrations.index');
        Route::get('/integrations/{project}', [SocialMediaController::class, 'showProject'])->name('integrations.project');
    });
});
