@extends('layouts.app')

@section('content')
<div class="w-full">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 mb-1">Unggah Dokumen Publik</h3>
            <p class="text-sm text-slate-500 font-medium">Bagikan format laporan, SOP, atau surat edaran ke seluruh divisi.</p>
        </div>
        <a href="{{ route('shared_documents.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 hover:text-slate-900 font-bold text-sm shadow-sm transition-colors">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-8 p-5 bg-rose-50 border border-rose-200 rounded-2xl shadow-sm flex items-start gap-4">
            <div class="mt-0.5 text-rose-500 text-2xl"><i class="bi bi-exclamation-triangle-fill"></i></div>
            <div class="flex-1">
                <h4 class="text-sm font-bold text-rose-800 mb-1">Gagal Mengunggah!</h4>
                <ul class="list-disc list-inside text-xs font-medium text-rose-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                <i class="bi bi-cloud-upload text-indigo-600 text-lg"></i>
                <h6 class="font-extrabold text-indigo-600 uppercase tracking-wider text-xs m-0">Form Unggah Dokumen</h6>
            </div>
            
            <div class="p-6 md:p-8">
                <form action="{{ route('shared_documents.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-6">
                        <label for="title" class="block text-sm font-bold text-slate-700 mb-2">Judul Dokumen <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" id="title" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-colors" value="{{ old('title') }}" required placeholder="Contoh: Format Laporan Keuangan 2026">
                    </div>

                    <div class="mb-6">
                        <label for="category_id" class="block text-sm font-bold text-slate-700 mb-2">Kategori Folder <span class="text-rose-500">*</span></label>
                        <select name="category_id" id="category_id" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-colors appearance-none" required>
                            <option value="" disabled selected>-- Pilih Kategori Penempatan --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-xs text-slate-500 font-medium"><i class="bi bi-info-circle mr-1"></i> Kategori membantu divisi lain menemukan dokumen lebih cepat.</p>
                    </div>

                    <div class="mb-6">
                        <label for="description" class="block text-sm font-bold text-slate-700 mb-2">Keterangan Singkat</label>
                        <textarea name="description" id="description" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-colors" rows="3" placeholder="Opsional: Tambahkan deskripsi atau petunjuk pengisian...">{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-8">
                        <label for="file" class="block text-sm font-bold text-slate-700 mb-2">Pilih File <span class="text-rose-500">*</span></label>
                        <input type="file" name="file" id="file" class="w-full text-sm text-slate-500 border border-slate-200 rounded-xl bg-slate-50 cursor-pointer file:mr-4 file:py-3 file:px-4 file:rounded-l-xl file:border-0 file:text-sm file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-all" required accept=".pdf,.doc,.docx,.xls,.xlsx">
                        <p class="mt-2 text-xs font-bold text-rose-500 flex items-center gap-1">
                            <i class="bi bi-info-circle"></i> Format: PDF, DOCX, XLSX. Maks. 10MB.
                        </p>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-slate-100">
                        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-sm transition-colors flex items-center gap-2">
                            <i class="bi bi-send"></i> Bagikan Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-1 bg-amber-50 border border-amber-100 rounded-2xl p-6 h-fit">
            <h6 class="font-extrabold text-amber-900 mb-4 text-xs uppercase tracking-wider flex items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill text-lg"></i> Perhatian!
            </h6>
            <div class="space-y-4 text-sm text-amber-900/80 font-medium">
                <p>Dokumen yang diunggah di halaman ini <strong>bersifat publik (internal)</strong> dan dapat diakses seluruh divisi.</p>
                <p class="mb-0">Mohon <strong>JANGAN</strong> mengunggah file rahasia/pribadi di menu ini. Gunakan modul "Manajemen Dokumen" untuk berkas sensitif.</p>
            </div>
        </div>
    </div>
</div>
@endsection