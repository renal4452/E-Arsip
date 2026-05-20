@extends('layouts.app')

@section('content')
@php
    $roleName = auth()->user()->role->name ?? 'User';
    $isUploader = $document->latestVersion && $document->latestVersion->uploaded_by == auth()->id();
    $canForceUpdate = in_array($roleName, ['Admin', 'Auditor', 'Inspektur']) || ($isUploader && $document->status != 'approved');
@endphp

<div class="w-full" x-data="{ 
    activeTab: 'versions',
    revisiModalOpen: false,
    forceUpdateModalOpen: false,
    tteModalOpen: false
}">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 mb-1">Detail Dokumen</h3>
            <p class="text-sm text-slate-500 font-medium">Informasi lengkap, histori revisi, dan jejak aktivitas berkas.</p>
        </div>
        <a href="{{ route('documents.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 hover:text-slate-900 font-bold text-sm shadow-sm transition-colors">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        
        <div class="lg:col-span-1 space-y-6">
            
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                    <h6 class="font-extrabold text-indigo-600 m-0 flex items-center gap-2 uppercase tracking-wider text-xs">
                        <i class="bi bi-info-circle text-lg"></i> Ringkasan Dokumen
                    </h6>
                </div>
                
                <div class="p-6 text-center bg-slate-50/30 border-b border-slate-100">
                    <i class="bi bi-file-earmark-text text-indigo-500 text-6xl mb-3 block"></i>
                    <h5 class="text-xl font-black text-slate-800 mb-3">{{ $document->no_doc }}</h5>
                    
                    @if($document->status == 'pending')
                        <span class="inline-flex px-4 py-1.5 bg-amber-100 text-amber-700 border border-amber-200 rounded-full text-xs font-extrabold tracking-widest">MENUNGGU ACC</span>
                    @elseif($document->status == 'revisi')
                        <span class="inline-flex px-4 py-1.5 bg-rose-100 text-rose-700 border border-rose-200 rounded-full text-xs font-extrabold tracking-widest">PERLU REVISI</span>
                    @else
                        <span class="inline-flex px-4 py-1.5 bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-full text-xs font-extrabold tracking-widest">DISETUJUI</span>
                    @endif
                </div>

                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1">Judul Dokumen</label>
                        <p class="text-sm font-bold text-slate-800 leading-tight">{{ $document->title }}</p>
                    </div>
                    
                    <div class="pt-5 border-t border-slate-100">
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1">Kategori Audit</label>
                        <p class="text-sm font-medium text-slate-700">{{ $document->docType->name_types ?? '-' }}</p>
                    </div>
                    
                    <div class="pt-5 border-t border-slate-100">
                        <label class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1">Divisi / Instansi</label>
                        <p class="text-sm font-medium text-slate-700">{{ $document->division->name ?? 'Internal Inspektorat' }}</p>
                    </div>

                    <div class="pt-6 border-t border-slate-100 flex flex-col gap-3">
                        @if($document->latestVersion)
                            <a href="{{ route('documents.download', $document->latestVersion->id) }}" class="w-full flex items-center justify-center gap-2 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-sm transition-colors text-sm">
                                <i class="bi bi-cloud-download text-lg"></i> Unduh Versi v{{ $document->current_version }}
                            </a>
                        @endif

                        @if(in_array($roleName, ['Auditor', 'Inspektur', 'Admin']) && $document->status == 'pending')
                            <form action="{{ route('documents.approve', $document->id) }}" method="POST" class="w-full">
                                @csrf
                                <button type="submit" class="w-full flex items-center justify-center gap-2 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl shadow-sm transition-colors text-sm" onclick="return confirm('Setujui dokumen ini?')">
                                    <i class="bi bi-check-lg text-lg"></i> Beri ACC
                                </button>
                            </form>
                            <button @click="revisiModalOpen = true" class="w-full flex items-center justify-center gap-2 py-3 bg-white border-2 border-rose-500 text-rose-500 hover:bg-rose-50 font-bold rounded-xl shadow-sm transition-colors text-sm">
                                <i class="bi bi-pencil-square text-lg"></i> Minta Revisi
                            </button>
                        @endif

                        @if($canForceUpdate && $document->status != 'approved')
                            <button @click="forceUpdateModalOpen = true" class="w-full flex items-center justify-center gap-2 py-3 bg-amber-400 hover:bg-amber-500 text-amber-950 font-bold rounded-xl shadow-sm transition-colors text-sm">
                                <i class="bi bi-cloud-upload text-lg"></i> Unggah Ulang Berkas
                            </button>
                        @endif

                        @if(in_array($roleName, ['Admin', 'Inspektur']) && $document->status == 'approved')
                            <button @click="tteModalOpen = true" class="w-full flex items-center justify-center gap-2 py-3 bg-sky-500 hover:bg-sky-600 text-white font-bold rounded-xl shadow-sm transition-colors text-sm">
                                <i class="bi bi-shield-check text-lg"></i> Unggah Final TTE
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            
            @if($document->status == 'revisi' && !empty($document->auditor_note))
                <div class="bg-rose-50 border-l-4 border-rose-500 rounded-xl shadow-sm p-5">
                    <h6 class="font-bold text-rose-800 mb-2 flex items-center gap-2">
                        <i class="bi bi-chat-left-dots-fill"></i> Catatan Auditor:
                    </h6>
                    <p class="text-sm text-rose-900 leading-relaxed font-medium">{{ $document->auditor_note }}</p>
                </div>
            @endif

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="flex border-b border-slate-100 bg-slate-50/50 p-2 gap-2">
                    <button @click="activeTab = 'versions'" 
                            :class="activeTab === 'versions' ? 'bg-white shadow-sm text-indigo-600 font-bold' : 'text-slate-500 hover:text-slate-700 font-medium'" 
                            class="flex-1 py-3 px-4 rounded-xl text-sm transition-all flex items-center justify-center gap-2">
                        <i class="bi bi-layers"></i> Histori Versi
                    </button>
                    <button @click="activeTab = 'logs'" 
                            :class="activeTab === 'logs' ? 'bg-white shadow-sm text-indigo-600 font-bold' : 'text-slate-500 hover:text-slate-700 font-medium'" 
                            class="flex-1 py-3 px-4 rounded-xl text-sm transition-all flex items-center justify-center gap-2">
                        <i class="bi bi-clock-history"></i> Log Aktivitas
                    </button>
                </div>

                <div x-show="activeTab === 'versions'" class="overflow-x-auto" x-cloak>
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
                                <th class="px-6 py-4">Versi</th>
                                <th class="px-6 py-4">Pengunggah</th>
                                <th class="px-6 py-4">Waktu</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($document->versions->sortByDesc('version_number') as $version)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <span class="px-3 py-1.5 bg-slate-100 text-slate-700 border border-slate-200 rounded-full text-xs font-bold">v{{ $version->version_number }}</span>
                                            @if($loop->first)
                                                <span class="px-2 py-1 bg-indigo-500 text-white rounded-full text-[9px] font-extrabold tracking-widest">TERKINI</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-slate-800">{{ $version->user->name ?? 'Unknown' }}</div>
                                        <div class="text-xs text-slate-500 font-medium">{{ number_format($version->file_size / 1024, 2) }} KB</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-slate-700">{{ $version->created_at->format('d M Y') }}</div>
                                        <div class="text-xs text-slate-500 font-medium">{{ $version->created_at->format('H:i') }} WIB</div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('documents.download', $version->id) }}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-white border border-indigo-200 text-indigo-600 hover:bg-indigo-50 font-bold text-xs rounded-lg transition-colors shadow-sm">
                                            <i class="bi bi-download"></i> Unduh
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div x-show="activeTab === 'logs'" class="p-6 md:p-8" x-cloak>
                    <div class="relative border-l-2 border-slate-100 ml-4 space-y-8">
                        @forelse($document->activityLogs->sortByDesc('created_at') as $log)
                            <div class="relative pl-8">
                                <div class="absolute -left-[21px] top-0 w-10 h-10 rounded-full border-4 border-white bg-white flex items-center justify-center text-lg shadow-sm">
                                    @if(in_array($log->action, ['approve'])) <i class="bi bi-check-circle-fill text-emerald-500"></i>
                                    @elseif(in_array($log->action, ['upload', 'force_update', 'upload_revision'])) <i class="bi bi-cloud-arrow-up-fill text-indigo-500"></i>
                                    @elseif(in_array($log->action, ['request_revision'])) <i class="bi bi-exclamation-circle-fill text-amber-500"></i>
                                    @elseif(in_array($log->action, ['upload_tte'])) <i class="bi bi-shield-check text-sky-500"></i>
                                    @else <i class="bi bi-info-circle-fill text-slate-400"></i> @endif
                                </div>
                                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 shadow-sm">
                                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-2 gap-1">
                                        <span class="text-sm font-bold text-slate-800">{{ $log->user->name ?? 'System' }}</span>
                                        <span class="text-xs font-bold text-slate-400">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <p class="text-sm text-slate-600 font-medium leading-relaxed">{{ $log->description }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300">
                                    <i class="bi bi-clock-history text-2xl"></i>
                                </div>
                                <p class="text-sm font-medium text-slate-500">Belum ada aktivitas tercatat.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>

    @if(in_array($roleName, ['Auditor', 'Inspektur', 'Admin']) && $document->status == 'pending')
    <div x-show="revisiModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" x-transition.opacity>
        <div @click.outside="revisiModalOpen = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden" x-transition.scale>
            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-white">
                <h5 class="font-extrabold text-slate-800 flex items-center gap-2"><i class="bi bi-pencil-square text-rose-500 text-xl"></i> Beri Catatan Revisi</h5>
                <button @click="revisiModalOpen = false" class="text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg"></i></button>
            </div>
            <form action="{{ route('documents.revisi', $document->id) }}" method="POST">
                @csrf
                <div class="p-6 bg-slate-50">
                    <textarea name="note" class="w-full bg-white border border-slate-200 text-slate-700 text-sm rounded-xl p-4 focus:ring-2 focus:ring-rose-500 focus:border-rose-500 focus:outline-none transition-colors" rows="5" placeholder="Tuliskan poin-poin yang perlu diperbaiki oleh pengunggah..." required></textarea>
                </div>
                <div class="px-6 py-4 bg-white border-t border-slate-100 flex justify-end gap-3">
                    <button type="button" @click="revisiModalOpen = false" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition-colors text-sm">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-rose-500 hover:bg-rose-600 text-white font-bold rounded-xl shadow-sm transition-colors text-sm">Kirim Revisi</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <div x-show="forceUpdateModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" x-transition.opacity>
        <div @click.outside="forceUpdateModalOpen = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden" x-transition.scale>
            <div class="px-6 py-5 bg-amber-50 border-b border-amber-100 flex justify-between items-center">
                <h5 class="font-extrabold text-amber-900 flex items-center gap-2"><i class="bi bi-cloud-upload text-amber-500 text-xl"></i> Unggah Ulang Berkas</h5>
                <button @click="forceUpdateModalOpen = false" class="text-amber-500 hover:text-amber-700"><i class="bi bi-x-lg"></i></button>
            </div>
            <form action="{{ route('documents.force_update', $document->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-6">
                    <p class="text-sm text-slate-600 font-medium mb-5 leading-relaxed">Memperbarui dokumen ini akan mengembalikan status menjadi <strong class="text-amber-600">Menunggu ACC</strong> dan menaikkan versinya.</p>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Pilih Berkas Baru <span class="text-rose-500">*</span></label>
                    <input type="file" name="file" class="w-full text-sm text-slate-500 border border-slate-200 rounded-xl bg-slate-50 cursor-pointer file:mr-4 file:py-2.5 file:px-4 file:rounded-l-xl file:border-0 file:text-sm file:font-bold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100" required accept=".pdf,.doc,.docx,.xls,.xlsx">
                    <p class="mt-2 text-xs font-medium text-slate-500">Maksimal 10MB. Format: PDF, Word, Excel.</p>
                </div>
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                    <button type="button" @click="forceUpdateModalOpen = false" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition-colors text-sm">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl shadow-sm transition-colors text-sm">Mulai Unggah</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="tteModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" x-transition.opacity>
        <div @click.outside="tteModalOpen = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden" x-transition.scale>
            <div class="px-6 py-5 bg-sky-50 border-b border-sky-100 flex justify-between items-center">
                <h5 class="font-extrabold text-sky-900 flex items-center gap-2"><i class="bi bi-shield-check text-sky-500 text-xl"></i> Unggah Berkas Final (TTE)</h5>
                <button @click="tteModalOpen = false" class="text-sky-500 hover:text-sky-700"><i class="bi bi-x-lg"></i></button>
            </div>
            <form action="{{ route('documents.upload_final', $document->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-6">
                    <div class="bg-sky-100/50 border border-sky-200 text-sky-800 text-sm font-medium p-4 rounded-xl mb-5 leading-relaxed">
                        Gunakan fitur ini untuk melampirkan berkas yang telah dibubuhi <strong class="text-sky-900">Tanda Tangan Elektronik (TTE)</strong>.
                    </div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Pilih Berkas PDF Final <span class="text-rose-500">*</span></label>
                    <input type="file" name="file" class="w-full text-sm text-slate-500 border border-slate-200 rounded-xl bg-slate-50 cursor-pointer file:mr-4 file:py-2.5 file:px-4 file:rounded-l-xl file:border-0 file:text-sm file:font-bold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100" required accept=".pdf">
                    <p class="mt-2 text-xs font-medium text-slate-500">Maksimal 10MB. Format wajib: PDF.</p>
                </div>
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                    <button type="button" @click="tteModalOpen = false" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition-colors text-sm">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-bold rounded-xl shadow-sm transition-colors text-sm">Unggah Berkas</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection