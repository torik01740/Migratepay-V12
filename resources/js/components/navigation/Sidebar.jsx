import React from 'react';
import { Box, Flex, VStack, HStack, Text, Badge, Button } from '@chakra-ui/react';
import { useAuth } from '../../hooks/useAuth';
import { UserAvatar } from '../common/UserAvatar';
import {
    DashboardIcon,
    DealsIcon,
    WalletIcon,
    DepositIcon,
    WithdrawalIcon,
    SupportIcon,
    SecurityIcon,
    UsersIcon,
    GatewaysIcon,
    AnalyticsIcon,
    LogoutIcon,
    ExternalLinkIcon,
    CloseIcon,
    PlusIcon,
    ChatIcon,
    ActivityIcon,
    SettingsIcon,
    BellIcon,
    MenuIcon,
} from '../common/Icons';

export const Sidebar = ({
    portal = 'user',
    activeNav = 'Dashboard',
    onNavSelect,
    isOpen = true,
    onClose,
    isMobile = false,
}) => {
    const { user, logout } = useAuth();

    const userNavItems = [
        { id: 'Dashboard', label: 'Command Center', icon: DashboardIcon },
        { id: 'Deals', label: 'Escrow Deals', icon: DealsIcon, badge: 'Active' },
        { id: 'CreateDeal', label: 'Initiate New', icon: PlusIcon },
        { id: 'Deposit', label: 'Add Balance', icon: DepositIcon },
        { id: 'Withdraw', label: 'Withdraw Funds', icon: WithdrawalIcon },
        { id: 'Activity', label: 'Financial Ledger', icon: ActivityIcon },
        { id: 'Messages', label: 'Deal Messages', icon: ChatIcon },
        { id: 'Profile', label: 'Profile & KYC', icon: UsersIcon },
        { id: 'Support', label: 'Support Tickets', icon: SupportIcon },
        { id: 'Security', label: 'Ironclad Security', icon: SecurityIcon },
    ];

    const adminNavItems = [
        // Operations
        { id: 'Dashboard', label: 'Control Tower', icon: DashboardIcon },
        { id: 'Users', label: 'User Directory', icon: UsersIcon },
        { id: 'Deposits', label: 'Deposit Requests', icon: DepositIcon, badge: 'Review' },
        { id: 'Withdrawals', label: 'Withdrawal Approvals', icon: WithdrawalIcon },
        { id: 'Deals', label: 'Disputes & Deals', icon: DealsIcon },
        { id: 'Gateways', label: 'Payment Vault', icon: GatewaysIcon },
        { id: 'Charges', label: 'Commission Tiers', icon: WalletIcon },
        { id: 'Support', label: 'Support Queue', icon: SupportIcon },
        { id: 'Reports', label: 'Dispute Reports', icon: ActivityIcon },
        { id: 'Analytics', label: 'System Analytics', icon: AnalyticsIcon },
        // Content & Marketing
        { id: 'Categories', label: 'Deal Categories', icon: PlusIcon },
        { id: 'Coupons', label: 'Fee Coupons', icon: WalletIcon },
        { id: 'Banners', label: 'Notice Banners', icon: ExternalLinkIcon },
        { id: 'ManagePages', label: 'CMS Static Pages', icon: DealsIcon },
        { id: 'Blog', label: 'Blog Engine', icon: ChatIcon },
        { id: 'Media', label: 'Media Vault', icon: ExternalLinkIcon },
        // Intelligence & Administration
        { id: 'AiLiveChat', label: 'AI Support Assistant', icon: ChatIcon },
        { id: 'ProjectSubmissions', label: 'Project Submissions', icon: DealsIcon },
        { id: 'Announcements', label: 'Broadcast Alerts', icon: BellIcon },
        { id: 'SiteSettings', label: 'Platform Branding', icon: SettingsIcon },
        { id: 'StaffSettings', label: 'Staff Privileges', icon: UsersIcon },
        { id: 'Menus', label: 'Navigation Menus', icon: MenuIcon },
        { id: 'Backup', label: 'Database Backup', icon: SecurityIcon },
        { id: 'Reviews', label: 'Review Moderation', icon: UsersIcon },
        { id: 'KnowledgeBase', label: 'Knowledge Base', icon: SupportIcon },
        { id: 'Ledger', label: 'Master Ledger Audit', icon: ActivityIcon },
        { id: 'Security', label: 'Security Policies', icon: SecurityIcon },
        { id: 'Alerts', label: 'Operational Alerts', icon: ActivityIcon, badge: 'Alerts' },
    ];

    const navItems = portal === 'admin' ? adminNavItems : userNavItems;

    const sidebarContent = (
        <Flex
            direction="column"
            h="100%"
            bg="#0A1128"
            color="white"
            w={isMobile ? '100%' : '260px'}
            p={4}
            justify="space-between"
            borderRightWidth="1px"
            borderColor="whiteAlpha.100"
        >
            <Box display="flex" flexDirection="column" flex={1} overflow="hidden">
                {/* Brand Header */}
                <Flex align="center" justify="space-between" mb={4} px={2} pt={2} flexShrink={0}>
                    <HStack gap={3}>
                        <Flex
                            w={9}
                            h={9}
                            bg="#1769FF"
                            borderRadius="lg"
                            align="center"
                            justify="center"
                            fontWeight="900"
                            fontSize="lg"
                            shadow="sm"
                        >
                            M
                        </Flex>
                        <Box>
                            <Text fontSize="md" fontWeight="800" letterSpacing="-0.02em" lineHeight="1.1">
                                MigratePay
                            </Text>
                            <Badge
                                bg={portal === 'admin' ? '#DC2626' : '#10B981'}
                                color="white"
                                fontSize="2xs"
                                px={1.5}
                                py={0}
                                borderRadius="sm"
                                mt={1}
                            >
                                {portal === 'admin' ? 'ADMIN TOWER' : 'ESCROW SECURED'}
                            </Badge>
                        </Box>
                    </HStack>
                    {isMobile && onClose && (
                        <Button
                            variant="ghost"
                            size="sm"
                            color="white"
                            onClick={onClose}
                            p={1}
                            minW="auto"
                            _hover={{ bg: 'whiteAlpha.200' }}
                        >
                            <CloseIcon size={20} />
                        </Button>
                    )}
                </Flex>

                {/* Navigation Menu */}
                <VStack
                    gap={1}
                    align="stretch"
                    flex={1}
                    overflowY="auto"
                    pr={1}
                    css={{
                        '&::-webkit-scrollbar': { width: '4px' },
                        '&::-webkit-scrollbar-thumb': { background: 'rgba(255,255,255,0.2)', borderRadius: '4px' },
                    }}
                >
                    <Text fontSize="2xs" fontWeight="700" color="whiteAlpha.500" textTransform="uppercase" px={3} py={1} letterSpacing="0.08em">
                        {portal === 'admin' ? 'Management Core' : 'Escrow Operations'}
                    </Text>

                    {navItems.map((item) => {
                        const Icon = item.icon;
                        const isActive = activeNav === item.id;

                        return (
                            <Flex
                                key={item.id}
                                as="button"
                                align="center"
                                justify="space-between"
                                px={3}
                                py={2.5}
                                borderRadius="lg"
                                cursor="pointer"
                                transition="all 0.15s ease-in-out"
                                bg={isActive ? '#1769FF' : 'transparent'}
                                color={isActive ? 'white' : 'whiteAlpha.800'}
                                fontWeight={isActive ? '700' : '500'}
                                fontSize="sm"
                                textAlign="left"
                                w="100%"
                                border="none"
                                _hover={{
                                    bg: isActive ? '#1769FF' : 'whiteAlpha.100',
                                    color: 'white',
                                }}
                                onClick={() => {
                                    if (onNavSelect) onNavSelect(item.id);
                                    if (isMobile && onClose) onClose();
                                }}
                            >
                                <HStack gap={3}>
                                    <Icon size={18} color={isActive ? '#FFFFFF' : '#94A3B8'} />
                                    <Text as="span">{item.label}</Text>
                                </HStack>
                                {item.badge && (
                                    <Badge
                                        bg={isActive ? 'white' : 'whiteAlpha.200'}
                                        color={isActive ? '#1769FF' : 'whiteAlpha.900'}
                                        fontSize="2xs"
                                        px={1.5}
                                        borderRadius="md"
                                    >
                                        {item.badge}
                                    </Badge>
                                )}
                            </Flex>
                        );
                    })}
                </VStack>
            </Box>

            {/* Bottom Section: User Profile & Quick Actions */}
            <Box pt={4} borderTopWidth="1px" borderColor="whiteAlpha.100">
                <Flex
                    align="center"
                    gap={3}
                    p={2}
                    borderRadius="lg"
                    bg="whiteAlpha.50"
                    mb={3}
                >
                    <UserAvatar
                        name={user?.name || user?.username || 'User'}
                        size="sm"
                        isVerified={user?.verification_status === 'VERIFIED'}
                    />
                    <Box flex={1} overflow="hidden">
                        <Text fontSize="xs" fontWeight="700" color="white" truncate>
                            {user?.name || user?.username || 'Authenticated'}
                        </Text>
                        <Text fontSize="2xs" color="whiteAlpha.600" truncate>
                            {user?.email || 'user@migratepay.com'}
                        </Text>
                    </Box>
                </Flex>

                <HStack gap={2}>
                    <Button
                        as="a"
                        href="/"
                        size="xs"
                        variant="ghost"
                        color="whiteAlpha.700"
                        _hover={{ bg: 'whiteAlpha.200', color: 'white' }}
                        flex={1}
                        borderRadius="md"
                    >
                        <ExternalLinkIcon size={14} style={{ marginRight: '6px' }} />
                        Main Site
                    </Button>
                    <Button
                        size="xs"
                        variant="ghost"
                        color="red.300"
                        _hover={{ bg: 'red.900', color: 'red.100' }}
                        onClick={logout}
                        borderRadius="md"
                    >
                        <LogoutIcon size={14} style={{ marginRight: '4px' }} />
                        Logout
                    </Button>
                </HStack>
            </Box>
        </Flex>
    );

    if (isMobile) {
        if (!isOpen) return null;
        return (
            <Box
                position="fixed"
                top={0}
                left={0}
                w="100vw"
                h="100vh"
                zIndex={1000}
                bg="blackAlpha.700"
                onClick={onClose}
            >
                <Box
                    w="280px"
                    h="100%"
                    onClick={(e) => e.stopPropagation()}
                >
                    {sidebarContent}
                </Box>
            </Box>
        );
    }

    return (
        <Box as="nav" h="100vh" position="sticky" top={0} zIndex={50} flexShrink={0}>
            {sidebarContent}
        </Box>
    );
};

export default Sidebar;

