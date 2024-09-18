<script setup lang="ts">
import { defineEmits } from "vue";
const emit = defineEmits();
const reload = () => {
  emit("reload");
};

</script>

<template>
  <button @click="reload" class="items-center px-2 py-1 bg-blue-500 text-white hover:bg-blue-600 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 border-black border-1">
    <span class="material-icons">refresh</span>
  </button>
</template>

<style scoped>

</style>