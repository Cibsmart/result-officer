import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { watch } from 'vite-plugin-watch';
import tailwindcss from '@tailwindcss/vite';
import path from 'path';
import { resolve } from 'node:path';
// import eslingPlugin from "vite-plugin-eslint";

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: 'resources/js/app.ts',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
            script: {
                globalTypeFiles: ['./resources/js/types/generated.d.ts'],
            },
        }),

        // eslingPlugin(),
        watch({
            pattern: 'app/{Data,Enums}/**/*.php',
            command: 'php artisan typescript:transform --format',
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
            'ziggy-js': resolve(__dirname, 'vendor/tightenco/ziggy'),
        },
    },
});
