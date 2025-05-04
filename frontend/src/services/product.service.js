import axios from 'axios';

const API_URL = 'http://localhost:8000/api';

class ProductService {
    async getAllProducts() {
        try {
            const response = await axios.get(`${API_URL}/products`);
            return response.data;
        } catch (error) {
            throw error.response?.data || { message: 'Error fetching products' };
        }
    }

    async getProduct(id) {
        try {
            const response = await axios.get(`${API_URL}/products/${id}`);
            return response.data;
        } catch (error) {
            throw error.response?.data || { message: 'Error fetching product' };
        }
    }

    async createProduct(productData) {
        try {
            const response = await axios.post(`${API_URL}/products`, productData);
            return response.data;
        } catch (error) {
            throw error.response?.data || { message: 'Error creating product' };
        }
    }

    async updateProduct(id, productData) {
        try {
            const response = await axios.put(`${API_URL}/products/${id}`, productData);
            return response.data;
        } catch (error) {
            throw error.response?.data || { message: 'Error updating product' };
        }
    }

    async deleteProduct(id) {
        try {
            const response = await axios.delete(`${API_URL}/products/${id}`);
            return response.data;
        } catch (error) {
            throw error.response?.data || { message: 'Error deleting product' };
        }
    }
}

export default new ProductService(); 