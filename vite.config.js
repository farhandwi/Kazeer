import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    server: {
<<<<<<< HEAD
      cors: {
        origin: 'https://kazeer.id',
        credentials: true
      },
      hmr: {
        host: 'localhost',
        protocol: 'ws',
      },
    },

=======
        cors: {
          origin: 'https://kazeer.id',
          credentials: true
        },
        hmr: {
          host: 'localhost',
          protocol: 'ws',
        },
    },
      
>>>>>>> 6affabaf0ee5a771d3ad9697ca1cd33499f1baa3
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
    ],
    build: {
        outDir: 'public/build',
        rollupOptions: {
            output: {
                manualChunks: undefined,
            },
        },
    },
});