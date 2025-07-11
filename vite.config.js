import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import { resolve } from 'path';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    return {
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.ts'],
                refresh: true,
            }),
            tailwindcss(),
            vue(),
        ],
        resolve: {
            alias: {
                '@': resolve(__dirname, './resources/js')
            },
        },
        server: {
            host: env.VITE_HOST || '0.0.0.0',
            port: parseInt(env.VITE_PORT || '5173'),
            strictPort: true,
            hmr: env.VITE_HMR_HOST === '' ? true : {
                host: env.VITE_HMR_HOST || 'localhost',
                clientPort: parseInt(env.VITE_HMR_PORT || '5173')
            },
        },
    };
});
