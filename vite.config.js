import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    // Tambahkan blok server ini
    server: {
        host: '0.0.0.0',
        hmr: {
            host: '192.168.1.216' // Ganti dengan IP lokal komputer server Anda
        }
    }
});