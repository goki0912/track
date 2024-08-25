<template>
  <div id="app">
    <SideMenu>
      <router-view />
    </SideMenu>
  </div>
</template>

<script lang="ts" setup>
import SideMenu from "./components/SideMenu.vue";
</script>
