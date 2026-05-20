<?php
namespace App\Observers;

use App\Models\User;
use App\Models\ActivityLog;

class UserObserver
{
    public function updated(User $user)
    {
        // Jika yang berubah adalah kolom password
        if ($user->wasChanged('password')) {
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'change_password',
                'description' => 'User memperbarui kata sandi profil',
                'ip_address' => request()->ip()
            ]);
        }
    }
}