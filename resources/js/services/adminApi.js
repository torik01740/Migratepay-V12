import api from './api';

export const adminApi = {
    // 1. User Management & KYC Verification
    async getUsers() {
        const response = await api.get('/api/admin/users');
        return response.data;
    },
    async updateUserStatus(userId, status) {
        const response = await api.put(`/api/admin/users/${userId}/status`, { status });
        return response.data;
    },
    async updateUserVerification(userId, verificationStatus, note = '') {
        const response = await api.put(`/api/admin/users/${userId}/verification`, {
            verification_status: verificationStatus,
            rejection_reason: note,
        });
        return response.data;
    },

    // 2. Deposits Management
    async getDeposits(status = '') {
        const response = await api.get('/api/admin/deposits', {
            params: status ? { status } : {},
        });
        return response.data;
    },
    async approveDeposit(depositId, adminNote = '') {
        const response = await api.post(`/api/admin/deposits/${depositId}/approve`, {
            admin_note: adminNote,
        });
        return response.data;
    },
    async rejectDeposit(depositId, adminNote = '') {
        const response = await api.post(`/api/admin/deposits/${depositId}/reject`, {
            admin_note: adminNote,
        });
        return response.data;
    },
    async markDepositRead(depositId) {
        const response = await api.post(`/api/admin/deposits/${depositId}/read`);
        return response.data;
    },

    // 3. Withdrawals Management
    async getWithdrawals(status = '') {
        const response = await api.get('/api/admin/withdrawals', {
            params: status ? { status } : {},
        });
        return response.data;
    },
    async approveWithdrawal(withdrawalId, adminNote = '') {
        const response = await api.post(`/api/admin/withdrawals/${withdrawalId}/approve`, {
            admin_note: adminNote,
        });
        return response.data;
    },
    async rejectWithdrawal(withdrawalId, adminNote = '') {
        const response = await api.post(`/api/admin/withdrawals/${withdrawalId}/reject`, {
            admin_note: adminNote,
        });
        return response.data;
    },

    // 4. Deals & Escrow Disbursal
    async getDeals(status = '') {
        const response = await api.get('/api/admin/deals', {
            params: status ? { status } : {},
        });
        return response.data;
    },
    async approveDealFunds(dealId) {
        const response = await api.post(`/api/admin/deals/${dealId}/approve-funds`);
        return response.data;
    },

    // 5. Reports & Dispute Resolution
    async getReports(status = '') {
        const response = await api.get('/api/admin/reports', {
            params: status ? { status } : {},
        });
        return response.data;
    },
    async resolveReport(reportId, resolutionNote) {
        const response = await api.post(`/api/admin/reports/${reportId}/resolve`, {
            resolution_note: resolutionNote,
        });
        return response.data;
    },

    // 6. Payment Gateways Configuration
    async getGatewaySettings() {
        const response = await api.get('/api/admin/gateway-settings');
        return response.data;
    },
    async updateGatewaySettings(gatewaySlug, settings) {
        const response = await api.post(`/api/admin/gateway-settings/${gatewaySlug}`, settings);
        return response.data;
    },

    // 7. Support Tickets Desk
    async getTickets() {
        const response = await api.get('/api/support-tickets');
        return response.data;
    },
    async replyTicket(ticketId, formData) {
        const response = await api.post(`/api/admin/support-tickets/${ticketId}/reply`, formData);
        return response.data;
    },
    async closeTicket(ticketId) {
        const response = await api.post(`/api/admin/support-tickets/${ticketId}/close`);
        return response.data;
    },

    // 8. Categories CRUD
    async getCategories() {
        const response = await api.get('/api/admin/categories');
        return response.data;
    },
    async createCategory(payload) {
        const response = await api.post('/api/admin/categories', payload);
        return response.data;
    },
    async updateCategory(categoryId, payload) {
        const response = await api.put(`/api/admin/categories/${categoryId}`, payload);
        return response.data;
    },
    async deleteCategory(categoryId) {
        const response = await api.delete(`/api/admin/categories/${categoryId}`);
        return response.data;
    },

    // 9. Coupons CRUD
    async getCoupons() {
        const response = await api.get('/api/admin/coupons');
        return response.data;
    },
    async createCoupon(payload) {
        const response = await api.post('/api/admin/coupons', payload);
        return response.data;
    },
    async updateCoupon(couponId, payload) {
        const response = await api.put(`/api/admin/coupons/${couponId}`, payload);
        return response.data;
    },
    async deleteCoupon(couponId) {
        const response = await api.delete(`/api/admin/coupons/${couponId}`);
        return response.data;
    },

    // 10. Banners Management
    async getBanners() {
        const response = await api.get('/api/admin/banners');
        return response.data;
    },
    async createBanner(payload) {
        const response = await api.post('/api/admin/banners', payload);
        return response.data;
    },
    async updateBanner(bannerId, payload) {
        const response = await api.put(`/api/admin/banners/${bannerId}`, payload);
        return response.data;
    },
    async deleteBanner(bannerId) {
        const response = await api.delete(`/api/admin/banners/${bannerId}`);
        return response.data;
    },

    // 11. CMS Pages Management
    async getCmsPages() {
        const response = await api.get('/api/admin/cms');
        return response.data;
    },
    async saveCmsPage(payload, id = null) {
        if (id) {
            const response = await api.put(`/api/admin/cms/${id}`, payload);
            return response.data;
        }
        const response = await api.post('/api/admin/cms', payload);
        return response.data;
    },
    async deleteCmsPage(id) {
        const response = await api.delete(`/api/admin/cms/${id}`);
        return response.data;
    },

    // 12. Blog Engine
    async getBlogPosts() {
        const response = await api.get('/api/admin/blog');
        return response.data;
    },
    async saveBlogPost(payload, id = null) {
        if (id) {
            const response = await api.put(`/api/admin/blog/${id}`, payload);
            return response.data;
        }
        const response = await api.post('/api/admin/blog', payload);
        return response.data;
    },
    async deleteBlogPost(id) {
        const response = await api.delete(`/api/admin/blog/${id}`);
        return response.data;
    },

    // 13. System Analytics & Ledger
    async getAnalyticsDashboard() {
        const response = await api.get('/api/admin/analytics/dashboard');
        return response.data;
    },
    async getLedger() {
        const response = await api.get('/api/admin/reports/ledger');
        return response.data;
    },

    // 14. AI Assistant & Knowledge Base
    async getAiSettings() {
        const response = await api.get('/api/admin/ai-settings');
        return response.data;
    },
    async saveAiSettings(settings) {
        const response = await api.post('/api/admin/ai-settings', settings);
        return response.data;
    },
    async getAiChatHistory() {
        const response = await api.get('/api/admin/ai-chat-history');
        return response.data;
    },
    async getKnowledgeBase() {
        const response = await api.get('/api/admin/knowledge-base');
        return response.data;
    },
    async saveKnowledgeBase(payload, id = null) {
        if (id) {
            const response = await api.put(`/api/admin/knowledge-base/${id}`, payload);
            return response.data;
        }
        const response = await api.post('/api/admin/knowledge-base', payload);
        return response.data;
    },
    async deleteKnowledgeBase(id) {
        const response = await api.delete(`/api/admin/knowledge-base/${id}`);
        return response.data;
    },

    // 15. Reviews
    async getReviews() {
        const response = await api.get('/api/admin/reviews');
        return response.data;
    },
    async deleteReview(id) {
        const response = await api.delete(`/api/admin/reviews/${id}`);
        return response.data;
    },

    // 16. Broadcast Notification / Announcements
    async sendBroadcastAnnouncement(payload) {
        const response = await api.post('/api/notifications', payload);
        return response.data;
    },

    // 17. Security Governance & Policies
    async getSecurityPolicies() {
        const response = await api.get('/api/admin/security/policies');
        return response.data;
    },
    async updateSecurityPolicies(payload) {
        const response = await api.post('/api/admin/security/policies', payload);
        return response.data;
    },
};

export default adminApi;

