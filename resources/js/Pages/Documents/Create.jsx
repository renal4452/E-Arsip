import React, { useState, useRef } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout';

export default function Create({ categories, divisions }) {
    const { data, setData, post, processing, errors } = useForm({
        title: '',
        no_doc: '',
        doc_type_id: '',
        division_id: '',
        file: null,
    });

    const [isDragging, setIsDragging] = useState(false);
    const fileInputRef = useRef(null);

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/documents');
    };

    // --- FITUR DRAG AND DROP ---
    const handleDragOver = (e) => { e.preventDefault(); setIsDragging(true); };
    const handleDragLeave = (e) => { e.preventDefault(); setIsDragging(false); };
    const handleDrop = (e) => {
        e.preventDefault();
        setIsDragging(false);
        if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            setData('file', e.dataTransfer.files[0]);
            e.dataTransfer.clearData();
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title="Unggah LHP Baru" />

            <div className="max-w-5xl mx-auto">
                {/* --- HEADER --- */}
                <div className="flex items-center justify-between mb-6">
                    <div>
                        <div className="flex items-center gap-2 text-sm text-gray-500 mb-2">
                            <Link href="/documents" className="hover:text-blue-600 transition-colors">Manajemen LHP</Link>
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 5l7 7-7 7"></path></svg>
                            <span className="text-gray-900 font-medium">Unggah Baru</span>
                        </div>
                        <h2 className="text-2xl font-bold text-gray-900">Unggah Dokumen LHP Baru</h2>
                    </div>
                    
                    <Link href="/documents" className="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50 shadow-sm flex items-center gap-2 transition-colors">
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Batal
                    </Link>
                </div>

                {/* --- KARTU FORMULIR UTAMA --- */}
                <div className="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <form onSubmit={handleSubmit}>
                        
                        <div className="grid grid-cols-1 lg:grid-cols-5 lg:divide-x divide-gray-100">
                            
                            {/* KOLOM KIRI (Data Meta LHP) */}
                            <div className="lg:col-span-3 p-6 sm:p-8 space-y-6">
                                <h3 className="text-lg font-bold text-gray-900 flex items-center gap-2 border-b border-gray-100 pb-4 mb-6">
                                    <svg className="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Informasi Dokumen
                                </h3>

                                <div className="space-y-5">
                                    <div>
                                        <label className="block text-sm font-bold text-gray-700 mb-1">Judul Dokumen / Perihal <span className="text-red-500">*</span></label>
                                        <input
                                            type="text"
                                            value={data.title}
                                            onChange={e => setData('title', e.target.value)}
                                            placeholder="Contoh: LHP Audit Reguler Dinas Kesehatan..."
                                            className="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-blue-500 focus:border-blue-500 text-sm py-2.5 transition-colors"
                                        />
                                        {errors.title && <p className="mt-1.5 text-xs font-bold text-red-600">{errors.title}</p>}
                                    </div>

                                    <div>
                                        <label className="block text-sm font-bold text-gray-700 mb-1">Nomor Registrasi LHP</label>
                                        <input
                                            type="text"
                                            value={data.no_doc}
                                            onChange={e => setData('no_doc', e.target.value)}
                                            placeholder="Kosongkan jika ingin diisi otomatis oleh sistem"
                                            className="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-blue-500 focus:border-blue-500 text-sm py-2.5 transition-colors"
                                        />
                                        {errors.no_doc && <p className="mt-1.5 text-xs font-bold text-red-600">{errors.no_doc}</p>}
                                    </div>

                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                        <div>
                                            <label className="block text-sm font-bold text-gray-700 mb-1">Kategori Dokumen <span className="text-red-500">*</span></label>
                                            <select
                                                value={data.doc_type_id}
                                                onChange={e => setData('doc_type_id', e.target.value)}
                                                className="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-blue-500 focus:border-blue-500 text-sm py-2.5 transition-colors"
                                            >
                                                <option value="">-- Pilih Kategori --</option>
                                                {categories?.map(cat => <option key={cat.id} value={cat.id}>{cat.name_types}</option>)}
                                            </select>
                                            {errors.doc_type_id && <p className="mt-1.5 text-xs font-bold text-red-600">{errors.doc_type_id}</p>}
                                        </div>

                                        <div>
                                            <label className="block text-sm font-bold text-gray-700 mb-1">Instansi / Divisi Terkait <span className="text-red-500">*</span></label>
                                            <select
                                                value={data.division_id}
                                                onChange={e => setData('division_id', e.target.value)}
                                                className="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:ring-blue-500 focus:border-blue-500 text-sm py-2.5 transition-colors"
                                            >
                                                <option value="">-- Pilih Instansi --</option>
                                                {divisions?.map(div => <option key={div.id} value={div.id}>{div.name}</option>)}
                                            </select>
                                            {errors.division_id && <p className="mt-1.5 text-xs font-bold text-red-600">{errors.division_id}</p>}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* KOLOM KANAN (Upload File area) */}
                            <div className="lg:col-span-2 bg-gray-50/50 p-6 sm:p-8 flex flex-col">
                                <h3 className="text-lg font-bold text-gray-900 flex items-center gap-2 border-b border-gray-200 pb-4 mb-6">
                                    <svg className="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                    Lampiran Berkas
                                </h3>

                                <div 
                                    className={`flex-1 flex justify-center px-6 py-10 border-2 border-dashed rounded-xl transition-all duration-200 bg-white cursor-pointer
                                        ${isDragging ? 'border-blue-500 bg-blue-50 scale-[1.02] shadow-md' : 'border-gray-300 hover:border-blue-400 hover:bg-gray-50'}
                                        ${data.file ? 'border-green-500 bg-green-50' : ''}
                                    `}
                                    onDragOver={handleDragOver}
                                    onDragLeave={handleDragLeave}
                                    onDrop={handleDrop}
                                    onClick={() => fileInputRef.current?.click()}
                                >
                                    <div className="space-y-4 text-center flex flex-col items-center justify-center w-full">
                                        {data.file ? (
                                            <div className="h-16 w-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                                                <svg className="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </div>
                                        ) : (
                                            <div className={`h-16 w-16 rounded-full flex items-center justify-center transition-colors ${isDragging ? 'bg-blue-200 text-blue-700' : 'bg-gray-100 text-gray-400'}`}>
                                                <svg className="h-8 w-8" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                                </svg>
                                            </div>
                                        )}

                                        <div className="text-sm text-gray-600">
                                            <span className="font-bold text-blue-600 hover:text-blue-700">
                                                {data.file ? 'Ganti File' : 'Pilih File'}
                                            </span>
                                            <input 
                                                type="file" ref={fileInputRef} className="sr-only" 
                                                accept=".pdf,.doc,.docx,.xls,.xlsx"
                                                onChange={e => setData('file', e.target.files[0])}
                                            />
                                            {!data.file && <p className="mt-1 font-medium">Atau tarik dan lepas file ke sini</p>}
                                        </div>
                                        
                                        <div className="text-xs font-medium text-gray-500">
                                            {data.file ? (
                                                <span className="text-green-700 bg-green-100 px-3 py-1 rounded-full">{data.file.name} ({(data.file.size / 1024 / 1024).toFixed(2)} MB)</span>
                                            ) : (
                                                'PDF, Word, Excel hingga 10MB'
                                            )}
                                        </div>
                                    </div>
                                </div>
                                {errors.file && <p className="mt-3 text-sm font-bold text-red-600 text-center bg-red-50 py-2 rounded-lg border border-red-100">{errors.file}</p>}
                                
                                <div className="mt-6 p-4 bg-yellow-50 rounded-xl border border-yellow-100 text-xs text-yellow-800">
                                    <strong>Info:</strong> Dokumen yang diunggah akan berstatus <strong>"Menunggu ACC"</strong>.
                                </div>
                            </div>
                        </div>

                        {/* --- FOOTER (TOMBOL SUBMIT) --- */}
                        <div className="bg-gray-50 px-6 sm:px-8 py-5 border-t border-gray-200 flex justify-end gap-3">
                            <button 
                                type="submit" 
                                disabled={processing}
                                className="px-6 py-2.5 bg-blue-600 border border-transparent rounded-xl text-sm font-bold text-white shadow-sm hover:bg-blue-700 disabled:opacity-50 transition-colors flex items-center gap-2"
                            >
                                {processing && (
                                    <svg className="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle><path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                )}
                                {processing ? 'Menyimpan...' : 'Unggah LHP Sekarang'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}