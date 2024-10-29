<script setup lang="ts">
import { computed, onMounted, ref } from "vue";
import { useThemeStore } from "@/stores/themeStore";
import { useRouter } from "vue-router";
import LoadingSpinner from "@/components/LoadingSpinner.vue";
import PageTitle from "@/components/PageTitle.vue";

const themeStore = useThemeStore();
const router = useRouter();
const loading = ref(true);

onMounted(async () => {
  await themeStore.fetchThemes();
  loading.value = false;
});
const themes = computed(() => themeStore.themes);
const showTheme = async (themeId: number) => {
  await router.push(`/theme/${themeId}/posts`);
};
</script>

<template>
  <div class="h-screen">
    <PageTitle :title="'Theme'" />
    <LoadingSpinner v-if="loading" :loading="loading" />
    <div v-else class="h-screen">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-1 mx-2">
        <div
          v-for="(theme, index) in themes"
          :key="theme.id"
          class="card shadow-lg bg-gradient-to-br from-green-100 to-green-50 cursor-pointer hover:shadow-xl transition-transform transform hover:scale-105"
          @click="showTheme(theme.id)"
        >
          <div class="card-body p-4 flex">
            <div
              class="card-title text-sm font-semibold truncate text-black justify-between"
            >
              <h2 class="gap-1">
                {{ theme.title }}
                <span v-if="index == 0" class="badge badge-accent">new</span>
              </h2>
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                class="w-6 h-6 text-gray-500 mr-3"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M4 12h16M14 6l6 6-6 6"
                />
              </svg>
            </div>

            <!-- 右矢印アイコン -->
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* 高さを固定してカードの見た目をシンプルに調整 */
.card-body {
  height: 100%;
}
</style>
