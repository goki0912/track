<template>
  <div class="min-h-screen flex items-center justify-center bg-green-50">
    <div class="max-w-md w-full bg-white shadow-md rounded-lg p-6">
      <h1 class="text-3xl font-bold text-green-600 mb-4">Home</h1>
      <p>Welcome to the Home page!</p>
      <Logout />
    </div>
  </div>
</template>

<script>
import Logout from './LogoutPage.vue';

export default {
  components: {
    Logout
  }
}
</script>
