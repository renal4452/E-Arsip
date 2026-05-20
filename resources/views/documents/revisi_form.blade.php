@extends('layouts.app')

@section('content')
<div class="w-full">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 mb-1">Kirim Revisi Dokumen</h3>
            <p class="text-sm text-slate-500 font-medium">Perbarui berkas Anda berdasarkan catatan perbaikan dari Auditor.</p>
        </div>
        <a href="{{ route('documents.show', $document->id) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 hover:text-slate-900 font-bold text-sm shadow-sm transition-colors">
            <i class="bi bi-arrow-left"></i> Batal
        </a>
    </div>

    <div class="max-w-3xl mx-auto">
        
        @if($document->auditor_note)
        <div class="bg-amber-50 border-l-4 border-amber-500 rounded-xl shadow-sm mb-6 p-5">
            <h6 class="font-bold text-amber-800 mb-2 flex items-center gap-2">
                <i class="bi bi-chat-square-dots-fill text-lg"></i> Instruksi Perbaikan:
            </h6>
            <p class="text-sm text-amber-900 leading-relaxed font-medium">
                "{{ $document->auditor_note }}"
            </p>
        </div>
        @endif

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                <i class="bi bi-cloud-arrow-up text-indigo-600 text-lg"></i>
                <h6 class="font-extrabold text-indigo-600 uppercase tracking-wider text-xs m-0">
                    Unggah Versi v{{ $document->current_version + 1 }}
                </h6>
            </div>
            
            <div class="p-6 md:p-8">
                <form action="{{ route('documents.update.revision', $document->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="bg-slate-50 border border-slate-100 p-4 rounded-xl flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 bg-white rounded-lg border border-slate-200 shadow-sm flex items-center justify-center shrink-0">
                            <i class="bi bi-file-earmark-pdf text-rose-500 text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Nama Dokumen</p>
                            <h4 class="text-sm font-bold text-slate-800">{{ $document->title }}</h4>
                            <p class="text-xs text-slate-500 font-medium">{{ $document->no_doc }}</p>
                        </div>
                    </div>

                    <div class="mb-8">
                        <label for="file" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                            Pilih Berkas Baru <span class="text-rose-500">*</span>
                        </label>
                        <input type="file" name="file" id="file" 
                               class="w-full text-sm text-slate-500 border border-slate-200 rounded-xl bg-slate-50 cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500
                               file:mr-4 file:py-3 file:px-4 file:rounded-l-xl file:border-0 file:text-sm file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-all
                               @error('file') border-rose-300 @enderror" 
                               required>
                        @error('file')
                            <p class="mt-2 text-xs font-bold text-rose-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-3 text-xs font-medium text-slate-500 flex items-center gap-1">
                            <i class="bi bi-info-circle"></i> Format yang didukung: <strong class="text-slate-700">PDF, DOCX, XLSX</strong>. Maksimal 10MB.
                        </p>
                    </div>

                    <hr class="border-slate-100 mb-6">

                    <button type="submit" class="w-full px-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-sm flex items-center justify-center gap-2 transition-colors">
                        <i class="bi bi-send-check text-lg"></i> Kirim Revisi Sekarang
                    </button>
                </form>
            </div>
        </div>

        <div class="mt-6 p-4 bg-white border border-slate-200 rounded-xl shadow-sm text-center">
            <p class="text-sm text-slate-500 font-medium flex items-center justify-center gap-2">
                <i class="bi bi-shield-fill-check text-emerald-500 text-lg"></i> 
                <span>Setelah mengunggah, status dokumen otomatis kembali menjadi <strong class="text-slate-700">Menunggu ACC</strong>.</span>
            </p>
        </div>

    </div>
</div>
@endsection