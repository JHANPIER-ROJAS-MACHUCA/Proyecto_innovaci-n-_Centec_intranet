import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
  plugins: [react()],
  base: './',
  publicDir: 'public',
  build: { outDir: 'dist', emptyOutDir: true },
  server: {
    port: 5173,
    proxy: {
      // El frontend llama a /Centecp_Intranet/backend/... ; en dev redirigir a Apache
      '/Centecp_Intranet/backend': 'http://localhost:80',
    },
  },
});
