<?php

namespace App\Observers;

use App\Models\Document;
use App\Models\ActivityLog;

class DocumentObserver
{
    // Terpicu otomatis saat Draf Baru (store) berhasil masuk ke database
    public function created(Document $document)
    {
        ActivityLog::create([
            // PENGAMAN SEEDER: Jika auth()->id() null (saat seeder berjalan), otomatis pakai ID 1 (Superadmin)
            'user_id' => auth()->id() ?? 1, 
            'document_id' => $document->id,
            'action' => 'create_draft',
            'description' => 'Mengunggah draf LHP baru.',
            'ip_address' => request()->ip() ?? '127.0.0.1'
        ]);
    }

    // Terpicu otomatis saat ada kolom yang di-update (ACC / Revisi / Update Mandiri)
    public function updated(Document $document)
    {
        // Cek apakah yang di-update itu kolom 'status'
        if ($document->wasChanged('status')) {
            $status = $document->status;
            
            // Tentukan label aksi
            $action = $status === 'approved' ? 'approved' : ($status === 'revisi' ? 'revision_requested' : 'status_updated');
            $desc = $status === 'approved' ? 'Inspektur memberikan ACC.' : ($status === 'revisi' ? 'Inspektur meminta revisi.' : 'Status dokumen diperbarui.');

            ActivityLog::create([
                // PENGAMAN SEEDER
                'user_id' => auth()->id() ?? 1,
                'document_id' => $document->id,
                'action' => $action,
                'description' => $desc,
                'ip_address' => request()->ip() ?? '127.0.0.1'
            ]);
        }
    }

    // Terpicu otomatis saat dokumen dihapus (destroy)
    public function deleted(Document $document)
    {
        ActivityLog::create([
            // PENGAMAN SEEDER
            'user_id' => auth()->id() ?? 1,
            'document_id' => $document->id,
            'action' => 'delete_document',
            'description' => 'Menghapus dokumen permanen.',
            'ip_address' => request()->ip() ?? '127.0.0.1'
        ]);
    }
}