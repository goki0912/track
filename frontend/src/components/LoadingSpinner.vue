<script setup lang="ts">
import { defineProps } from "vue";
defineProps({
  loading: Boolean,
});
</script>

<template>
  <div>
    <!-- ローディングスピナーを画面中央に表示 -->
    <div v-if="loading" class="flex justify-center items-center h-96">
      <span class="loading loading-spinner loading-lg text-primary"></span>
    </div>
    <!-- ローディングが完了したらスロットの内容を表示 -->
    <slot v-else />
  </div>
</template>

<style scoped>

</style>
