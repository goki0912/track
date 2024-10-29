<template>
  <div v-if="isOpen" class="modal modal-open" @click.self="closeModal">
    <div class="modal-box relative bg-gray-100">
      <button
          @click="closeModal"
          class="btn btn-sm btn-circle absolute right-2 top-2"
      >
        ✕
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
/* 必要に応じて追加のスタイルを設定 */
</style>
