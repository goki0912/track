<script setup lang="ts">
import { useAuthStore } from "@/stores/auth";
import { ref } from "vue";
const authStore = useAuthStore();

const mail = ref("");
const submit = () => {
  console.log("ああああ", mail.value);
  authStore.sendPasswordResetEmail(mail.value);
};
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-green-50">
    <div class="max-w-md w-full bg-white shadow-md rounded-lg p-6">
      <h1 class="text-3xl font-bold text-green-600 mb-4">Forgot password</h1>
      <form @submit.prevent="submit">
        <div class="mb-4">
          <input
            v-model="mail"
            type="email"
            placeholder="Email"
            class="w-full px-3 py-2 border border-green-300 rounded-md focus:outline-none focus:ring focus:ring-green-200"
          />
        </div>
        <button
            type="submit"
            class="w-full py-2 bg-green-500 text-white rounded-md hover:bg-green-600"
        >
          send reset mail
        </button>
      </form>
    </div>
  </div>
</template>

<style scoped>

</style>
