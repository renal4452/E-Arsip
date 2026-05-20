<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\ActivityLog;
use App\Http\Requests\UpdatePasswordRequest;

// ❌ HAPUS: use App\Traits\ApiResponse;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil user dengan statistik & riwayat
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // 1. Ambil Statistik dari Model User (Bersih!)
        $stats = $user->getDocumentStats();

        // 2. Riwayat Aktivitas
        $myLogs = ActivityLog::where('user_id', $user->id)
                    ->with('document') 
                    ->latest()
                    ->take(5)
                    ->get();

        // ✅ UBAH: Langsung lempar ke view Blade
        return view('profile.index', compact('user', 'stats', 'myLogs'));
    }

    /**
     * Memproses pembaruan password
     */
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Validasi & Cek kecocokan password lama sudah diurus 100% oleh Form Request!
        
        // Update password baru. 
        // (Log Aktivitas akan otomatis dicatat oleh UserObserver di belakang layar!)
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        // ✅ UBAH: Redirect kembali dengan session flash message
        return back()->with('success', 'Kata sandi berhasil diperbarui.');
    }
}