import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react'; // 👈 Import plugin react

export default defineConfig({

    server: {
        host: '127.0.0.1',
    },
    
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.jsx'], // 👈 Ubah ekstensi js jadi jsx
            refresh: true,
        }),
        react(), // 👈 Nyalakan plugin react
    ],
});