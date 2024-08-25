import { createRouter, createWebHistory } from "vue-router";
import HomePage from "../components/HomePage.vue";
import LoginPage from "@/components/LoginPage.vue"; // ホームページ用のコンポーネントを作成します
import RegisterPage from "@/components/RegisterPage.vue";
import ProfilePage from "@/components/ProfilePage.vue";
import { useAuthStore } from "@/stores/auth";

const routes = [
  {
    path: "/login",
    component: LoginPage,
    meta: {
      requiresAuth: false,
      guestOnly: true,
    },
  },
  {
    path: "/register",
    component: RegisterPage,
    meta: {
      requiresAuth: false,
      guestOnly: true,
    },
  },
  {
    path: "/home",
    component: HomePage,
    meta: {
      requiresAuth: true,
    },
  },
  {
    path: "/profile",
    component: ProfilePage,
    meta: {
      requiresAuth: true,
    },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();
  authStore.checkAuth(); // ここで認証状態を確認
  if (
    to.matched.some((record) => record.meta.requiresAuth) &&
    !authStore.isAuthenticated
  ) {
    next("/login");
  } else if (
    to.matched.some((record) => record.meta.guestOnly) &&
    authStore.isAuthenticated
  ) {
    next("/home");
  } else {
    next();
  }
});

export default router;
