import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources'), // allows @/css, @/js, @/images usage
        },
    },
    build: {
        rollupOptions: {
            output: {
                assetFileNames: (assetInfo) => {
                    let ext = assetInfo.name.split('.').pop();
                    if (/ttf|woff|woff2|eot/.test(ext)) {
                        return 'fonts/[name][extname]';
                    }
                    if (/png|jpe?g|svg|gif|webp/.test(ext)) {
                        return 'images/[name][extname]';
                    }
                    return 'assets/[name][extname]';
                },
            },
        },
    },
});

