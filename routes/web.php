<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EImzoAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home redirect
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// E-IMZO Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [EImzoAuthController::class, 'showLogin'])->name('login');
    Route::post('/eimzo/authenticate', [EImzoAuthController::class, 'authenticate'])->name('eimzo.authenticate');
});

// E-IMZO API Routes (accessible without auth for challenge)
Route::get('/frontend/challenge', [EImzoAuthController::class, 'getChallenge'])->name('eimzo.challenge');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [EImzoAuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Documents
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/create', [DocumentController::class, 'create'])->name('documents.create');
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
    Route::post('/documents/{document}/sign', [DocumentController::class, 'sign'])->name('documents.sign');
    Route::get('/documents/{document}/data', [DocumentController::class, 'getDocumentData'])->name('documents.data');
});

// Public verification routes (QR code scan)
Route::get('/verify/{qrCode}', [DocumentController::class, 'verify'])->name('documents.verify');
Route::get('/api/verify/{qrCode}', [DocumentController::class, 'verifyApi'])->name('documents.verify.api');
