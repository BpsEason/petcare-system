<script setup>
import { useI18n } from 'vue-i18n';
const { t } = useI18n();
</script>

<template>
  <div class="home">
    <h1>{{ t('welcome_message') }}</h1>
    <p>{{ t('app_description') }}</p>
    <p>{{ t('please_login_register') }}</p>
  </div>
</template>

<style scoped>
.home {
  text-align: center;
  padding: 20px;
}
</style>
