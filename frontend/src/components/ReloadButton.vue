<script setup lang="ts">
import { defineEmits } from "vue";
const emit = defineEmits();
const reload = () => {
  emit("reload");
};
</script>

<template>
  <!-- DaisyUIのスタイルを適用したリロードボタン -->
  <button
      @click="reload"
      class="btn btn-secondary btn-circle btn-sm shadow-md hover:shadow-lg transition duration-300"
  >
    <span class="material-icons text-white">refresh</span>
  </button>
</template>
