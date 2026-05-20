@extends('layouts.app')

@section('content')
@php
    $roleName = auth()->user()->role->name ?? 'User';
    $isAdminOrInspektur = in_array($roleName, ['Admin', 'Inspektur']);
    $viewMode = request('view', 'folder'); 
    $items = $documents instanceof \Illuminate\Pagination\LengthAwarePaginator ? collect($documents->items()) : $documents;
@endphp

<div class="w-full" x-data="{ 
    filterOpen: {{ request()->hasAny(['year', 'type', 'start_date', 'end_date']) ? 'true' : 'false' }},
    activeFolder: null,
    folderName: '',
    tteModalOpen: false, tteFormAction: '', tteDocNumber: '',
    forceUpdateModalOpen: false, forceUpdateFormAction: '', forceUpdateDocNumber: ''
}">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 mb-1">Manajemen Dokumen & Arsip</h3>
            <p class="text-sm text-slate-500 font-medium">Pusat kendali draf LHP, Nota Dinas, dan riwayat persetujuan dokumen audit.</p>
        </div>
        <a href="{{ route('documents.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-sm transition-colors shrink-0">
            <i class="bi bi-cloud-arrow-up text-lg"></i> Unggah Dokumen Baru
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white border-l-4 border-indigo-500 shadow-sm rounded-2xl p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <i class="bi bi-files text-2xl"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Dokumen</p>
                <h4 class="text-xl font-black text-slate-800 leading-none">{{ $documents instanceof \Illuminate\Pagination\LengthAwarePaginator ? $documents->total() : $documents->count() }} Berkas</h4>
            </div>
        </div>
        
        <div class="bg-white border-l-4 border-amber-500 shadow-sm rounded-2xl p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <i class="bi bi-hourglass-split text-2xl"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Dalam Proses</p>
                <h4 class="text-xl font-black text-slate-800 leading-none">{{ $stats['pending'] ?? 0 }} Berkas</h4>
            </div>
        </div>
        
        <div class="bg-white border-l-4 border-emerald-500 shadow-sm rounded-2xl p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i class="bi bi-check-circle text-2xl"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Disetujui</p>
                <h4 class="text-xl font-black text-slate-800 leading-none">{{ $stats['approved'] ?? 0 }} Berkas</h4>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm mb-6 p-4 lg:p-5">
        <div class="flex flex-col lg:flex-row justify-between lg:items-center gap-4">
            
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mr-1"><i class="bi bi-funnel"></i> Status:</span>
                <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="px-4 py-1.5 rounded-full text-xs font-bold transition-colors {{ !request('status') ? 'bg-slate-800 text-white shadow-sm' : 'bg-slate-50 text-slate-600 border border-slate-200 hover:bg-slate-100' }}">Semua</a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}" class="px-4 py-1.5 rounded-full text-xs font-bold transition-colors {{ request('status') == 'pending' ? 'bg-amber-100 text-amber-700 border border-amber-200 shadow-sm' : 'bg-slate-50 text-slate-600 border border-slate-200 hover:bg-slate-100' }}">Menunggu ACC</a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'revisi']) }}" class="px-4 py-1.5 rounded-full text-xs font-bold transition-colors {{ request('status') == 'revisi' ? 'bg-rose-100 text-rose-700 border border-rose-200 shadow-sm' : 'bg-slate-50 text-slate-600 border border-slate-200 hover:bg-slate-100' }}">Revisi</a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'approved']) }}" class="px-4 py-1.5 rounded-full text-xs font-bold transition-colors {{ request('status') == 'approved' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200 shadow-sm' : 'bg-slate-50 text-slate-600 border border-slate-200 hover:bg-slate-100' }}">Disetujui</a>
                
                <button @click="filterOpen = !filterOpen" class="ml-2 px-3 py-1.5 rounded-lg text-xs font-bold text-indigo-600 border border-indigo-200 bg-indigo-50 hover:bg-indigo-100 transition-colors flex items-center gap-1">
                    <i class="bi bi-sliders"></i> Filter Lanjutan
                </button>
            </div>

            <div class="flex items-center gap-3">
                <form action="{{ route('documents.index') }}" method="GET" class="flex gap-2 w-full lg:w-auto">
                    @foreach(request()->except(['search', 'view']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <input type="hidden" name="view" value="{{ $viewMode }}">
                    <div class="relative w-full lg:w-64">
                        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="search" class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-colors" placeholder="Cari No/Judul..." value="{{ request('search') }}">
                    </div>
                </form>

                <div class="flex items-center bg-slate-100 rounded-xl p-1 border border-slate-200 shrink-0">
                    <a href="{{ request()->fullUrlWithQuery(['view' => 'folder']) }}" class="px-3 py-1.5 rounded-lg text-sm transition-colors {{ $viewMode == 'folder' ? 'bg-white text-indigo-600 shadow-sm font-bold' : 'text-slate-500 hover:text-slate-700' }}"><i class="bi bi-folder-fill"></i></a>
                    <a href="{{ request()->fullUrlWithQuery(['view' => 'table']) }}" class="px-3 py-1.5 rounded-lg text-sm transition-colors {{ $viewMode == 'table' ? 'bg-white text-indigo-600 shadow-sm font-bold' : 'text-slate-500 hover:text-slate-700' }}"><i class="bi bi-list-ul"></i></a>
                </div>
            </div>
        </div>

        <div x-show="filterOpen" x-collapse>
            <hr class="border-slate-100 my-4">
            <form action="{{ route('documents.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
                <input type="hidden" name="view" value="{{ $viewMode }}">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input type="hidden" name="search" value="{{ request('search') }}">
                
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tahun</label>
                    <select name="year" class="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-xl px-4 py-2 text-sm focus:ring-indigo-500">
                        <option value="">Semua Tahun</option>
                        @for($y = date('Y'); $y >= 2022; $y--)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kategori Dokumen</label>
                    <select name="type" class="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-xl px-4 py-2 text-sm focus:ring-indigo-500">
                        <option value="">Semua Kategori</option>
                        @foreach($categories ?? [] as $cat)
                            <option value="{{ $cat->id }}" {{ request('type') == $cat->id ? 'selected' : '' }}>{{ $cat->name_types }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tgl Mulai</label>
                    <input type="date" name="start_date" class="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-xl px-4 py-2 text-sm focus:ring-indigo-500" value="{{ request('start_date') }}">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Sampai</label>
                    <input type="date" name="end_date" class="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-xl px-4 py-2 text-sm focus:ring-indigo-500" value="{{ request('end_date') }}">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white font-bold py-2 rounded-xl text-sm transition-colors">Terapkan</button>
                    @if(request()->anyFilled(['search', 'status', 'year', 'type', 'start_date', 'end_date']))
                        <a href="{{ route('documents.index', ['view' => $viewMode]) }}" class="px-3 py-2 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-xl border border-rose-200 flex items-center justify-center transition-colors" title="Reset">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if($items->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                <i class="bi bi-folder-x text-4xl"></i>
            </div>
            <h6 class="text-lg font-extrabold text-slate-700 mb-2">Dokumen Tidak Ditemukan</h6>
            <p class="text-sm text-slate-500 mb-4">Coba hapus filter atau ubah kata kunci pencarian Anda.</p>
            @if(request()->anyFilled(['search', 'status', 'year', 'type', 'start_date', 'end_date']))
                <a href="{{ route('documents.index') }}" class="inline-block px-4 py-2 bg-indigo-50 text-indigo-600 rounded-full text-sm font-bold border border-indigo-100 hover:bg-indigo-100 transition-colors">Reset Semua Filter</a>
            @endif
        </div>
    @else
        
        @if($viewMode == 'folder')
            @php
                $groupedDocuments = $items->groupBy(function ($item) use ($isAdminOrInspektur) {
                    return $isAdminOrInspektur ? ($item->division->name ?? 'Umum') : ($item->docType->name_types ?? 'General');
                });
            @endphp

            <div x-show="activeFolder !== null" x-cloak class="flex items-center mb-6">
                <button @click="activeFolder = null; folderName = ''" class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 shadow-sm rounded-full text-sm font-bold text-slate-700 hover:bg-slate-50 transition-colors mr-4">
                    <i class="bi bi-arrow-left"></i> Kembali
                </button>
                <div class="flex items-center gap-2 text-indigo-600 font-extrabold text-lg">
                    <i class="{{ $isAdminOrInspektur ? 'bi-building' : 'bi-tags' }}"></i>
                    <span x-text="folderName"></span>
                </div>
            </div>

            <div x-show="activeFolder === null" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                @foreach($groupedDocuments as $groupName => $docsInGroup)
                    @php $folderSlug = Str::slug($groupName); @endphp
                    <div @click="activeFolder = '{{ $folderSlug }}'; folderName = '{{ $groupName }}'" 
                         class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm hover:border-indigo-500 hover:shadow-md hover:-translate-y-1 transition-all cursor-pointer group">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                <i class="bi bi-folder-fill text-2xl"></i>
                            </div>
                            <div class="overflow-hidden">
                                <h6 class="font-bold text-slate-800 truncate mb-1" title="{{ $groupName }}">{{ $groupName }}</h6>
                                <p class="text-xs font-bold text-slate-400">{{ $docsInGroup->count() }} Dokumen</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div x-show="('{{ $viewMode }}' === 'table') || ('{{ $viewMode }}' === 'folder' && activeFolder !== null)" class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-6" x-cloak>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[10px] sm:text-xs font-extrabold text-slate-400 uppercase tracking-wider">
                            <th class="px-6 py-4 w-48">No. Dokumen</th>
                            <th class="px-6 py-4">Judul Dokumen</th>
                            <th class="px-6 py-4 w-48">Divisi / Kategori</th>
                            <th class="px-6 py-4 text-center w-24">Versi</th>
                            <th class="px-6 py-4 text-center w-36">Status</th>
                            <th class="px-6 py-4 text-center w-40">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($items as $doc)
                            @php
                                $groupName = $isAdminOrInspektur ? ($doc->division->name ?? 'Umum') : ($doc->docType->name_types ?? 'General');
                                $folderSlug = Str::slug($groupName);
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors group" 
                                x-show="'{{ $viewMode }}' === 'table' || ('{{ $viewMode }}' === 'folder' && activeFolder === '{{ $folderSlug }}')">
                                
                                <td class="px-6 py-4 text-sm font-bold text-slate-700">{{ $doc->no_doc ?? '-' }}</td>
                                
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                                            <i class="bi bi-file-earmark-text text-lg"></i>
                                        </div>
                                        <div class="overflow-hidden">
                                            <div class="font-bold text-slate-800 text-sm truncate" title="{{ $doc->title }}">{{ $doc->title }}</div>
                                            <div class="text-xs text-slate-500 font-medium">{{ $doc->docType->name_types ?? 'General' }}</div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-700 text-sm truncate">{{ $doc->division->name ?? 'Internal' }}</div>
                                    <div class="text-xs text-slate-400 font-medium">{{ $doc->updated_at->format('d/m/Y') }}</div>
                                </td>
                                
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 border border-slate-200 text-xs font-bold">
                                        v{{ $doc->current_version ?? 1 }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 text-center">
                                    @if($doc->status == 'pending')
                                        <span class="inline-flex px-2 py-1 bg-amber-100 text-amber-700 border border-amber-200 rounded-full text-[10px] font-extrabold tracking-wide">MENUNGGU ACC</span>
                                    @elseif($doc->status == 'revisi')
                                        <span class="inline-flex px-2 py-1 bg-rose-100 text-rose-700 border border-rose-200 rounded-full text-[10px] font-extrabold tracking-wide">PERLU REVISI</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-full text-[10px] font-extrabold tracking-wide">DISETUJUI</span>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('documents.show', $doc->id) }}" class="p-1.5 bg-white border border-slate-200 rounded-lg text-indigo-600 hover:bg-indigo-50 hover:border-indigo-200 transition-colors shadow-sm" title="Detail"><i class="bi bi-eye-fill"></i></a>
                                        
                                        @if($doc->latestVersion)
                                            <a href="{{ route('documents.download', $doc->latestVersion->id) }}" class="p-1.5 bg-white border border-slate-200 rounded-lg text-emerald-600 hover:bg-emerald-50 hover:border-emerald-200 transition-colors shadow-sm" title="Unduh"><i class="bi bi-download"></i></a>
                                        @endif
                                        
                                        @if(in_array($roleName, ['Auditor', 'Admin', 'Inspektur']) && $doc->status == 'pending')
                                            <form action="{{ route('documents.approve', $doc->id) }}" method="POST" class="m-0 inline-block">
                                                @csrf <button type="submit" class="p-1.5 bg-emerald-500 border border-emerald-600 rounded-lg text-white hover:bg-emerald-600 transition-colors shadow-sm" title="ACC" onclick="return confirm('Setujui dokumen ini?')"><i class="bi bi-check-lg"></i></button>
                                            </form>
                                        @endif
                                        
                                        @if(in_array($roleName, ['Auditor', 'Admin', 'Inspektur']) && $doc->status == 'revisi')
                                            <a href="{{ route('documents.revisi.form', $doc->id) }}" class="p-1.5 bg-white border border-slate-200 rounded-lg text-amber-500 hover:bg-amber-50 hover:border-amber-200 transition-colors shadow-sm" title="Revisi"><i class="bi bi-pencil-square"></i></a>
                                        @endif
                                        
                                        @php
                                            $isUploader = $doc->latestVersion && $doc->latestVersion->uploaded_by == auth()->id();
                                            $canForceUpdate = in_array($roleName, ['Admin', 'Inspektur']) || ($isUploader && $doc->status != 'approved');
                                        @endphp
                                        
                                        @if($canForceUpdate && $doc->status != 'approved')
                                            <button @click="forceUpdateModalOpen = true; forceUpdateFormAction = '{{ route('documents.force_update', $doc->id) }}'; forceUpdateDocNumber = '{{ $doc->no_doc ?? 'Tanpa Nomor' }}'" class="p-1.5 bg-white border border-slate-200 rounded-lg text-amber-500 hover:bg-amber-50 hover:border-amber-200 transition-colors shadow-sm" title="Unggah Ulang Berkas">
                                                <i class="bi bi-cloud-upload"></i>
                                            </button>
                                        @endif

                                        @if(in_array($roleName, ['Admin', 'Inspektur']) && $doc->status == 'approved')
                                            <button @click="tteModalOpen = true; tteFormAction = '{{ route('documents.upload_final', $doc->id) }}'; tteDocNumber = '{{ $doc->no_doc }}'" class="p-1.5 bg-white border border-slate-200 rounded-lg text-sky-500 hover:bg-sky-50 hover:border-sky-200 transition-colors shadow-sm" title="Unggah Final TTE">
                                                <i class="bi bi-upload"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if($documents instanceof \Illuminate\Pagination\LengthAwarePaginator && $documents->hasPages())
        <div class="mt-4 flex justify-center">
            {{ $documents->appends(request()->query())->links() }}
        </div>
    @endif


    <div x-show="tteModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" x-transition.opacity>
        <div @click.outside="tteModalOpen = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden" x-transition.scale>
            <div class="px-6 py-4 bg-emerald-50 border-b border-emerald-100 flex justify-between items-center">
                <h5 class="font-extrabold text-emerald-800 flex items-center gap-2"><i class="bi bi-shield-check text-xl"></i> Unggah Berkas Final (TTE)</h5>
                <button @click="tteModalOpen = false" class="text-emerald-500 hover:text-emerald-700"><i class="bi bi-x-lg"></i></button>
            </div>
            <form :action="tteFormAction" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-6">
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm p-4 rounded-xl mb-4 font-medium">
                        Mengunggah dokumen final untuk No: <strong x-text="tteDocNumber"></strong>. <br>
                        Gunakan fitur ini untuk melampirkan berkas yang telah dibubuhi <strong>Tanda Tangan Elektronik (TTE)</strong>.
                    </div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Pilih Berkas PDF Final <span class="text-rose-500">*</span></label>
                    <input type="file" name="file" class="w-full text-sm text-slate-500 border border-slate-200 rounded-xl bg-slate-50 cursor-pointer file:mr-4 file:py-2.5 file:px-4 file:rounded-l-xl file:border-0 file:text-sm file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100" required accept=".pdf">
                    <p class="mt-2 text-xs text-slate-500">Maksimal 10MB. Format wajib: PDF.</p>
                </div>
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                    <button type="button" @click="tteModalOpen = false" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl shadow-sm">Unggah Berkas</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="forceUpdateModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm" x-transition.opacity>
        <div @click.outside="forceUpdateModalOpen = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden" x-transition.scale>
            <div class="px-6 py-4 bg-amber-50 border-b border-amber-100 flex justify-between items-center">
                <h5 class="font-extrabold text-amber-800 flex items-center gap-2"><i class="bi bi-cloud-upload text-xl"></i> Unggah Ulang Berkas</h5>
                <button @click="forceUpdateModalOpen = false" class="text-amber-500 hover:text-amber-700"><i class="bi bi-x-lg"></i></button>
            </div>
            <form :action="forceUpdateFormAction" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-6">
                    <p class="text-sm text-slate-600 mb-4 leading-relaxed font-medium">Memperbarui dokumen No: <strong x-text="forceUpdateDocNumber" class="text-slate-900"></strong>.<br> Tindakan ini akan mengembalikan status menjadi <strong class="text-amber-600">Menunggu ACC</strong> dan menaikkan versi dokumen.</p>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Pilih Berkas Baru <span class="text-rose-500">*</span></label>
                    <input type="file" name="file" class="w-full text-sm text-slate-500 border border-slate-200 rounded-xl bg-slate-50 cursor-pointer file:mr-4 file:py-2.5 file:px-4 file:rounded-l-xl file:border-0 file:text-sm file:font-bold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100" required accept=".pdf,.doc,.docx,.xls,.xlsx">
                    <p class="mt-2 text-xs text-slate-500">Maksimal 10MB. Format: PDF, Word, Excel.</p>
                </div>
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-2">
                    <button type="button" @click="forceUpdateModalOpen = false" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl shadow-sm">Mulai Unggah</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection