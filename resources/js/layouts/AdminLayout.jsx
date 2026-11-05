import React, { useState, useEffect } from 'react';
import { Box } from '@chakra-ui/react';
import { DashboardLayout } from './DashboardLayout';

// Core Reconstructed Modules
import { AdminDashboardHome } from '../pages/dashboard/admin/DashboardHome';
import { UsersManagementModule } from '../pages/dashboard/admin/UsersManagementModule';
import { DepositsManagementModule } from '../pages/dashboard/admin/DepositsManagementModule';
import { WithdrawalsManagementModule } from '../pages/dashboard/admin/WithdrawalsManagementModule';
import { DealsManagementModule } from '../pages/dashboard/admin/DealsManagementModule';
import { PaymentGatewaysModule } from '../pages/dashboard/admin/PaymentGatewaysModule';
import { ChargeManagementModule } from '../pages/dashboard/admin/ChargeManagementModule';
import { SupportTicketsModule } from '../pages/dashboard/admin/SupportTicketsModule';
import { ReportsManagementModule } from '../pages/dashboard/admin/ReportsManagementModule';
import { AnalyticsModule } from '../pages/dashboard/admin/AnalyticsModule';
import { ManageCategoryModule } from '../pages/dashboard/admin/ManageCategoryModule';
import { CouponsModule } from '../pages/dashboard/admin/CouponsModule';
import { BannersModule } from '../pages/dashboard/admin/BannersModule';
import { ManagePagesModule } from '../pages/dashboard/admin/ManagePagesModule';
import { BlogPostsModule } from '../pages/dashboard/admin/BlogPostsModule';
import { MediaModule } from '../pages/dashboard/admin/MediaModule';
import { AiLiveChatModule } from '../pages/dashboard/admin/AiLiveChatModule';
import { ProjectSubmissionModule } from '../pages/dashboard/admin/ProjectSubmissionModule';
import { AnnouncementsModule } from '../pages/dashboard/admin/AnnouncementsModule';
import { SiteSettingsModule } from '../pages/dashboard/admin/SiteSettingsModule';
import { UserSettingsModule } from '../pages/dashboard/admin/UserSettingsModule';
import { MenusModule } from '../pages/dashboard/admin/MenusModule';
import { BackupModule } from '../pages/dashboard/admin/BackupModule';
import { ReviewsModerationModule } from '../pages/dashboard/admin/ReviewsModerationModule';
import { KnowledgeBaseModule } from '../pages/dashboard/admin/KnowledgeBaseModule';
import { LedgerAuditModule } from '../pages/dashboard/admin/LedgerAuditModule';
import { SecurityPoliciesModule } from '../pages/dashboard/admin/SecurityPoliciesModule';
import { SystemAlertsModule, UnreadBanner } from '../pages/dashboard/admin/SystemAlertsModule';

const ROUTE_NAV_MAP = {
    users: 'Users',
    deposits: 'Deposits',
    withdrawals: 'Withdrawals',
    deals: 'Deals',
    gateways: 'Gateways',
    charges: 'Charges',
    support: 'Support',
    reports: 'Reports',
    analytics: 'Analytics',
    categories: 'Categories',
    coupons: 'Coupons',
    banners: 'Banners',
    cms: 'ManagePages',
    pages: 'ManagePages',
    blog: 'Blog',
    media: 'Media',
    aichat: 'AiLiveChat',
    submissions: 'ProjectSubmissions',
    announcements: 'Announcements',
    sitesettings: 'SiteSettings',
    staff: 'StaffSettings',
    usersettings: 'StaffSettings',
    menus: 'Menus',
    backup: 'Backup',
    reviews: 'Reviews',
    kb: 'KnowledgeBase',
    knowledgebase: 'KnowledgeBase',
    ledger: 'Ledger',
    security: 'Security',
    alerts: 'Alerts',
};

export const AdminLayout = () => {
    const [activeNav, setActiveNav] = useState(() => {
        if (typeof window !== 'undefined') {
            const pathParts = window.location.pathname.split('/');
            const sub = pathParts[2];
            if (sub && ROUTE_NAV_MAP[sub.toLowerCase()]) {
                return ROUTE_NAV_MAP[sub.toLowerCase()];
            }
        }
        return 'Dashboard';
    });

    const handleNavSelect = (navId) => {
        setActiveNav(navId);
        const slug = navId === 'Dashboard' ? '' : navId.toLowerCase();
        const newPath = slug ? `/admin/${slug}` : '/admin';
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
                if (sub && ROUTE_NAV_MAP[sub.toLowerCase()]) {
                    setActiveNav(ROUTE_NAV_MAP[sub.toLowerCase()]);
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
                return <AdminDashboardHome onNavigate={handleNavSelect} />;
            case 'Users':
                return <UsersManagementModule />;
            case 'Deposits':
                return <DepositsManagementModule />;
            case 'Withdrawals':
                return <WithdrawalsManagementModule />;
            case 'Deals':
                return <DealsManagementModule />;
            case 'Gateways':
                return <PaymentGatewaysModule />;
            case 'Charges':
                return <ChargeManagementModule />;
            case 'Support':
                return <SupportTicketsModule />;
            case 'Reports':
                return <ReportsManagementModule />;
            case 'Analytics':
                return <AnalyticsModule />;
            case 'Categories':
                return <ManageCategoryModule />;
            case 'Coupons':
                return <CouponsModule />;
            case 'Banners':
                return <BannersModule />;
            case 'ManagePages':
                return <ManagePagesModule />;
            case 'Blog':
                return <BlogPostsModule />;
            case 'Media':
                return <MediaModule />;
            case 'AiLiveChat':
                return <AiLiveChatModule />;
            case 'ProjectSubmissions':
                return <ProjectSubmissionModule />;
            case 'Announcements':
                return <AnnouncementsModule />;
            case 'SiteSettings':
                return <SiteSettingsModule />;
            case 'StaffSettings':
                return <UserSettingsModule />;
            case 'Menus':
                return <MenusModule />;
            case 'Backup':
                return <BackupModule />;
            case 'Reviews':
                return <ReviewsModerationModule />;
            case 'KnowledgeBase':
                return <KnowledgeBaseModule />;
            case 'Ledger':
                return <LedgerAuditModule />;
            case 'Security':
                return <SecurityPoliciesModule />;
            case 'Alerts':
                return <SystemAlertsModule onNavigate={handleNavSelect} />;
            default:
                return <AdminDashboardHome onNavigate={handleNavSelect} />;
        }
    };

    return (
        <DashboardLayout
            portal="admin"
            activeNav={activeNav}
            onNavSelect={handleNavSelect}
        >
            <Box>
                {activeNav !== 'Alerts' && activeNav !== 'Deposits' && (
                    <UnreadBanner onNavigate={handleNavSelect} />
                )}
                {renderContent()}
            </Box>
        </DashboardLayout>
    );
};

export default AdminLayout;