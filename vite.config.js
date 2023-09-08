import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';
import i18n from 'laravel-vue-i18n/vite';
export default defineConfig({

    build: {
        outDir: 'public/build/storage', // Шлях до папки збірки
        emptyOutDir: true,      // Очищати папку збірки перед збіркою
    },

    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        i18n(),
    ],
    resolve: {
        alias: {
            Modules: path.resolve(__dirname, 'Modules'),
            Jetstream: path.resolve(__dirname, 'Modules/Jetstream/resources/js'),
        }
    }
});
