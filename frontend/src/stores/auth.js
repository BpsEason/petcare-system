import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('sanctum_token') || null,
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
    userName: (state) => state.user ? state.user.name : 'Guest',
  },
  actions: {
    async login(email, password) {
      try {
        const response = await axios.post('/login', { email, password });
        this.token = response.data.token;
        this.user = response.data.user;
        localStorage.setItem('sanctum_token', this.token);
        return true;
      } catch (error) {
        this.token = null;
        this.user = null;
        localStorage.removeItem('sanctum_token');
        throw error;
      }
    },
    async register(name, email, password, password_confirmation) {
      try {
        const response = await axios.post('/register', { name, email, password, password_confirmation });
        this.token = response.data.token;
        this.user = response.data.user;
        localStorage.setItem('sanctum_token', this.token);
        return true;
      } catch (error) {
        this.token = null;
        this.user = null;
        localStorage.removeItem('sanctum_token');
        throw error;
      }
    },
    async logout() {
      try {
        await axios.post('/logout');
      } catch (error) {
        console.error('Logout error:', error);
      } finally {
        this.token = null;
        this.user = null;
        localStorage.removeItem('sanctum_token');
      }
    },
    async fetchUser() {
      if (this.token && !this.user) {
        try {
          const response = await axios.get('/user');
          this.user = response.data;
        } catch (error) {
          console.error('Failed to fetch user data:', error);
          this.logout(); // Token might be invalid or expired
        }
      }
    },
  },
});
