import React from 'react';
import { Link } from '@inertiajs/react';
import Pagination from '../../../Components/Table/Pagination'; // Sesuaikan letak Pagination Anda

export default function DocumentTable({ documents, manager }) {
    const { auth, hasRole, openModal } = manager;
    const items = documents?.data || [];

    const getStatusBadge = (status) => {
        const badges = {
            pending: <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 border border-yellow-200">Menunggu ACC</span>,
            revisi: <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200">Perlu Revisi</span>,
            approved: <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-200">Disetujui</span>
        };
        return badges[status?.toLowerCase()] || <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-800 border border-gray-200">Unknown</span>;
    };

    return (
        <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No. Dokumen</th>
                            <th className="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Informasi Dokumen</th>
                            <th className="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Versi</th>
                            <th className="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            <th className="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                        {items.length === 0 ? (
                            <tr>
                                <td colSpan="5" className="px-6 py-12 text-center">
                                    <svg className="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <h3 className="mt-2 text-sm font-medium text-gray-900">Dokumen Tidak Ditemukan</h3>
                                    <p className="mt-1 text-sm text-gray-500">Tidak ada data yang cocok dengan filter saat ini.</p>
                                </td>
                            </tr>
                        ) : (
                            items.map((doc) => {
                                // Logic Access Control UI
                                const isUploader = doc.latestVersion?.uploaded_by === auth.user.id;
                                const canForceUpdate = hasRole('Admin', 'Inspektur') || (isUploader && doc.status !== 'approved');

                                return (
                                    <tr key={doc.id} className="hover:bg-gray-50/50 transition-colors">
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <span className="text-sm font-bold text-gray-900">{doc.no_doc || '-'}</span>
                                        </td>
                                        <td className="px-6 py-4">
                                            <div className="flex items-center gap-3">
                                                <div className="flex-shrink-0 h-10 w-10 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600">
                                                    <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                </div>
                                                <div>
                                                    <div className="text-sm font-bold text-gray-900 max-w-xs truncate" title={doc.title}>{doc.title}</div>
                                                    <div className="text-xs text-gray-500 mt-0.5">{doc.division?.name || 'Internal'} • {doc.docType?.name_types || 'General'}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-center">
                                            <span className="text-xs font-semibold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-md border border-gray-200">v{doc.current_version || 1}</span>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-center">
                                            {getStatusBadge(doc.status)}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-center">
                                            <div className="flex items-center justify-center gap-2">
                                                {/* Action: Lihat Detail */}
                                                <Link href={`/documents/${doc.id}`} className="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors" title="Lihat Detail">
                                                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                </Link>

                                                {/* Action: Download Latest */}
                                                {doc.latestVersion && (
                                                    <a href={`/documents/download/${doc.latestVersion.id}`} className="p-1.5 text-gray-400 hover:text-gray-900 hover:bg-gray-100 rounded-md transition-colors" title="Unduh Berkas Terbaru">
                                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                    </a>
                                                )}

                                                {/* Action: Approve (Khusus Pimpinan/Auditor) */}
                                                {hasRole('Auditor', 'Inspektur', 'Admin') && doc.status === 'pending' && (
                                                    <Link href={`/documents/${doc.id}/approve`} method="post" as="button" className="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-md transition-colors" title="Setujui (ACC)">
                                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    </Link>
                                                )}

                                                {/* Action: Revisi */}
                                                {hasRole('Auditor', 'Admin', 'Inspektur') && doc.status === 'revisi' && (
                                                    <Link href={`/documents/${doc.id}/revisi-form`} className="p-1.5 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 rounded-md transition-colors" title="Beri Catatan Revisi">
                                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    </Link>
                                                )}

                                                {/* Action: Force Update / Upload Ulang */}
                                                {canForceUpdate && (
                                                    <button onClick={() => openModal('UPDATE', doc)} className="p-1.5 text-gray-400 hover:text-orange-600 hover:bg-orange-50 rounded-md transition-colors" title="Unggah Ulang Berkas">
                                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                    </button>
                                                )}

                                                {/* Action: Upload TTE */}
                                                {hasRole('Admin', 'Inspektur') && doc.status === 'approved' && (
                                                    <button onClick={() => openModal('TTE', doc)} className="p-1.5 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded-md transition-colors" title="Unggah Berkas Final (TTE)">
                                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                                    </button>
                                                )}
                                            </div>
                                        </td>
                                    </tr>
                                );
                            })
                        )}
                    </tbody>
                </table>
            </div>

            {/* Area Paginasi Terintegrasi */}
            {documents?.links && documents.data.length > 0 && (
                <div className="px-6 py-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
                    <span className="text-sm text-gray-500 font-medium">
                        Menampilkan <span className="font-bold text-gray-900">{documents.from}</span> - <span className="font-bold text-gray-900">{documents.to}</span> dari <span className="font-bold text-gray-900">{documents.total}</span> dokumen
                    </span>
                    <Pagination links={documents.links} />
                </div>
            )}
        </div>
    );
}