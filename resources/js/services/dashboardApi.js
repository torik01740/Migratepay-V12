import api from './api';

export const dealsApi = {
    getAll: () => api.get('/api/deals').then((res) => res.data),
    getById: (id) => api.get(`/api/deals/${id}`).then((res) => res.data),
    create: (data) => api.post('/api/deals', data).then((res) => res.data),
    accept: (id) => api.post(`/api/deals/${id}/accept`).then((res) => res.data),
    decline: (id) => api.post(`/api/deals/${id}/decline`).then((res) => res.data),
    release: (id) => api.post(`/api/deals/${id}/release`).then((res) => res.data),
    submitWork: (id, formData) =>
        api.post(`/api/deals/${id}/submit`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        }).then((res) => res.data),
    reviewSubmission: (id, decision) =>
        api.post(`/api/deals/${id}/review-submission`, { decision }).then((res) => res.data),
    requestExtension: (id, days, reason) =>
        api.post(`/api/deals/${id}/extension`, { days, reason }).then((res) => res.data),
    approveExtension: (id) =>
        api.post(`/api/deals/${id}/approve-extension`).then((res) => res.data),
    reportDispute: (id, reason, description) =>
        api.post(`/api/deals/${id}/report`, { reason, description }).then((res) => res.data),
    sendMessage: (id, text) =>
        api.post(`/api/deals/${id}/message`, { text }).then((res) => res.data),
    submitReview: (id, rating, comment, breakdown = {}) =>
        api.post(`/api/deals/${id}/review`, { rating, comment, ...breakdown }).then((res) => res.data),
    calculateCharge: (params) =>
        api.get('/api/public/calculate-charge', { params }).then((res) => res.data),
    getCategories: () =>
        api.get('/api/categories').then((res) => res.data),
};

export const financeApi = {
    getDeposits: () => api.get('/api/deposits').then((res) => res.data),
    submitDeposit: (data) => api.post('/api/deposits', data).then((res) => res.data),
    uploadScreenshot: (formData) =>
        api.post('/api/deposits/upload-screenshot', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        }).then((res) => res.data),
    getGateways: () => api.get('/api/public/gateway-settings').then((res) => res.data),
    getWithdrawals: () => api.get('/api/withdrawals').then((res) => res.data),
    submitWithdrawal: (data) => api.post('/api/withdrawals', data).then((res) => res.data),
    getWithdrawalMethods: () => api.get('/api/withdrawal-methods').then((res) => res.data),
    saveWithdrawalMethod: (data) => api.post('/api/withdrawal-methods', data).then((res) => res.data),
    deleteWithdrawalMethod: (id) => api.delete(`/api/withdrawal-methods/${id}`).then((res) => res.data),
    getTransactions: (page = 1) => api.get(`/api/transactions?page=${page}`).then((res) => res.data),
};

export const userProfileApi = {
    getMe: () => api.get('/api/me').then((res) => res.data),
    updateProfile: (data) => api.put('/api/profile', data).then((res) => res.data),
    changePassword: (data) => api.post('/api/change-password', data).then((res) => res.data),
    requestVerification: (data) => api.post('/api/user/request-verification', data).then((res) => res.data),
    getNotifications: () => api.get('/api/notifications').then((res) => res.data),
    markNotificationRead: (id) => api.post(`/api/notifications/${id}/read`).then((res) => res.data),
    markAllNotificationsRead: () => api.post('/api/notifications/read-all').then((res) => res.data),
    getUserReviews: (userId) => api.get(`/api/users/${userId}/reviews`).then((res) => res.data),
};

export const supportDeskApi = {
    getTickets: (source = '') =>
        api.get('/api/support-tickets', { params: source ? { source } : {} }).then((res) => res.data),
    createTicket: (formData) =>
        api.post('/api/support-tickets', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        }).then((res) => res.data),
    replyTicket: (id, formData) =>
        api.post(`/api/support-tickets/${id}/reply`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        }).then((res) => res.data),
};

export default {
    dealsApi,
    financeApi,
    userProfileApi,
    supportDeskApi,
};

