<!-- NavigationMenu.vue -->
<template>
    <nav>
      <router-link to="/dashboard" v-if="authenticated">Dashboard</router-link>
      <router-link to="/summary" v-if="authenticated">Summary</router-link>
      <router-link to="/buy" v-if="authenticated">Buy</router-link>
      <router-link to="/sell" v-if="authenticated">Sell</router-link>
      <router-link to="/login" v-if="!authenticated">Login</router-link>
      <router-link to="/logout" v-if="authenticated"><Logout /></router-link>
    </nav>
  </template>
  
  <script>
  import Logout from './LogoutPage.vue';
  export default {
    computed: {
      authenticated() {
        const token = localStorage.getItem('token');
        return !!token; // Return true if the token exists
      },
    },
    components: {
      Logout,
    },

  };
  </script>
  
  <style>
  /* Add your CSS styles for the navigation menu */
  </style>
  