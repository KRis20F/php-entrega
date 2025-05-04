<template>
  <div class="min-h-screen bg-gradient-to-br from-[#1a1a35] via-[#2d1b69] to-[#1a1a35]">
    <nav class="navbar" v-if="isAuthenticated">
      <div class="navbar-container">
        <div class="navbar-left">
          <router-link to="/products" class="navbar-brand">
            <span class="brand-text">Products App</span>
          </router-link>
        </div>
        
        <div class="navbar-right">
          <div class="user-info">
            <span class="user-name">{{ currentUser?.user?.name }}</span>
            <button
              @click="handleLogout"
              class="logout-button"
            >
              Logout
            </button>
          </div>
        </div>
      </div>
    </nav>

    <main>
      <router-view></router-view>
    </main>
  </div>
</template>

<script>
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import AuthService from './services/auth.service';
import './assets/styles/navbar.css';

export default {
  name: 'App',
  setup() {
    const router = useRouter();

    const isAuthenticated = computed(() => !!AuthService.getCurrentUser());
    const currentUser = computed(() => AuthService.getCurrentUser());

    const handleLogout = () => {
      AuthService.logout();
      router.push('/login');
    };

    return {
      isAuthenticated,
      currentUser,
      handleLogout,
    };
  },
};
</script> 