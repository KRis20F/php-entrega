import { createRouter, createWebHistory } from 'vue-router'
import Login from '../views/Login.vue'
import Products from '../views/Products.vue'
import AuthService from '../services/auth.service'

const routes = [
  {
    path: '/',
    redirect: '/products'
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { requiresGuest: true }
  },
  {
    path: '/products',
    name: 'Products',
    component: Products,
    meta: { requiresAuth: true }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const isAuthenticated = !!AuthService.getCurrentUser()

  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login')
  } else if (to.meta.requiresGuest && isAuthenticated) {
    next('/products')
  } else {
    next()
  }
})

export default router 