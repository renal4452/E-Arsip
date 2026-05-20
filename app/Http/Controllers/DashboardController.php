<?php

namespace App\Http\Controllers;

use App\Models\Document; // Wajib ada
use Illuminate\Http\Request;
use Illuminate\View\View; // ✅ TAMBAHKAN INI (Opsional tapi best practice)

// ❌ HAPUS: use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(): View
    {
        // 1. Hitung Statistik
        $stats = [
            'total' => Document::count(),
            'pending' => Document::where('status', 'pending')->count(),
            'revisi' => Document::where('status', 'revisi')->count(),
            'approved' => Document::where('status', 'approved')->count(),
        ];

        // 2. Ambil 5 Dokumen Terbaru (LHP) 
        // ✅ Eager Loading ditambahkan biar ngerender di Blade nanti ngebut
        $recentDocs = Document::with(['division', 'docType'])->latest()->take(5)->get();

        // 3. Render ke Blade (resources/views/dashboard.blade.php)
        // ✅ UBAH: Pakai view() dan compact()
        return view('dashboard.index', compact('stats', 'recentDocs'));
    }
}