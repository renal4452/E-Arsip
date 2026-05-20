<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Division extends Model
{
    protected $fillable = ['name', 'code', 'description'];

    /**
     * Relasi: Satu Divisi memiliki banyak User (Pegawai)
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relasi: Satu Divisi memiliki banyak Dokumen LHP
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Relasi: Satu Divisi memiliki banyak Dokumen Publik (Shared)
     */
    public function sharedDocuments(): HasMany
    {
        return $this->hasMany(SharedDocument::class);
    }
}