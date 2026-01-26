<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrochureController;
use App\Http\Controllers\CompanyRegistrationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FormQRController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
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

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [CompanyRegistrationController::class, 'showRegistrationForm'])->name('company.register');
    Route::post('/register', [CompanyRegistrationController::class, 'register']);
});

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
        Route::delete('/inquiries/{inquiry}', [InquiryController::class, 'destroy'])->name('inquiries.destroy');

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
    });

        // Users (Admin only)
        Route::middleware('role:Admin')->group(function () {
            Route::resource('users', UserController::class);
        });
});
