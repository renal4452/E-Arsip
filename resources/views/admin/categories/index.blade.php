@extends('layouts.app')

@section('content')
<div class="w-full max-w-7xl mx-auto" x-data="{ 
    activeTab: 'audit', 
    addModalOpen: false 
}">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight mb-1">Manajemen Kategori</h3>
            <p class="text-sm text-slate-500 font-medium">Kelola klasifikasi untuk draf Audit dan Dokumen Publik.</p>
        </div>
        <button @click="addModalOpen = true" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 shrink-0">
            <i class="bi bi-plus-lg text-lg"></i> Tambah Kategori
        </button>
    </div>

    <div class="flex bg-slate-100 rounded-xl p-1 border border-slate-200 mb-6 max-w-md w-full sm:w-auto">
        <button @click="activeTab = 'audit'" 
                :class="activeTab === 'audit' ? 'bg-white text-indigo-600 shadow-sm font-bold' : 'text-slate-500 hover:text-slate-800'" 
                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm transition-all duration-200">
            <i class="bi bi-clipboard-check text-base"></i> Kategori Audit (Pre-LHP)
        </button>
        <button @click="activeTab = 'shared'" 
                :class="activeTab === 'shared' ? 'bg-white text-indigo-600 shadow-sm font-bold' : 'text-slate-500 hover:text-slate-800'" 
                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm transition-all duration-200">
            <i class="bi bi-share text-base"></i> Kategori Ruang Berbagi
        </button>
    </div>

    <div class="w-full">
        <div x-show="activeTab === 'audit'" x-transition.opacity>
            @include('admin.categories.partials.table_audit', ['types' => $auditTypes])
        </div>

        <div x-show="activeTab === 'shared'" x-transition.opacity x-cloak>
            @include('admin.categories.partials.table_shared', ['types' => $sharedTypes])
        </div>
    </div>

    <div x-show="addModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div @click.outside="addModalOpen = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95">
            
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                <h5 class="font-extrabold text-slate-800 flex items-center gap-2 text-lg">
                    <i class="bi bi-folder-plus text-xl text-indigo-600"></i> Tambah Kategori Baru
                </h5>
                <button @click="addModalOpen = false" class="text-slate-400 hover:text-slate-600 transition-colors bg-slate-100 hover:bg-slate-200 w-8 h-8 rounded-full flex items-center justify-center">
                    <i class="bi bi-x-lg text-sm"></i>
                </button>
            </div>

            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Peruntukan Modul</label>
                        <select name="module" class="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" required>
                            <option value="audit">Manajemen Dokumen (Audit)</option>
                            <option value="shared">Ruang Berbagi (Publik)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nama Kategori</label>
                        <input type="text" name="name" class="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-xl px-4 py-2.5 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" placeholder="Contoh: SOP atau LHP Khusus" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Deskripsi (Opsional)</label>
                        <textarea name="description" rows="3" class="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-xl px-4 py-2.5 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" placeholder="Jelaskan kegunaan kategori ini..."></textarea>
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
                    <button type="button" @click="addModalOpen = false" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 font-bold rounded-xl hover:bg-slate-100 hover:text-slate-900 transition-colors shadow-sm text-sm">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md hover:shadow-lg transition-all text-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection