import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [tailwindcss()],
    build: {
        outDir: 'resources/dist',
        rollupOptions: {
            input: {
                css: 'src/input.css',
                js: 'src/input.js'
            },
            output: {
                entryFileNames: (chunkInfo) => {
                    if (chunkInfo.name === 'js') return 'search.js';
                    return '[name].js';
                },
                assetFileNames: 'app.[ext]'
            }
        }
    }
});
