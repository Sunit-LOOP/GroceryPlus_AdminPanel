// API Configuration
const CONFIG = {
    API_BASE_URL: 'http://localhost/groceryplus/api',
    TOKEN_KEY: 'admin_token',
    USER_KEY: 'admin_user'
};

// API Client
class APIClient {
    constructor() {
        this.baseURL = CONFIG.API_BASE_URL;
    }

    async request(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        const token = localStorage.getItem(CONFIG.TOKEN_KEY);

        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                ...(token && { 'Authorization': `Bearer ${token}` })
            }
        };

        const mergedOptions = { ...defaultOptions, ...options };
        if (mergedOptions.body && typeof mergedOptions.body === 'object') {
            mergedOptions.body = JSON.stringify(mergedOptions.body);
        }

        try {
            const response = await fetch(url, mergedOptions);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || `HTTP error! status: ${response.status}`);
            }

            return data;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }

    // Authentication
    async login(credentials) {
        return this.request('/auth', {
            method: 'POST',
            body: credentials
        });
    }

    // Analytics
    async getAnalytics() {
        return this.request('/analytics');
    }

    // Products
    async getProducts(params = {}) {
        const queryString = new URLSearchParams(params).toString();
        return this.request(`/products?${queryString}`);
    }

    async createProduct(product) {
        return this.request('/products', {
            method: 'POST',
            body: product
        });
    }

    async updateProduct(id, product) {
        return this.request(`/products/${id}`, {
            method: 'PUT',
            body: product
        });
    }

    async deleteProduct(id) {
        return this.request(`/products/${id}`, {
            method: 'DELETE'
        });
    }

    // Categories
    async getCategories() {
        return this.request('/categories');
    }

    async createCategory(category) {
        return this.request('/categories', {
            method: 'POST',
            body: category
        });
    }

    async updateCategory(id, category) {
        return this.request(`/categories/${id}`, {
            method: 'PUT',
            body: category
        });
    }

    async deleteCategory(id) {
        return this.request(`/categories/${id}`, {
            method: 'DELETE'
        });
    }

    // Orders
    async getOrders() {
        return this.request('/orders');
    }

    async updateOrder(id, order) {
        return this.request(`/orders/${id}`, {
            method: 'PUT',
            body: order
        });
    }

    // Users
    async getUsers() {
        return this.request('/users');
    }

    async createUser(user) {
        return this.request('/users', {
            method: 'POST',
            body: user
        });
    }

    async updateUser(id, user) {
        return this.request(`/users/${id}`, {
            method: 'PUT',
            body: user
        });
    }

    async deleteUser(id) {
        return this.request(`/users/${id}`, {
            method: 'DELETE'
        });
    }
}

// Global API instance
const api = new APIClient();