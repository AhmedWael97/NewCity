import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        // Minify assets
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
            },
        },
        // Optimize CSS
        cssMinify: true,
        // Code splitting
        rollupOptions: {
            output: {
                manualChunks: {
                    'vendor': ['vue', 'axios'],
                },
            },
        },
        // Reduce chunk size
        chunkSizeWarningLimit: 1000,
    },
    // Performance optimizations
    optimizeDeps: {
        include: ['axios'],
    },
});
