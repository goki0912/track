<template>
  <a @click="logout" class="block py-2 px-4 rounded text-red-400 hover:bg-green-800">Logout</a>
</template>

<script>
import { useAuthStore } from '@/stores/auth';
import axios from 'axios';

axios.defaults.withCredentials = true;

export default {
  methods: {
    async logout() {
      const authStore = useAuthStore();
      try {
        await axios.get('http://localhost:8000/sanctum/csrf-cookie'); // CSRFトークンを取得
        await axios.post('/logout');
        document.cookie = 'token=; Max-Age=0; path=/'; // トークンをCookieから削除
        document.cookie = 'isAuthenticated=; Max-Age=0; path=/'; // 認証情報をCookieから削除
        delete axios.defaults.headers.common['Authorization']; // ヘッダーからトークンを削除
        authStore.logout();
        this.$router.push('/login');
      } catch (error) {
        console.error(error);
        alert('Logout failed');
      }
    }
  }
}
</script>
