@extends('layouts.app')

@section('content')
@php
    $roleName = auth()->user()->role->name ?? 'User';
    $isAdminOrInspektur = in_array($roleName, ['Admin', 'Inspektur']);
    $viewMode = request('view', 'folder'); 
    $items = $documents instanceof \Illuminate\Pagination\LengthAwarePaginator ? collect($documents->items()) : $documents;
@endphp

<div class="w-full" x-data="{ 
    filterOpen: {{ request()->hasAny(['category_id', 'year', 'start_date', 'end_date']) ? 'true' : 'false' }},
    activeFolder: null,
    folderName: ''
}">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 mb-1">Ruang Berbagi (Pusat Unduhan)</h3>
            <p class="text-sm text-slate-500 font-medium">Akses cepat untuk format laporan, SOP, dan dokumen referensi umum.</p>
        </div>
        <a href="{{ route('shared_documents.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-sm transition-colors shrink-0">
            <i class="bi bi-cloud-arrow-up text-lg"></i> Unggah Shared Dokumen
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 mb-6">
        <div class="flex flex-col lg:flex-row justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 bg-slate-50 px-4 py-2 rounded-xl border border-slate-200">
                    <i class="bi bi-cloud-check text-indigo-500"></i>
                    <span class="text-sm font-bold text-slate-700">Total: {{ $documents instanceof \Illuminate\Pagination\LengthAwarePaginator ? $documents->total() : $documents->count() }} Berkas</span>
                </div>
                <button @click="filterOpen = !filterOpen" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                    <i class="bi bi-sliders"></i> Filter
                </button>
            </div>

            <div class="flex items-center gap-2">
                <form action="{{ route('shared_documents.index') }}" method="GET" class="flex-1">
                    <div class="relative">
                        <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="search" class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="Cari dokumen..." value="{{ request('search') }}">
                    </div>
                </form>
                <div class="flex bg-slate-100 rounded-xl p-1 shrink-0">
                    <a href="{{ request()->fullUrlWithQuery(['view' => 'folder']) }}" class="p-2 rounded-lg {{ $viewMode == 'folder' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-400' }}"><i class="bi bi-folder-fill"></i></a>
                    <a href="{{ request()->fullUrlWithQuery(['view' => 'table']) }}" class="p-2 rounded-lg {{ $viewMode == 'table' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-400' }}"><i class="bi bi-list-ul"></i></a>
                </div>
            </div>
        </div>

        <div x-show="filterOpen" x-collapse class="pt-4 mt-4 border-t border-slate-100">
            <form action="{{ route('shared_documents.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input type="hidden" name="view" value="{{ $viewMode }}">
                <select name="category_id" class="bg-slate-50 border-slate-200 rounded-xl text-sm p-2">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                <input type="date" name="start_date" class="bg-slate-50 border-slate-200 rounded-xl text-sm p-2" value="{{ request('start_date') }}">
                <input type="date" name="end_date" class="bg-slate-50 border-slate-200 rounded-xl text-sm p-2" value="{{ request('end_date') }}">
                <button type="submit" class="bg-slate-800 text-white font-bold rounded-xl text-sm">Terapkan</button>
            </form>
        </div>
    </div>

    @if($items->isEmpty())
        <div class="text-center py-20">
            <i class="bi bi-folder-x text-6xl text-slate-300"></i>
            <h5 class="font-bold text-slate-700 mt-4">Data Tidak Ditemukan</h5>
        </div>
    @else
        @if($viewMode == 'folder')
            @php
                $grouped = $items->groupBy(fn($item) => $isAdminOrInspektur ? ($item->division->name ?? 'Sekretariat') : ($item->category->name ?? 'Umum'));
            @endphp
            
            <div x-show="activeFolder === null" class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($grouped as $groupName => $docs)
                    <div @click="activeFolder = '{{ Str::slug($groupName) }}'; folderName = '{{ $groupName }}'" class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md cursor-pointer text-center transition-all">
                        <i class="bi bi-folder-fill text-indigo-500 text-5xl"></i>
                        <p class="font-bold text-slate-800 mt-4 truncate">{{ $groupName }}</p>
                        <p class="text-xs text-slate-400">{{ $docs->count() }} Berkas</p>
                    </div>
                @endforeach
            </div>
            
            <div x-show="activeFolder !== null" class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden" x-cloak>
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <button @click="activeFolder = null" class="text-indigo-600 font-bold text-sm"><i class="bi bi-arrow-left"></i> Kembali ke Folder</button>
                    <h6 class="font-bold text-slate-700" x-text="folderName"></h6>
                </div>
                @include('shared_documents._table', ['items' => $items])
            </div>
        @else
            @include('shared_documents._table', ['items' => $items])
        @endif
    @endif
</div>
@endsection