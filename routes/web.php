<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ApplicationDocumentController;
use App\Http\Controllers\DalolatnomaController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EImzoAuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
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

    // ─── Notifications ───
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.json');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::get('/notifications/page', [NotificationController::class, 'page'])->name('notifications.page');

    // ─── Profile ───
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::delete('/profile/sessions/{session}', [ProfileController::class, 'terminateSession'])->name('profile.session.terminate');
    Route::post('/profile/sessions/terminate-all', [ProfileController::class, 'terminateAllSessions'])->name('profile.sessions.terminate-all');

    // ─── IT Admin panel ───
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');
        Route::get('/applications', [AdminController::class, 'applications'])->name('applications');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
        Route::patch('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
        Route::post('/users/{user}/force-logout', [AdminController::class, 'forceLogout'])->name('users.force-logout');
        Route::post('/users/{user}/notify', [AdminController::class, 'sendNotification'])->name('users.notify');
        Route::post('/users/{user}/toggle-backup', [AdminController::class, 'toggleBackup'])->name('users.toggle-backup');
        Route::get('/sessions', [AdminController::class, 'sessions'])->name('sessions');
        Route::get('/roles', [AdminController::class, 'roles'])->name('roles');
        Route::post('/approvals/{approval}/reassign', [AdminController::class, 'reassignApproval'])->name('approvals.reassign');
    });
});

// Public verification routes
Route::get('/verify/{qrCode}', [DocumentController::class, 'verify'])->name('documents.verify');
Route::get('/api/verify/{qrCode}', [DocumentController::class, 'verifyApi'])->name('documents.verify.api');

// Dalolatnoma public QR verification
Route::get('/dalo/verify/{qrCode}', [DalolatnomaController::class, 'verify'])->name('dalolatnoma.verify');

