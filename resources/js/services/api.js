import axios from 'axios';
import { toaster } from '../components/feedback/toaster';

// Obtain CSRF token from HTML meta tag
export const getCsrfToken = () => {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
};

// Create configured Axios instance
export const api = axios.create({
    baseURL: '/',
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
    },
    withCredentials: true,
});

// Request interceptor to attach current CSRF token
api.interceptors.request.use(
    (config) => {
        const token = getCsrfToken();
        if (token) {
            config.headers['X-CSRF-TOKEN'] = token;
        }
        return config;
    },
    (error) => Promise.reject(error)
);

// Response interceptor for centralized error notifications and session timeouts
api.interceptors.response.use(
    (response) => response,
    (error) => {
        const status = error?.response?.status;
        const message = error?.response?.data?.message || error?.message || 'An unexpected error occurred.';

        if (status === 401) {
            // Session expired or unauthenticated
            toaster.create({
                title: 'Session Expired',
                description: 'Please log in again to continue.',
                type: 'warning',
            });
            setTimeout(() => {
                window.location.href = '/auth';
            }, 1200);
        } else if (status === 403) {
            toaster.create({
                title: 'Access Restricted',
                description: message || 'You do not have permission to perform this action.',
                type: 'error',
            });
        } else if (status === 422) {
            // Validation error
            const errors = error.response.data?.errors;
            const firstError = errors ? Object.values(errors).flat()[0] : message;
            toaster.create({
                title: 'Validation Error',
                description: firstError || message,
                type: 'error',
            });
        } else if (status >= 500) {
            toaster.create({
                title: 'Server Error',
                description: 'Internal server error. Please try again shortly.',
                type: 'error',
            });
        }

        return Promise.reject(error);
    }
);

export default api;

