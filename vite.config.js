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
        // Minify assets (using esbuild instead of terser)
        minify: 'esbuild',
        // Optimize CSS
        cssMinify: true,
        // Code splitting
        rollupOptions: {
            output: {
                manualChunks: undefined,
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
