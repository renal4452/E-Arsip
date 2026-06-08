@extends('layouts.app')

@section('content')
@php
    $roleName = auth()->user()->role->name ?? 'User';
    $isAdminOrInspektur = in_array($roleName, ['Admin', 'Inspektur']);
    $viewMode = request('view', 'folder'); 
    $items = $documents instanceof \Illuminate\Pagination\LengthAwarePaginator ? collect($documents->items()) : $documents;
@endphp

<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6" x-data="{ 
    filterOpen: {{ request()->hasAny(['year', 'type', 'start_date', 'end_date']) ? 'true' : 'false' }},
    activeFolder: null,
    folderName: '',
    tteModalOpen: false, tteFormAction: '', tteDocNumber: '',
    forceUpdateModalOpen: false, forceUpdateFormAction: '', forceUpdateDocNumber: ''
}">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4 border-b border-slate-100 pb-5">
        <div>
            <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mb-1">Manajemen Dokumen</h3>
            <p class="text-sm text-slate-500 font-medium">Pusat kendali draf LHP, Nota Dinas, dan riwayat persetujuan audit.</p>
        </div>
        <a href="{{ route('documents.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 shrink-0">
            <i class="bi bi-cloud-arrow-up text-lg"></i> Unggah Dokumen Baru
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-5 flex items-center gap-4 hover:shadow-md hover:border-indigo-300 transition-all duration-300 group">
            <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform duration-300">
                <i class="bi bi-files text-2xl"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Dokumen</p>
                <h4 class="text-2xl font-black text-slate-800 leading-none">{{ $documents instanceof \Illuminate\Pagination\LengthAwarePaginator ? $documents->total() : $documents->count() }}</h4>
            </div>
        </div>
        
        <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-5 flex items-center gap-4 hover:shadow-md hover:border-amber-300 transition-all duration-300 group">
            <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform duration-300">
                <i class="bi bi-hourglass-split text-2xl"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Dalam Proses</p>
                <h4 class="text-2xl font-black text-slate-800 leading-none">{{ $stats['pending'] ?? 0 }}</h4>
            </div>
        </div>
        
        <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-5 flex items-center gap-4 hover:shadow-md hover:border-emerald-300 transition-all duration-300 group">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform duration-300">
                <i class="bi bi-check-circle text-2xl"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Disetujui</p>
                <h4 class="text-2xl font-black text-slate-800 leading-none">{{ $stats['approved'] ?? 0 }}</h4>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-6 p-4 lg:p-5">
        <div class="flex flex-col lg:flex-row justify-between lg:items-center gap-4">
            
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mr-2"><i class="bi bi-funnel"></i> Status:</span>
                <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="px-4 py-1.5 rounded-full text-xs font-bold transition-all duration-200 {{ !request('status') ? 'bg-slate-800 text-white shadow-md' : 'bg-slate-50 text-slate-600 border border-slate-200 hover:bg-slate-100 hover:text-slate-800' }}">Semua</a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}" class="px-4 py-1.5 rounded-full text-xs font-bold transition-all duration-200 {{ request('status') == 'pending' ? 'bg-amber-500 text-white shadow-md' : 'bg-slate-50 text-slate-600 border border-slate-200 hover:bg-slate-100 hover:text-slate-800' }}">Menunggu ACC</a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'revisi']) }}" class="px-4 py-1.5 rounded-full text-xs font-bold transition-all duration-200 {{ request('status') == 'revisi' ? 'bg-rose-500 text-white shadow-md' : 'bg-slate-50 text-slate-600 border border-slate-200 hover:bg-slate-100 hover:text-slate-800' }}">Revisi</a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'approved']) }}" class="px-4 py-1.5 rounded-full text-xs font-bold transition-all duration-200 {{ request('status') == 'approved' ? 'bg-emerald-500 text-white shadow-md' : 'bg-slate-50 text-slate-600 border border-slate-200 hover:bg-slate-100 hover:text-slate-800' }}">Disetujui</a>
                
                <button @click="filterOpen = !filterOpen" class="ml-auto lg:ml-2 px-3 py-1.5 rounded-lg text-xs font-bold text-indigo-600 border border-indigo-200 bg-indigo-50 hover:bg-indigo-100 transition-colors flex items-center gap-1">
                    <i class="bi bi-sliders"></i> Filter Lanjutan
                </button>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-3">
                <form action="{{ route('documents.index') }}" method="GET" class="flex gap-2 w-full lg:w-auto">
                    @foreach(request()->except(['search', 'view']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <input type="hidden" name="view" value="{{ $viewMode }}">
                    <div class="relative w-full lg:w-64">
                        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="search" class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all duration-200" placeholder="Cari No/Judul..." value="{{ request('search') }}">
                    </div>
                </form>

                <div class="flex items-center bg-slate-100 rounded-xl p-1 border border-slate-200 shrink-0 w-full sm:w-auto justify-center">
                    <a href="{{ request()->fullUrlWithQuery(['view' => 'folder']) }}" class="px-4 py-1.5 rounded-lg text-sm transition-all duration-200 {{ $viewMode == 'folder' ? 'bg-white text-indigo-600 shadow-sm font-bold' : 'text-slate-500 hover:text-slate-800' }}" title="Tampilan Folder"><i class="bi bi-folder-fill"></i></a>
                    <a href="{{ request()->fullUrlWithQuery(['view' => 'table']) }}" class="px-4 py-1.5 rounded-lg text-sm transition-all duration-200 {{ $viewMode == 'table' ? 'bg-white text-indigo-600 shadow-sm font-bold' : 'text-slate-500 hover:text-slate-800' }}" title="Tampilan Tabel"><i class="bi bi-list-ul"></i></a>
                </div>
            </div>
        </div>

        <div x-show="filterOpen" x-collapse x-cloak>
            <hr class="border-slate-100 my-5">
            <form action="{{ route('documents.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
                <input type="hidden" name="view" value="{{ $viewMode }}">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input type="hidden" name="search" value="{{ request('search') }}">
                
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tahun</label>
                    <select name="year" class="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        <option value="">Semua Tahun</option>
                        @for($y = date('Y'); $y >= 2022; $y--)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kategori Dokumen</label>
                    <select name="type" class="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        <option value="">Semua Kategori</option>
                        @foreach($categories ?? [] as $cat)
                            <option value="{{ $cat->id }}" {{ request('type') == $cat->id ? 'selected' : '' }}>{{ $cat->name_types }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tgl Mulai</label>
                    <input type="date" name="start_date" class="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" value="{{ request('start_date') }}">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Sampai</label>
                    <input type="date" name="end_date" class="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" value="{{ request('end_date') }}">
                </div>
                <div class="flex gap-2 h-10">
                    <button type="submit" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl text-sm transition-colors shadow-sm">Terapkan</button>
                    @if(request()->anyFilled(['search', 'status', 'year', 'type', 'start_date', 'end_date']))
                        <a href="{{ route('documents.index', ['view' => $viewMode]) }}" class="w-10 flex items-center justify-center bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-xl border border-rose-200 transition-colors shadow-sm" title="Reset Filter">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if($items->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-16 text-center">
            <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-5 text-slate-300">
                <i class="bi bi-folder-x text-5xl"></i>
            </div>
            <h6 class="text-xl font-extrabold text-slate-800 mb-2">Tidak Ada Dokumen</h6>
            <p class="text-slate-500 mb-6 max-w-sm mx-auto">Kami tidak dapat menemukan dokumen yang sesuai dengan kriteria pencarian atau filter Anda.</p>
            @if(request()->anyFilled(['search', 'status', 'year', 'type', 'start_date', 'end_date']))
                <a href="{{ route('documents.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-50 text-indigo-700 rounded-xl text-sm font-bold border border-indigo-200 hover:bg-indigo-100 transition-colors">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset Semua Filter
                </a>
            @endif
        </div>
    @else
        
        @if($viewMode == 'folder')
            @php
                $groupedDocuments = $items->groupBy(function ($item) use ($isAdminOrInspektur) {
                    return $isAdminOrInspektur ? ($item->division->name ?? 'Umum') : ($item->docType->name_types ?? 'General');
                });
            @endphp

            <div x-show="activeFolder !== null" x-cloak class="flex items-center mb-6" x-transition.opacity>
                <button @click="activeFolder = null; folderName = ''" class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 shadow-sm rounded-xl text-sm font-bold text-slate-700 hover:bg-slate-50 transition-colors mr-4 hover:-translate-x-1 duration-200">
                    <i class="bi bi-arrow-left"></i> Kembali
                </button>
                <div class="flex items-center gap-2 text-slate-800 font-extrabold text-lg">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">
                        <i class="{{ $isAdminOrInspektur ? 'bi-building' : 'bi-tags' }}"></i>
                    </div>
                    <span x-text="folderName"></span>
                </div>
            </div>

            <div x-show="activeFolder === null" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 mb-6" x-transition.opacity>
                @foreach($groupedDocuments as $groupName => $docsInGroup)
                    @php $folderSlug = Str::slug($groupName); @endphp
                    <div @click="activeFolder = '{{ $folderSlug }}'; folderName = '{{ $groupName }}'" 
                         class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:border-indigo-400 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer group flex flex-col justify-between h-full">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center shrink-0 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                                <i class="bi bi-folder-fill text-3xl"></i>
                            </div>
                            <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-bold">{{ $docsInGroup->count() }} Berkas</span>
                        </div>
                        <div>
                            <h6 class="font-extrabold text-slate-800 leading-tight group-hover:text-indigo-700 transition-colors" title="{{ $groupName }}">{{ $groupName }}</h6>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div x-show="('{{ $viewMode }}' === 'table') || ('{{ $viewMode }}' === 'folder' && activeFolder !== null)" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6" x-cloak>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs font-extrabold text-slate-500 uppercase tracking-wider">
                            <th class="px-6 py-4 w-48 whitespace-nowrap">No. Dokumen</th>
                            <th class="px-6 py-4 min-w-[250px]">Judul Dokumen</th>
                            <th class="px-6 py-4 w-48 whitespace-nowrap">Divisi / Kategori</th>
                            <th class="px-6 py-4 text-center w-24 whitespace-nowrap">Versi</th>
                            <th class="px-6 py-4 text-center w-36 whitespace-nowrap">Status</th>
                            <th class="px-6 py-4 text-center w-40 whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($items as $doc)
                            @php
                                $groupName = $isAdminOrInspektur ? ($doc->division->name ?? 'Umum') : ($doc->docType->name_types ?? 'General');
                                $folderSlug = Str::slug($groupName);
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors duration-200 group" 
                                x-show="'{{ $viewMode }}' === 'table' || ('{{ $viewMode }}' === 'folder' && activeFolder === '{{ $folderSlug }}')">
                                
                                <td class="px-6 py-4 text-sm font-bold text-slate-700 whitespace-nowrap">{{ $doc->no_doc ?? '-' }}</td>
                                
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 border border-slate-200 flex items-center justify-center shrink-0">
                                            <i class="bi bi-file-earmark-text text-lg"></i>
                                        </div>
                                        <div class="overflow-hidden">
                                            <div class="font-bold text-slate-800 text-sm line-clamp-2" title="{{ $doc->title }}">{{ $doc->title }}</div>
                                            <div class="text-xs text-slate-500 mt-0.5">{{ $doc->docType->name_types ?? 'General' }}</div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-bold text-slate-700 text-sm">{{ $doc->division->name ?? 'Internal' }}</div>
                                    <div class="text-xs text-slate-500 mt-0.5 flex items-center gap-1">
                                        <i class="bi bi-clock-history"></i> {{ $doc->updated_at->format('d M Y') }}
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 border border-slate-200 text-xs font-bold">
                                        v{{ $doc->current_version ?? 1 }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    @if($doc->status == 'pending')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-lg text-xs font-bold tracking-wide">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> MENUNGGU ACC
                                        </span>
                                    @elseif($doc->status == 'revisi')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-200 rounded-lg text-xs font-bold tracking-wide">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> PERLU REVISI
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg text-xs font-bold tracking-wide">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> DISETUJUI
                                        </span>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('documents.show', $doc->id) }}" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-lg text-indigo-600 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all shadow-sm" title="Detail Dokumen">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        
                                        @if($doc->latestVersion)
                                            <a href="{{ route('documents.download', $doc->latestVersion->id) }}" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-800 hover:text-white hover:border-slate-800 transition-all shadow-sm" title="Unduh File">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        @endif
                                        
                                        @if(in_array($roleName, ['Auditor', 'Admin', 'Inspektur']) && $doc->status == 'pending')
                                            <form action="{{ route('documents.approve', $doc->id) }}" method="POST" class="m-0 inline-block">
                                                @csrf 
                                                <button type="submit" class="w-8 h-8 flex items-center justify-center bg-emerald-500 border border-emerald-600 rounded-lg text-white hover:bg-emerald-600 hover:scale-105 transition-all shadow-sm" title="Setujui Dokumen" onclick="return confirm('Apakah Anda yakin ingin menyetujui dokumen ini?')">
                                                    <i class="bi bi-check-lg text-lg"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if(in_array($roleName, ['Auditor', 'Admin', 'Inspektur']) && $doc->status == 'revisi')
                                            <a href="{{ route('documents.revisi.form', $doc->id) }}" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-lg text-amber-600 hover:bg-amber-500 hover:text-white hover:border-amber-500 transition-all shadow-sm" title="Beri Catatan Revisi">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        @endif
                                        
                                        @php
                                            $isUploader = $doc->latestVersion && $doc->latestVersion->uploaded_by == auth()->id();
                                            $canForceUpdate = in_array($roleName, ['Admin', 'Inspektur']) || ($isUploader && $doc->status != 'approved');
                                        @endphp
                                        
                                        @if($canForceUpdate && $doc->status != 'approved')
                                            <button @click="forceUpdateModalOpen = true; forceUpdateFormAction = '{{ route('documents.force_update', $doc->id) }}'; forceUpdateDocNumber = '{{ $doc->no_doc ?? 'Tanpa Nomor' }}'" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-lg text-amber-600 hover:bg-amber-50 hover:border-amber-300 transition-all shadow-sm" title="Unggah Ulang (Timpa File)">
                                                <i class="bi bi-cloud-upload"></i>
                                            </button>
                                        @endif

                                        @if(in_array($roleName, ['Admin', 'Inspektur']) && $doc->status == 'approved')
                                            <button @click="tteModalOpen = true; tteFormAction = '{{ route('documents.upload_final', $doc->id) }}'; tteDocNumber = '{{ $doc->no_doc }}'" class="w-8 h-8 flex items-center justify-center bg-sky-50 border border-sky-200 rounded-lg text-sky-600 hover:bg-sky-600 hover:text-white hover:border-sky-600 transition-all shadow-sm" title="Unggah File Final (TTE)">
                                                <i class="bi bi-shield-lock"></i>
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
        <div class="mt-6 flex justify-center">
            {{ $documents->appends(request()->query())->links() }}
        </div>
    @endif

    <div x-show="tteModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div @click.outside="tteModalOpen = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95">
            
            <div class="px-6 py-4 bg-emerald-50 border-b border-emerald-100 flex justify-between items-center">
                <h5 class="font-extrabold text-emerald-800 flex items-center gap-2 text-lg"><i class="bi bi-shield-check text-xl"></i> Unggah Berkas Final (TTE)</h5>
                <button @click="tteModalOpen = false" class="text-emerald-500 hover:text-emerald-800 transition-colors bg-emerald-100 hover:bg-emerald-200 w-8 h-8 rounded-full flex items-center justify-center">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <form :action="tteFormAction" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-6">
                    <div class="bg-emerald-50/50 border border-emerald-200 text-emerald-800 text-sm p-4 rounded-xl mb-5 leading-relaxed">
                        Anda akan mengunggah dokumen final untuk No: <strong x-text="tteDocNumber" class="text-emerald-900"></strong>.<br> 
                        Gunakan fitur ini untuk melampirkan berkas yang telah dibubuhi <strong>Tanda Tangan Elektronik (TTE)</strong>.
                    </div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Pilih Berkas PDF <span class="text-rose-500">*</span></label>
                    <input type="file" name="file" class="w-full text-sm text-slate-500 border border-slate-200 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 cursor-pointer file:mr-4 file:py-2.5 file:px-4 file:rounded-l-xl file:border-0 file:border-r file:border-slate-200 file:text-sm file:font-bold file:bg-slate-50 file:text-slate-700 hover:file:bg-slate-100 transition-all shadow-sm" required accept=".pdf">
                    <p class="mt-2 text-xs text-slate-500 font-medium"><i class="bi bi-info-circle"></i> Maksimal ukuran: 10MB. Format wajib: PDF.</p>
                </div>
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
                    <button type="button" @click="tteModalOpen = false" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 font-bold rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-colors shadow-sm">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-md hover:shadow-lg transition-all">Unggah Berkas</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="forceUpdateModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div @click.outside="forceUpdateModalOpen = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95">
            
            <div class="px-6 py-4 bg-amber-50 border-b border-amber-200 flex justify-between items-center">
                <h5 class="font-extrabold text-amber-800 flex items-center gap-2 text-lg"><i class="bi bi-cloud-upload text-xl"></i> Unggah Ulang Berkas (Revisi Baru)</h5>
                <button @click="forceUpdateModalOpen = false" class="text-amber-500 hover:text-amber-800 transition-colors bg-amber-100 hover:bg-amber-200 w-8 h-8 rounded-full flex items-center justify-center">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <form :action="forceUpdateFormAction" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-6">
                    <div class="bg-amber-50/50 border border-amber-200 text-amber-800 text-sm p-4 rounded-xl mb-5 leading-relaxed">
                        Anda akan memperbarui dokumen No: <strong x-text="forceUpdateDocNumber" class="text-amber-900"></strong>.<br> 
                        Tindakan ini akan otomatis menaikkan versi berkas saat ini dan mengembalikan status peninjauan berkas menjadi <strong class="bg-amber-100 px-1.5 py-0.5 rounded text-amber-700">Menunggu ACC</strong>.
                    </div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Pilih Berkas Baru <span class="text-rose-500">*</span></label>
                    <input type="file" name="file" class="w-full text-sm text-slate-500 border border-slate-200 rounded-xl bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 cursor-pointer file:mr-4 file:py-2.5 file:px-4 file:rounded-l-xl file:border-0 file:border-r file:border-slate-200 file:text-sm file:font-bold file:bg-slate-50 file:text-slate-700 hover:file:bg-slate-100 transition-all shadow-sm" required>
                    <p class="mt-2 text-xs text-slate-500 font-medium"><i class="bi bi-info-circle"></i> Maksimal ukuran: 10MB. Gunakan format dokumen yang sesuai (PDF/Word/Excel).</p>
                </div>
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
                    <button type="button" @click="forceUpdateModalOpen = false" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 font-bold rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-colors shadow-sm">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl shadow-md hover:shadow-lg transition-all">Perbarui Berkas</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection