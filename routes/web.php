<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmailLogController;
use App\Http\Controllers\ScheduleJobController;
\Log::info('Login Attempt', [
    'username' => 'TestUser',
    'password' => 'TestPassword',
]);
// Public routes
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('auth')->group(function () {
    // Dashboard redirect karo leads pe
    Route::get('/dashboard', [LeadController::class, 'index'])->name('dashboard');

    Route::resource('leads', LeadController::class);
    Route::resource('companies', CompanyController::class)->only(['index', 'show', 'destroy', 'create', 'store', 'edit', 'update']);
    Route::resource('email-logs', EmailLogController::class)->only(['index', 'show', 'destroy', 'create', 'store', 'edit', 'update']);
    Route::resource('schedule-jobs', ScheduleJobController::class)->only(['index', 'show', 'destroy', 'create', 'store', 'edit', 'update']);
});