import { defineStore } from "pinia";
import { ref } from "vue";
import { useToast } from "vue-toast-notification";
import axios from "axios";

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

  const sendPasswordResetEmail = async (email: string) => {
    console.log(email);
    try {
      // パスワードリセットメールを送信する処理
      const response = await axios.post("/password/email", {
        email,
      });
      console.log(response);
    } catch (error) {
      console.error(error);
    }
  };

  const resetPassword = async (email: string, password: string, token: string) => {
    try {
      // パスワードリセット処理
      const response = await axios.post("/reset-password", {
        email,
        password,
        token,
      });
      console.log(response);
    } catch (error) {
      console.error(error);
    }
  };

  return {
    isAuthenticated,
    login,
    logout,
    checkAuth,
    sendPasswordResetEmail,
    resetPassword,
  };
});
