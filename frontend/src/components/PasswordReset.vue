<script setup lang="ts">
import { ref } from "vue";
import { useRoute } from "vue-router";
import { useAuthStore } from "@/stores/auth";

// ルート情報の取得
const route = useRoute();

const authStore = useAuthStore();

// リアクティブ変数
const email = ref(route.query.email || ""); // クエリからメール取得
const password = ref("");
const passwordConfirmation = ref("");

const submit = () => {
  authStore.resetPassword(email.value, password.value, passwordConfirmation.value);
};

</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-800">
    <div class="max-w-md w-full bg-white shadow-md rounded-lg p-6 mx-4">
      <h1 class="text-2xl font-bold text-green-600 mb-4">Reset Password</h1>

      <form @submit.prevent="submit">
        <div class="mb-4">
          <label class="block text-gray-700">Email</label>
          <input
              v-model="email"
              type="email"
              class="input input-bordered w-full px-3 py-2 border bg-white text-black input-primary rounded-md"
              readonly
          />
        </div>

        <div class="mb-4">
          <label class="block text-gray-700">New Password</label>
          <input
              v-model="password"
              type="password"
              placeholder="Enter new password"
              class="input input-bordered w-full px-3 py-2 border bg-white text-black input-primary rounded-md"
          />
        </div>

        <div class="mb-4">
          <label class="block text-gray-700">Confirm Password</label>
          <input
              v-model="passwordConfirmation"
              type="password"
              placeholder="Confirm new password"
              class="input input-bordered w-full px-3 py-2 border bg-white text-black input-primary rounded-md"
          />
        </div>
        <button
            type="submit"
            class="w-full py-2 bg-green-500 text-white rounded-md hover:bg-green-600"
        >
          Reset Password
        </button>
      </form>
    </div>
  </div>
</template>
