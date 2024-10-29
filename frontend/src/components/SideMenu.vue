<script lang="ts" setup>
import Logout from "@/components/LogoutPage.vue";
import { ref, computed } from "vue";
import { useRoute } from "vue-router";

const menuItems = ref([
  { name: "Top", path: "/theme" },
  { name: "Profile", path: "/profile" },
  { name: "Settings", path: "/settings" },
  // 他のメニュー項目もここに追加できます
]);

const route = useRoute();
// メニューを非表示にするルートパスのリスト
const hideMenuRoutePatterns = [
  "/login",
  "/register",
  "/forgot-password",
  // eslint-disable-next-line
  /^\/password-reset\/[^\/?]+/,
];

// 現在のルートがメニュー非表示パターンに一致するか判定
const showMenu = computed(() => {
  return !hideMenuRoutePatterns.some((pattern) => {
    if (typeof pattern === "string") {
      return pattern === route.path;
    } else {
      return pattern.test(route.path); // 正規表現の場合はパターンマッチ
    }
  });
});
</script>

<template>
  <div class="flex bg-base-300">
    <div
      v-if="showMenu"
      class="hidden md:block md:w-56 bg-gradient-to-r from-green-500 via-green-400 to-green-300 text-white min-h-screen"
    >
      <div class="p-4">
        <img src="@/assets/logo.png" alt="TrackWave" class="h-8 w-8">
        <h2 class="text-2xl font-bold" style="color: #323F5D">TrackWave</h2>
        <ul>
          <li v-for="item in menuItems" :key="item.path" class="my-2">
            <a
              :href="item.path"
              class="block py-2 px-4 rounded hover:bg-gray-100"
              >{{ item.name }}</a
            >
          </li>
          <Logout />
        </ul>
      </div>
    </div>
    <div class="flex-1 bg-base-100">
      <div
        v-if="showMenu"
        class="navbar md:hidden bg-gradient-to-r from-green-500 via-green-400 to-green-300 p-4 text-white flex justify-between items-center"
      >
        <div class="space-x-1">
          <img src="@/assets/logo.png" alt="TrackWave" class="h-8 w-8">
          <h2 class="text-2xl font-bold" style="color: #323F5D">TrackWave</h2>
        </div>
        <div class="space-x-1">
          <div class="dropdown dropdown-end">
            <button class="bg-opacity-0 border-0 list-none">
              <svg
                class="w-6 h-6 text-white"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M4 6h16M4 12h16m-7 6h7"
                ></path>
              </svg>
            </button>
            <ul class="menu dropdown-content bg-white rounded-box border border-gray-200 z-50">
              <li v-for="item in menuItems" :key="item.path" class="mb-1 text-black hover:bg-gray-200">
                <a
                  :href="item.path"
                  class="block py-2 px-4 rounded"
                  >{{ item.name }}</a
                >
              </li>
              <Logout class="mb-1" />
            </ul>
          </div>
        </div>
      </div>
      <div class="">
        <slot></slot>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* 必要に応じて追加のスタイルをここに追加します */
</style>
