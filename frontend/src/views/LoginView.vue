<template>
  <div class="login-container">
    <div class="login-card">
      <h2 class="login-title">
        Sign in to your account
      </h2>
      <form @submit.prevent="handleLogin">
        <div class="form-group">
          <label for="email-address" class="form-label">Email address</label>
          <input
            id="email-address"
            name="email"
            type="email"
            required
            v-model="email"
            class="form-input"
            :class="{ 'error': errors.email }"
            placeholder="Enter your email"
          />
          <span v-if="errors.email" class="error-message">{{ errors.email[0] }}</span>
        </div>
        <div class="form-group">
          <label for="password" class="form-label">Password</label>
          <input
            id="password"
            name="password"
            type="password"
            required
            v-model="password"
            class="form-input"
            :class="{ 'error': errors.password }"
            placeholder="Enter your password"
          />
          <span v-if="errors.password" class="error-message">{{ errors.password[0] }}</span>
        </div>

        <div v-if="generalError" class="alert alert-error mb-4">
          {{ generalError }}
        </div>

        <button
          type="submit"
          class="btn btn-primary"
          style="width: 100%"
          :disabled="loading"
        >
          {{ loading ? 'Signing in...' : 'Sign in' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const loading = ref(false)
const errors = ref({})
const generalError = ref('')

const handleLogin = async () => {
  loading.value = true
  errors.value = {}
  generalError.value = ''
  
  try {
    const result = await authStore.login(email.value, password.value)
    if (result.success) {
      router.push('/')
    }
  } catch (error) {
    console.error('Login error:', error)
    
    if (error.error) {
      // Handle validation errors
      if (error.error.errors) {
        errors.value = Object.entries(error.error.errors).reduce((acc, [field, messages]) => {
          acc[field] = Array.isArray(messages) ? messages : [messages]
          return acc
        }, {})
      }
      
      // Set general error message
      generalError.value = error.error.message
    } else {
      generalError.value = 'An unexpected error occurred. Please try again.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.error {
  border-color: #ef4444 !important;
  background-color: #fef2f2;
}

.error:focus {
  box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2);
  outline: none;
}

.error-message {
  color: #ef4444;
  font-size: 0.875rem;
  margin-top: 0.25rem;
  display: block;
  animation: fadeIn 0.2s ease-in;
}

.alert {
  padding: 0.75rem 1rem;
  border-radius: 0.375rem;
  margin-bottom: 1rem;
  animation: fadeIn 0.3s ease-in;
}

.alert-error {
  background-color: #fee2e2;
  color: #991b1b;
  border: 1px solid #fecaca;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-5px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.form-group {
  position: relative;
  margin-bottom: 1.5rem;
}

.form-input {
  width: 100%;
  padding: 0.625rem 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  transition: all 0.2s;
}

.form-input:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
}

.form-label {
  display: block;
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
}

.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.625rem 1rem;
  font-size: 0.875rem;
  font-weight: 500;
  border-radius: 0.375rem;
  transition: all 0.2s;
}

.btn-primary {
  background-color: #2563eb;
  color: white;
  border: none;
}

.btn-primary:hover:not(:disabled) {
  background-color: #1d4ed8;
}

.btn-primary:disabled {
  background-color: #93c5fd;
  cursor: not-allowed;
}
</style> 