<?php
namespace App\Services;

use App\Models\Document;
use App\Models\DocVersion;
use App\Notifications\SystemNotification;
use App\Mail\DocumentNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class WorkflowService
{
    public function processApproval($document, $user)
    {
        return DB::transaction(function () use ($document, $user) {
            $document->update([
                'status' => 'approved',
                'auditor_id' => $user->id,
                'approve_at' => now()
            ]);

            $this->sendNotificationToUploader($document, 'ACC');
            
            return $document;
        });
    }

    public function processRejection($document, $note, $user)
    {
        return DB::transaction(function () use ($document, $note, $user) {
            $document->update([
                'status' => 'revisi',
                'auditor_note' => $note
            ]);

            $this->sendNotificationToUploader($document, 'Revisi');

            return $document;
        });
    }

    private function sendNotificationToUploader($document, $action)
    {
        $firstVersion = DocVersion::where('doc_id', $document->id)->orderBy('version_number', 'asc')->first();
        if (!$firstVersion || !$firstVersion->user) return;

        if ($action === 'ACC') {
            $firstVersion->user->notify(new SystemNotification($document, 'Dokumen Anda telah Di-ACC oleh Inspektur.', 'success'));
            try {
                Mail::to($firstVersion->user->email)->send(new DocumentNotification($document, 'Selamat! Dokumen Anda telah disetujui.', 'success'));
            } catch (\Exception $e) {}
        } else {
            $firstVersion->user->notify(new SystemNotification($document, 'Dokumen Anda membutuhkan revisi.', 'danger'));
            try {
                Mail::to($firstVersion->user->email)->send(new DocumentNotification($document, 'Dokumen Anda membutuhkan perbaikan.', 'danger'));
            } catch (\Exception $e) {}
        }
    }
}