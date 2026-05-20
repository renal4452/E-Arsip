@extends('layouts.app')

@section('content')
<div class="w-full">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 mb-1">Manajemen User</h3>
            <p class="text-sm text-slate-500 font-medium">Kelola data pengguna, hak akses, dan asal divisi/instansi.</p>
        </div>
        <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-sm transition-colors shrink-0">
            <i class="bi bi-person-plus-fill text-lg"></i> Tambah User Baru
        </a>
    </div>

    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-transition class="mb-8 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl shadow-sm flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="text-emerald-500 text-xl"><i class="bi bi-check-circle-fill"></i></div>
            <p class="text-sm font-bold text-emerald-800">{{ session('success') }}</p>
        </div>
        <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 transition-colors">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
                        <th class="px-6 py-4 w-16 text-center">No</th>
                        <th class="px-6 py-4">Profil Pengguna</th>
                        <th class="px-6 py-4">Divisi / OPD</th>
                        <th class="px-6 py-4 text-center">Hak Akses</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $index => $user)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-bold text-slate-400 text-center">{{ $index + 1 }}</td>
                            
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm shrink-0 border border-indigo-100">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-800 text-sm">{{ $user->name }}</div>
                                        <div class="text-xs text-slate-500 flex items-center gap-1">
                                            <i class="bi bi-envelope"></i> {{ $user->email ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-slate-700">{{ $user->division->name ?? 'Administrator Sistem' }}</span>
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                @php
                                    $role = optional($user->role)->name;
                                    $badgeStyle = 'bg-slate-100 text-slate-600 border-slate-200';
                                    if($role == 'Admin') $badgeStyle = 'bg-rose-100 text-rose-700 border-rose-200';
                                    if($role == 'Auditor') $badgeStyle = 'bg-sky-100 text-sky-700 border-sky-200';
                                    if($role == 'Inspektur') $badgeStyle = 'bg-amber-100 text-amber-700 border-amber-200';
                                @endphp
                                <span class="inline-flex px-3 py-1 border rounded-full text-[10px] font-extrabold tracking-widest uppercase {{ $badgeStyle }}">
                                    {{ $role ?? 'User' }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('users.edit', $user->id) }}" class="p-2 bg-white border border-slate-200 rounded-lg text-indigo-600 hover:bg-indigo-50 transition-colors shadow-sm" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    
                                    @if(auth()->id() !== $user->id)
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 bg-white border border-slate-200 rounded-lg text-rose-600 hover:bg-rose-50 transition-colors shadow-sm" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button disabled class="p-2 bg-slate-50 border border-slate-100 rounded-lg text-slate-300 shadow-sm cursor-not-allowed">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300">
                                    <i class="bi bi-people text-2xl"></i>
                                </div>
                                <h6 class="text-sm font-bold text-slate-700">Belum ada data User</h6>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection