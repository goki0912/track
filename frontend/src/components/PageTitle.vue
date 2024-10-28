<template>
  <h1 class="text-3xl font-bold text-primary my-3 text-center">{{ title }}</h1>
  <div class="divider text-secondary">***</div>
</template>
<script type="ts" setup>
import { defineProps } from 'vue';
defineProps({
  title: String
})
</script>