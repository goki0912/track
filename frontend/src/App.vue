<template>
  <div id="app">
    <nav>
      <router-link to="/">Home</router-link>
      <router-link to="/auth">Auth</router-link>
    </nav>
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
