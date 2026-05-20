@extends('layouts.app')

@section('content')
<div class="w-full">
    
    <div class="flex items-center mb-8">
        <div class="w-12 h-12 bg-indigo-600 text-white rounded-xl flex items-center justify-center mr-4 shrink-0 shadow-sm">
            <i class="bi bi-person-vcard text-2xl"></i>
        </div>
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 mb-1">Profil Pengguna</h3>
            <p class="text-sm text-slate-500 font-medium">Informasi akun, statistik kontribusi, dan riwayat aktivitas Anda.</p>
        </div>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        
        <div class="lg:col-span-1 space-y-6">
            
            <div class="bg-white border border-slate-100 shadow-sm rounded-2xl p-6 text-center">
                <div class="w-24 h-24 mx-auto bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center text-3xl font-black mb-4 border-4 border-white shadow-md">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h5 class="text-lg font-extrabold text-slate-800 mb-1">{{ $user->name }}</h5>
                <span class="inline-block bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-[10px] font-extrabold tracking-widest uppercase mb-4">
                    {{ $user->role->name }}
                </span>

                <div class="border-t border-slate-100 pt-5 mt-2 text-left space-y-4">
                    <div>
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1">Unit Kerja / Divisi</p>
                        <p class="text-sm font-bold text-slate-700">{{ $user->division->name ?? 'Inspektorat' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1">Email Terdaftar</p>
                        <p class="text-sm font-bold text-slate-700 break-all">{{ $user->email }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-slate-100 shadow-sm rounded-2xl p-6">
                <h6 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-4"><i class="bi bi-bar-chart-fill text-indigo-500 mr-1"></i> Kinerja Dokumen Saya</h6>
                <div class="grid grid-cols-2 gap-4 text-center">
                    
                    <div class="bg-slate-50 border border-slate-100 p-4 rounded-2xl hover:-translate-y-1 hover:shadow-md transition-all duration-300">
                        <h3 class="text-2xl font-black text-indigo-600 mb-1">{{ $stats['total_upload'] }}</h3>
                        <p class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Total Upload</p>
                    </div>
                    
                    <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-2xl hover:-translate-y-1 hover:shadow-md transition-all duration-300">
                        <h3 class="text-2xl font-black text-emerald-600 mb-1">{{ $stats['approved'] }}</h3>
                        <p class="text-[10px] font-extrabold text-emerald-600 uppercase tracking-wider">Selesai / ACC</p>
                    </div>
                    
                    <div class="bg-amber-50 border border-amber-100 p-4 rounded-2xl hover:-translate-y-1 hover:shadow-md transition-all duration-300">
                        <h3 class="text-2xl font-black text-amber-600 mb-1">{{ $stats['pending'] }}</h3>
                        <p class="text-[10px] font-extrabold text-amber-600 uppercase tracking-wider">Menunggu ACC</p>
                    </div>
                    
                    <div class="bg-rose-50 border border-rose-100 p-4 rounded-2xl hover:-translate-y-1 hover:shadow-md transition-all duration-300">
                        <h3 class="text-2xl font-black text-rose-600 mb-1">{{ $stats['revisi'] }}</h3>
                        <p class="text-[10px] font-extrabold text-rose-600 uppercase tracking-wider">Perlu Revisi</p>
                    </div>

                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            
            <div class="bg-white border border-slate-100 shadow-sm rounded-2xl overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                    <h6 class="font-extrabold text-slate-800 m-0 flex items-center gap-2">
                        <i class="bi bi-shield-lock text-rose-500 text-lg"></i> Keamanan Akun
                    </h6>
                </div>
                <div class="p-6">
                    <form action="{{ route('profile.password.update') }}" method="POST">
                        @csrf @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">Password Lama <span class="text-rose-500">*</span></label>
                                <input type="password" name="current_password" required 
                                       class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:bg-white transition-colors
                                       @error('current_password') border-rose-300 bg-rose-50 focus:border-rose-500 focus:ring-rose-500 @else focus:border-indigo-500 focus:ring-indigo-500 @enderror">
                                @error('current_password') <p class="mt-1.5 text-[10px] font-bold text-rose-500">{{ $message }}</p> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">Password Baru <span class="text-rose-500">*</span></label>
                                <input type="password" name="new_password" required 
                                       class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:bg-white transition-colors
                                       @error('new_password') border-rose-300 bg-rose-50 focus:border-rose-500 focus:ring-rose-500 @else focus:border-indigo-500 focus:ring-indigo-500 @enderror">
                                @error('new_password') <p class="mt-1.5 text-[10px] font-bold text-rose-500">{{ $message }}</p> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">Konfirmasi Password <span class="text-rose-500">*</span></label>
                                <input type="password" name="new_password_confirmation" required 
                                       class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:border-indigo-500 focus:ring-indigo-500 focus:bg-white transition-colors">
                            </div>

                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="px-6 py-2.5 bg-slate-800 hover:bg-slate-900 text-white font-bold text-sm rounded-xl shadow-sm transition-colors">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white border border-slate-100 shadow-sm rounded-2xl overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h6 class="font-extrabold text-slate-800 m-0">Riwayat Pekerjaan Terakhir</h6>
                    <span class="px-3 py-1 bg-slate-200 text-slate-600 rounded-full text-[10px] font-extrabold tracking-widest uppercase">5 Aksi Terakhir</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white border-b border-slate-100 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
                                <th class="px-6 py-4">Aktivitas</th>
                                <th class="px-6 py-4">Dokumen</th>
                                <th class="px-6 py-4 text-right">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($myLogs as $log)
                            <tr class="hover:bg-slate-50 transition-colors group">
                                <td class="px-6 py-4">
                                    @php
                                        // Mapping Ikon dan Warna Tailwind
                                        $icon = 'bi-activity'; $colorClass = 'bg-slate-100 text-slate-500';
                                        if(in_array($log->action, ['store', 'create', 'upload'])) { $icon = 'bi-cloud-arrow-up'; $colorClass = 'bg-indigo-100 text-indigo-600'; }
                                        if(in_array($log->action, ['approve'])) { $icon = 'bi-check-circle-fill'; $colorClass = 'bg-emerald-100 text-emerald-600'; }
                                        if(in_array($log->action, ['revisi', 'request_revision'])) { $icon = 'bi-exclamation-circle'; $colorClass = 'bg-rose-100 text-rose-600'; }
                                        if(in_array($log->action, ['update', 'update_revision', 'force_update'])) { $icon = 'bi-pencil-square'; $colorClass = 'bg-amber-100 text-amber-600'; }
                                        if(in_array($log->action, ['destroy', 'delete'])) { $icon = 'bi-trash'; $colorClass = 'bg-slate-200 text-slate-700'; }
                                    @endphp

                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 {{ $colorClass }}">
                                            <i class="bi {{ $icon }} text-lg"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800 text-sm mb-0.5">{{ $log->description }}</div>
                                            <div class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest">Sistem: {{ str_replace('_', ' ', $log->action) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($log->document)
                                        <a href="{{ route('documents.show', $log->document->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-50 border border-slate-200 text-indigo-600 hover:bg-indigo-50 font-bold text-xs rounded-lg transition-colors">
                                            <i class="bi bi-file-earmark-text"></i> {{ $log->document->no_doc }}
                                        </a>
                                    @else
                                        <span class="text-slate-300 font-bold">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right text-xs font-bold text-slate-400 whitespace-nowrap">
                                    {{ $log->created_at->diffForHumans() }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center">
                                    <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-2 text-slate-300">
                                        <i class="bi bi-inbox text-xl"></i>
                                    </div>
                                    <p class="text-sm font-medium text-slate-500">Belum ada riwayat aktivitas.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection