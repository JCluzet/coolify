import { defineConfig, loadEnv } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import autoprefixer from 'autoprefixer';
import tailwindcss from 'tailwindcss';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '')

    return {
        server: {
            watch: {
                ignored: [
                    "**/dev_*_data/**",
                    "**/storage/**",
                ],
            },
            host: "0.0.0.0",
            hmr: {
                host: env.VITE_HOST || '0.0.0.0'
            },
        },
        plugins: [
            laravel({
                input: ["resources/css/app.css", "resources/js/app.js", "resources/js/inertia.app.js", "resources/css/inertia.css"],
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
        ],
        resolve: {
            alias: {
                vue: "vue/dist/vue.esm-bundler.js",
            },
        },
        css: {
            postcss: {
                plugins: [tailwindcss, autoprefixer],
            },
        },
    }
});
