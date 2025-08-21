import vue from "@vitejs/plugin-vue";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";
import { defineConfig } from "vite";
import viteCompression from "vite-plugin-compression";

export default defineConfig({
  plugins: [
    laravel({
      input: ["resources/js/app.ts"],
      ssr: "resources/js/ssr.ts",
      refresh: true,
    }),
    tailwindcss(),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
    viteCompression({
      algorithm: "gzip",
      ext: ".gz",
    }),
    viteCompression({
      algorithm: "brotliCompress",
      ext: ".br",
    }),
  ],
  server: {
    host: "localhost",
    port: 5173,
    hmr: {
      host: "localhost",
    },
  },
  resolve: {
    alias: {
      "@": "/resources/js",
    },
  },
  build: {
    chunkSizeWarningLimit: 1000,
    minify: "terser",
    terserOptions: {
      compress: {
        drop_console: true,
        drop_debugger: true,
      },
    },
    rollupOptions: {
      output: {
        manualChunks: {
          "vendor-maps": ["leaflet"],
          "vendor-utils": ["@vueuse/core"],
          "vendor-vue": ["vue", "@inertiajs/vue3"],
          "vendor-headless": ["@headlessui/vue", "@heroicons/vue"],
        },
      },
    },
    cssCodeSplit: true,
    sourcemap: false,
  },
});
