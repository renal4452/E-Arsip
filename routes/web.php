<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController; // ✅ IMPORT BARU
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SharedDocumentController;
use App\Http\Controllers\CategoryManagerController;

/*
|--------------------------------------------------------------------------
| Web Routes - Inspektorat Document Management System
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | 1. AREA UMUM (Semua Role)
    |--------------------------------------------------------------------------
    */

    // ✅ DIPERBAIKI: Menggunakan DashboardController
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'index'])
        ->name('profile.index');

    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password.update');

    /*
    | Notifications
    */
    Route::post('/notifications/mark-as-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.read_all');

    /*
    | Shared Documents (read & download)
    */
    Route::get('/shared-documents', [SharedDocumentController::class, 'index'])
        ->name('shared_documents.index');

    Route::get('/shared-documents/{sharedDocument}/download', [SharedDocumentController::class, 'download'])
        ->name('shared_documents.download');

    /*
    |--------------------------------------------------------------------------
    | 2. DOCUMENT OPERATIONS (User, Admin, Auditor, Inspektur)
    |--------------------------------------------------------------------------
    */

    Route::middleware(['role:User,Admin,Auditor,Inspektur'])->group(function () {

        Route::get('/documents/create', [DocumentController::class, 'create'])
            ->name('documents.create');

        Route::post('/documents', [DocumentController::class, 'store'])
            ->name('documents.store');

        Route::get('/documents/{document}/revisi', [DocumentController::class, 'revisiForm'])
            ->name('documents.revisi.form');

        Route::patch('/documents/{document}/revision', [DocumentController::class, 'updateRevision'])
            ->name('documents.update.revision');

        Route::post('/documents/{document}/force-update', [DocumentController::class, 'forceUpdateFile'])
            ->name('documents.force_update');

        /*
        | Shared upload
        */
        Route::get('/shared-documents/create', [SharedDocumentController::class, 'create'])
            ->name('shared_documents.create');

        Route::post('/shared-documents', [SharedDocumentController::class, 'store'])
            ->name('shared_documents.store');
    });

    /*
    |--------------------------------------------------------------------------
    | 3. DOCUMENT VIEW (ALL ROLES)
    |--------------------------------------------------------------------------
    */

    Route::get('/documents', [DocumentController::class, 'index'])
        ->name('documents.index');

    Route::get('/documents/{document}', [DocumentController::class, 'show'])
        ->name('documents.show');

    // Pastikan {version} sinkron dengan controller lu
    Route::get('/documents/download/{version}', [DocumentController::class, 'download'])
        ->name('documents.download');

    /*
    |--------------------------------------------------------------------------
    | 4. VERIFICATION (Auditor, Admin, Inspektur)
    |--------------------------------------------------------------------------
    */

    Route::middleware(['role:Auditor,Admin,Inspektur'])->group(function () {

        Route::post('/documents/{document}/approve', [DocumentController::class, 'approve'])
            ->name('documents.approve');

        Route::post('/documents/{document}/revision', [DocumentController::class, 'storeRevisi'])
            ->name('documents.revisi');

        Route::post('/documents/{document}/upload-final', [DocumentController::class, 'uploadFinalDocument'])
            ->name('documents.upload_final');
    });

    /*
    |--------------------------------------------------------------------------
    | 5. ADMIN AREA (STRICT)
    |--------------------------------------------------------------------------
    */

    // ✅ DIPERBAIKI: Menghapus ->name('admin.') agar sinkron dengan file UI
    Route::middleware(['role:Admin'])->prefix('admin')->group(function () {

        // Users
        Route::resource('users', UserController::class);

        // Categories
        Route::get('/categories', [CategoryManagerController::class, 'index'])
            ->name('categories.index');

        Route::post('/categories', [CategoryManagerController::class, 'store'])
            ->name('categories.store');

        Route::patch('/categories/{id}/toggle', [CategoryManagerController::class, 'toggleStatus'])
            ->name('categories.toggle');

        Route::delete('/categories/{id}', [CategoryManagerController::class, 'destroy'])
            ->name('categories.destroy');

        // Logs
        Route::get('/logs', [LogController::class, 'index'])
            ->name('logs.index');

        Route::get('/logs/print', [LogController::class, 'print'])
            ->name('logs.print');

        // Shared document delete (Membagikan route model binding yang benar)
        Route::delete('/shared-documents/{sharedDocument}', [SharedDocumentController::class, 'destroy'])
            ->name('shared_documents.destroy');
    });

});

require __DIR__.'/auth.php';