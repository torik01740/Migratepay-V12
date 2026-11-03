import React, { createContext, useContext, useState, useEffect, useCallback } from 'react';
import authService from '../services/auth';

const AuthContext = createContext(null);

export const AuthProvider = ({ children, initialUser = null, portal = 'user' }) => {
    const [user, setUser] = useState(() => {
        if (initialUser) return initialUser;
        if (typeof window !== 'undefined' && window.AUTH_USER) {
            return window.AUTH_USER;
        }
        return null;
    });

    const [loading, setLoading] = useState(!user);

    const refreshUser = useCallback(async () => {
        try {
            setLoading(true);
            const freshUser = await authService.getCurrentUser();
            if (freshUser) {
                setUser(freshUser);
            }
        } catch (err) {
            console.error('Failed to sync authenticated user:', err);
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => {
        if (!user) {
            refreshUser();
        }
    }, [user, refreshUser]);

    const logout = useCallback(() => {
        authService.logout();
    }, []);

    const value = {
        user,
        setUser,
        loading,
        isAdmin: authService.isAdmin(user),
        portal,
        currency: '৳',
        refreshUser,
        logout,
    };

    return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
};

export const useAuth = () => {
    const context = useContext(AuthContext);
    if (!context) {
        throw new Error('useAuth must be used within an AuthProvider');
    }
    return context;
};

export default useAuth;

