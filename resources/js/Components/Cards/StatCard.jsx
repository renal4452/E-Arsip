import React from 'react';

export default function StatCard({ label, value, icon, colorClass, borderClass }) {
    return (
        <div className={`bg-white p-6 rounded-2xl shadow-sm border ${borderClass} flex items-center gap-4 transition-transform hover:scale-[1.02]`}>
            <div className={`h-12 w-12 rounded-xl flex items-center justify-center text-2xl ${colorClass}`}>
                {icon}
            </div>
            <div>
                <p className="text-xs font-bold text-gray-400 uppercase tracking-wider">{label}</p>
                <p className="text-2xl font-black text-gray-900">{value}</p>
            </div>
        </div>
    );
}