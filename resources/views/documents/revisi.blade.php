@extends('layouts.app')

@section('content')
<div class="w-full">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800 mb-1">Unggah Revisi Dokumen</h3>
            <p class="text-sm text-slate-500 font-medium">Kirimkan perbaikan berkas berdasarkan catatan dari Auditor.</p>
        </div>
        <a href="{{ route('documents.show', $document->id) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 hover:text-slate-900 font-bold text-sm shadow-sm transition-colors">
            <i class="bi bi-arrow-left"></i> Batal
        </a>
    </div>

    <div class="max-w-3xl mx-auto">
        
        <div class="bg-amber-50 border-l-4 border-amber-500 rounded-xl shadow-sm mb-6 p-5">
            <h6 class="font-bold text-amber-800 mb-2 flex items-center gap-2">
                <i class="bi bi-chat-left-dots-fill text-lg"></i> Catatan Perbaikan Auditor:
            </h6>
            <p class="text-sm text-amber-900 leading-relaxed font-medium">
                {{ $document->auditor_note ?? 'Silakan unggah berkas perbaikan sesuai instruksi sebelumnya.' }}
            </p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                <i class="bi bi-cloud-arrow-up text-indigo-600 text-lg"></i>
                <h6 class="font-extrabold text-indigo-600 uppercase tracking-wider text-xs m-0">
                    Form Pembaruan Versi
                </h6>
            </div>
            
            <div class="p-6 md:p-8">
                <form action="{{ route('documents.update.revision', $document->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nomor Dokumen</label>
                            <div class="px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-700 text-sm">
                                {{ $document->no_doc }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Target Versi Baru</label>
                            <div class="px-4 py-3 bg-indigo-50 border border-indigo-100 rounded-xl font-bold text-indigo-700 text-sm flex items-center justify-between">
                                <span>v{{ $document->current_version + 1 }}</span>
                                <span class="px-2 py-0.5 bg-indigo-200 text-indigo-800 text-[10px] rounded-full uppercase tracking-widest">Revisi</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Judul Dokumen</label>
                        <input type="text" value="{{ $document->title }}" readonly disabled
                               class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-xl text-sm font-semibold text-slate-500 cursor-not-allowed">
                    </div>

                    <div class="mb-8">
                        <label for="file" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                            Pilih Berkas Baru <span class="text-rose-500">*</span>
                        </label>
                        <div class="p-6 border-2 border-dashed border-slate-300 bg-slate-50 rounded-xl text-center hover:bg-slate-100 hover:border-indigo-400 transition-colors">
                            <i class="bi bi-file-earmark-arrow-up text-4xl text-slate-400 mb-3 block"></i>
                            <input type="file" name="file" id="file" 
                                   class="block w-full text-sm text-slate-500 mx-auto max-w-xs
                                   file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-indigo-100 file:text-indigo-700 hover:file:bg-indigo-200 cursor-pointer" 
                                   required accept=".pdf,.doc,.docx,.xls,.xlsx">
                            <p class="mt-4 text-xs font-medium text-slate-500">
                                Format: <strong class="text-slate-700">PDF, DOCX, atau XLSX</strong> (Maks. 10MB)
                            </p>
                        </div>
                        @error('file')
                            <p class="mt-2 text-xs font-bold text-rose-500 text-center">{{ $message }}</p>
                        @enderror
                    </div>

                    <hr class="border-slate-100 mb-6">

                    <button type="submit" class="w-full px-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-sm flex items-center justify-center gap-2 transition-colors">
                        <i class="bi bi-send-check text-lg"></i> Kirim Revisi ke Auditor
                    </button>
                </form>
            </div>
        </div>

        <div class="mt-6 text-center">
            <p class="text-sm text-slate-500 font-medium flex items-center justify-center gap-2">
                <i class="bi bi-shield-lock text-slate-400 text-lg"></i> 
                <span>Setelah diunggah, status dokumen akan kembali menjadi <strong class="text-slate-700">Menunggu ACC</strong>.</span>
            </p>
        </div>

    </div>
</div>
@endsection