<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { useAuthStore } from '../stores/auth'; // 引入 auth store

const name = ref('');
const email = ref('');
const password = ref('');
const password_confirmation = ref('');
const errorMessage = ref('');
const router = useRouter();
const { t } = useI18n();
const authStore = useAuthStore(); // 使用 auth store

const handleRegister = async () => {
  errorMessage.value = '';
  try {
    await authStore.register(name.value, email.value, password.value, password_confirmation.value);
    alert(t('registration_success_alert'));
    router.push('/dashboard');
  } catch (error) {
    errorMessage.value = error.response?.data?.message || t('registration_failed');
    if (error.response?.data?.errors) {
      errorMessage.value = Object.values(error.response.data.errors).flat().join(' ');
    }
    console.error('Register error:', error);
  }
};
</script>

<template>
  <div class="auth-container">
    <h2>{{ t('register') }}</h2>
    <form @submit.prevent="handleRegister">
      <div class="form-group">
        <label for="name">{{ t('name') }}：</label>
        <input type="text" id="name" v-model="name" required />
      </div>
      <div class="form-group">
        <label for="email">{{ t('email') }}：</label>
        <input type="email" id="email" v-model="email" required />
      </div>
      <div class="form-group">
        <label for="password">{{ t('password') }}：</label>
        <input type="password" id="password" v-model="password" required />
      </div>
      <div class="form-group">
        <label for="password_confirmation">{{ t('confirm_password') }}：</label>
        <input type="password" id="password_confirmation" v-model="password_confirmation" required />
      </div>
      <button type="submit">{{ t('register') }}</button>
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
input[type="text"],
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
