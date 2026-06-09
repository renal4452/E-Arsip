@extends('layouts.app')

@section('content')
@php
    $roleName = auth()->user()->role->name ?? 'User';
    $isAdminOrInspektur = in_array($roleName, ['Admin', 'Inspektur']);
    $viewMode = request('view', 'folder'); 
    $items = $documents instanceof \Illuminate\Pagination\LengthAwarePaginator ? collect($documents->items()) : $documents;
@endphp

<div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6" x-data="{ 
    filterOpen: {{ request()->hasAny(['category_id', 'year', 'start_date', 'end_date']) ? 'true' : 'false' }},
    activeFolder: null,
    folderName: ''
}">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4 border-b border-slate-100 pb-5">
        <div>
            <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mb-1">Ruang Berbagi</h3>
            <p class="text-sm text-slate-500 font-medium">Pusat Unduhan: Akses cepat untuk format laporan, SOP, dan dokumen referensi umum.</p>
        </div>
        <a href="{{ route('shared_documents.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 shrink-0">
            <i class="bi bi-cloud-arrow-up text-lg"></i> Unggah Dokumen Publik
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-5 flex items-center gap-4 hover:shadow-md transition-all duration-300 group">
            <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform duration-300">
                <i class="bi bi-cloud-check text-2xl"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Berkas Publik</p>
                <h4 class="text-2xl font-black text-slate-800 leading-none">
                    {{ $documents instanceof \Illuminate\Pagination\LengthAwarePaginator ? $documents->total() : $documents->count() }}
                </h4>
            </div>
        </div>

        <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-5 flex items-center gap-4 hover:shadow-md transition-all duration-300 group">
            <div class="w-14 h-14 rounded-2xl bg-slate-50 text-slate-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform duration-300">
                <i class="bi bi-tags text-2xl"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Kategori</p>
                <h4 class="text-2xl font-black text-slate-800 leading-none">{{ $categories->count() }}</h4>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-6 p-4 lg:p-5">
        <div class="flex flex-col lg:flex-row justify-between lg:items-center gap-4">
            
            <div class="flex flex-wrap items-center gap-2">
                <button @click="filterOpen = !filterOpen" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 border border-slate-200 bg-white hover:bg-slate-50 transition-colors flex items-center gap-1.5 shadow-sm">
                    <i class="bi bi-sliders"></i> Filter Dokumen
                </button>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-3">
                <form action="{{ route('shared_documents.index') }}" method="GET" class="flex gap-2 w-full lg:w-auto">
                    @foreach(request()->except(['search', 'view']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <input type="hidden" name="view" value="{{ $viewMode }}">
                    <div class="relative w-full lg:w-64">
                        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="search" class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-all duration-200" placeholder="Cari berkas..." value="{{ request('search') }}">
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
            <form action="{{ route('shared_documents.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <input type="hidden" name="view" value="{{ $viewMode }}">
                <input type="hidden" name="search" value="{{ request('search') }}">
                
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Kategori</label>
                    <select name="category_id" class="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
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
                
                <div class="md:col-span-4 flex justify-end gap-2 mt-2">
                    @if(request()->anyFilled(['search', 'category_id', 'start_date', 'end_date']))
                        <a href="{{ route('shared_documents.index', ['view' => $viewMode]) }}" class="px-4 py-2 flex items-center justify-center bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-xl border border-rose-200 text-sm font-bold transition-colors shadow-sm" title="Reset Filter">
                            <i class="bi bi-arrow-clockwise mr-1"></i> Reset
                        </a>
                    @endif
                    <button type="submit" class="px-6 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl text-sm transition-colors shadow-sm">Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>

    @if($items->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-16 text-center">
            <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-5 text-slate-300">
                <i class="bi bi-folder-x text-5xl"></i>
            </div>
            <h6 class="text-xl font-extrabold text-slate-800 mb-2">Data Tidak Ditemukan</h6>
            <p class="text-slate-500 mb-6 max-w-sm mx-auto">Tidak ada berkas publik yang sesuai dengan kriteria pencarian Anda.</p>
        </div>
    @else
        
        @if($viewMode == 'folder')
            @php
                $grouped = $items->groupBy(fn($item) => $isAdminOrInspektur ? ($item->division->name ?? 'Sekretariat') : ($item->category->name ?? 'Umum'));
            @endphp

            <div x-show="activeFolder !== null" x-cloak class="flex items-center mb-6" x-transition.opacity>
                <button @click="activeFolder = null; folderName = ''" class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 shadow-sm rounded-xl text-sm font-bold text-slate-700 hover:bg-slate-50 transition-colors mr-4 hover:-translate-x-1 duration-200">
                    <i class="bi bi-arrow-left"></i> Kembali
                </button>
                <div class="flex items-center gap-2 text-slate-800 font-extrabold text-lg">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">
                        <i class="bi bi-folder2-open"></i>
                    </div>
                    <span x-text="folderName"></span>
                </div>
            </div>

            <div x-show="activeFolder === null" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 mb-6" x-transition.opacity>
                @foreach($grouped as $groupName => $docs)
                    @php $folderSlug = Str::slug($groupName); @endphp
                    <div @click="activeFolder = '{{ $folderSlug }}'; folderName = '{{ $groupName }}'" 
                         class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:border-indigo-400 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer group flex flex-col justify-between h-full">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center shrink-0 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                                <i class="bi bi-folder-fill text-3xl"></i>
                            </div>
                            <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-bold">{{ $docs->count() }} Berkas</span>
                        </div>
                        <div>
                            <h6 class="font-extrabold text-slate-800 leading-tight group-hover:text-indigo-700 transition-colors" title="{{ $groupName }}">{{ $groupName }}</h6>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div x-show="activeFolder !== null" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6" x-cloak>
                <div class="p-1 bg-slate-50 border-b border-slate-200">
                    <div x-data="{ 
                        filterTable(rowSlug) {
                            return activeFolder === rowSlug;
                        }
                    }">
                        @include('shared_documents._table', ['items' => $items])
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6">
                @include('shared_documents._table', ['items' => $items])
            </div>
        @endif
    @endif
</div>
@endsection