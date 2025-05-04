import axios from 'axios';

const API_URL = 'http://localhost:8000/api';

class AuthService {
    async login(email, password) {
        try {
            const response = await axios.post(`${API_URL}/login`, {
                email,
                password
            });
            if (response.data.access_token) {
                localStorage.setItem('user', JSON.stringify(response.data));
                axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.access_token}`;
            }
            return response.data;
        } catch (error) {
            throw error.response?.data || { message: 'Error during login' };
        }
    }

    async register(name, email, password) {
        try {
            const response = await axios.post(`${API_URL}/register`, {
                name,
                email,
                password
            });
            if (response.data.access_token) {
                localStorage.setItem('user', JSON.stringify(response.data));
                axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.access_token}`;
            }
            return response.data;
        } catch (error) {
            throw error.response?.data || { message: 'Error during registration' };
        }
    }

    logout() {
        localStorage.removeItem('user');
        delete axios.defaults.headers.common['Authorization'];
    }

    getCurrentUser() {
        return JSON.parse(localStorage.getItem('user'));
    }

    isAdmin() {
        const user = this.getCurrentUser();
        return user?.user?.role?.name === 'admin';
    }
}

export default new AuthService(); 