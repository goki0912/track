import { defineStore } from "pinia";
import { ref } from "vue";

export const useAuthStore = defineStore("auth", () => {
  // State
  const isAuthenticated = ref<boolean>(false);

  // Actions
  const login = () => {
    isAuthenticated.value = true;
    document.cookie = "isAuthenticated=true"; // cookie に認証情報をセット
  };

  const logout = () => {
    isAuthenticated.value = false;
    document.cookie = "isAuthenticated=false"; // cookie の認証情報を削除
  };

  const checkAuth = () => {
    isAuthenticated.value = document.cookie.includes("isAuthenticated=true");
  };

  return {
    isAuthenticated,
    login,
    logout,
    checkAuth,
  };
});
