@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    
    <div class="mb-8">
        <a href="{{ route('users.index') }}" class="inline-flex items-center text-sm font-bold text-slate-500 hover:text-indigo-600 transition-colors mb-4">
            <i class="bi bi-arrow-left mr-2"></i> Kembali ke Daftar User
        </a>
        <h3 class="text-2xl font-extrabold text-slate-800">Edit Data Pengguna</h3>
        <p class="text-sm text-slate-500 font-medium">Perbarui informasi profil dan hak akses pengguna sistem.</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
            <h6 class="font-extrabold text-indigo-600 uppercase tracking-wider text-xs m-0 flex items-center gap-2">
                <i class="bi bi-person-gear text-lg"></i> Formulir Pengubahan Data
            </h6>
        </div>

        <form action="{{ route('users.update', $user->id) }}" method="POST" class="p-8">
            @csrf @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">Nama Lengkap / Instansi</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">Alamat Email Dinas</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">Hak Akses (Role)</label>
                    <select name="role_id" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none appearance-none" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">Divisi / Unit Kerja</label>
                    <select name="division_id" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none appearance-none" required>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}" {{ $user->division_id == $division->id ? 'selected' : '' }}>{{ $division->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">Status Akun</label>
                    <select name="is_active" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none appearance-none" required>
                        <option value="1" {{ $user->is_active ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>
            </div>

            <div class="mb-8" x-data="{ show: false }">
                <label for="password" class="block text-xs font-bold text-slate-700 mb-2">Password Baru</label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" name="password" id="password" 
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none pr-12 transition-all" 
                           placeholder="Kosongkan jika tidak ingin mengubah sandi">
                    <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-indigo-600">
                        <i class="bi" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
                    </button>
                </div>
                <p class="mt-2 text-[10px] font-bold text-slate-400 uppercase tracking-wide">Hanya isi jika ingin mereset/mengganti password.</p>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('users.index') }}" class="px-6 py-3 bg-white border border-slate-200 text-slate-600 font-bold text-sm rounded-xl hover:bg-slate-50 transition-colors">Batal</a>
                <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-sm transition-colors flex items-center gap-2">
                    <i class="bi bi-save"></i> Update Data
                </button>
            </div>
        </form>
    </div>
</div>
@endsection