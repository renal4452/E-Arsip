import React from 'react';
import { Link } from '@inertiajs/react';

export default function Sidebar({ auth, isSidebarOpen, setIsSidebarOpen }) {
    const isActive = (path) => window.location.pathname.startsWith(path);

    const navigation = [
        { name: 'Dashboard', href: '/dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
        { name: 'Manajemen LHP', href: '/documents', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
        { name: 'Ruang Berbagi', href: '/shared-documents', icon: 'M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z' },
        { name: 'Log & Monitoring', href: '/monitoring', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
    ];

    return (
        <>
            {/* --- SIDEBAR DESKTOP --- */}
            <aside className="hidden lg:flex lg:flex-col w-72 bg-gray-900 text-white fixed inset-y-0 shadow-xl z-20">
                <div className="h-16 flex items-center px-8 bg-gray-950 border-b border-gray-800">
                    <div className="flex items-center gap-3">
                        <div className="bg-blue-600 text-white p-1.5 rounded-lg">
                            <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <span className="text-xl font-bold tracking-wide">E-Arsip</span>
                    </div>
                </div>

                <div className="flex-1 overflow-y-auto py-6 px-4 space-y-1">
                    <p className="px-4 text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Menu Utama</p>
                    {navigation.map((item) => (
                        <Link 
                            key={item.name} 
                            href={item.href}
                            className={`flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 ${
                                isActive(item.href) 
                                ? 'bg-blue-600 text-white shadow-md shadow-blue-900/20' 
                                : 'text-gray-400 hover:bg-gray-800 hover:text-white'
                            }`}
                        >
                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d={item.icon}></path></svg>
                            <span className="font-semibold text-sm">{item.name}</span>
                        </Link>
                    ))}
                </div>
                
                {/* User Info */}
                <div className="p-4 bg-gray-950 border-t border-gray-800">
                    <div className="flex items-center gap-3 px-2">
                        <div className="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold border-2 border-gray-700">
                            {auth.user?.name.charAt(0)}
                        </div>
                        <div className="overflow-hidden">
                            <p className="text-sm font-bold text-white truncate">{auth.user?.name}</p>
                            <p className="text-xs font-medium text-gray-400 truncate">{auth.user?.role?.name || 'User'}</p>
                        </div>
                    </div>
                </div>
            </aside>

            {/* --- SIDEBAR MOBILE (Off-canvas) --- */}
            {isSidebarOpen && (
                <div className="fixed inset-0 z-40 lg:hidden">
                    <div className="fixed inset-0 bg-gray-900/80 backdrop-blur-sm" onClick={() => setIsSidebarOpen(false)}></div>
                    <div className="fixed inset-y-0 left-0 w-72 bg-gray-900 text-white shadow-2xl flex flex-col">
                        <div className="h-16 flex items-center justify-between px-6 bg-gray-950 border-b border-gray-800">
                            <span className="text-xl font-bold">E-Arsip</span>
                            <button onClick={() => setIsSidebarOpen(false)} className="text-gray-400 hover:text-white">
                                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        <div className="flex-1 overflow-y-auto py-6 px-4 space-y-1">
                            {navigation.map((item) => (
                                <Link key={item.name} href={item.href} onClick={() => setIsSidebarOpen(false)} className={`flex items-center gap-3 px-4 py-3 rounded-xl ${isActive(item.href) ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-800'}`}>
                                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d={item.icon}></path></svg>
                                    <span className="font-semibold text-sm">{item.name}</span>
                                </Link>
                            ))}
                        </div>
                    </div>
                </div>
            )}
        </>
    );
}