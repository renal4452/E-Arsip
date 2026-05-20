@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    
    <div class="mb-8">
        <a href="{{ route('users.index') }}" class="inline-flex items-center text-sm font-bold text-slate-500 hover:text-indigo-600 transition-colors mb-4">
            <i class="bi bi-arrow-left mr-2"></i> Kembali ke Daftar User
        </a>
        <h3 class="text-2xl font-extrabold text-slate-800">Tambah Pengguna Baru</h3>
        <p class="text-sm text-slate-500 font-medium">Buat akun akses baru untuk pegawai atau staf inspektorat.</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
            <h6 class="font-extrabold text-indigo-600 uppercase tracking-wider text-xs m-0 flex items-center gap-2">
                <i class="bi bi-person-plus text-lg"></i> Detail Akun Pengguna
            </h6>
        </div>

        <form action="{{ route('users.store') }}" method="POST" class="p-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 mb-2">Nama Lengkap / Instansi <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" 
                           class="w-full px-4 py-3 bg-slate-50 border @error('name') border-rose-300 @else border-slate-200 @enderror rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all" 
                           placeholder="Contoh: Admin Dinas Kesehatan" required>
                    @error('name') <p class="mt-1.5 text-[10px] font-bold text-rose-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 mb-2">Alamat Email Dinas <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" 
                           class="w-full px-4 py-3 bg-slate-50 border @error('email') border-rose-300 @else border-slate-200 @enderror rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all" 
                           placeholder="Contoh: dinkes@pemda.local" required>
                    @error('email') <p class="mt-1.5 text-[10px] font-bold text-rose-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="role_id" class="block text-xs font-bold text-slate-700 mb-2">Hak Akses (Role) <span class="text-rose-500">*</span></label>
                    <select name="role_id" id="role_id" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none appearance-none" required>
                        <option value="" disabled selected>Pilih Role...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="division_id" class="block text-xs font-bold text-slate-700 mb-2">Divisi / Unit Kerja <span class="text-rose-500">*</span></label>
                    <select name="division_id" id="division_id" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none appearance-none" required>
                        <option value="" disabled selected>Pilih Divisi/Irban...</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}">{{ $division->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-8" x-data="{ show: false }">
                <label for="password" class="block text-xs font-bold text-slate-700 mb-2">Kata Sandi Awal <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" name="password" id="password" 
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none pr-12" 
                           placeholder="Minimal 6 karakter" required>
                    <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-indigo-600">
                        <i class="bi" :class="show ? 'bi-eye-slash' : 'bi-eye'"></i>
                    </button>
                </div>
                <p class="mt-2 text-[10px] font-bold text-slate-400 uppercase tracking-wide">
                    Saran: Gunakan sandi standar (misal: <span class="text-indigo-600">inspektorat123</span>) dan informasikan kepada pengguna.
                </p>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('users.index') }}" class="px-6 py-3 bg-white border border-slate-200 text-slate-600 font-bold text-sm rounded-xl hover:bg-slate-50 transition-colors">Batal</a>
                <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-sm transition-colors flex items-center gap-2">
                    <i class="bi bi-save"></i> Simpan Pengguna
                </button>
            </div>
        </form>
    </div>
</div>
@endsection