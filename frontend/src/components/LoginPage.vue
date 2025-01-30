<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-800">
    <div class="max-w-md w-full bg-white shadow-md rounded-lg p-6 mx-4">
      <div class="flex justify-center">
        <img src="@/assets/logo.png" alt="TrackWave" class="h-14 w-14">
      </div>
      <p class="text-center text-xs font-semibold" style="color: #323F5D">TrackWave</p>
      <h1 class="text-3xl font-bold text-green-600 mb-4">Login</h1>
      <form @submit.prevent="login">
        <div class="mb-4">
          <input
              v-model="email"
              type="email"
              placeholder="Email"
              class="input input-bordered w-full px-3 py-2 border bg-white text-black input-primary rounded-md"
          />
        </div>
        <div class="mb-4">
          <input
              v-model="password"
              type="password"
              placeholder="Password"
              class="input input-bordered w-full px-3 py-2 border bg-white text-black input-primary rounded-md"
          />
        </div>
        <button
            type="submit"
            class="w-full py-2 bg-green-500 text-white rounded-md hover:bg-green-600"
        >
          Login
        </button>
      </form>
      <p class="mt-2 text-center text-green-600">
        Don't have an account?
        <router-link to="/register" class="text-green-500 hover:underline"
        >Register here</router-link
        >
      </p>
      <p class="mt-2 text-center text-green-600">
        Forget you password?
        <router-link to="/forgot-password" class="text-green-500 hover:underline"
        >Reset here</router-link
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
    // ごめん
    await axios.get("https://trackwave.net/sanctum/csrf-cookie");
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
