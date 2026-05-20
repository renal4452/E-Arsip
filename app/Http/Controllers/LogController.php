<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Traits\ApiResponse;

class LogController extends Controller
{
    use ApiResponse; // API-Ready

    /**
     * Menampilkan halaman Log (Mendukung React & Blade)
     */
    public function index(Request $request)
    {
        // Ajaib! Kita cukup memanggil method filter() dari Model.
        $logs = ActivityLog::with(['user.role', 'document'])
                    ->filter($request->only(['search', 'action_type', 'start_date', 'end_date']))
                    ->latest()
                    ->paginate(50); 

        if ($request->wantsJson()) {
            return $this->successResponse('Data log berhasil diambil', $logs);
        }

        return view('logs.index', compact('logs'));
    }
    
    /**
     * Menampilkan data tanpa pagination untuk di-Print / Export
     */
    public function print(Request $request)
    {
        // Logika yang sama persis, hanya beda di get()
        $logs = ActivityLog::with(['user.role', 'document'])
                    ->filter($request->only(['search', 'action_type', 'start_date', 'end_date']))
                    ->latest()
                    ->get();

        if ($request->wantsJson()) {
            return $this->successResponse('Data cetak log berhasil diambil', $logs);
        }

        return view('logs.print', compact('logs'));
    }
}