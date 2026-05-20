import React from 'react';
import Pagination from './Pagination';

export default function DataTable({ columns, data, paginationLinks, renderRow, emptyMessage = "Tidak ada data yang ditemukan" }) {
    return (
        <div className="w-full">
            <div className="card-base overflow-x-auto">
                <table className="w-full text-left border-collapse">
                    <thead className="bg-slate-50/80 text-xs uppercase font-bold text-slate-500 border-b border-slate-200">
                        <tr>
                            {columns.map((col, i) => (
                                <th key={i} className="px-6 py-4 tracking-wider">
                                    {col}
                                </th>
                            ))}
                        </tr>
                    </thead>

                    <tbody className="divide-y divide-slate-100 text-sm">
                        {data.length > 0 ? (
                            data.map(renderRow)
                        ) : (
                            <tr>
                                <td colSpan={columns.length} className="px-6 py-12 text-center">
                                    <div className="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 mb-3">
                                        <svg className="w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                    </div>
                                    <p className="text-slate-500 font-medium">{emptyMessage}</p>
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>

            {/* Pagination ditaruh di luar card biar rapi */}
            {paginationLinks && <Pagination links={paginationLinks} />}
        </div>
    );
}