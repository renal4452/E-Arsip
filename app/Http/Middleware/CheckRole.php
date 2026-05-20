<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // 1. Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        // 2. Cet apakah role user ada dalam daftar role yang diizinkan
        // Asumsi: Anda punya relasi $user->role->name
        if (in_array($user->role->name, $roles)) {
            return $next($request);
        }

        // 3. Jika tidak punya akses, lempar ke halaman 403 atau Dashboard dengan pesan error
        abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
    }
}