<script setup lang="ts">
import { onMounted, ref, watch } from "vue";
import { Theme } from "@/types";
import { useThemeStore } from "@/stores/themeStore";
import { useRouter } from "vue-router";

const themeStore = useThemeStore();
const themes = ref<Theme[]>([]);
const router = useRouter();
onMounted(async () => {
  await themeStore.fetchThemes();
});
watch(
  () => themeStore.themes,
  (newTheme) => {
    themes.value = newTheme;
  },
);
const showTheme = async (themeId: number) => {
  await router.push(`/theme/${themeId}/posts`);
};

</script>

<template>
  <h1>Theme Page</h1>
  <p v-for="theme in themes">
    <button
        @click="showTheme(theme.id)"
        class="bg-stone-200 text-left w-full py-2 px-4 rounded hover:bg-stone-400 transition duration-300"
    >
      {{theme.title}}
    </button>
  </p>
</template>

<style scoped>

</style>
