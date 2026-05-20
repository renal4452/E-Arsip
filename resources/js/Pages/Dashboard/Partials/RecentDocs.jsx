import React from 'react';
import { Link } from '@inertiajs/react';
import StatusBadge from '../../../Components/UI/StatusBadge';

export default function RecentDocs({ documents }) {
    return (
        <div className="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div className="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/30">
                <h3 className="font-bold text-gray-900">Dokumen Terbaru</h3>
                <Link href="/documents" className="text-xs font-bold text-blue-600 hover:underline">Lihat Semua</Link>
            </div>
            <div className="overflow-x-auto">
                <table className="w-full text-left">
                    <thead className="bg-gray-50 text-[10px] uppercase font-bold text-gray-500 border-b border-gray-100">
                        <tr>
                            <th className="px-6 py-3">Judul Dokumen</th>
                            <th className="px-6 py-3 text-center">Status</th>
                            <th className="px-6 py-3 text-right">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-50 text-sm">
                        {documents.length > 0 ? documents.map((doc) => (
                            <tr key={doc.id} className="hover:bg-gray-50 transition-colors">
                                <td className="px-6 py-4">
                                    <Link href={`/documents/${doc.id}`} className="font-semibold text-gray-900 hover:text-blue-600 truncate block max-w-xs">
                                        {doc.title}
                                    </Link>
                                    <span className="text-[10px] text-gray-400 font-medium">{doc.no_doc || '-'}</span>
                                </td>
                                <td className="px-6 py-4 text-center">
                                    <StatusBadge status={doc.status} />
                                </td>
                                <td className="px-6 py-4 text-right text-gray-500 text-xs font-medium">
                                    {new Date(doc.created_at).toLocaleDateString('id-ID')}
                                </td>
                            </tr>
                        )) : (
                            <tr><td colSpan="3" className="p-10 text-center text-gray-400 italic">Belum ada dokumen.</td></tr>
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    );
}