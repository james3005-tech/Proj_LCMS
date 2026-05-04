<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CaseController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\HearingController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

// ── Auth Routes ───────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login']);
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')->middleware('auth');

// ── Authenticated Routes ──────────────────────────────────
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Clients — admin & lawyer only
    Route::middleware('role:admin,lawyer')->group(function () {
        Route::resource('clients', ClientController::class);
    });

    // Cases
    Route::resource('cases', CaseController::class);

    // Client case request routes
    Route::middleware('role:client')->group(function () {
        Route::get('/cases/request/form', [CaseController::class, 'requestForm'])->name('cases.request.form');
        Route::post('/cases/request/submit', [CaseController::class, 'submitRequest'])->name('cases.request.submit');
    });

    // Lawyer accept/deny routes
    Route::middleware('role:admin,lawyer')->group(function () {
        Route::post('/cases/{case}/accept', [CaseController::class, 'accept'])->name('cases.accept');
        Route::post('/cases/{case}/deny',   [CaseController::class, 'deny'])->name('cases.deny');
    });

    // Hearings
    Route::resource('hearings', HearingController::class);

    // Documents
    Route::resource('documents', DocumentController::class);
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])
        ->name('documents.download');
});