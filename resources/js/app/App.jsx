import React from 'react';
import { AppProviders } from './providers/AppProviders';
import { UserLayout } from '../layouts/UserLayout';
import { AdminLayout } from '../layouts/AdminLayout';

export const App = ({ portal: propPortal = null, initialUser = null }) => {
    // Determine portal type: prop -> window.APP_CONFIG -> path inspection
    const determinePortal = () => {
        if (propPortal) return propPortal;
        if (typeof window !== 'undefined') {
            if (window.APP_CONFIG?.portal) return window.APP_CONFIG.portal;
            if (window.location.pathname.startsWith('/admin')) return 'admin';
        }
        return 'user';
    };

    const portal = determinePortal();

    return (
        <AppProviders initialUser={initialUser} portal={portal}>
            {portal === 'admin' ? <AdminLayout /> : <UserLayout />}
        </AppProviders>
    );
};

export default App;

