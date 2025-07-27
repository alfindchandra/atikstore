import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import react from "@vitejs/plugin-react"; // Make sure this is imported

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css", // If you have global CSS
                "resources/js/app.js",
            ],
            refresh: true,
        }),
        react(), // Make sure this is enabled
    ],
});
