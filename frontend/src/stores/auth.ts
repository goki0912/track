import { defineStore } from "pinia";
import { ref } from "vue";
import { useToast } from "vue-toast-notification";

export const useAuthStore = defineStore("auth", () => {
  const toast = useToast();
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
    toast.success("Logged out successfully", {
      position: "top-right",
    });
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
