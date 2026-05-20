<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class LogActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Hanya catat log jika user sudah login dan rute memiliki nama
        if (Auth::check() && $request->route() && $request->route()->getName()) {
            
            $routeName = $request->route()->getName();
            
            // --- KAMUS TERJEMAHAN RUTE KE AKTIVITAS ---
            $loggableRoutes = [
                // Manajemen LHP
                'documents.store'           => ['action' => 'upload',           'desc' => 'Mengunggah draf dokumen pertama kali'],
                'documents.approve'         => ['action' => 'approve',          'desc' => 'Memberikan persetujuan (ACC) dokumen'],
                'documents.revisi'          => ['action' => 'request_revision', 'desc' => 'Meminta perbaikan (revisi) ke pengunggah'],
                'documents.update.revision' => ['action' => 'upload_revision',  'desc' => 'Mengirimkan ulang file yang sudah direvisi'],
                'documents.update'          => ['action' => 'update',           'desc' => 'Memperbarui detail informasi dokumen'],
                'documents.destroy'         => ['action' => 'delete',           'desc' => 'Menghapus dokumen secara permanen'],
                'documents.download'        => ['action' => 'download',         'desc' => 'Mengunduh berkas fisik LHP'],
                
                // Rute Baru (Pembaruan & TTE)
                'documents.force_update'    => ['action' => 'force_update',     'desc' => 'Melakukan pembaruan/unggah ulang berkas secara mandiri'],
                'documents.upload_final'    => ['action' => 'upload_tte',       'desc' => 'Mengunggah berkas final bertanda tangan (TTE)'],
                
                // Ruang Berbagi
                'shared_documents.store'    => ['action' => 'upload_shared',    'desc' => 'Membagikan dokumen ke ruang publik'],
                'shared_documents.download' => ['action' => 'download_shared',  'desc' => 'Mengunduh dokumen dari ruang publik'],
                'shared_documents.destroy'  => ['action' => 'delete_shared',    'desc' => 'Menarik dokumen dari ruang publik'],
                
                // Manajemen User & Akun
                'profile.password.update'   => ['action' => 'update_password',  'desc' => 'Memperbarui kata sandi akun'],
                'users.store'               => ['action' => 'create_user',      'desc' => 'Menambahkan pegawai baru ke sistem'],
                'users.update'              => ['action' => 'update_user',      'desc' => 'Memperbarui data pegawai'],
            ];

            if (array_key_exists($routeName, $loggableRoutes)) {
                
                // ✅ BUG FIXED: Ekstrak ID dari Route Model Binding
                $docParam = $request->route('document') ?? $request->route('shared_document') ?? $request->route('id');
                $documentId = is_object($docParam) ? $docParam->id : $docParam;
                
                // PENGECUALIAN: Jangan log jika aksi download gagal
                if (in_array($loggableRoutes[$routeName]['action'], ['download', 'download_shared']) && $response->status() != 200) {
                    return $response; 
                }

                ActivityLog::create([
                    'user_id'     => Auth::id(),
                    'action'      => $loggableRoutes[$routeName]['action'],
                    'description' => $loggableRoutes[$routeName]['desc'],
                    'ip_address'  => $request->ip(),
                    'document_id' => $documentId
                ]);
            }
        }

        return $response;
    }
}