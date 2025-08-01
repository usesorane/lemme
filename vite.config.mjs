import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [tailwindcss()],
    build: {
        outDir: 'resources/dist',
        rollupOptions: {
            input: {
                css: 'src/input.css',
                js: 'src/search.js'
            },
            output: {
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name && assetInfo.name.includes('input')) {
                        return 'app.css';
                    }
                    return '[name].[ext]';
                },
                entryFileNames: (chunkInfo) => {
                    if (chunkInfo.name === 'js') {
                        return 'search.js';
                    }
                    return '[name].js';
                }
            }
        }
    }
});
