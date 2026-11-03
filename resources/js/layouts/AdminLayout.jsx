import React, { useState, useEffect } from 'react';
import { Box, Heading, Text, Button } from '@chakra-ui/react';
import { DashboardLayout } from './DashboardLayout';
import { AdminDashboardHome } from '../pages/dashboard/admin/DashboardHome';
import { DashboardCard } from '../components/common/DashboardCard';

export const AdminLayout = () => {
    const [activeNav, setActiveNav] = useState(() => {
        if (typeof window !== 'undefined') {
            const pathParts = window.location.pathname.split('/');
            const sub = pathParts[2];
            if (sub) {
                const map = {
                    users: 'Users',
                    deposits: 'Deposits',
                    withdrawals: 'Withdrawals',
                    deals: 'Deals',
                    gateways: 'Gateways',
                    support: 'Support',
                    analytics: 'Analytics',
                };
                return map[sub.toLowerCase()] || 'Dashboard';
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
                if (sub) {
                    setActiveNav(sub.charAt(0).toUpperCase() + sub.slice(1));
                } else {
                    setActiveNav('Dashboard');
                }
            }
        };

        window.addEventListener('popstate', handlePopState);
        return () => window.removeEventListener('popstate', handlePopState);
    }, []);

    const renderContent = () => {
        if (activeNav === 'Dashboard') {
            return <AdminDashboardHome onNavigate={handleNavSelect} />;
        }

        return (
            <DashboardCard
                title={`${activeNav} Management Console`}
                subtitle="Scheduled for full reconstruction in Phase 8."
            >
                <Box p={8} textAlign="center">
                    <Heading size="lg" color="#0A1128" mb={2}>
                        {activeNav} Administrative Queue
                    </Heading>
                    <Text fontSize="sm" color="gray.600" maxW="520px" mx="auto" mb={6}>
                        The administrative authentication barrier, Chakra UI 3 provider, navigation shell, and API services are established.
                        The comprehensive administrative {activeNav.toLowerCase()} control panels will be migrated in <strong>Phase 8 (Admin Dashboard Reconstruction)</strong>.
                    </Text>
                    <Button
                        size="sm"
                        bg="#DC2626"
                        color="white"
                        _hover={{ bg: '#B91C1C' }}
                        onClick={() => handleNavSelect('Dashboard')}
                        borderRadius="lg"
                    >
                        Back to Control Tower
                    </Button>
                </Box>
            </DashboardCard>
        );
    };

    return (
        <DashboardLayout
            portal="admin"
            activeNav={activeNav}
            onNavSelect={handleNavSelect}
        >
            {renderContent()}
        </DashboardLayout>
    );
};

export default AdminLayout;

