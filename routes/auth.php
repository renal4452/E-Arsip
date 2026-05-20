<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

// Rute untuk user yang BELUM login (Guest)
Route::middleware('guest')->group(function () {
    
    // Menampilkan Halaman Login
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    
    // Memproses Percobaan Login (Throttling otomatis oleh Laravel biasanya aktif di sini)
    Route::post('login', [AuthController::class, 'login']);
    
    /** * Catatan: Halaman 'Register' sengaja tidak dibuat di sini 
     * agar pendaftaran pegawai hanya bisa dilakukan oleh ADMIN melalui UserController.
     */
});

// Rute untuk user yang SUDAH login (Auth)
Route::middleware('auth')->group(function () {
    
    // Memproses Logout (Menghapus Session)
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    
});