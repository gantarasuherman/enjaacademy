import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';
import { VitePWA } from 'vite-plugin-pwa';
import path from 'node:path';

export default defineConfig({
    /*
     * The SPA is mounted under /app on the Laravel host (public/app), and its
     * React routes are already written as /app/*. Setting base here makes the
     * emitted asset URLs match in both dev and production, so no HTML rewriting
     * is needed on the server side.
     */
    base: '/app/',

    plugins: [
        react(),
        tailwindcss(),
        VitePWA({
            registerType: 'autoUpdate',
            includeAssets: ['favicon.svg', 'robots.txt'],
            manifest: {
                name: 'English Academy — Belajar Bahasa Inggris',
                short_name: 'English Academy',
                description:
                    'Belajar Bahasa Inggris terstruktur: vocabulary, grammar, listening, speaking, reading, writing, quiz, dan flashcard.',
                theme_color: '#02468B',
                background_color: '#F8FAFC',
                display: 'standalone',
                start_url: '/app/dashboard',
                scope: '/app/',
                icons: [
                    { src: '/icons/icon-192.png', sizes: '192x192', type: 'image/png' },
                    { src: '/icons/icon-512.png', sizes: '512x512', type: 'image/png' },
                    {
                        src: '/icons/icon-512-maskable.png',
                        sizes: '512x512',
                        type: 'image/png',
                        purpose: 'maskable',
                    },
                ],
            },
            workbox: {
                globPatterns: ['**/*.{js,css,html,svg,png,woff2}'],
                runtimeCaching: [
                    {
                        // Lesson content should keep working offline once seen.
                        urlPattern: /\/api\/(learning|vocabulary|grammar)\//,
                        handler: 'StaleWhileRevalidate',
                        options: {
                            cacheName: 'api-content',
                            expiration: { maxEntries: 200, maxAgeSeconds: 60 * 60 * 24 * 7 },
                        },
                    },
                    {
                        urlPattern: /\.(?:mp3|wav|ogg)$/,
                        handler: 'CacheFirst',
                        options: {
                            cacheName: 'audio',
                            expiration: { maxEntries: 100, maxAgeSeconds: 60 * 60 * 24 * 30 },
                        },
                    },
                ],
            },
            devOptions: { enabled: false },
        }),
    ],

    resolve: {
        alias: {
            '@': path.resolve(__dirname, './src'),
        },
    },

    server: {
        host: '0.0.0.0',
        port: 5173,
        // Docker bind-mounts don't propagate inotify events on every host.
        watch: { usePolling: true },
        proxy: {
            // Keeps the browser same-origin in dev, so Sanctum cookies work.
            '/api': {
                target: process.env.VITE_PROXY_TARGET ?? 'http://localhost:8000',
                changeOrigin: true,
            },
        },
    },

    build: {
        outDir: 'dist',
        sourcemap: false,
        rollupOptions: {
            output: {
                // Split the heavy, rarely-changing libraries out of the app
                // chunk so a content deploy doesn't invalidate everything.
                manualChunks: {
                    react: ['react', 'react-dom', 'react-router-dom'],
                    charts: ['recharts'],
                    motion: ['framer-motion'],
                },
            },
        },
    },
});
