<!-- Logout.vue -->
<template>
    <div>
      <button @click="logout">Logout</button>
    </div>
  </template>
  
  <script>
  export default {
    methods: {
      logout() {
        localStorage.removeItem('token');
        this.$router.push('/login');
        window.location.reload();
      },
    },
  };
  </script>
  