<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocType extends Model
{
    protected $fillable = [
        'name_types',
        'description',
        'is_active'
    ];

    /**
     * Casts: Memastikan tipe data yang keluar dari database sesuai
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relasi: Satu Tipe Dokumen memiliki banyak Dokumen LHP
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}