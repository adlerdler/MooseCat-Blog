import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

// 引入 vue
import vue from '@vitejs/plugin-vue';
import path from 'path'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        // 配置
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),

    ],
    resolve: {
        alias: {
          '@': path.resolve(__dirname, './resources/js'),
        },
      },
});
