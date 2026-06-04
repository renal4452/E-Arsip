<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class DocVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'doc_id',
        'version_number',
        'file_path',
        'file_size',
        'uploaded_by' // Kolom ini yang menghubungkan ke tabel users
    ];

    /**
     * Relasi ke User yang mengunggah versi ini
     */
    public function uploader()
    {
        // Kita arahkan ke model User menggunakan foreign key 'uploaded_by'
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function user()
    {
        // Sesuaikan 'uploaded_by' dengan nama kolom ID user di tabel document_versions Anda.
        // Jika di database namanya 'user_id', Anda cukup menulis: return $this->belongsTo(User::class);
        return $this->belongsTo(User::class, 'uploaded_by');
    }
    /**
     * Relasi kembali ke dokumen induk
     */
    public function document()
    {
        return $this->belongsTo(Document::class, 'doc_id');
    }
}