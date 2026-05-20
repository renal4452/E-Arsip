<?php
namespace App\Services;

use App\Models\Document;
use App\Models\DocVersion;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    /**
     * Logika untuk Upload Draf Baru (Fungsi Store)
     */
    public function createNewDocument($data, $user)
    {
        return DB::transaction(function () use ($data, $user) {
            // 1. Simpan tabel Document
            $document = Document::create([
                'no_doc' => $data['no_doc'],
                'title' => $data['title'],
                'doc_type_id' => $data['doc_type_id'],
                'division_id' => $user->division_id,
                'status' => 'pending',
                'current_version' => 1
            ]);

            // 2. Upload fisik PDF
            $file = $data['file'];
            $fileName = 'v1_' . time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('documents', $fileName);

            // 3. Simpan tabel DocVersion
            DocVersion::create([
                'doc_id' => $document->id,
                'version_number' => 1,
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'uploaded_by' => $user->id
            ]);

            // 4. Kirim Lonceng ke Atasan
            $this->notifyInspectors($document, 'Ada dokumen LHP baru yang menunggu persetujuan (ACC).', 'info');

            return $document;
        });
    }

    /**
     * Logika untuk Upload Revisi (Fungsi updateRevision & forceUpdate)
     */
    public function uploadNewVersion($document, $file, $user, $status, $note)
    {
        return DB::transaction(function () use ($document, $file, $user, $status, $note) {
            $nextVersion = $document->current_version + 1;
            
            $fileName = 'v' . $nextVersion . '_' . time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('documents', $fileName);

            DocVersion::create([
                'doc_id' => $document->id,
                'version_number' => $nextVersion,
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'uploaded_by' => $user->id
            ]);

            $document->update([
                'status' => $status,
                'current_version' => $nextVersion,
                'auditor_note' => $note
            ]);

            return $document;
        });
    }

    private function notifyInspectors($document, $message, $type)
    {
        $inspectors = User::whereHas('role', function($q) {
            $q->whereIn('name', ['Inspektur', 'Admin']);
        })->get();

        foreach ($inspectors as $inspector) {
            $inspector->notify(new SystemNotification($document, $message, $type));
        }
    }
    
// Fungsi untuk Upload File Final (TTE)
    public function uploadFinalTTE($document, $file, $user)
    {
        return DB::transaction(function () use ($document, $file, $user) {
            $nextVersion = $document->current_version + 1;
            $fileName = 'v' . $nextVersion . '_FINAL_TTE_' . time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('documents', $fileName);

            DocVersion::create([
                'doc_id' => $document->id,
                'version_number' => $nextVersion,
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'uploaded_by' => $user->id 
            ]);

            $document->update([
                'current_version' => $nextVersion,
                'auditor_note' => 'Dokumen Final / Bertanda Tangan Digital telah diunggah.'
            ]);

            // Kirim notifikasi ke pengunggah pertama
            $firstVersion = DocVersion::where('doc_id', $document->id)->orderBy('version_number', 'asc')->first();
            if ($firstVersion && $firstVersion->user) {
                $firstVersion->user->notify(new SystemNotification($document, 'Dokumen Final Ber-TTE telah diunggah.', 'success'));
            }

            return $document;
        });
    }

    // Fungsi untuk Hapus Dokumen
    public function deleteDocument($document)
    {
        return DB::transaction(function () use ($document) {
            $versions = DocVersion::where('doc_id', $document->id)->get();
            foreach ($versions as $version) {
                if (Storage::exists($version->file_path)) {
                    Storage::delete($version->file_path);
                }
            }
            $document->delete();
        });
    } 
}