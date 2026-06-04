@extends('layouts.app')

@section('content')
<div class="w-full">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 mb-1">Unggah Dokumen Baru</h3>
            <p class="text-sm text-slate-500 font-medium">Masukkan draf dokumen untuk diproses ke tahap persetujuan.</p>
        </div>
        <a href="{{ route('documents.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 hover:text-slate-900 font-bold text-sm shadow-sm transition-colors">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    @if ($errors->any())
        <div x-data="{ show: true }" x-show="show" class="mb-8 p-5 bg-rose-50 border border-rose-200 rounded-2xl shadow-sm flex items-start gap-4" x-transition>
            <div class="mt-0.5 text-rose-500 text-2xl">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <div class="flex-1">
                <h4 class="text-sm font-bold text-rose-800 mb-1">Gagal Menyimpan!</h4>
                <p class="text-xs text-rose-600 mb-2">Periksa kembali isian form Anda:</p>
                <ul class="list-disc list-inside text-xs font-medium text-rose-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button @click="show = false" class="text-rose-400 hover:text-rose-600 transition-colors">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <h6 class="font-extrabold text-indigo-600 m-0 flex items-center gap-2">
                    <i class="bi bi-file-earmark-plus text-lg"></i> Form Unggah Dokumen
                </h6>
            </div>
            
            <div class="p-6 md:p-8">
                <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="md:col-span-2">
                            <label for="no_doc" class="block text-sm font-bold text-slate-700 mb-2">
                                Nomor Dokumen / Referensi <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="no_doc" id="no_doc" 
                                   class="w-full px-4 py-3 rounded-xl text-sm transition-colors border focus:outline-none focus:ring-2 focus:bg-white
                                   @error('no_doc') border-rose-300 bg-rose-50 focus:border-rose-500 focus:ring-rose-500 text-rose-900 
                                   @else border-slate-200 bg-slate-50 focus:border-indigo-500 focus:ring-indigo-500 text-slate-900 @enderror" 
                                   value="{{ old('no_doc') }}" 
                                   placeholder="Contoh: 001/SOP/HRD/2026" 
                                   required>
                            @error('no_doc') 
                                <p class="mt-2 text-xs font-bold text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">
                                Pengunggah Sistem
                            </label>
                            <div class="w-full px-4 py-3 rounded-xl text-sm border border-slate-200 bg-slate-100 text-slate-500 flex items-center gap-2 font-semibold select-none">
                                <i class="bi bi-person-circle text-slate-400 text-base"></i>
                                <span class="truncate">{{ auth()->user()->name ?? 'Tidak Terdeteksi' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="title" class="block text-sm font-bold text-slate-700 mb-2">
                            Judul Dokumen <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" 
                               class="w-full px-4 py-3 rounded-xl text-sm transition-colors border focus:outline-none focus:ring-2 focus:bg-white
                               @error('title') border-rose-300 bg-rose-50 focus:border-rose-500 focus:ring-rose-500 text-rose-900 
                               @else border-slate-200 bg-slate-50 focus:border-indigo-500 focus:ring-indigo-500 text-slate-900 @enderror" 
                               value="{{ old('title') }}" 
                               placeholder="Contoh: LHP Kinerja Operasional" 
                               required>
                        @error('title') 
                            <p class="mt-2 text-xs font-bold text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8" x-data="{ selectedFile: '', selectedSize: '' }">
                        <div>
                            <label for="doc_type_id" class="block text-sm font-bold text-slate-700 mb-2">
                                Kategori Dokumen <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="doc_type_id" id="doc_type_id" 
                                        class="w-full px-4 py-3 rounded-xl text-sm transition-colors border focus:outline-none focus:ring-2 focus:bg-white appearance-none pr-10
                                        @error('doc_type_id') border-rose-300 bg-rose-50 focus:border-rose-500 focus:ring-rose-500 text-rose-900 
                                        @else border-slate-200 bg-slate-50 focus:border-indigo-500 focus:ring-indigo-500 text-slate-900 @enderror" 
                                        required>
                                    <option value="" class="text-slate-400">-- Pilih Kategori --</option>
                                    @foreach($categories ?? [] as $cat)
                                        <option value="{{ $cat->id }}" {{ old('doc_type_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name_types }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                    <i class="bi bi-chevron-down text-xs"></i>
                                </div>
                            </div>
                            @error('doc_type_id')
                                <p class="mt-2 text-xs font-bold text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="file" class="block text-sm font-bold text-slate-700 mb-2">
                                File Dokumen <span class="text-rose-500">*</span>
                            </label>
                            <input type="file" name="file" id="file" 
                                   @change="
                                        const file = $event.target.files[0];
                                        if (file) {
                                            selectedFile = file.name;
                                            selectedSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                                        } else {
                                            selectedFile = '';
                                            selectedSize = '';
                                        }
                                   "
                                   class="w-full text-sm text-slate-500 border border-slate-200 rounded-xl bg-slate-50 cursor-pointer
                                   file:mr-4 file:py-3 file:px-4 file:rounded-l-xl file:border-0 file:text-sm file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-all
                                   @error('file') border-rose-300 bg-rose-50 @enderror" 
                                   required 
                                   accept=".pdf,.doc,.docx,.xls,.xlsx">
                            
                            <template x-if="selectedFile">
                                <div class="mt-3 p-3 bg-indigo-50/50 border border-indigo-100 rounded-xl flex items-center justify-between">
                                    <span class="text-xs font-bold text-indigo-700 truncate max-w-[70%]" x-text="selectedFile"></span>
                                    <span class="text-[10px] font-extrabold text-indigo-500 uppercase tracking-wider bg-white px-2 py-1 rounded-md shadow-sm border border-indigo-50/80" x-text="selectedSize"></span>
                                </div>
                            </template>

                            <p class="mt-2 text-xs font-medium text-slate-500 flex items-center gap-1" x-show="!selectedFile">
                                <i class="bi bi-info-circle"></i> Format: PDF, Word, atau Excel.
                            </p>
                            @error('file') 
                                <p class="mt-2 text-xs font-bold text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <hr class="border-slate-100 mb-6">

                    <div class="flex flex-col sm:flex-row justify-end gap-3">
                        <a href="{{ route('documents.index') }}" class="px-6 py-2.5 bg-white border border-slate-200 text-slate-700 font-bold text-sm rounded-xl hover:bg-slate-50 text-center transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-sm flex items-center justify-center gap-2 transition-colors">
                            <i class="bi bi-send"></i> Simpan Dokumen
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-1 bg-sky-50 border border-sky-100 rounded-2xl shadow-sm h-fit">
            <div class="p-6">
                <h6 class="font-extrabold text-sky-800 mb-4 text-xs uppercase tracking-wider flex items-center gap-2">
                    <i class="bi bi-info-circle-fill text-lg"></i> Ketentuan Unggah
                </h6>
                <ul class="text-sm font-medium text-sky-800/80 space-y-3 list-disc list-inside">
                    <li>Gunakan format <strong class="text-sky-900">PDF</strong> untuk dokumen final.</li>
                    <li>Gunakan format <strong class="text-sky-900">Word/Excel</strong> untuk draf yang butuh reviu.</li>
                    <li>Batas maksimal ukuran file adalah <strong class="text-sky-900">10MB</strong>.</li>
                    <li>Pastikan Nomor Dokumen unik dan belum pernah digunakan sebelumnya.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection