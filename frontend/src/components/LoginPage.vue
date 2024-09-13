<template>
  <div class="min-h-screen flex items-center justify-center bg-green-50">
    <div class="max-w-md w-full bg-white shadow-md rounded-lg p-6">
      <h1 class="text-3xl font-bold text-green-600 mb-4">Login</h1>
      <form @submit.prevent="login">
        <div class="mb-4">
          <input
              v-model="email"
              type="email"
              placeholder="Email"
              class="w-full px-3 py-2 border border-green-300 rounded-md focus:outline-none focus:ring focus:ring-green-200"
          />
        </div>
        <div class="mb-4">
          <input
              v-model="password"
              type="password"
              placeholder="Password"
              class="w-full px-3 py-2 border border-green-300 rounded-md focus:outline-none focus:ring focus:ring-green-200"
          />
        </div>
        <button
            type="submit"
            class="w-full py-2 bg-green-500 text-white rounded-md hover:bg-green-600"
        >
          Login
        </button>
      </form>
      <p class="mt-4 text-center text-green-600">
        Don't have an account?
        <router-link to="/register" class="text-green-500 hover:underline"
        >Register here</router-link
        >
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import axios from "axios";

axios.defaults.withCredentials = true;

const email = ref("");
const password = ref("");
const authStore = useAuthStore();
const router = useRouter();

const login = async () => {
  try {
    await axios.get("http://localhost:8000/sanctum/csrf-cookie"); // CSRFトークンを取得
    const response = await axios.post("/login", {
      email: email.value,
      password: password.value,
    });

    const token = response.data.access_token;
    axios.defaults.headers.common.Authorization = `Bearer ${token}`;
    document.cookie = `token=${token}; path=/`; // トークンをCookieに保存
    document.cookie = "isAuthenticated=true; path=/"; // 認証情報をCookieに保存

    alert("Logged in successfully");
    authStore.login(); // 認証状態を更新
    router.push("/theme/list");
  } catch (error) {
    console.error(error);
    alert("Login failed");
  }
};
</script>
