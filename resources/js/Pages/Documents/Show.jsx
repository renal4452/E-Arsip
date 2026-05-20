import React from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout';

export default function Show({ document, versions, logs }) {
    const { auth } = usePage().props;
    const roleName = auth.user?.role?.name || 'User';

    const getStatusBadge = (status) => {
        const badges = {
            pending: <span className="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-bold uppercase tracking-wider border border-yellow-200">Menunggu ACC</span>,
            revisi: <span className="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-bold uppercase tracking-wider border border-red-200">Perlu Revisi</span>,
            approved: <span className="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold uppercase tracking-wider border border-green-200">Disetujui</span>
        };
        return badges[status?.toLowerCase()] || <span className="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-bold uppercase border">Unknown</span>;
    };

    return (
        <AuthenticatedLayout>
            <Head title={`Detail Dokumen - ${document.no_doc || 'LHP'}`} />

            {/* Header: Tombol Kembali & Aksi Cepat */}
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <Link href="/documents" className="flex items-center text-sm font-semibold text-gray-500 hover:text-blue-600 transition-colors bg-white px-4 py-2 rounded-lg border border-gray-200 shadow-sm">
                    <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Daftar
                </Link>

                <div className="flex gap-2">
                    {/* Logika Tombol Aksi di Header bisa ditaruh di sini nanti */}
                </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {/* Kiri: Informasi Utama (Mengambil 2/3 layar) */}
                <div className="lg:col-span-2 space-y-6">
                    
                    {/* Card Detail LHP */}
                    <div className="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div className="border-b border-gray-100 bg-gray-50/50 p-6 flex justify-between items-center">
                            <div className="flex items-center gap-4">
                                <div className="h-12 w-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                                    <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <div>
                                    <h2 className="text-xl font-bold text-gray-900">{document.title}</h2>
                                    <p className="text-sm font-medium text-gray-500">{document.no_doc || 'Belum ada nomor registrasi'}</p>
                                </div>
                            </div>
                            <div>{getStatusBadge(document.status)}</div>
                        </div>

                        <div className="p-6 grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <p className="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Kategori Dokumen</p>
                                <p className="text-sm font-semibold text-gray-900">{document.doc_type?.name_types || 'General'}</p>
                            </div>
                            <div>
                                <p className="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Divisi / Instansi Terkait</p>
                                <p className="text-sm font-semibold text-gray-900">{document.division?.name || 'Internal'}</p>
                            </div>
                            <div>
                                <p className="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Dibuat Pada</p>
                                <p className="text-sm font-semibold text-gray-900">{new Date(document.created_at).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</p>
                            </div>
                            <div>
                                <p className="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Terakhir Diperbarui</p>
                                <p className="text-sm font-semibold text-gray-900">{new Date(document.updated_at).toLocaleDateString('id-ID')}</p>
                            </div>
                        </div>

                        {document.status === 'revisi' && document.auditor_note && (
                            <div className="mx-6 mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                                <h4 className="text-sm font-bold text-red-800 mb-2 flex items-center gap-2">
                                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    Catatan Revisi dari {document.auditor?.name || 'Auditor'}
                                </h4>
                                <p className="text-sm text-red-700 italic">{document.auditor_note}</p>
                            </div>
                        )}
                    </div>

                    {/* Card Riwayat Versi (File Downloads) */}
                    <div className="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div className="border-b border-gray-100 bg-gray-50/50 p-4 px-6">
                            <h3 className="text-base font-bold text-gray-900">Riwayat Berkas (Versions)</h3>
                        </div>
                        <div className="p-0">
                            <ul className="divide-y divide-gray-100">
                                {versions.map((ver, index) => (
                                    <li key={ver.id} className="p-4 px-6 hover:bg-gray-50 transition-colors flex items-center justify-between">
                                        <div className="flex items-center gap-4">
                                            <div className={`h-10 w-10 rounded-lg flex items-center justify-center font-bold text-sm ${index === 0 ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 text-gray-500'}`}>
                                                v{ver.version_number}
                                            </div>
                                            <div>
                                                <p className="text-sm font-bold text-gray-900">{ver.file_name}</p>
                                                <p className="text-xs text-gray-500 mt-0.5">
                                                    Diunggah oleh <span className="font-semibold">{ver.uploader?.name || 'Sistem'}</span> pada {new Date(ver.created_at).toLocaleDateString('id-ID')}
                                                </p>
                                                {ver.description && <p className="text-xs text-gray-400 mt-1 italic">"{ver.description}"</p>}
                                            </div>
                                        </div>
                                        <a href={`/documents/download/${ver.id}`} className="flex items-center justify-center h-9 w-9 bg-green-50 text-green-600 hover:bg-green-600 hover:text-white rounded-lg transition-colors border border-green-200 hover:border-transparent" title="Unduh File">
                                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        </a>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    </div>

                </div>

                {/* Kanan: Timeline Aktivitas (Mengambil 1/3 layar) */}
                <div className="space-y-6">
                    <div className="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden sticky top-6">
                        <div className="border-b border-gray-100 bg-gray-50/50 p-4 px-6">
                            <h3 className="text-base font-bold text-gray-900 flex items-center gap-2">
                                <svg className="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Log Aktivitas
                            </h3>
                        </div>
                        <div className="p-6">
                            {logs.length === 0 ? (
                                <p className="text-sm text-gray-500 text-center py-4">Belum ada aktivitas terekam.</p>
                            ) : (
                                <div className="flow-root">
                                    <ul className="-mb-8">
                                        {logs.map((log, logIdx) => (
                                            <li key={log.id}>
                                                <div className="relative pb-8">
                                                    {logIdx !== logs.length - 1 ? (
                                                        <span className="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                                    ) : null}
                                                    <div className="relative flex space-x-3">
                                                        <div>
                                                            <span className="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center ring-8 ring-white border border-gray-200">
                                                                <svg className="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                            </span>
                                                        </div>
                                                        <div className="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                            <div>
                                                                <p className="text-sm text-gray-900 font-medium">{log.description}</p>
                                                                <p className="text-xs text-gray-500 mt-0.5">{log.user?.name || 'Sistem'}</p>
                                                            </div>
                                                            <div className="text-right text-xs whitespace-nowrap text-gray-400 font-medium">
                                                                {new Date(log.created_at).toLocaleDateString('id-ID', { month: 'short', day: 'numeric' })}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            )}
                        </div>
                    </div>
                </div>

            </div>
        </AuthenticatedLayout>
    );
}