<template>
  <button @click="logout" class="text-red-400 rounded hover:bg-gray-200 cursor-pointer">
      Logout
  </button>
</template>

<script setup>
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import axios from "axios";

// CSRFトークンをクッキーに保存する設定
axios.defaults.withCredentials = true;

const authStore = useAuthStore();
const router = useRouter();

const logout = async () => {
  try {
    // CSRFトークンを取得
    await axios.get(`${process.env.VUE_APP_API_BASE_URL}/sanctum/csrf-cookie`);
    // ログアウトAPIにリクエスト
    await axios.post("/logout");
    // Cookieからトークンと認証情報を削除
    document.cookie = "token=; Max-Age=0; path=/";
    document.cookie = "isAuthenticated=; Max-Age=0; path=/";
    // Axiosの認証ヘッダーを削除
    if (axios.defaults.headers.common.Authorization) {
      delete axios.defaults.headers.common.Authorization;
    }
    // authStoreを使ってローカルのログアウト処理
    await authStore.logout();
    // ログインページへリダイレクト(なぜかawaitないとだめ
    await router.push("/login");
  } catch (error) {
    console.error(error);
    alert("Logout failed");
  }
};
</script>
