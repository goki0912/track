<script setup lang="ts">
import { useAuthStore } from "@/stores/auth";
import { ref } from "vue";
const authStore = useAuthStore();

const mail = ref("");
const submit = () => {
  authStore.sendPasswordResetEmail(mail.value);
};
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-800">
    <div class="max-w-md w-full bg-white shadow-md rounded-lg p-6 mx-4">
      <h1 class="text-2xl font-bold text-green-600 mb-4">Forgot password</h1>
      <form @submit.prevent="submit">
        <div class="mb-4">
          <input
            v-model="mail"
            type="email"
            placeholder="Enter your email"
            class="input input-bordered w-full px-3 py-2 border bg-white text-black input-primary rounded-md"
          />
        </div>
        <button
            type="submit"
            class="w-full py-2 bg-green-500 text-white rounded-md hover:bg-green-600"
        >
          send reset mail
        </button>
      </form>
      <p class="mt-2 text-center text-green-600">
        Already have an account?
        <router-link to="/" class="text-green-500 hover:underline"
        >Login here</router-link
        >
      </p>
    </div>
  </div>
</template>

<style scoped>

</style>
