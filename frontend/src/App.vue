<script setup>
import { useI18n } from 'vue-i18n';
import { useAuthStore } from './stores/auth'; // 引入 auth store
import { useRouter } from 'vue-router'; // 引入 useRouter

const { t } = useI18n(); // 引入 t 函數
const authStore = useAuthStore(); // 使用 auth store
const router = useRouter();

const handleLogout = async () => {
  await authStore.logout();
  router.push('/login'); // 登出後導向登錄頁
};
</script>

<template>
  <div id="app">
    <nav>
      <router-link to="/">{{ t('home') }}</router-link> |
      <router-link v-if="!authStore.isAuthenticated" to="/login">{{ t('login') }}</router-link>
      <span v-if="!authStore.isAuthenticated"> | </span>
      <router-link v-if="!authStore.isAuthenticated" to="/register">{{ t('register') }}</router-link>
      <span v-if="authStore.isAuthenticated"> | </span>
      <router-link v-if="authStore.isAuthenticated" to="/dashboard">{{ t('dashboard') }}</router-link>
      
      <span v-if="authStore.isAuthenticated" style="margin-left: 20px;">
        {{ t('welcome_user', { name: authStore.userName }) }}
        <button @click="handleLogout" class="logout-btn">{{ t('logout') }}</button>
      </span>

      <select v-model=".locale" style="margin-left: 20px;">
        <option value="en">English</option>
        <option value="zh_TW">繁體中文</option>
        <option value="zh_CN">简体中文</option>
      </select>
    </nav>
    <router-view />
  </div>
</template>

<style scoped>
nav {
  padding: 30px;
  text-align: center;
  background-color: #f8f8f8;
  border-bottom: 1px solid #eee;
}
nav a {
  font-weight: bold;
  color: #2c3e50;
  margin: 0 10px;
  text-decoration: none;
}
nav a.router-link-exact-active {
  color: #42b983;
}
.logout-btn {
  background-color: #f44336;
  color: white;
  padding: 5px 10px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
  margin-left: 10px;
}
.logout-btn:hover {
  background-color: #d32f2f;
}
</style>
