<script lang="ts" setup>
import Logout from "@/components/LogoutPage.vue";
import { ref, computed } from "vue";
import { useRoute } from "vue-router";

const menuItems = ref([
  { name: "Top", path: "/top" },
  { name: "Profile", path: "/profile" },
  { name: "Settings", path: "/settings" },
  // 他のメニュー項目もここに追加できます
]);

const isMenuOpen = ref(false);

const route = useRoute();
// メニューを非表示にするルートパスのリスト
const hideMenuRoutePatterns = [
  "/login",
  "/register",
  "/forgot-password",
  /^\/password-reset\/[^\/?]+/
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
const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value;
};
</script>

<template>
  <div class="flex bg-green-50">
    <div
      v-if="showMenu"
      class="hidden md:block md:w-56 bg-gradient-to-r from-green-500 via-green-400 to-green-300 text-white min-h-screen"
    >
      <div class="p-4">
        <h2 class="text-2xl font-bold">Track.com</h2>
        <ul>
          <li v-for="item in menuItems" :key="item.path" class="my-2">
            <a
              :href="item.path"
              class="block py-2 px-4 rounded hover:bg-green-800"
              >{{ item.name }}</a
            >
          </li>
          <Logout />
        </ul>
      </div>
    </div>
    <div class="flex-1 bg-green-50">
      <div
        v-if="showMenu"
        class="md:hidden bg-gradient-to-r from-green-500 via-green-400 to-green-300 p-4 text-white flex justify-between items-center"
      >
        <h2 class="text-2xl font-bold">Track.com</h2>
        <button @click="toggleMenu" class="focus:outline-none">
          <svg
            class="w-6 h-6"
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
      </div>
      <div
        v-if="showMenu && isMenuOpen"
        class="md:hidden bg-gradient-to-r from-green-500 via-green-400 to-green-300 text-white p-4"
      >
        <ul>
          <li v-for="item in menuItems" :key="item.path" class="my-2">
            <a
              :href="item.path"
              class="block py-2 px-4 rounded hover:bg-green-800"
              >{{ item.name }}</a
            >
          </li>
          <Logout />
        </ul>
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
