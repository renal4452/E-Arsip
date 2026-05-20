<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    /* =========================================================
       GLOBAL POLICIES (Tidak butuh spesifik ID dokumen)
       Dipakai untuk render Menu, Tombol Tambah, & Dashboard
       ========================================================= */

    // Cek boleh akses fitur review secara general? (Misal: lihat menu Action Needed)
    public function reviewAny(User $user): bool
    {
        return in_array($user->role->name, ['Admin', 'Inspektur']);
    }

    // Cek boleh klik tombol "Unggah Baru"?
    public function create(User $user): bool
    {
        // Asumsi: Semua role yang bisa login boleh unggah draf LHP
        // Kalau mau dibatasi, ganti jadi: return in_array($user->role->name, ['Admin', 'User']); dll
        return true; 
    }


    /* =========================================================
       INSTANCE POLICIES (Butuh objek $document spesifik)
       Dipakai untuk render Tombol Aksi di dalam baris Tabel
       ========================================================= */

    // Cek boleh ACC / Minta Revisi dokumen INI?
    public function review(User $user, Document $document): bool
    {
        return in_array($user->role->name, ['Admin', 'Inspektur']);
    }

    // Cek boleh Hapus dokumen INI?
    public function delete(User $user, Document $document): bool
    {
        return $user->role->name === 'Admin';
    }

    // Cek boleh Update Mandiri dokumen INI?
    public function forceUpdate(User $user, Document $document): bool
    {
        $isUploader = $document->latestVersion && $document->latestVersion->uploaded_by === $user->id;
        return in_array($user->role->name, ['Admin', 'Inspektur']) || $isUploader;
    }
}