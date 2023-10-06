<!-- NavigationMenu.vue -->
<template>
    <nav class="navbar">
      <ul>
      <li><router-link to="/dashboard" v-if="authenticated">Dashboard</router-link></li>
      <li><router-link to="/summary" v-if="authenticated">Summary</router-link></li>
      <li><router-link to="/buy" v-if="authenticated">Buy</router-link></li>
      <li><router-link to="/sell" v-if="authenticated">Sell</router-link></li>
      <li><router-link to="/login" v-if="!authenticated">Login</router-link></li>
      <li><router-link to="/logout" v-if="authenticated"><Logout /></router-link></li>
      </ul>
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
  