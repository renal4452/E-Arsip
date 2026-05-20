<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Models\Role;
use App\Models\Division;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

// ❌ HAPUS: use App\Traits\ApiResponse;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::with(['role', 'division'])->latest()->get();
        
        // ✅ UBAH: Langsung lempar ke view Blade
        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $roles = Role::all();
        $divisions = Division::all();
        
        return view('users.create', compact('roles', 'divisions'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        User::create($data);

        // ✅ UBAH: Langsung redirect ke index
        return redirect()->route('users.index')->with('success', 'Akun pengguna baru berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        $roles = Role::all();
        $divisions = Division::all();
        
        return view('users.edit', compact('user', 'roles', 'divisions'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']); // Hapus dari array agar tidak me-reset password jadi null
        }

        $user->update($data);

        // ✅ UBAH: Langsung redirect ke index
        return redirect()->route('users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        // Proteksi Hapus Diri Sendiri
        if ($user->id === $request->user()->id) {
            // ✅ UBAH: Langsung kembali dengan pesan error
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri saat sedang login.');
        }

        $user->delete();

        // ✅ UBAH: Langsung kembali dengan pesan sukses
        return back()->with('success', 'Akun pengguna berhasil dihapus dari sistem.');
    }
}