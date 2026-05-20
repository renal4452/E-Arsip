import React from 'react';
import { Link } from '@inertiajs/react';

export default function ActionNeeded({ pendingCount }) {
    if (pendingCount === 0) return null;

    return (
        <div className="bg-amber-50 border border-amber-200 rounded-2xl p-6 flex items-center justify-between shadow-sm animate-pulse-subtle">
            <div className="flex items-center gap-4">
                <div className="h-12 w-12 bg-amber-200 rounded-full flex items-center justify-center text-xl">🔔</div>
                <div>
                    <h4 className="font-bold text-amber-900">Perhatian: Ada {pendingCount} Dokumen Menunggu</h4>
                    <p className="text-sm text-amber-700 font-medium">Jangan biarkan antrean menumpuk, periksa sekarang.</p>
                </div>
            </div>
            <Link 
                href="/documents?status=pending" 
                className="px-6 py-2 bg-amber-600 text-white rounded-xl text-sm font-bold hover:bg-amber-700 transition-colors shadow-md"
            >
                Proses Sekarang
            </Link>
        </div>
    );
}