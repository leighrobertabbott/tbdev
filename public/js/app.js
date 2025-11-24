// Alpine.js is loaded from CDN in the layout
// Just ensure it's available
if (typeof Alpine !== 'undefined') {
    window.Alpine = Alpine;
}

// API client
const API = {
    baseURL: '/api',
    
    async request(endpoint, options = {}) {
        const token = localStorage.getItem('auth_token') || this.getCookie('auth_token');
        const headers = {
            'Content-Type': 'application/json',
            ...options.headers,
        };
        
        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }
        
        const response = await fetch(`${this.baseURL}${endpoint}`, {
            ...options,
            headers,
        });
        
        if (!response.ok) {
            const error = await response.json().catch(() => ({ error: 'Request failed' }));
            throw new Error(error.error || 'Request failed');
        }
        
        return response.json();
    },
    
    get(endpoint) {
        return this.request(endpoint, { method: 'GET' });
    },
    
    post(endpoint, data) {
        return this.request(endpoint, {
            method: 'POST',
            body: JSON.stringify(data),
        });
    },
    
    put(endpoint, data) {
        return this.request(endpoint, {
            method: 'PUT',
            body: JSON.stringify(data),
        });
    },
    
    delete(endpoint) {
        return this.request(endpoint, { method: 'DELETE' });
    },
    
    getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    },
};

// Auth functions
const Auth = {
    async login(username, password) {
        const response = await API.post('/auth/login', { username, password });
        if (response.token) {
            localStorage.setItem('auth_token', response.token);
            localStorage.setItem('user', JSON.stringify(response.user));
        }
        return response;
    },
    
    logout() {
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
        API.post('/auth/logout');
        window.location.href = '/';
    },
    
    isAuthenticated() {
        return !!localStorage.getItem('auth_token');
    },
    
    getUser() {
        const user = localStorage.getItem('user');
        return user ? JSON.parse(user) : null;
    },
};

// Make API and Auth globally available
window.API = API;
window.Auth = Auth;

// Notification system
const Notifications = {
    show(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
            type === 'error' ? 'bg-red-500 text-white' :
            type === 'success' ? 'bg-green-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    },
    
    error(message) {
        this.show(message, 'error');
    },
    
    success(message) {
        this.show(message, 'success');
    },
    
    info(message) {
        this.show(message, 'info');
    },
};

window.Notifications = Notifications;

// Format helpers
const Format = {
    bytes(bytes) {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    },
    
    date(timestamp) {
        return new Date(timestamp * 1000).toLocaleDateString();
    },
    
    timeAgo(timestamp) {
        const seconds = Math.floor((new Date() - new Date(timestamp * 1000)) / 1000);
        if (seconds < 60) return 'just now';
        const minutes = Math.floor(seconds / 60);
        if (minutes < 60) return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
        const hours = Math.floor(minutes / 60);
        if (hours < 24) return `${hours} hour${hours > 1 ? 's' : ''} ago`;
        const days = Math.floor(hours / 24);
        if (days < 7) return `${days} day${days > 1 ? 's' : ''} ago`;
        return this.date(timestamp);
    },
};

window.Format = Format;

console.log('TorrentBits 2025 - Modern Frontend Loaded');


