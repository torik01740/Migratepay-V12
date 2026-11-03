import React, { useState, useEffect } from 'react';
import { Box, Flex, VStack, HStack, Text, Badge, Button, Spinner } from '@chakra-ui/react';
import { userProfileApi } from '../../services/dashboardApi';
import { BellIcon, CheckCircleIcon, CloseIcon } from '../common/Icons';
import { toaster } from '../feedback/toaster';

export const NotificationDropdown = ({ isOpen, onClose, onNavigate }) => {
    const [notifications, setNotifications] = useState([]);
    const [loading, setLoading] = useState(false);

    const fetchNotifications = async () => {
        setLoading(true);
        try {
            const data = await userProfileApi.getNotifications();
            if (data.success) {
                setNotifications(data.notifications || []);
            }
        } catch (e) {
            console.error('Failed to fetch notifications:', e);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        if (isOpen) {
            fetchNotifications();
        }
    }, [isOpen]);

    const handleMarkAllRead = async () => {
        try {
            await userProfileApi.markAllNotificationsRead();
            setNotifications(notifications.map((n) => ({ ...n, is_read: true })));
            toaster.create({
                title: 'Notifications Cleared',
                description: 'All notifications marked as read.',
                type: 'info',
            });
        } catch (e) {
            console.error(e);
        }
    };

    if (!isOpen) return null;

    const unreadCount = notifications.filter((n) => !n.is_read).length;

    return (
        <Box
            position="absolute"
            top="56px"
            right={{ base: '-60px', sm: '0px' }}
            w={{ base: '320px', sm: '380px' }}
            bg="white"
            borderRadius="2xl"
            borderWidth="1px"
            borderColor="gray.200"
            shadow="xl"
            zIndex={100}
            overflow="hidden"
        >
            {/* Header */}
            <Flex
                px={4}
                py={3}
                align="center"
                justify="space-between"
                borderBottomWidth="1px"
                borderColor="gray.100"
                bg="gray.50"
            >
                <HStack gap={2}>
                    <Text fontSize="xs" fontWeight="800" textTransform="uppercase" color="#0A1128">
                        Notifications
                    </Text>
                    {unreadCount > 0 && (
                        <Badge colorPalette="red" variant="subtle" fontSize="2xs" px={2} borderRadius="full">
                            {unreadCount} new
                        </Badge>
                    )}
                </HStack>

                <HStack gap={1}>
                    {unreadCount > 0 && (
                        <Button
                            size="2xs"
                            variant="ghost"
                            color="#1769FF"
                            fontSize="2xs"
                            onClick={handleMarkAllRead}
                        >
                            Mark All Read
                        </Button>
                    )}
                    <Button size="2xs" variant="ghost" onClick={onClose} p={1} minW="auto">
                        <CloseIcon size={14} />
                    </Button>
                </HStack>
            </Flex>

            {/* Notifications List */}
            <Box maxH="380px" overflowY="auto">
                {loading ? (
                    <Flex justify="center" align="center" p={8}>
                        <Spinner size="sm" color="#1769FF" />
                    </Flex>
                ) : notifications.length === 0 ? (
                    <Box p={8} textAlign="center">
                        <Flex justify="center" mb={2} color="gray.300">
                            <BellIcon size={28} />
                        </Flex>
                        <Text fontSize="xs" color="gray.500" fontWeight="500">
                            No notifications yet
                        </Text>
                        <Text fontSize="2xs" color="gray.400">
                            Updates on your escrow deals and transactions will appear here.
                        </Text>
                    </Box>
                ) : (
                    <VStack gap={0} align="stretch">
                        {notifications.map((n) => (
                            <Box
                                key={n.id}
                                p={3}
                                borderBottomWidth="1px"
                                borderColor="gray.100"
                                bg={n.is_read ? 'white' : 'blue.50'}
                                transition="all 0.15s"
                                _hover={{ bg: 'gray.50' }}
                                cursor="pointer"
                                onClick={() => {
                                    if (n.data?.deal_id) {
                                        onNavigate?.('Projects');
                                    }
                                    onClose();
                                }}
                            >
                                <Flex justify="space-between" align="flex-start" gap={2}>
                                    <Box flex={1}>
                                        <Text fontSize="xs" fontWeight={n.is_read ? '600' : '800'} color="#0A1128">
                                            {n.title || n.data?.title || 'System Notification'}
                                        </Text>
                                        <Text fontSize="2xs" color="gray.600" mt={0.5}>
                                            {n.message || n.data?.message || 'New update recorded.'}
                                        </Text>
                                        <Text fontSize="3xs" color="gray.400" mt={1}>
                                            {new Date(n.created_at).toLocaleString()}
                                        </Text>
                                    </Box>
                                    {!n.is_read && (
                                        <Box w={2} h={2} borderRadius="full" bg="#1769FF" mt={1} />
                                    )}
                                </Flex>
                            </Box>
                        ))}
                    </VStack>
                )}
            </Box>
        </Box>
    );
};

export default NotificationDropdown;

