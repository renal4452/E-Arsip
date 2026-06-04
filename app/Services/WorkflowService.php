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
            // Menggunakan properti langsung untuk bypass proteksi $fillable
            $document->status = 'approved';
            $document->auditor_id = $user->id;
            $document->approve_at = now();
            $document->save();

            // Dibungkus try-catch agar jika notifikasi gagal, status database TETAP tersimpan
            try {
                $this->sendNotificationToUploader($document, 'ACC');
            } catch (\Exception $e) {
                logger('Gagal mengirim notifikasi ACC: ' . $e->getMessage());
            }
            
            return $document;
        });
    }

    public function processRejection($document, $note, $user)
    {
        return DB::transaction(function () use ($document, $note, $user) {
            // Menggunakan properti langsung untuk bypass proteksi $fillable
            $document->status = 'revisi';
            $document->auditor_note = $note;
            $document->save();

            // Dibungkus try-catch agar jika notifikasi gagal, status database TETAP tersimpan
            try {
                $this->sendNotificationToUploader($document, 'Revisi');
            } catch (\Exception $e) {
                logger('Gagal mengirim notifikasi Revisi: ' . $e->getMessage());
            }

            return $document;
        });
    }

    private function sendNotificationToUploader($document, $action)
    {
        $firstVersion = DocVersion::where('doc_id', $document->id)->orderBy('version_number', 'asc')->first();
        if (!$firstVersion || !$firstVersion->user) return;

        if ($action === 'ACC') {
            // Amankan metode notify dengan try-catch
            try {
                $firstVersion->user->notify(new SystemNotification($document, 'Dokumen Anda telah Di-ACC oleh Inspektur.', 'success'));
            } catch (\Exception $e) {
                logger('Gagal kirim SystemNotification ACC: ' . $e->getMessage());
            }

            try {
                Mail::to($firstVersion->user->email)->send(new DocumentNotification($document, 'Selamat! Dokumen Anda telah disetujui.', 'success'));
            } catch (\Exception $e) {}
        } else {
            // Amankan metode notify dengan try-catch
            try {
                $firstVersion->user->notify(new SystemNotification($document, 'Dokumen Anda membutuhkan revisi.', 'danger'));
            } catch (\Exception $e) {
                logger('Gagal kirim SystemNotification Revisi: ' . $e->getMessage());
            }

            try {
                Mail::to($firstVersion->user->email)->send(new DocumentNotification($document, 'Dokumen Anda membutuhkan perbaikan.', 'danger'));
            } catch (\Exception $e) {}
        }
    }
}