/**
 * Inspektorat General Helper Script
 */
const AppHelper = {
    // 1. Logika Grafik (Chart.js)
    renderChart: (canvasId, type, labels, datasets, options = {}) => {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        const defaultOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
            }
        };

        return new Chart(ctx, {
            type: type,
            data: { labels: labels, datasets: datasets },
            options: { ...defaultOptions, ...options }
        });
    },

    // 2. SweetAlert Konfirmasi Global
    confirmAction: (title, text, confirmText, callback) => {
        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d',
            confirmButtonText: confirmText,
            cancelButtonText: 'Batal',
            border: 'none',
            borderRadius: '15px'
        }).then((result) => {
            if (result.isConfirmed && callback) callback();
        });
    },

    // 3. Copy to Clipboard (Misal No Dokumen)
    copyToClipboard: (text) => {
        navigator.clipboard.writeText(text).then(() => {
            Swal.fire({ icon: 'success', title: 'Tersalin!', timer: 1000, showConfirmButton: false });
        });
    }
};

// Inisialisasi Tooltip Bootstrap secara otomatis
document.addEventListener('DOMContentLoaded', () => {
    const tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltips.map(t => new bootstrap.Tooltip(t));
});