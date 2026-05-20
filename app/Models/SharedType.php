<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SharedType extends Model
{
    use HasFactory;

    // Tambahkan baris ini untuk mengizinkan input data
    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];

    /**
     * Relasi ke dokumen publik (SharedDocument)
     */
    public function sharedDocuments()
    {
        return $this->hasMany(SharedDocument::class, 'category_id');
    }
}