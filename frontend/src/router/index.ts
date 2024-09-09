import { createRouter, createWebHistory, RouteRecordRaw } from "vue-router";
import TopPage from "../components/TopPage.vue";
import LoginPage from "@/components/LoginPage.vue"; // ホームページ用のコンポーネントを作成します
import RegisterPage from "@/components/RegisterPage.vue";
import ProfilePage from "@/components/ProfilePage.vue";
import { useAuthStore } from "@/stores/auth";
import { useSpotifyStore } from "@/stores/spotify";
import ThemeList from "@/components/ThemeList.vue";

const routes: Array<RouteRecordRaw> = [
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
    path: "/top",
    component: TopPage,
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
  {
    path: "/theme",
    component: ThemeList,
    meta: {
        requiresAuth: true,
    }
  },
  {
    path: "/theme/:id",
    component: TopPage,
    meta: {
        requiresAuth: true,
    }
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();
  authStore.checkAuth(); // ここで認証状態を確認
  // 存在しないページにアクセスした場合/loginへ
  if (!to.matched.length) {
    next("/login");
  }
  if (
      to.matched.some((record) => record.meta.requiresAuth) &&
      !authStore.isAuthenticated
  ) {
    next("/login");
  } else {
    const spotifyStore = useSpotifyStore();
    await spotifyStore.ensureAuthenticated();
    if (
      to.matched.some((record) => record.meta.guestOnly) &&
      authStore.isAuthenticated
    ) {
      next("/top");
    } else {
      next();
    }
  }
});

export default router;
