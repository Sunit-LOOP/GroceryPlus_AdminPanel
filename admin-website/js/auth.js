// Authentication handling
class Auth {
    constructor() {
        this.checkAuth();
    }

    checkAuth() {
        const token = localStorage.getItem(CONFIG.TOKEN_KEY);
        const user = localStorage.getItem(CONFIG.USER_KEY);

        if (token && user) {
            // Verify token is still valid
            this.verifyToken(token).then(valid => {
                if (valid) {
                    this.showApp();
                } else {
                    this.logout();
                }
            }).catch(() => {
                this.logout();
            });
        } else {
            this.showLogin();
        }
    }

    async login(email, password) {
        try {
            const response = await api.login({ email, password });
            if (response.success && response.data.user.type === 'admin') {
                const { token, user } = response.data;
                localStorage.setItem(CONFIG.TOKEN_KEY, token);
                localStorage.setItem(CONFIG.USER_KEY, JSON.stringify(user));
                this.showApp();
                return { success: true };
            } else {
                return { success: false, error: 'Invalid admin credentials' };
            }
        } catch (error) {
            return { success: false, error: error.message };
        }
    }

    async verifyToken(token) {
        try {
            // Try to make a simple API call to verify token
            await api.getAnalytics();
            return true;
        } catch (error) {
            return false;
        }
    }

    logout() {
        localStorage.removeItem(CONFIG.TOKEN_KEY);
        localStorage.removeItem(CONFIG.USER_KEY);
        this.showLogin();
    }

    showLogin() {
        document.getElementById('loginModal').classList.remove('d-none');
        document.getElementById('mainApp').classList.add('d-none');
    }

    showApp() {
        document.getElementById('loginModal').classList.add('d-none');
        document.getElementById('mainApp').classList.remove('d-none');
        // Initialize dashboard
        loadDashboard();
    }
}

// Initialize auth
const auth = new Auth();

// Login form handler
document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const email = document.getElementById('loginEmail').value;
    const password = document.getElementById('loginPassword').value;
    const errorDiv = document.getElementById('loginError');

    // Clear previous error
    errorDiv.classList.add('d-none');

    // Show loading state
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';
    submitBtn.disabled = true;

    const result = await auth.login(email, password);

    submitBtn.innerHTML = originalText;
    submitBtn.disabled = false;

    if (!result.success) {
        errorDiv.textContent = result.error;
        errorDiv.classList.remove('d-none');
    }
});

// Logout function (global)
function logout() {
    auth.logout();
}