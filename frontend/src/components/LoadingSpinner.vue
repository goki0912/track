<script setup lang="ts">
import { defineProps } from "vue";
defineProps({
  loading: Boolean,
});
</script>

<template>
  <div>
    <!-- ローディングスピナーを画面中央に表示 -->
    <div
        v-if="loading"
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
    >
      <span class="material-icons animate-spin text-6xl text-emerald-500">
        refresh
      </span>
    </div>
    <!-- ローディングが完了したらスロットの内容を表示 -->
    <slot v-else />
  </div>
</template>

<style scoped>

</style>
