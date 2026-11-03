import api from './api';

export const authService = {
    /**
     * Fetch authenticated user session from backend
     */
    async getCurrentUser() {
        const response = await api.get('/api/me');
        return response.data?.user || response.data;
    },

    /**
     * Gracefully log out via Laravel session endpoint
     */
    async logout() {
        try {
            await api.post('/api/logout');
        } catch (e) {
            console.error('Logout error:', e);
        } finally {
            window.location.href = '/auth';
        }
    },

    /**
     * Check if given user has admin privileges
     */
    isAdmin(user) {
        return user?.role === 'admin';
    },

    /**
     * Format Bangladeshi Taka (৳) currency
     */
    formatCurrency(amount, symbol = '৳') {
        const num = parseFloat(amount || 0);
        return `${symbol} ${num.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        })}`;
    },
};

export default authService;

