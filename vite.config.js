import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                assetFileNames: assetInfo => {
                    if (/\.css$/.test(assetInfo.name)) {
                        return 'css/[name].[hash][extname]';
                    }

                    if (/\.ttf|\.woff|\.woff2|\.eot$/.test(assetInfo.name)) {
                        return 'fonts/[name].[hash][extname]';
                    }

                    return 'assets/[name].[hash][extname]';
                },
            },
        },
    },
});
