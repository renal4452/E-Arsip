document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('mainSidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const closeBtn = document.getElementById('closeSidebar');
    const overlay = document.getElementById('sidebarOverlay');

    // Fungsi Buka
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.add('show');
            overlay.classList.add('show');
            document.body.style.overflow = 'hidden'; // Kunci scroll layar saat menu buka
        });
    }

    // Fungsi Tutup (Tombol X atau klik area gelap)
    const closeMenu = () => {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
        document.body.style.overflow = 'auto'; // Aktifkan kembali scroll
    };

    if (closeBtn) closeBtn.addEventListener('click', closeMenu);
    if (overlay) overlay.addEventListener('click', closeMenu);
});