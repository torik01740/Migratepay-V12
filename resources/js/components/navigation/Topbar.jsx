import React, { useState } from 'react';
import { Box, Flex, HStack, Text, Button, Badge } from '@chakra-ui/react';
import { useAuth } from '../../hooks/useAuth';
import { UserAvatar } from '../common/UserAvatar';
import authService from '../../services/auth';
import { MenuIcon, BellIcon, WalletIcon } from '../common/Icons';
import { NotificationDropdown } from './NotificationDropdown';

export const Topbar = ({
    portal = 'user',
    onMenuToggle,
    activeNav = 'Dashboard',
    onNavSelect,
}) => {
    const { user, currency } = useAuth();
    const [showNotifications, setShowNotifications] = useState(false);

    return (
        <Flex
            as="header"
            h="64px"
            bg="white"
            borderBottomWidth="1px"
            borderColor="gray.200"
            px={{ base: 4, md: 8 }}
            align="center"
            justify="space-between"
            position="sticky"
            top={0}
            zIndex={40}
        >
            {/* Left: Hamburger (mobile) + Active View Label */}
            <HStack gap={3}>
                <Button
                    variant="ghost"
                    size="sm"
                    display={{ base: 'inline-flex', md: 'none' }}
                    onClick={onMenuToggle}
                    p={2}
                    minW="auto"
                >
                    <MenuIcon size={20} />
                </Button>

                <HStack gap={2}>
                    <Text fontSize="md" fontWeight="800" color="#0A1128">
                        {activeNav}
                    </Text>
                    <Badge
                        colorPalette={portal === 'admin' ? 'red' : 'blue'}
                        variant="subtle"
                        fontSize="2xs"
                        px={2}
                        py={0.5}
                        borderRadius="md"
                        display={{ base: 'none', sm: 'inline-block' }}
                    >
                        {portal === 'admin' ? 'Admin Gateway' : 'User Portal'}
                    </Badge>
                </HStack>
            </HStack>

            {/* Right: Balance Pill, Notifications, User Profile */}
            <HStack gap={{ base: 2, md: 4 }}>
                {portal === 'user' ? (
                    <Flex
                        align="center"
                        gap={2}
                        bg="#F8FAFC"
                        borderWidth="1px"
                        borderColor="gray.200"
                        px={3}
                        py={1.5}
                        borderRadius="xl"
                    >
                        <WalletIcon size={16} color="#006A4E" />
                        <Box>
                            <Text fontSize="2xs" color="gray.500" fontWeight="600" lineHeight="1">
                                BALANCE
                            </Text>
                            <Text fontSize="xs" fontWeight="800" color="#006A4E" lineHeight="1.2">
                                {authService.formatCurrency(user?.wallet_balance || 0, currency)}
                            </Text>
                        </Box>
                    </Flex>
                ) : (
                    <Badge
                        colorPalette="purple"
                        variant="subtle"
                        fontSize="xs"
                        px={2.5}
                        py={1}
                        borderRadius="md"
                    >
                        Shield Active
                    </Badge>
                )}

                {/* Notifications Button & Dropdown */}
                <Box position="relative">
                    <Button
                        variant="ghost"
                        size="sm"
                        borderRadius="full"
                        w={9}
                        h={9}
                        p={0}
                        position="relative"
                        color={showNotifications ? '#1769FF' : 'gray.600'}
                        bg={showNotifications ? 'blue.50' : 'transparent'}
                        _hover={{ bg: 'gray.100' }}
                        aria-label="Notifications"
                        onClick={() => setShowNotifications(!showNotifications)}
                    >
                        <BellIcon size={18} />
                        <Box
                            position="absolute"
                            top="8px"
                            right="8px"
                            w={2}
                            h={2}
                            borderRadius="full"
                            bg="#1769FF"
                        />
                    </Button>
                    <NotificationDropdown
                        isOpen={showNotifications}
                        onClose={() => setShowNotifications(false)}
                        onNavigate={(nav) => {
                            setShowNotifications(false);
                            onNavSelect?.(nav);
                        }}
                    />
                </Box>

                {/* Profile Pill */}
                <HStack gap={2} pl={2} borderLeftWidth="1px" borderColor="gray.200">
                    <UserAvatar
                        name={user?.name || user?.username || 'User'}
                        size="sm"
                        isVerified={user?.verification_status === 'VERIFIED'}
                    />
                    <Box display={{ base: 'none', lg: 'block' }}>
                        <Text fontSize="xs" fontWeight="700" color="#0A1128" lineHeight="1.1">
                            {user?.name || user?.username || 'User'}
                        </Text>
                        <Text fontSize="2xs" color="gray.500">
                            {user?.role === 'admin' ? 'Administrator' : 'Verified Counterparty'}
                        </Text>
                    </Box>
                </HStack>
            </HStack>
        </Flex>
    );
};

export default Topbar;

