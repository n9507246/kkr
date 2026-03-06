import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        host: '0.0.0.0', // Лучше чем true - слушает все интерфейсы
        port: 5173,
        hmr: {
            host: '192.168.1.237', // Например: '192.168.1.100'
        },
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/guest.js'],
            refresh: true,
        }),
    ],
});