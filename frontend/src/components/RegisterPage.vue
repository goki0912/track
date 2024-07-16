<template>
  <div class="min-h-screen flex items-center justify-center bg-green-50">
    <div class="max-w-md w-full bg-white shadow-md rounded-lg p-6">
      <h1 class="text-3xl font-bold text-green-600 mb-4">Register</h1>
      <form @submit.prevent="register">
        <div class="mb-4">
          <input v-model="name" type="text" placeholder="Name" class="w-full px-3 py-2 border border-green-300 rounded-md focus:outline-none focus:ring focus:ring-green-200">
        </div>
        <div class="mb-4">
          <input v-model="email" type="email" placeholder="Email" class="w-full px-3 py-2 border border-green-300 rounded-md focus:outline-none focus:ring focus:ring-green-200">
        </div>
        <div class="mb-4">
          <input v-model="password" type="password" placeholder="Password" class="w-full px-3 py-2 border border-green-300 rounded-md focus:outline-none focus:ring focus:ring-green-200">
        </div>
        <div class="mb-4">
          <input v-model="password_confirmation" type="password" placeholder="Confirm Password" class="w-full px-3 py-2 border border-green-300 rounded-md focus:outline-none focus:ring focus:ring-green-200">
        </div>
        <button type="submit" class="w-full py-2 bg-green-500 text-white rounded-md hover:bg-green-600">Register</button>
      </form>
      <p class="mt-4 text-center text-green-600">
        Already have an account? <router-link to="/" class="text-green-500 hover:underline">Login here</router-link>
      </p>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      name: '',
      email: '',
      password: '',
      password_confirmation: ''
    }
  },
  methods: {
    async register() {
      try {
        await axios.post('/register', {
          name: this.name,
          email: this.email,
          password: this.password,
          password_confirmation: this.password_confirmation
        });
        alert('Registered successfully');
        this.$router.push('/');
      } catch (error) {
        console.error(error);
        alert('Registration failed');
      }
    }
  }
}
</script>
