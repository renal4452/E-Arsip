<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\View\View;

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

        // 2. Ambil 5 Dokumen Terbaru dengan relasi
        $recentDocs = Document::with(['division', 'docType'])
            ->latest()
            ->take(5)
            ->get();

        // 3. Return ke Blade
        return view('dashboard.index', compact('stats', 'recentDocs'));
    }
}