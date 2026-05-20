<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\Division;
use App\Models\DocType;
use App\Models\User;
use App\Models\Document;
use App\Models\SharedType;
use App\Models\SharedDocument;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Master Data
        $roles = ['Admin', 'Auditor', 'Inspektur', 'User'];
        foreach ($roles as $role) { Role::create(['name' => $role]); }

        $divisions = [
            ['code' => 'SEK', 'name' => 'Sekretariat'],
            ['code' => 'IRB-1', 'name' => 'Irban Wilayah I'],
            ['code' => 'IRB-2', 'name' => 'Irban Wilayah II'],
            ['code' => 'IRB-3', 'name' => 'Irban Wilayah III'],
            ['code' => 'INV', 'name' => 'Irban Investigasi']
        ];
        foreach ($divisions as $division) { Division::create($division); }

        $docTypes = [
            ['name_types' => 'LHP Reguler', 'description' => 'Laporan Hasil Pemeriksaan Reguler Tahunan'],
            ['name_types' => 'LHP Khusus', 'description' => 'Laporan Hasil Pemeriksaan Khusus / Kasus'],
            ['name_types' => 'Review RKPD', 'description' => 'Laporan Hasil Review Rencana Kerja OPD'],
            ['name_types' => 'Dokumen PKPT', 'description' => 'Program Kerja Pengawasan Tahunan']
        ];
        foreach ($docTypes as $docType) { DocType::create($docType); }

        // 2. Akun Pengguna
        $defaultPassword = Hash::make('rahasia123');
        User::create(['name' => 'Administrator Sistem', 'email' => 'admin@inspektorat.local', 'password' => $defaultPassword, 'role_id' => 1, 'division_id' => 1]);
        User::create(['name' => 'Budi Auditor, S.E.', 'email' => 'auditor@inspektorat.local', 'password' => $defaultPassword, 'role_id' => 2, 'division_id' => 2]);
        User::create(['name' => 'Inspektur Utama', 'email' => 'inspektur@inspektorat.local', 'password' => $defaultPassword, 'role_id' => 3, 'division_id' => 1]);
        User::create(['name' => 'Admin Dinas Pendidikan', 'email' => 'opd.diknas@pemda.local', 'password' => $defaultPassword, 'role_id' => 4, 'division_id' => 2]);

        // 3. Generate Data Dokumen Pra-Audit (Shared Documents)
        SharedType::factory(5)->create();
        SharedDocument::factory(50)->create(); // 15 dokumen pra-audit

        // 4. Generate Data Dokumen Resmi (beserta Versi File & Log Aktivitas otomatis)
        Document::factory(50)->create(); // 20 dokumen resmi
    }
}