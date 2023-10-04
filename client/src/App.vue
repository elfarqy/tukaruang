<!-- App.vue -->
<template>
  <div id="app">
    <NavigationMenu />
    <router-view />

    <LoginForm />
  </div>
</template>

<script>
import NavigationMenu from './components/NavigationMenu.vue';

export default {
  components: {
    NavigationMenu
  },
};
</script>

<style>
/* Add your CSS styles for the overall layout */
</style>
