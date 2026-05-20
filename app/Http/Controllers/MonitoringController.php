<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Document;

// ❌ HAPUS: use App\Traits\ApiResponse;

class MonitoringController extends Controller
{
    /**
     * Kunci Gerbang: Biasanya menu Monitoring khusus untuk pimpinan/admin
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_if(!in_array($request->user()->role->name, ['Admin', 'Inspektur']), 403, 'Akses Ditolak. Hanya untuk Admin dan Inspektur.');
            return $next($request);
        });
    }

    /**
     * Menampilkan tabel monitoring dengan Pagination
     */
    public function index(Request $request): View
    {
        // Panggil scope filterMonitoring() yang baru saja kita buat di Model
        $documents = Document::with(['division', 'docType', 'auditor'])
            ->filterMonitoring($request->only(['status', 'division']))
            ->latest()
            ->paginate(50); 

        // ✅ UBAH: Langsung lempar ke view Blade
        return view('monitoring.index', compact('documents'));
    }   
        
    /**
     * Menampilkan halaman khusus cetak (SINKRON DENGAN FILTER)
     */
    public function print(Request $request): View
    {
        // Data Document disinkronkan dengan filter yang sama
        $documents = Document::with(['division', 'docType', 'auditor'])
            ->filterMonitoring($request->only(['status', 'division']))
            ->latest()
            ->get(); 

        // ✅ UBAH: Langsung lempar ke view Blade untuk cetak
        return view('monitoring.print', compact('documents'));
    }
}