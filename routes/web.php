<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ApplicationDocumentController;
use App\Http\Controllers\DalolatnomaController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EImzoAuthController;
use App\Http\Controllers\WorkflowController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ─── Public: homepage + citizen application form (no auth required) ───
Route::get('/', [ApplicationController::class, 'publicHome'])->name('home');
Route::get('/home', fn() => redirect('/'));
Route::post('/apply', [ApplicationController::class, 'publicStore'])->name('apply');
Route::get('/apply/success/{number}', [ApplicationController::class, 'publicSuccess'])->name('apply.success');
Route::get('/apply/track/{number}', [ApplicationController::class, 'publicTrack'])->name('apply.track');
Route::get('/apply/track', [ApplicationController::class, 'publicTrackSearch'])->name('apply.track.search');

// ─── E-IMZO Auth ───
Route::middleware('guest')->group(function () {
    Route::get('/login', [EImzoAuthController::class, 'showLogin'])->name('login');
    Route::post('/eimzo/authenticate', [EImzoAuthController::class, 'authenticate'])->name('eimzo.authenticate');
    // Password login (for commission members & staff)
    Route::post('/login/password', [EImzoAuthController::class, 'loginWithPassword'])->name('login.password');
});

// Challenge endpoint (no auth needed for PKCS7 creation)
Route::get('/frontend/challenge', [EImzoAuthController::class, 'getChallenge'])->name('eimzo.challenge');

// ─── Authenticated (staff/admin) ───
Route::middleware('auth')->group(function () {
    Route::post('/logout', [EImzoAuthController::class, 'logout'])->name('logout');

    // Staff/admin dashboard — consumers are redirected to /my-applications
    Route::get('/dashboard', function () {
        if (auth()->user()->isConsumer()) {
            return redirect()->route('my-applications');
        }
        return view('dashboard');
    })->name('dashboard');

    // Citizen portal — shows own applications
    Route::get('/my-applications', [ApplicationController::class, 'myApplications'])->name('my-applications');

    // Applications — inbox before resource to avoid route conflict
    Route::get('/applications/inbox', [ApplicationController::class, 'inbox'])->name('applications.inbox');
    Route::resource('applications', ApplicationController::class)->only(['index', 'create', 'store', 'show']);

    // Application document upload/delete
    Route::post('/applications/{application}/documents', [ApplicationDocumentController::class, 'store'])
        ->name('applications.documents.store');
    Route::delete('/application-documents/{document}', [ApplicationDocumentController::class, 'destroy'])
        ->name('applications.documents.destroy');

    // Workflow approve/reject
    Route::post('/workflow/approvals/{approval}/approve', [WorkflowController::class, 'approve'])
        ->name('workflow.approve');

    // Dalolatnoma E-IMZO signing (commission members only)
    Route::post('/applications/{application}/dalolatnoma/sign', [DalolatnomaController::class, 'sign'])
        ->name('dalolatnoma.sign');
});

// Public verification routes
Route::get('/verify/{qrCode}', [DocumentController::class, 'verify'])->name('documents.verify');
Route::get('/api/verify/{qrCode}', [DocumentController::class, 'verifyApi'])->name('documents.verify.api');

// Dalolatnoma public QR verification
Route::get('/dalo/verify/{qrCode}', [DalolatnomaController::class, 'verify'])->name('dalolatnoma.verify');

