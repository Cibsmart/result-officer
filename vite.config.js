import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import { watch } from "vite-plugin-watch";
// import eslingPlugin from "vite-plugin-eslint";

export default defineConfig({
  plugins: [
    // eslingPlugin(),
    laravel({
      input: "resources/js/app.ts",
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
    watch({
      pattern: "app/{Data,Enums}/**/*.php",
      command: "php artisan typescript:transform --format",
    }),
  ],
});
