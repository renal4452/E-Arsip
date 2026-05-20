import React from 'react';

export default function StatusBadge({ status }) {
    // Mapping status dari database ke class CSS yang sudah kita buat
    const statusMap = {
        pending: { text: 'Menunggu ACC', className: 'badge-pending' },
        revisi: { text: 'Perlu Revisi', className: 'badge-revisi' },
        approved: { text: 'Disetujui', className: 'badge-approved' }
    };

    const currentStatus = statusMap[status?.toLowerCase()];

    if (currentStatus) {
        return <span className={`badge ${currentStatus.className}`}>{currentStatus.text}</span>;
    }

    // Default Fallback
    return <span className="badge bg-slate-100 text-slate-800 border-slate-200">Unknown</span>;
}