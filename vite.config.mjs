import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [tailwindcss()],
    build: {
        outDir: 'resources/dist',
        rollupOptions: {
            input: 'src/input.css',
            output: {
                assetFileNames: 'app.css'
            }
        }
    }
});
