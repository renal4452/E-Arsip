<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// ❌ HAPUS: use Inertia\Inertia; 

class AuthController extends Controller
{
    use ApiResponse; 

    /**
     * Menampilkan halaman form login (Kembali pakai Blade!)
     */
    public function showLoginForm()
    {
        // Kalau udah login, tendang langsung ke dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        // ✅ UBAH: Panggil file resources/views/auth/login.blade.php
        return view('auth.login');
    }

    /**
     * Memproses data login
     */
    public function login(LoginRequest $request)
    {
        // Validasi, brute-force, dan cek is_active selesai di sini
        $request->authenticate();

        // Keamanan: Mencegah Session Fixation
        $request->session()->regenerate();

        // Redirect biasa bawaan Laravel
        return redirect()->intended('dashboard');
    }

    /**
     * Memproses Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Kembali ke halaman utama / form login
        return redirect('/');
    }
}