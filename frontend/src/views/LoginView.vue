<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { useAuthStore } from '../stores/auth'; // 引入 auth store

const email = ref('');
const password = ref('');
const errorMessage = ref('');
const router = useRouter();
const { t } = useI18n();
const authStore = useAuthStore(); // 使用 auth store

const handleLogin = async () => {
  errorMessage.value = '';
  try {
    await authStore.login(email.value, password.value);
    alert(t('login_success_alert'));
    router.push('/dashboard');
  } catch (error) {
    errorMessage.value = error.response?.data?.message || t('login_failed');
    console.error('Login error:', error);
  }
};
</script>

<template>
  <div class="auth-container">
    <h2>{{ t('login') }}</h2>
    <form @submit.prevent="handleLogin">
      <div class="form-group">
        <label for="email">{{ t('email') }}：</label>
        <input type="email" id="email" v-model="email" required />
      </div>
      <div class="form-group">
        <label for="password">{{ t('password') }}：</label>
        <input type="password" id="password" v-model="password" required />
      </div>
      <button type="submit">{{ t('login') }}</button>
      <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>
    </form>
  </div>
</template>

<style scoped>
.auth-container {
  max-width: 400px;
  margin: 50px auto;
  padding: 20px;
  border: 1px solid #ccc;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}
.form-group {
  margin-bottom: 15px;
}
label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
}
input[type="email"],
input[type="password"] {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
}
button {
  width: 100%;
  padding: 10px;
  background-color: #42b983;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
}
button:hover {
  background-color: #369c71;
}
.error-message {
  color: red;
  margin-top: 10px;
  text-align: center;
}
</style>
