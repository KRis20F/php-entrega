import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token'),
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    isAdmin: (state) => state.user?.role === 'admin',
  },

  actions: {
    async register(name, email, password) {
      try {
        const response = await axios.post('/api/register', {
          name,
          email,
          password,
        })
        
        this.token = response.data.token
        this.user = response.data.user
        localStorage.setItem('token', this.token)
        
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
        
        return true
      } catch (error) {
        console.error('Registration error:', error)
        return false
      }
    },

    async login(email, password) {
      try {
        const response = await axios.post('/api/login', {
          email,
          password,
        })
        
        this.token = response.data.token
        this.user = response.data.user
        localStorage.setItem('token', this.token)
        
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
        
        return { success: true, data: response.data }
      } catch (error) {
        console.error('Login error:', error)
        
        // Format the error response in a consistent way
        const errorResponse = {
          success: false,
          error: {
            message: 'An unexpected error occurred',
            errors: {},
            status: error.response?.status || 500
          }
        }

        if (error.response) {
          errorResponse.error.status = error.response.status
          
          if (error.response.status === 422) {
            errorResponse.error.message = error.response.data.message || 'Validation failed'
            errorResponse.error.errors = error.response.data.errors || {}
          } else if (error.response.status === 401) {
            errorResponse.error.message = 'Invalid credentials'
          } else if (error.response.status === 429) {
            errorResponse.error.message = 'Too many attempts. Please try again later'
          }
        } else if (error.request) {
          errorResponse.error.message = 'Network error. Please check your connection'
        }
        
        throw errorResponse
      }
    },

    async logout() {
      try {
        await axios.post('/api/logout')
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.token = null
        this.user = null
        localStorage.removeItem('token')
        delete axios.defaults.headers.common['Authorization']
      }
    },

    async checkAuth() {
      if (!this.token) return false
      
      try {
        const response = await axios.get('/api/user')
        this.user = response.data
        return true
      } catch (error) {
        this.token = null
        this.user = null
        localStorage.removeItem('token')
        return false
      }
    },

    setupAxiosInterceptors() {
      // Request interceptor
      axios.interceptors.request.use(
        (config) => {
          const token = this.token
          if (token) {
            config.headers.Authorization = `Bearer ${token}`
          }
          return config
        },
        (error) => {
          return Promise.reject(error)
        }
      )

      // Response interceptor
      axios.interceptors.response.use(
        (response) => response,
        (error) => {
          if (error.response?.status === 401) {
            this.token = null
            this.user = null
            localStorage.removeItem('token')
          }
          return Promise.reject(error)
        }
      )
    }
  },
}) 