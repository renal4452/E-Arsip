import React from 'react';

export default function WelcomeBanner({ user }) {
    return (
        // w-full dan overflow-hidden memastikan tidak ada warna/elemen yang tumpah ke luar
        <div className="relative overflow-hidden w-full bg-gradient-to-r from-slate-900 to-indigo-900 rounded-3xl shadow-lg border border-slate-800 p-6 sm:p-10">
            
            {/* Dekorasi Abstrak */}
            <div className="absolute top-0 right-0 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl -mr-10 -mt-10 pointer-events-none"></div>
            <div className="absolute bottom-0 right-32 w-32 h-32 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>

            <div className="relative z-10 flex flex-col sm:flex-row items-start sm:items-center gap-5 sm:gap-6">
                
                {/* shrink-0: Mencegah kotak emoji menyusut atau gepeng saat di layar kecil */}
                <div className="shrink-0 h-16 w-16 sm:h-20 sm:w-20 bg-white/5 backdrop-blur-md rounded-2xl flex items-center justify-center text-3xl sm:text-4xl border border-white/10 shadow-inner">
                    👋
                </div>

                {/* min-w-0 flex-1: Memaksa teks tetap berada di dalam batas kontainer */}
                <div className="min-w-0 flex-1 w-full">
                    <h2 className="text-2xl sm:text-3xl font-black text-white tracking-tight mb-2 truncate">
                        Selamat datang, <span className="text-indigo-400">{user?.name || 'Admin'}</span>
                    </h2>
                    <p className="text-slate-300 font-medium text-sm sm:text-base max-w-2xl leading-relaxed break-words">
                        Pantau seluruh aktivitas E-Arsip Inspektorat Anda. Hari ini adalah hari yang baik untuk menyelesaikan dokumen yang tertunda.
                    </p>
                </div>

            </div>
        </div>
    );
}