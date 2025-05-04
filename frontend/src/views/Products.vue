<template>
  <div class="products-wrapper">
    <div class="products-container">
      <div class="products-header">
        <h1 class="products-title">Products</h1>
        <div class="header-actions">
          <button 
            @click="isAdmin ? showCreateModal = true : null"
            :class="['add-button', { 'disabled-button': !isAdmin }]"
            :title="!isAdmin ? 'No tienes permisos para añadir productos' : ''"
          >
            <span class="button-icon">+</span>
            Añadir Producto
            <span v-if="!isAdmin" class="lock-icon">🔒</span>
          </button>
        </div>
      </div>

      <!-- Products Table -->
      <div class="table-container">
        <table class="products-table">
          <thead>
            <tr>
              <th @click="sortBy('name')" class="sortable-header">
                Name
                <span v-if="sortField === 'name'" class="sort-indicator">
                  {{ sortOrder === 'asc' ? '↑' : '↓' }}
                </span>
              </th>
              <th>Description</th>
              <th @click="sortBy('price')" class="sortable-header">
                Price
                <span v-if="sortField === 'price'" class="sort-indicator">
                  {{ sortOrder === 'asc' ? '↑' : '↓' }}
                </span>
              </th>
              <th>Stock</th>
              <th v-if="isAdmin" class="actions-header">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in sortedProducts" :key="product.id" class="product-row">
              <td>{{ product.name }}</td>
              <td>{{ product.description }}</td>
              <td>${{ Number(product.price).toFixed(2) }}</td>
              <td>{{ product.stock }}</td>
              <td v-if="isAdmin" class="actions-cell">
                <button @click="editProduct(product)" class="action-button edit">
                  Edit
                </button>
                <button @click="confirmDelete(product)" class="action-button delete">
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Create/Edit Modal -->
      <div v-if="showCreateModal || showEditModal" class="modal-overlay">
        <div class="modal-content">
          <div class="modal-header">
            <h2 class="modal-title">{{ showEditModal ? 'Edit Product' : 'Create Product' }}</h2>
            <button @click="closeModal" class="close-button">&times;</button>
          </div>
          
          <form @submit.prevent="handleSubmit" class="product-form">
            <div class="form-group">
              <label for="name">Name</label>
              <input
                type="text"
                id="name"
                v-model="formData.name"
                required
                class="form-input"
                placeholder="Enter product name"
              />
            </div>

            <div class="form-group">
              <label for="description">Description</label>
              <textarea
                id="description"
                v-model="formData.description"
                required
                class="form-input"
                rows="3"
                placeholder="Enter product description"
              ></textarea>
            </div>

            <div class="form-group">
              <label for="price">Price</label>
              <input
                type="number"
                id="price"
                v-model="formData.price"
                step="0.01"
                required
                class="form-input"
                placeholder="Enter price"
              />
            </div>

            <div class="form-group">
              <label for="stock">Stock</label>
              <input
                type="number"
                id="stock"
                v-model="formData.stock"
                required
                class="form-input"
                placeholder="Enter stock quantity"
              />
            </div>

            <div class="modal-actions">
              <button type="button" @click="closeModal" class="cancel-button">
                Cancel
              </button>
              <button type="submit" class="submit-button">
                {{ showEditModal ? 'Update' : 'Create' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Delete Confirmation Modal -->
      <div v-if="showDeleteModal" class="modal-overlay">
        <div class="modal-content delete-modal">
          <div class="modal-header">
            <h2 class="modal-title">Confirm Delete</h2>
            <button @click="showDeleteModal = false" class="close-button">&times;</button>
          </div>
          
          <div class="delete-message">
            Are you sure you want to delete this product?
            <strong>{{ selectedProduct?.name }}</strong>
          </div>

          <div class="modal-actions">
            <button @click="showDeleteModal = false" class="cancel-button">
              Cancel
            </button>
            <button @click="deleteProduct" class="delete-button">
              Delete
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import ProductService from '../services/product.service';
import AuthService from '../services/auth.service';
import '../assets/styles/products.css';

export default {
  name: 'Products',
  setup() {
    const products = ref([]);
    const showCreateModal = ref(false);
    const showEditModal = ref(false);
    const showDeleteModal = ref(false);
    const sortField = ref('name');
    const sortOrder = ref('asc');
    const selectedProduct = ref(null);
    const formData = ref({
      name: '',
      description: '',
      price: 0,
      stock: 0
    });

    const isAdmin = computed(() => AuthService.isAdmin());

    const sortedProducts = computed(() => {
      return [...products.value].sort((a, b) => {
        const aValue = a[sortField.value];
        const bValue = b[sortField.value];
        
        if (sortField.value === 'price') {
          return sortOrder.value === 'asc' 
            ? parseFloat(aValue) - parseFloat(bValue)
            : parseFloat(bValue) - parseFloat(aValue);
        }
        
        return sortOrder.value === 'asc'
          ? String(aValue).localeCompare(String(bValue))
          : String(bValue).localeCompare(String(aValue));
      });
    });

    const loadProducts = async () => {
      try {
        const response = await ProductService.getAllProducts();
        products.value = response.map(product => ({
          ...product,
          price: Number(product.price),
          stock: Number(product.stock)
        }));
      } catch (error) {
        console.error('Error loading products:', error);
      }
    };

    const sortBy = (field) => {
      if (sortField.value === field) {
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
      } else {
        sortField.value = field;
        sortOrder.value = 'asc';
      }
    };

    const editProduct = (product) => {
      selectedProduct.value = product;
      formData.value = { ...product };
      showEditModal.value = true;
    };

    const confirmDelete = (product) => {
      selectedProduct.value = product;
      showDeleteModal.value = true;
    };

    const deleteProduct = async () => {
      try {
        await ProductService.deleteProduct(selectedProduct.value.id);
        await loadProducts();
        showDeleteModal.value = false;
        selectedProduct.value = null;
      } catch (error) {
        console.error('Error deleting product:', error);
      }
    };

    const handleSubmit = async () => {
      try {
        const productData = {
          ...formData.value,
          price: Number(formData.value.price),
          stock: Number(formData.value.stock)
        };

        if (showEditModal.value) {
          await ProductService.updateProduct(selectedProduct.value.id, productData);
        } else {
          await ProductService.createProduct(productData);
        }
        await loadProducts();
        closeModal();
      } catch (error) {
        console.error('Error saving product:', error);
      }
    };

    const closeModal = () => {
      showCreateModal.value = false;
      showEditModal.value = false;
      selectedProduct.value = null;
      formData.value = {
        name: '',
        description: '',
        price: 0,
        stock: 0
      };
    };

    onMounted(loadProducts);

    return {
      products,
      sortedProducts,
      showCreateModal,
      showEditModal,
      showDeleteModal,
      formData,
      selectedProduct,
      isAdmin,
      sortField,
      sortOrder,
      sortBy,
      editProduct,
      confirmDelete,
      deleteProduct,
      handleSubmit,
      closeModal
    };
  }
};
</script> 