<script setup lang="ts">
import { computed, onMounted, ref } from "vue";
import { useThemeStore } from "@/stores/themeStore";
import { useRouter } from "vue-router";
import LoadingSpinner from "@/components/LoadingSpinner.vue";

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
  <div>
    <LoadingSpinner :loading="loading" />
    <h1>Theme Page</h1>
    <div v-for="theme in themes" :key="theme.id">
      <button
          @click="showTheme(theme.id)"
          class="bg-stone-200 text-left w-full py-2 px-4 rounded hover:bg-stone-400 transition duration-300"
      >
        {{theme.title}}
      </button>
    </div>
  </div>
</template>

<style scoped>

</style>
