<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmailLogController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::resource('leads', LeadController::class);
    Route::resource('companies', CompanyController::class)->only(['index', 'show', 'destroy']);
    Route::resource('email-logs', EmailLogController::class)->only(['index', 'show', 'destroy']);
});