import './bootstrap';
import '../css/app.css';

import React from 'react';
import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

// Mengambil nama aplikasi dari tag <title> di app.blade.php
const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'E-Arsip';

createInertiaApp({
    // Konfigurasi Title dinamis (Contoh: "Login - E-Arsip")
    title: (title) => `${title} - ${appName}`,
    
    // Otomatis mencari komponen React di dalam folder resources/js/Pages/
    resolve: (name) => resolvePageComponent(`./Pages/${name}.jsx`, import.meta.glob('./Pages/**/*.jsx')),
    
    // Menyuntikkan aplikasi React ke dalam elemen <div id="app"> di app.blade.php
    setup({ el, App, props }) {
        const root = createRoot(el);
        root.render(<App {...props} />);
    },
    
    // Konfigurasi loading bar saat pindah halaman
    progress: {
        color: '#2563EB', // Warna biru (menyesuaikan tema Tailwind)
        showSpinner: true,
    },
});