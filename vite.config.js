import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/fonts/your-font-file.ttf' // Add your font files here
            ],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            input: {
                app: 'resources/js/app.js',
            },
            output: {
                assetFileNames: 'assets/[name]-[hash][extname]',
            },
        },
    },
    resolve: {
        alias: {
            '@images': '/resources/images',
            '@fonts': '/resources/fonts',
        },
    },
});
