<template>
  <div id="app">
    <router-view></router-view>
  </div>
</template>

<script>
export default {
  name: 'App',
}
</script>

<style>
nav {
  display: flex;
  gap: 10px;
}

router-link {
  text-decoration: none;
  color: #42b983;
}
</style>
