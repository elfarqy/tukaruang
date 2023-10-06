<!-- src/components/LoginForm.vue -->


<script>
import axios from "axios";
// import { useRouter } from 'vue-router';
export default {
  data() {
    return {
      username: "",
      password: "",
    };
  },
  methods: {
  async login() {
    try {
      const response = await axios.post("http://localhost:8081/backend/backend/web/api/login", {
        username: this.username,
        password: this.password,
      });
      if (response.status == 200) {
          console.log(response.data.data.user.token);
          const token = response.data.data.user.token;

          localStorage.setItem('token', token);
          this.$router.push('/dashboard');
          window.location.reload();
        } else {
          console.error('Login failed');
        }
    } catch (error) {
      console.error(error);
    }
  },
}
};
</script>

<template>
  <div class="login-form">
    <h2>Nuker Duit</h2>
    <form @submit.prevent="login">
      <input type="text" v-model="username" placeholder="Username" required />
      <input type="password" v-model="password" placeholder="Password" required />
      <button type="submit">Login</button>
    </form>
  </div>
</template>

<style scoped>
/* Add your CSS styles here */
</style>
