import { createApp } from 'vue'
import App from './App.vue'

import router from './router'
import 'vue-toast-notification/dist/theme-default.css'; // Import toast notification styles
import VueToast from 'vue-toast-notification';


const app = createApp(App)

app.use(VueToast, {
    position: 'top-right', // Change the position as needed
    duration: 3000, // Toast message duration in milliseconds
  });

app.use(router)

app.mount('#app')
