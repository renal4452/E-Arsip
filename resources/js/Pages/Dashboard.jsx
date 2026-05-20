import React from 'react';
import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '../Layouts/AuthenticatedLayout';
import StatCard from '../Components/Cards/StatCard';
import WelcomeBanner from './Dashboard/Partials/WelcomeBanner';
import RecentDocs from './Dashboard/Partials/RecentDocs';
import ActionNeeded from './Dashboard/Partials/ActionNeeded'; // 👈 Jangan lupa di-import!

export default function Dashboard({ auth, stats, recentDocs }) {
    // 1. Definisikan Config Statistik (Data-Driven)
    const statConfigs = [
        { 
            label: 'Total LHP', 
            value: stats.total, 
            icon: '📁', 
            color: 'blue', 
            description: 'Total arsip tersimpan' 
        },
        { 
            label: 'Menunggu ACC', 
            value: stats.pending, 
            icon: '⏳', 
            color: 'yellow', 
            description: 'Segera tinjau & proses' 
        },
        { 
            label: 'Perlu Revisi', 
            value: stats.revisi, 
            icon: '⚠️', 
            color: 'red', 
            description: 'Perlu perbaikan segera' 
        },
        { 
            label: 'Disetujui', 
            value: stats.approved, 
            icon: '✅', 
            color: 'green', 
            description: 'Dokumen sudah final' 
        },
    ];

    // Cek Role User dengan aman
    const userRole = auth?.user?.role?.name || 'User';

    return (
        <AuthenticatedLayout>
            <Head title="Dashboard Utama" />

            <div className="space-y-6 max-w-7xl mx-auto">
                
                {/* 1. Banner Utama */}
                <WelcomeBanner user={auth.user} />

                {/* 2. Actionable Insight (Khusus Auditor/Admin jika ada pending) */}
                {(userRole === 'Admin' || userRole === 'Auditor') && (
                    <ActionNeeded pendingCount={stats.pending} />
                )}
                
                {/* 3. Render StatCards secara Dinamis */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    {statConfigs.map((stat, idx) => (
                        <StatCard 
                            key={idx}
                            label={stat.label}
                            value={stat.value}
                            icon={stat.icon}
                            // Kita panggil manual nama class-nya agar Tailwind tidak menghapusnya (Safe rendering)
                            colorClass={
                                stat.color === 'blue' ? 'bg-blue-50 text-blue-600' :
                                stat.color === 'yellow' ? 'bg-yellow-50 text-yellow-600' :
                                stat.color === 'red' ? 'bg-red-50 text-red-600' :
                                'bg-green-50 text-green-600'
                            }
                            borderClass={
                                stat.color === 'blue' ? 'border-l-4 border-l-blue-500' :
                                stat.color === 'yellow' ? 'border-l-4 border-l-yellow-500' :
                                stat.color === 'red' ? 'border-l-4 border-l-red-500' :
                                'border-l-4 border-l-green-500'
                            }
                            description={stat.description}
                        />
                    ))}
                </div>

                {/* 4. Main Content Section */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Daftar Dokumen Terbaru */}
                    <div className="lg:col-span-2">
                        <RecentDocs documents={recentDocs || []} />
                    </div>

                    {/* Quick Tips / Info Sidebar */}
                    <div className="space-y-6">
                        <div className="bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl p-6 text-white shadow-lg">
                            <h4 className="font-bold mb-2">Bantuan Cepat 💡</h4>
                            <p className="text-sm text-gray-300 leading-relaxed">
                                Anda dapat mengunggah draf LHP baru langsung melalui tombol "Unggah" di menu Manajemen LHP.
                            </p>
                            <button className="mt-4 w-full py-2 bg-blue-600 hover:bg-blue-500 rounded-xl text-sm font-bold transition-colors">
                                Baca Panduan
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </AuthenticatedLayout>
    );
}