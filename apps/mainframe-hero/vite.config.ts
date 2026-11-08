import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
  plugins: [react(), tailwindcss()],

  // Relative base so the built preview works from any path — the atelier
  // dashboard embeds it at /previews/mainframe/ under GitHub Pages.
  base: './',

  build: {
    outDir: '../../previews/mainframe',
    emptyOutDir: true,
  },

  server: {
    host: '0.0.0.0',
    port: 5173,
    // The preview is served through a proxied host, so allow it explicitly.
    allowedHosts: true,
  },
});
