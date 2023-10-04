// router.js
import { createRouter, createWebHistory } from 'vue-router';

import Dashboard from './components/DashboardPage.vue';
import Summary from './components/SummaryPage.vue';
import LoginForm from './components/LoginForm.vue';
import BuyForm from './components/BuyPage.vue';
import SellForm from './components/SellPage.vue';
import LogoutView from './components/LogoutPage.vue';

const routes = [
  { path: '/dashboard', component: Dashboard, meta: { requiresAuth: true } },
  { path: '/summary', component: Summary, meta: { requiresAuth: true } },
  { path: '/summary', component: Summary, meta: { requiresAuth: true } },
  { path: '/buy', component: BuyForm, meta: { requiresAuth: true } },
  { path: '/sell', component: SellForm, meta: { requiresAuth: true } },
  {
    path: '/logout',
    name: 'Logout',
    component: LogoutView,meta: { requiresAuth: true }
  },
  { path: '/login', component: LoginForm },
  { path: '/', redirect: '/dashboard' },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
    if (to.meta.requiresAuth && !localStorage.getItem('token')) {
      // Redirect to the login page if trying to access a protected route without authentication
      next('/login');
    } else {
      next(); // Proceed with the navigation
    }
  });

export default router;
