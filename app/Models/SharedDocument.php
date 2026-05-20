<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class SharedDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category_id', // Pastikan ini sudah masuk fillable
        'description',
        'file_path',
        'division_id',
        'user_id'
    ];

    /**
     * Relasi ke Kategori Dinamis (SharedType)
     * Menghubungkan category_id di tabel ini ke id di tabel shared_types
     */
    public function category()
    {
        return $this->belongsTo(SharedType::class, 'category_id');
    }

    /**
     * Relasi ke Divisi
     */
    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    /**
     * Relasi ke User pengunggah
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeFilter(Builder $query, array $filters)
    {
        $query->when($filters['category_id'] ?? false, fn($q, $category) => $q->where('category_id', $category));

        $query->when($filters['search'] ?? false, function($q, $search) {
            $q->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        });

        $query->when($filters['year'] ?? false, fn($q, $year) => $q->whereYear('created_at', $year));

        $query->when(($filters['start_date'] ?? false) && ($filters['end_date'] ?? false), function($q) use ($filters) {
            $start = Carbon::parse($filters['start_date'])->startOfDay();
            $end = Carbon::parse($filters['end_date'])->endOfDay();
            $q->whereBetween('created_at', [$start, $end]);
        });
    }
}