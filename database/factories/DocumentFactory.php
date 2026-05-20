<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\DocVersion;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition()
    {
        $status = $this->faker->randomElement(['pending', 'revisi', 'approved']);
        $currentVersion = $this->faker->numberBetween(1, 3);
        
        // Dokumen dibuat secara acak dalam rentang 3 tahun terakhir
        $createdAt = $this->faker->dateTimeBetween('-3 years', 'now');
        
        // Logika untuk tanggal approval (jika statusnya approved)
        // Harus dipastikan approval terjadi setelah dokumen dibuat
        $approveAt = $status === 'approved' 
            ? $this->faker->dateTimeBetween($createdAt, 'now') 
            : null;

        return [
            // Gunakan tahun dari tanggal yang di-generate agar nomor dokumen sinkron dengan tahun pembuatan
            'no_doc' => $this->faker->unique()->numerify('DOC/INSP/' . $createdAt->format('Y') . '/####'),
            'title' => 'Laporan ' . $this->faker->sentence(4),
            'doc_type_id' => $this->faker->numberBetween(1, 4),
            'division_id' => $this->faker->numberBetween(1, 5),
            'status' => $status,
            'current_version' => $currentVersion,
            'auditor_id' => in_array($status, ['revisi', 'approved']) ? 2 : null,
            'auditor_note' => $status === 'revisi' ? 'Mohon lengkapi lampiran dokumen.' : null,
            'approve_at' => $approveAt,
            'created_at' => $createdAt,
            'updated_at' => $approveAt ?? $this->faker->dateTimeBetween($createdAt, 'now'),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Document $document) {
            $uploaderId = 4; // Asumsi diunggah oleh Admin Dinas Pendidikan (OPD)
            $reviewerId = 2; // Budi Auditor

            // 1. Generate Versi File Berurutan & Log Aktivitas
            for ($i = 1; $i <= $document->current_version; $i++) {
                $timestamp = $document->created_at->addDays($i)->timestamp;
                $slugTitle = Str::slug($document->title);
                
                // Buat fisik file virtual di tabel doc_versions
                DocVersion::factory()->create([
                    'doc_id' => $document->id,
                    'version_number' => $i,
                    'file_path' => "documents/v{$i}_{$timestamp}_{$slugTitle}_v{$i}.pdf",
                    'uploaded_by' => $uploaderId,
                ]);

                // Buat Log Upload
                DB::table('activity_logs')->insert([
                    'user_id' => $uploaderId,
                    'document_id' => $document->id,
                    'action' => $i === 1 ? 'upload' : 'upload_revision',
                    'description' => $i === 1 ? 'Upload draf awal versi 1' : "Mengunggah revisi versi {$i}",
                    'ip_address' => '127.0.0.1',
                    'created_at' => $document->created_at->addDays($i)->addMinutes(5),
                ]);

                // Buat Log Request Revisi (Jika dokumen masih akan direvisi lagi ke depannya)
                if ($i < $document->current_version || $document->status === 'revisi') {
                    DB::table('activity_logs')->insert([
                        'user_id' => $reviewerId,
                        'document_id' => $document->id,
                        'action' => 'request_revision',
                        'description' => 'Reviewer meminta perbaikan dokumen',
                        'ip_address' => '127.0.0.1',
                        'created_at' => $document->created_at->addDays($i)->addHours(2),
                    ]);
                }
            }

            // 2. Jika Status Approved, tambahkan log persetujuan
            if ($document->status === 'approved') {
                DB::table('activity_logs')->insert([
                    'user_id' => $reviewerId,
                    'document_id' => $document->id,
                    'action' => 'approve',
                    'description' => 'Dokumen telah disetujui (ACC)',
                    'ip_address' => '127.0.0.1',
                    'created_at' => $document->approve_at,
                ]);
            }
        });
    }
}