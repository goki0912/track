import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    isAuthenticated: false
  }),
  actions: {
    login () {
      this.isAuthenticated = true
    },
    logout () {
      this.isAuthenticated = false
    },
    checkAuth () {
      this.isAuthenticated = document.cookie.includes('isAuthenticated=true')
    }
  }
})
