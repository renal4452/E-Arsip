<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Document; // 🔥 Tambahkan import ini agar getDocumentStats() bisa jalan!

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',      // Tambahkan ini agar bisa disimpan ke DB
        'division_id',
        'is_active',    // Tambahkan ini agar bisa disimpan ke DB
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    // ==========================================
    // RELASI DATABASE
    // ==========================================

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function uploadedDocuments()
    {
        return $this->hasMany(DocVersion::class, 'uploaded_by');
    }

    public function auditedDocuments()
    {
        return $this->hasMany(Document::class, 'auditor_id');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // ==========================================
    // HELPER ROLE & PERMISSION
    // ==========================================

    public function hasRole($role)
    {
        return $this->role->name === $role;
    }

    public function hasAnyRole(array $roles)
    {
        return in_array($this->role->name, $roles);
    }
    // ==========================================
    // LOGIKA BISNIS (FAT MODEL)
    // ==========================================

    /**
     * Menghitung statistik dokumen milik user ini.
     * Menggantikan logika kotor yang sebelumnya ada di ProfileController.
     */
    public function getDocumentStats(): array
    {
        $myDocsQuery = Document::whereHas('versions', function($q) {
            $q->where('uploaded_by', $this->id);
        });

        return [
            'total_upload' => (clone $myDocsQuery)->count(),
            'pending'      => (clone $myDocsQuery)->where('status', 'pending')->count(),
            'revisi'       => (clone $myDocsQuery)->where('status', 'revisi')->count(),
            'approved'     => (clone $myDocsQuery)->where('status', 'approved')->count(),
        ];
    }
}