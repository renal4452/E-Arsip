import React, { useState } from 'react';
import { usePage } from '@inertiajs/react';
import Sidebar from './Components/Sidebar';
import Topbar from './Components/Topbar';
import Toast from '../Components/Toast';

export default function AuthenticatedLayout({ children }) {
    const { auth } = usePage().props;
    const [isSidebarOpen, setIsSidebarOpen] = useState(false);

    return (
        <div className="min-h-screen bg-gray-50 flex text-gray-900 font-sans">
            
            {/* Panggil Komponen Sidebar */}
            <Sidebar 
                auth={auth} 
                isSidebarOpen={isSidebarOpen} 
                setIsSidebarOpen={setIsSidebarOpen} 
            />

            {/* Konten Utama (Kanan) */}
            <div className="flex-1 lg:ml-72 flex flex-col min-h-screen">
                
                {/* Panggil Komponen Topbar */}
                <Topbar 
                    setIsSidebarOpen={setIsSidebarOpen} 
                />

                {/* Area Konten Dinamis (Children) */}
                <main className="flex-1 p-4 sm:p-6 lg:p-8 w-full max-w-7xl mx-auto">
                    {children}
                </main>
            </div>

            {/* Panggil Komponen Toast Global */}
            <Toast />
        </div>
    );
}