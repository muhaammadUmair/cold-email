<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmailLogController;
use App\Http\Controllers\LeadAutomationFlowController;
use App\Http\Controllers\LeadGmailEmailController;
use App\Http\Controllers\ScheduleJobController;
use App\Http\Controllers\WebsiteResearchController;
\Log::info('Login Attempt', [
    'username' => 'TestUser',
    'password' => 'TestPassword',
]);
// Public routes
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::match(['GET', 'POST'], '/external/company-research/create', [WebsiteResearchController::class, 'createCompanyResearch'])
    ->name('external.company-research.create')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);

Route::match(['GET', 'POST'], '/external/leads/process-next-unsent-email', [LeadAutomationFlowController::class, 'processNextUnsentLead'])
    ->name('external.leads.process-next-unsent-email')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);

Route::match(['GET', 'POST'], '/external/leads/process-next-company-research', [LeadAutomationFlowController::class, 'processNextPendingCompanyResearch'])
    ->name('external.leads.process-next-company-research')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);

Route::match(['GET', 'POST'], '/leads/{lead}/send-gmail-email', LeadGmailEmailController::class)
    ->name('leads.send-gmail-email')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);

// Protected routes
Route::middleware('auth')->group(function () {
    // Dashboard redirect karo leads pe
    Route::get('/dashboard', [LeadController::class, 'index'])->name('dashboard');

    Route::resource('leads', LeadController::class);
    Route::resource('companies', CompanyController::class)->only(['index', 'show', 'destroy', 'create', 'store', 'edit', 'update']);
    Route::resource('email-logs', EmailLogController::class)->only(['index', 'show', 'destroy', 'create', 'store', 'edit', 'update']);
    Route::resource('schedule-jobs', ScheduleJobController::class)->only(['index', 'show', 'destroy', 'create', 'store', 'edit', 'update']);
    Route::post('/website-research/discover-pages', [WebsiteResearchController::class, 'discoverPages'])->name('website-research.discover-pages');
});