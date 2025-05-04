<template>
  <div>
    <nav class="navbar">
      <div class="container nav-content">
        <router-link to="/" class="nav-brand">
          Products Management
        </router-link>
        <div class="flex items-center">
          <template v-if="isAuthenticated">
            <span class="text-secondary">{{ user?.name }}</span>
            <button @click="logout" class="btn btn-primary ml-4">
              Logout
            </button>
          </template>
        </div>
      </div>
    </nav>

    <main class="container mt-4">
      <router-view></router-view>
    </main>
  </div>
</template>

<script setup>
import { useAuthStore } from './stores/auth'
import { storeToRefs } from 'pinia'
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const authStore = useAuthStore()
const { user, isAuthenticated } = storeToRefs(authStore)

const logout = async () => {
  await authStore.logout()
  router.push('/login')
}

onMounted(async () => {
  if (!isAuthenticated.value) {
    router.push('/login')
  }
})
</script> 