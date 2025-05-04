<template>
  <div class="login-wrapper">
    <div class="login-container">
      <div class="login-content">
        <h1 class="login-title">Sign in to your account</h1>
        
        <div class="login-box">
          <form @submit.prevent="handleLogin" class="login-form">
            <div class="form-group">
              <label for="email">Email address</label>
              <input
                id="email"
                v-model="email"
                type="email"
                required
                class="login-input"
                placeholder="Enter your email"
              />
            </div>

            <div class="form-group">
              <label for="password">Password</label>
              <input
                id="password"
                v-model="password"
                type="password"
                required
                class="login-input"
                placeholder="Enter your password"
                autocomplete="current-password"
              />
            </div>

            <button
              type="submit"
              :disabled="loading"
              class="login-button"
            >
              {{ loading ? 'Signing in...' : 'Sign in' }}
            </button>
          </form>

          <div v-if="error" class="error-message">
            {{ error }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import AuthService from '../services/auth.service';
import '../assets/styles/login.css';

export default {
  name: 'Login',
  setup() {
    const router = useRouter();
    const email = ref('');
    const password = ref('');
    const loading = ref(false);
    const error = ref('');

    const handleLogin = async () => {
      loading.value = true;
      error.value = '';

      try {
        await AuthService.login(email.value, password.value);
        router.push('/products');
      } catch (err) {
        error.value = err.message || 'Failed to login';
      } finally {
        loading.value = false;
      }
    };

    return {
      email,
      password,
      loading,
      error,
      handleLogin,
    };
  },
};
</script> 