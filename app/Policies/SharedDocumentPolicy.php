<?php
namespace App\Policies;

use App\Models\SharedDocument;
use App\Models\User;

class SharedDocumentPolicy
{
    public function delete(User $user, SharedDocument $sharedDocument)
    {
        return $user->role->name === 'Admin' || $user->id === $sharedDocument->user_id;
    }
}