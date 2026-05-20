<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'document_id',
        'action',
        'description',
        'ip_address'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * LOCAL SCOPE: Membungkus logika filter agar Controller tetap bersih.
     */
    public function scopeFilter(Builder $query, array $filters)
    {
        // Filter Pencarian Teks
        $query->when($filters['search'] ?? false, function ($q, $search) {
            $searchTerm = '%' . $search . '%';
            $q->where(function ($q) use ($searchTerm) {
                $q->where('description', 'like', $searchTerm)
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', $searchTerm))
                  ->orWhereHas('document', fn($d) => $d->where('no_doc', 'like', $searchTerm));
            });
        });

        // Filter Jenis Aksi (Action Type)
        $query->when($filters['action_type'] ?? false, function ($q, $action) {
            $q->where('action', $action);
        });

        // Filter Rentang Waktu (Carbon)
        $query->when(($filters['start_date'] ?? false) && ($filters['end_date'] ?? false), function ($q) use ($filters) {
            $start = Carbon::parse($filters['start_date'])->startOfDay();
            $end = Carbon::parse($filters['end_date'])->endOfDay();
            $q->whereBetween('created_at', [$start, $end]);
        });
    }
}