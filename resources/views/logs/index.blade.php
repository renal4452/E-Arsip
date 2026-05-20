@extends('layouts.app')

@section('content')
<div class="w-full">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 mb-1">Log Aktivitas Sistem</h3>
            <p class="text-sm text-slate-500 font-medium">Pantau riwayat unggahan, persetujuan, dan unduhan berkas secara real-time.</p>
        </div>
        <a href="{{ route('logs.print', request()->query()) }}" target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-sm transition-colors shrink-0">
            <i class="bi bi-printer text-lg"></i> Cetak Laporan
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white border-l-4 border-indigo-500 shadow-sm rounded-2xl p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <i class="bi bi-activity text-2xl"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Log Aktivitas</p>
                <h4 class="text-xl font-black text-slate-800 leading-none">{{ number_format($logs->total(), 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm mb-6 p-4 lg:p-6">
        <form action="{{ route('logs.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-4 items-end">
            
            <div class="lg:col-span-3">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pencarian</label>
                <div class="relative">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 text-slate-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-colors" placeholder="Cari nama, deskripsi..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="lg:col-span-3">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Jenis Aktivitas</label>
                <select name="action_type" class="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-colors appearance-none">
                    <option value="">Semua Aktivitas</option>
                    <option value="upload" {{ request('action_type') == 'upload' ? 'selected' : '' }}>Upload Baru</option>
                    <option value="approve" {{ request('action_type') == 'approve' ? 'selected' : '' }}>Persetujuan (ACC)</option>
                    <option value="request_revision" {{ request('action_type') == 'request_revision' ? 'selected' : '' }}>Minta Revisi</option>
                    <option value="download" {{ request('action_type') == 'download' ? 'selected' : '' }}>Unduh Dokumen</option>
                    <option value="security_breach" {{ request('action_type') == 'security_breach' ? 'selected' : '' }}>Pelanggaran Sistem</option>
                </select>
            </div>

            <div class="lg:col-span-2">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Dari Tanggal</label>
                <input type="date" name="start_date" class="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-colors" value="{{ request('start_date') }}">
            </div>
            <div class="lg:col-span-2">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Hingga Tanggal</label>
                <input type="date" name="end_date" class="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-colors" value="{{ request('end_date') }}">
            </div>

            <div class="lg:col-span-2 flex gap-2">
                <button type="submit" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white font-bold py-2 rounded-xl text-sm transition-colors shadow-sm">Terapkan</button>
                
                @if(request()->anyFilled(['search', 'action_type', 'start_date', 'end_date']))
                    <a href="{{ route('logs.index') }}" class="px-3 py-2 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-xl border border-rose-200 flex items-center justify-center transition-colors shadow-sm" title="Reset Semua Filter">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
                        <th class="px-6 py-4">Waktu (WIB)</th>
                        <th class="px-6 py-4">Pengguna</th>
                        <th class="px-6 py-4">Aktivitas</th>
                        <th class="px-6 py-4">Deskripsi</th>
                        <th class="px-6 py-4 text-center">Dokumen</th>
                        <th class="px-6 py-4 text-right">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50 transition-colors">
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-slate-800">{{ $log->created_at->format('d M Y') }}</div>
                                <div class="text-xs text-slate-500 font-medium">{{ $log->created_at->format('H:i:s') }}</div>
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-slate-800">{{ $log->user->name ?? 'Sistem / Guest' }}</div>
                                        <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">{{ $log->user->role->name ?? 'Unknown' }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                @php
                                    $actionStyle = [
                                        'upload' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                                        'upload_revision' => 'bg-sky-100 text-sky-700 border-sky-200',
                                        'approve' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                        'request_revision' => 'bg-amber-100 text-amber-700 border-amber-200',
                                        'download' => 'bg-slate-100 text-slate-700 border-slate-200',
                                        'security_breach' => 'bg-rose-100 text-rose-700 border-rose-200',
                                    ];
                                    $currentStyle = $actionStyle[$log->action] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                                @endphp
                                <span class="inline-flex px-3 py-1 border rounded-full text-[10px] font-extrabold tracking-widest uppercase {{ $currentStyle }}">
                                    {{ str_replace('_', ' ', $log->action) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 max-w-xs">
                                <div class="text-sm font-medium text-slate-700 truncate" title="{{ $log->description }}">{{ $log->description }}</div>
                                <div class="text-[10px] text-slate-400 font-bold mt-0.5 flex items-center gap-1">
                                    <i class="bi bi-fingerprint"></i> ID Log: #{{ str_pad($log->id, 5, '0', STR_PAD_LEFT) }}
                                </div>
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if($log->document)
                                    <a href="{{ route('documents.show', $log->document_id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-slate-200 text-indigo-600 hover:bg-indigo-50 font-bold text-xs rounded-lg transition-colors shadow-sm whitespace-nowrap">
                                        <i class="bi bi-file-earmark-text"></i> {{ $log->document->no_doc ?? 'Lihat' }}
                                    </a>
                                @else
                                    <span class="text-slate-300 font-bold">-</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right">
                                <code class="font-mono text-xs font-bold px-2 py-1 bg-slate-100 text-slate-600 border border-slate-200 rounded">
                                    {{ $log->ip_address }}
                                </code>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300">
                                    <i class="bi bi-database-exclamation text-2xl"></i>
                                </div>
                                <h6 class="text-base font-extrabold text-slate-700 mb-1">Data Tidak Ditemukan</h6>
                                <p class="text-sm font-medium text-slate-500">Belum ada rekaman aktivitas yang sesuai dengan filter Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($logs, 'links') && $logs->hasPages())
        <div class="mt-4 flex justify-center">
            {{ $logs->appends(request()->query())->links() }}
        </div>
    @endif

</div>
@endsection