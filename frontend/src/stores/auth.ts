import { defineStore } from "pinia";
import { ref } from "vue";
import { useToast } from "vue-toast-notification";
import axios from "axios";
import { useRoute, useRouter } from "vue-router";

export const useAuthStore = defineStore("auth", () => {
  const toast = useToast();
  const route = useRoute();
  const router = useRouter();
  // State
  const isAuthenticated = ref<boolean>(false);

  // Actions
  const login = () => {
    isAuthenticated.value = true;
    document.cookie = "isAuthenticated=true"; // cookie に認証情報をセット
  };

  const logout = async () => {
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
      await axios.post("/password/email", {
        email,
      });
      toast.success("Password reset email sent", {
        position: "top-right",
      });
    } catch (error) {
      toast.error("Failed to send password reset email", {
        position: "top-right",
      });
      console.error(error);
    }
  };

  const resetPassword = async (email: string, password: string, passwordConfirmation: string) => {
    const token = route.params.token as string; // パラメータからトークン取得

    try {
      await axios.post("/password/reset", {
        email: email.value,
        password: password.value,
        password_confirmation: passwordConfirmation.value,
        token,
      });
      toast.success("Password reset successfully", {
        position: "top-right",
      });

      setTimeout(() => {
        router.push("/login"); // ログインページにリダイレクト
      }, 3000);
    } catch (error: any) {
      console.log(error.response.data);
      toast.error("Failed to reset password", {
        position: "top-right",
      });
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
