import React from 'react';
import { Link } from '@inertiajs/react';

export default function Topbar({ setIsSidebarOpen }) {
    return (
        <header className="h-16 bg-white/90 backdrop-blur-md border-b border-slate-200 shadow-sm flex items-center justify-between px-4 sm:px-6 lg:px-8 sticky top-0 z-40 transition-all">
            
            {/* Bagian Kiri (Hamburger & Judul) */}
            <div className="flex items-center">
                <button onClick={() => setIsSidebarOpen(true)} className="lg:hidden text-slate-500 hover:text-slate-700 focus:outline-none p-2 -ml-2 rounded-lg">
                    <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                
                <div className="hidden sm:block ml-4 text-sm font-bold text-slate-700 tracking-wide">
                    Inspektorat Document System
                </div>
            </div>

            {/* Bagian Kanan (Notifikasi & Logout) */}
            <div className="flex items-center gap-4">
                <button className="text-slate-400 hover:text-indigo-600 relative p-2 rounded-full hover:bg-slate-100 transition-colors">
                    <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    {/* Titik merah notifikasi */}
                    <span className="absolute top-1 right-1 block h-2.5 w-2.5 rounded-full bg-rose-500 ring-2 ring-white"></span>
                </button>

                {/* Tombol Logout */}
                <Link href="/logout" method="post" as="button" className="btn-danger">
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Keluar
                </Link>
            </div>
        </header>
    );
}