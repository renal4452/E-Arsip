<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_doc',
        'title',
        'doc_type_id',
        'division_id', 
        'status',
        'current_version',
        'auditor_id',
        'auditor_note',
        'approve_at'
    ];

    // Cast attributes untuk memudahkan manipulasi tanggal di Blade
    protected $casts = [
        'approve_at' => 'datetime',
    ];

    public function docType(): BelongsTo
    {
        return $this->belongsTo(DocType::class, 'doc_type_id');
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function auditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocVersion::class, 'doc_id');
    }

    // PENTING: Untuk mengambil versi terbaru secara efisien (Eager Loading)
    public function latestVersion(): HasOne
    {
        return $this->hasOne(DocVersion::class, 'doc_id')->latestOfMany();
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isConfidential(): bool
    {
        return $this->docType && $this->docType->name_types === 'Rahasia';
    }

    public function isLocked(): bool
    {
        return $this->isApproved() || $this->isConfidential();
    }

    /**
     * LOCAL SCOPE: Untuk filter halaman Monitoring
     */
    public function scopeFilterMonitoring(Builder $query, array $filters): void
    {
        $query->when($filters['status'] ?? false, function ($q, $status) {
            $q->where('status', $status);
        });

        $query->when($filters['division'] ?? false, function ($q, $division) {
            $q->where('division_id', $division);
        });
    }
}