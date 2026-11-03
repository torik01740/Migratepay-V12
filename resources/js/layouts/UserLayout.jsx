import React, { useState, useEffect } from 'react';
import { Box } from '@chakra-ui/react';
import { DashboardLayout } from './DashboardLayout';

// Phase 7 User Dashboard Modules
import { DashboardHome } from '../pages/dashboard/user/DashboardHome';
import { ProjectManagementModule } from '../pages/dashboard/user/ProjectManagementModule';
import { CreateDealModule } from '../pages/dashboard/user/CreateDealModule';
import { DepositModule } from '../pages/dashboard/user/DepositModule';
import { WithdrawalModule } from '../pages/dashboard/user/WithdrawalModule';
import { ActivityModule } from '../pages/dashboard/user/ActivityModule';
import { MessagesModule } from '../pages/dashboard/user/MessagesModule';
import { ProfileModule } from '../pages/dashboard/user/ProfileModule';
import { SupportModule } from '../pages/dashboard/user/SupportModule';
import { SecurityModule } from '../pages/dashboard/user/SecurityModule';

const ROUTE_TO_NAV = {
    dashboard: 'Dashboard',
    deals: 'Deals',
    projects: 'Deals',
    'create-deal': 'CreateDeal',
    createdeal: 'CreateDeal',
    initiate: 'CreateDeal',
    deposit: 'Deposit',
    withdraw: 'Withdraw',
    activity: 'Activity',
    ledger: 'Activity',
    messages: 'Messages',
    profile: 'Profile',
    kyc: 'Profile',
    support: 'Support',
    security: 'Security',
};

const NAV_TO_SLUG = {
    Dashboard: '',
    Deals: 'deals',
    CreateDeal: 'create-deal',
    Deposit: 'deposit',
    Withdraw: 'withdraw',
    Activity: 'activity',
    Messages: 'messages',
    Profile: 'profile',
    Support: 'support',
    Security: 'security',
};

export const UserLayout = () => {
    const [activeNav, setActiveNav] = useState(() => {
        if (typeof window !== 'undefined') {
            const pathParts = window.location.pathname.split('/');
            const sub = pathParts[2];
            if (sub && ROUTE_TO_NAV[sub.toLowerCase()]) {
                return ROUTE_TO_NAV[sub.toLowerCase()];
            }
        }
        return 'Dashboard';
    });

    // Sync browser URL history on nav change
    const handleNavSelect = (navId) => {
        setActiveNav(navId);
        const slug = NAV_TO_SLUG[navId] !== undefined ? NAV_TO_SLUG[navId] : navId.toLowerCase();
        const newPath = slug ? `/dashboard/${slug}` : '/dashboard';
        if (window.location.pathname !== newPath) {
            window.history.pushState({ navId }, '', newPath);
        }
    };

    useEffect(() => {
        const handlePopState = (e) => {
            if (e.state && e.state.navId) {
                setActiveNav(e.state.navId);
            } else {
                const pathParts = window.location.pathname.split('/');
                const sub = pathParts[2];
                if (sub && ROUTE_TO_NAV[sub.toLowerCase()]) {
                    setActiveNav(ROUTE_TO_NAV[sub.toLowerCase()]);
                } else {
                    setActiveNav('Dashboard');
                }
            }
        };

        window.addEventListener('popstate', handlePopState);
        return () => window.removeEventListener('popstate', handlePopState);
    }, []);

    const renderContent = () => {
        switch (activeNav) {
            case 'Dashboard':
            case 'Command Center':
                return <DashboardHome onNavigate={handleNavSelect} />;
            case 'Deals':
            case 'Projects':
            case 'Project Management':
                return <ProjectManagementModule onNavigate={handleNavSelect} />;
            case 'CreateDeal':
            case 'Initiate New':
                return <CreateDealModule onNavigate={handleNavSelect} />;
            case 'Deposit':
            case 'Add Balance':
                return <DepositModule onNavigate={handleNavSelect} />;
            case 'Withdraw':
            case 'Withdraw Funds':
                return <WithdrawalModule onNavigate={handleNavSelect} />;
            case 'Activity':
            case 'Financial Ledger':
                return <ActivityModule onNavigate={handleNavSelect} />;
            case 'Messages':
            case 'Deal Messages':
                return <MessagesModule onNavigate={handleNavSelect} />;
            case 'Profile':
            case 'Profile Settings':
            case 'KYC':
                return <ProfileModule onNavigate={handleNavSelect} />;
            case 'Support':
            case 'Support Tickets':
                return <SupportModule onNavigate={handleNavSelect} />;
            case 'Security':
            case 'Ironclad Security':
                return <SecurityModule onNavigate={handleNavSelect} />;
            default:
                return <DashboardHome onNavigate={handleNavSelect} />;
        }
    };

    return (
        <DashboardLayout
            portal="user"
            activeNav={activeNav}
            onNavSelect={handleNavSelect}
        >
            {renderContent()}
        </DashboardLayout>
    );
};

export default UserLayout;
