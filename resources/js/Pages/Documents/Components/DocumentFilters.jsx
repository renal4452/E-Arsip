import React, { useState } from 'react';

export default function DocumentFilters({ filters, updateFilter, resetFilters, categories }) {
    const [showAdvanced, setShowAdvanced] = useState(false);

    return (
        <div className="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 p-5">
            <div className="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                
                {/* Search Bar (Kiri) */}
                <div className="w-full lg:w-96 relative">
                    <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg className="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input
                        type="text"
                        value={filters.search}
                        onChange={(e) => updateFilter('search', e.target.value)}
                        placeholder="Cari nomor surat, judul dokumen..."
                        className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-gray-50 placeholder-gray-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors"
                    />
                </div>

                {/* Status Pills & Toggle Advanced (Kanan) */}
                <div className="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                    <span className="text-sm font-semibold text-gray-500 mr-2">Status:</span>
                    {[
                        { label: 'Semua', value: '' },
                        { label: 'Menunggu ACC', value: 'pending' },
                        { label: 'Revisi', value: 'revisi' },
                        { label: 'Disetujui', value: 'approved' }
                    ].map((s, i) => (
                        <button 
                            key={i} 
                            onClick={() => updateFilter('status', s.value)} 
                            className={`px-4 py-1.5 rounded-full text-sm font-medium transition-colors border ${
                                filters.status === s.value 
                                ? 'bg-gray-800 text-white border-gray-800' 
                                : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'
                            }`}
                        >
                            {s.label}
                        </button>
                    ))}
                    
                    <button 
                        onClick={() => setShowAdvanced(!showAdvanced)} 
                        className="ml-2 p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors border border-transparent"
                        title="Filter Lanjutan"
                    >
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    </button>
                </div>
            </div>

            {/* Advanced Filters Container */}
            {showAdvanced && (
                <div className="mt-5 pt-5 border-t border-gray-100 grid grid-cols-1 md:grid-cols-4 gap-4 items-end animate-fade-in">
                    <div>
                        <label className="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Tahun</label>
                        <select 
                            value={filters.year} 
                            onChange={e => updateFilter('year', e.target.value)} 
                            className="w-full border-gray-300 rounded-lg text-sm shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">Semua Tahun</option>
                            {[2026, 2025, 2024, 2023].map(y => <option key={y} value={y}>{y}</option>)}
                        </select>
                    </div>
                    <div>
                        <label className="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Kategori</label>
                        <select 
                            value={filters.type} 
                            onChange={e => updateFilter('type', e.target.value)} 
                            className="w-full border-gray-300 rounded-lg text-sm shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">Semua Kategori</option>
                            {categories?.map(c => <option key={c.id} value={c.id}>{c.name_types}</option>)}
                        </select>
                    </div>
                    <div className="flex gap-2">
                        <button 
                            onClick={resetFilters} 
                            className="w-full bg-red-50 text-red-600 border border-red-100 py-2 rounded-lg font-semibold text-sm hover:bg-red-100 transition-colors flex items-center justify-center gap-2"
                        >
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Reset Filter
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
}