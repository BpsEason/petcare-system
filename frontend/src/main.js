import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import axios from 'axios';
import { createI18n } from 'vue-i18n';
import { createPinia } from 'pinia'; // 引入 createPinia
import { useAuthStore } from './stores/auth'; // 引入 auth store

// 引入語言檔案
import en from './locales/en.json';
import zh_TW from './locales/zh_TW.json';
import zh_CN from './locales/zh_CN.json'; // 引入簡體中文

// 建立 i18n 實例
const i18n = createI18n({
  locale: 'zh_TW', // 設置預設語言為繁體中文
  fallbackLocale: 'en', // 設置備用語言為英文
  messages: {
    en,
    zh_TW,
    zh_CN // 添加簡體中文
  }
});

// 建立 Pinia 實例
const pinia = createPinia();

// Axios 全局配置
axios.defaults.baseURL = import.meta.env.VITE_API_BASE_URL || "http://localhost:8080/api";
axios.defaults.withCredentials = true; // 允許發送跨域憑證（如 cookie, authorization headers）

// Axios 請求攔截器：添加認證 Token 和 Accept-Language 頭
axios.interceptors.request.use((config) => {
  const token = localStorage.getItem("sanctum_token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  // 添加 Accept-Language 頭，告訴後端當前前端使用的語言
  config.headers['Accept-Language'] = i18n.global.locale.value;
  return config;
});

// Axios 回應攔截器：處理 401 未認證錯誤
axios.interceptors.response.use(
  (response) => response,
  (error) => {
    // 如果是 401 錯誤且不是登出請求，則移除 token 並重導向
    if (error.response && error.response.status === 401 && error.response.data.message !== "Unauthenticated.") {
      const authStore = useAuthStore();
      authStore.logout(); // 使用 Pinia 的 logout action
      router.push('/login'); // 重導向到登錄頁面
    }
    return Promise.reject(error);
  }
);

const app = createApp(App);
app.use(router); // 使用 Vue Router
app.use(i18n);   // 使用 vue-i18n
app.use(pinia);  // 使用 Pinia

// 在應用程式掛載前嘗試載入用戶資訊 (如果已有 token)
const authStore = useAuthStore();
if (authStore.isAuthenticated) {
    authStore.fetchUser();
}

app.mount('#app');

// PWA 註冊 Service Worker
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js')
      .then(registration => {
        console.log('Service Worker registered: ', registration);
      })
      .catch(registrationError => {
        console.log('Service Worker registration failed: ', registrationError);
      });
  });
}
