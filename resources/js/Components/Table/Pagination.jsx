import React from 'react';
import { Link } from '@inertiajs/react';

export default function Pagination({ links }) {
    // Kalau cuma 1 halaman, gak usah render pagination
    if (!links || links.length <= 3) return null;

    return (
        <div className="flex flex-wrap items-center justify-center gap-1 mt-6">
            {links.map((link, index) => {
                // Bersihkan label dari tag HTML bawaan Laravel (&laquo; / &raquo;)
                let label = link.label
                    .replace('&laquo; Previous', '←')
                    .replace('Next &raquo;', '→');

                return link.url === null ? (
                    <div 
                        key={index} 
                        className="px-3 py-1.5 text-sm text-slate-400 bg-slate-50 border border-slate-100 rounded-lg cursor-not-allowed"
                    >
                        {label}
                    </div>
                ) : (
                    <Link
                        key={index}
                        href={link.url}
                        className={`px-3 py-1.5 text-sm rounded-lg border transition-colors ${
                            link.active
                                ? 'bg-indigo-600 text-white border-indigo-600 font-bold shadow-md shadow-indigo-200'
                                : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:border-slate-300'
                        }`}
                    >
                        {label}
                    </Link>
                );
            })}
        </div>
    );
}