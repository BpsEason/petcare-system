import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { VitePWA } from 'vite-plugin-pwa' // 引入 VitePWA

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    vue(),
    VitePWA({
      registerType: 'autoUpdate',
      injectRegister: 'auto',
      workbox: {
        globPatterns: ['**/*.{js,css,html,ico,png,svg,json}'], // 緩存所有相關文件
      },
      manifest: {
        name: 'PetCare System',
        short_name: 'PetCare',
        description: 'Your ultimate pet health management companion.',
        theme_color: '#42b983',
        icons: [
          {
            src: 'assets/icon-192x192.png',
            sizes: '192x192',
            type: 'image/png',
          },
          {
            src: 'assets/icon-512x512.png',
            sizes: '512x512',
            type: 'image/png',
          },
        ],
      },
      devOptions: {
        enabled: true, // 開發模式下啟用 PWA
      },
    }),
  ],
  server: {
    port: 3000,
    proxy: {
      '/api': {
        target: 'http://backend:8000', # Changed from localhost to backend service name
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/api/, '') # remove /api prefix when forwarding
      }
    }
  }
})
