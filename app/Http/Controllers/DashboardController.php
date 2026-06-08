<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // Cek apakah user adalah Superadmin (1) atau Inspektur (3)
        $isExclusiveRole = in_array($user->role_id, [1, 3]);

        // 1. Hitung Statistik (Disaring jika bukan Superadmin/Inspektur)
        $stats = [
            'total' => Document::when(!$isExclusiveRole, function ($query) use ($user) {
                            return $query->where('division_id', $user->division_id);
                       })->count(),
                       
            'pending' => Document::where('status', 'pending')
                       ->when(!$isExclusiveRole, function ($query) use ($user) {
                            return $query->where('division_id', $user->division_id);
                       })->count(),
                       
            'revisi' => Document::where('status', 'revisi')
                       ->when(!$isExclusiveRole, function ($query) use ($user) {
                            return $query->where('division_id', $user->division_id);
                       })->count(),
                       
            'approved' => Document::where('status', 'approved')
                       ->when(!$isExclusiveRole, function ($query) use ($user) {
                            return $query->where('division_id', $user->division_id);
                       })->count(),
        ];

        // 2. Ambil 5 Dokumen Terbaru (Disaring jika bukan Superadmin/Inspektur)
        $recentDocs = Document::with(['division', 'docType'])
            ->when(!$isExclusiveRole, function ($query) use ($user) {
                return $query->where('division_id', $user->division_id);
            })
            ->latest()
            ->take(5)
            ->get();

        // 3. Return ke Blade
        return view('dashboard.index', compact('stats', 'recentDocs'));
    }
}