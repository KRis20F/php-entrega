<template>
  <div>
    <div class="flex justify-between items-center mb-4">
      <h1 class="text-2xl font-semibold">Products</h1>
      <button
        v-if="isAdmin"
        @click="showCreateModal = true"
        class="btn btn-primary"
      >
        Add Product
      </button>
    </div>

    <div class="table-container">
      <table class="table">
        <thead>
          <tr>
            <th @click="sortBy('name')">
              Name
              <span v-if="sortField === 'name'">
                {{ sortOrder === 'asc' ? '↑' : '↓' }}
              </span>
            </th>
            <th @click="sortBy('price')">
              Price
              <span v-if="sortField === 'price'">
                {{ sortOrder === 'asc' ? '↑' : '↓' }}
              </span>
            </th>
            <th>Description</th>
            <th v-if="isAdmin">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in sortedProducts" :key="product.id">
            <td>{{ product.name }}</td>
            <td>${{ product.price }}</td>
            <td>{{ product.description }}</td>
            <td v-if="isAdmin" class="flex gap-2 justify-end">
              <button
                @click="editProduct(product)"
                class="btn btn-primary"
              >
                Edit
              </button>
              <button
                @click="deleteProduct(product)"
                class="btn btn-danger"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showCreateModal || showEditModal" class="modal-overlay">
      <div class="modal">
        <div class="modal-header">
          <h3 class="modal-title">
            {{ showEditModal ? 'Edit Product' : 'Create Product' }}
          </h3>
        </div>
        <form @submit.prevent="handleSubmit">
          <div class="form-group">
            <label class="form-label">Name</label>
            <input
              type="text"
              v-model="formData.name"
              class="form-input"
              required
            />
          </div>
          <div class="form-group">
            <label class="form-label">Price</label>
            <input
              type="number"
              step="0.01"
              v-model="formData.price"
              class="form-input"
              required
            />
          </div>
          <div class="form-group">
            <label class="form-label">Description</label>
            <textarea
              v-model="formData.description"
              class="form-input"
              rows="3"
              required
            ></textarea>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              @click="closeModal"
              class="btn btn-secondary"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="btn btn-primary"
              :disabled="loading"
            >
              {{ showEditModal ? 'Update' : 'Create' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'

const router = useRouter()
const authStore = useAuthStore()
const isAdmin = computed(() => authStore.isAdmin)

const products = ref([])
const sortField = ref('name')
const sortOrder = ref('asc')
const showCreateModal = ref(false)
const showEditModal = ref(false)
const loading = ref(false)
const formData = ref({
  name: '',
  price: '',
  description: ''
})

const sortedProducts = computed(() => {
  return [...products.value].sort((a, b) => {
    const aValue = a[sortField.value]
    const bValue = b[sortField.value]
    
    if (sortField.value === 'price') {
      return sortOrder.value === 'asc'
        ? parseFloat(aValue) - parseFloat(bValue)
        : parseFloat(bValue) - parseFloat(aValue)
    }
    
    return sortOrder.value === 'asc'
      ? aValue.localeCompare(bValue)
      : bValue.localeCompare(aValue)
  })
})

const fetchProducts = async () => {
  try {
    loading.value = true
    const response = await axios.get('/api/products')
    products.value = response.data
  } catch (error) {
    console.error('Error fetching products:', error)
    if (error.response?.status === 401) {
      router.push('/login')
    }
  } finally {
    loading.value = false
  }
}

const sortBy = (field) => {
  if (sortField.value === field) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortField.value = field
    sortOrder.value = 'asc'
  }
}

const closeModal = () => {
  showCreateModal.value = false
  showEditModal.value = false
  formData.value = {
    name: '',
    price: '',
    description: ''
  }
}

const editProduct = (product) => {
  formData.value = { ...product }
  showEditModal.value = true
}

const deleteProduct = async (product) => {
  if (!confirm('Are you sure you want to delete this product?')) return
  
  try {
    loading.value = true
    await axios.delete(`/api/products/${product.id}`)
    await fetchProducts()
  } catch (error) {
    console.error('Error deleting product:', error)
    if (error.response?.status === 403) {
      alert('You do not have permission to delete products')
    } else {
      alert('Failed to delete product')
    }
  } finally {
    loading.value = false
  }
}

const handleSubmit = async () => {
  try {
    loading.value = true
    if (showEditModal.value) {
      await axios.put(`/api/products/${formData.value.id}`, formData.value)
    } else {
      await axios.post('/api/products', formData.value)
    }
    await fetchProducts()
    closeModal()
  } catch (error) {
    console.error('Error saving product:', error)
    if (error.response?.status === 403) {
      alert('You do not have permission to modify products')
    } else if (error.response?.status === 422) {
      alert('Please check the form data and try again')
    } else {
      alert('Failed to save product')
    }
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchProducts()
})
</script> 