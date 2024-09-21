<template>
  <div
    v-if="isOpen"
    class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center z-50"
    @click.self="closeModal"
  >
    <div
      class="bg-white p-6 rounded-lg w-11/12 relative max-h-full overflow-y-auto"
    >
      <button
        @click="closeModal"
        class="text-gray-500 hover:text-gray-700 absolute top-4 right-4"
      >
        &times;
      </button>
      <slot></slot>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { defineProps, defineEmits } from "vue";

defineProps({
  isOpen: Boolean,
});

const emit = defineEmits(["close"]);

const closeModal = () => {
  emit("close");
};
</script>

<style scoped>
/* 追加のスタイリングをここに記述 */
</style>
