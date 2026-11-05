import React, { useState, useEffect } from 'react';
import {
    Box,
    Flex,
    Heading,
    Text,
    Button,
    Badge,
    VStack,
    HStack,
    Spinner,
} from '@chakra-ui/react';
import { adminApi } from '../../../services/adminApi';
import { DashboardCard } from '../../../components/common/DashboardCard';

export const UnreadBanner = ({ onNavigate }) => {
    const [unreadDeposits, setUnreadDeposits] = useState([]);

    useEffect(() => {
        adminApi.getDeposits('pending')
            .then((data) => {
                const list = data.deposits || data.data || data;
                if (Array.isArray(list)) {
                    setUnreadDeposits(list.filter((d) => !d.is_read).slice(0, 3));
                }
            })
            .catch(() => {});
    }, []);

    if (unreadDeposits.length === 0) return null;

    return (
        <VStack gap={2} mb={4} align="stretch">
            {unreadDeposits.map((dep) => (
                <Flex
                    key={dep.id}
                    direction={{ base: 'column', md: 'row' }}
                    align={{ base: 'flex-start', md: 'center' }}
                    justify="space-between"
                    p={4}
                    bg="#FFFBEB"
                    borderWidth="1px"
                    borderColor="#FCD34D"
                    borderRadius="xl"
                    shadow="sm"
                    gap={{ base: 3, md: 0 }}
                >
                    <Box>
                        <Flex align="center" gap={2}>
                            <Badge bg="#F59E0B" color="white" fontSize="2xs" px={2} py={0.5} borderRadius="sm" fontWeight="800">
                                PENDING DEPOSIT REVIEW
                            </Badge>
                            <Text fontSize="sm" fontWeight="800" color="#78350F">
                                Deposit #{dep.id} Requires Verification
                            </Text>
                        </Flex>
                        <Text fontSize="xs" color="#92400E" mt={1}>
                            User {dep.user?.email || `ID #${dep.user_id}`} transferred ৳ {Number(dep.amount || 0).toLocaleString()} via {dep.payment_gateway || 'Manual Gateway'}.
                        </Text>
                    </Box>

                    <Button
                        size="xs"
                        bg="#D97706"
                        color="white"
                        _hover={{ bg: '#B45309' }}
                        borderRadius="md"
                        fontWeight="700"
                        onClick={() => {
                            if (onNavigate) onNavigate('Deposits');
                            adminApi.markDepositRead(dep.id).catch(() => {});
                            setUnreadDeposits((prev) => prev.filter((x) => x.id !== dep.id));
                        }}
                    >
                        Review Deposit &rarr;
                    </Button>
                </Flex>
            ))}
        </VStack>
    );
};

export const SystemAlertsModule = ({ onNavigate }) => {
    const [alerts, setAlerts] = useState([
        {
            id: 1,
            title: 'Unsettled Payout Queue',
            message: 'Pending withdrawal clearances awaiting administrative bank disbursement.',
            severity: 'warning',
            category: 'Financial',
            timestamp: 'Just now',
            target: 'Withdrawals',
        },
        {
            id: 2,
            title: 'National Identity Verifications Pending',
            message: 'Client KYC document uploads require photo and identity validation.',
            severity: 'info',
            category: 'Compliance',
            timestamp: '10m ago',
            target: 'Users',
        },
        {
            id: 3,
            title: 'Escrow Dispute Flags',
            message: 'Buyer reported deliverable mismatch on domain transfer contract.',
            severity: 'error',
            category: 'Dispute',
            timestamp: '1h ago',
            target: 'Reports',
        },
    ]);

    const handleDismiss = (id) => {
        setAlerts((prev) => prev.filter((a) => a.id !== id));
    };

    return (
        <VStack gap={6} align="stretch" pb={12}>
            <UnreadBanner onNavigate={onNavigate} />

            <DashboardCard
                title="System Operational Alerts & Event Ribbon"
                subtitle="High-priority platform events requiring human intervention or compliance sign-off"
            >
                {alerts.length === 0 ? (
                    <Box textAlign="center" py={12} color="gray.500">
                        <Text fontSize="lg" fontWeight="700">All Systems Nominal</Text>
                        <Text fontSize="sm" mt={1}>No active administrative alerts or unhandled operational warnings.</Text>
                    </Box>
                ) : (
                    <VStack gap={3} align="stretch">
                        {alerts.map((alert) => (
                            <Flex
                                key={alert.id}
                                justify="space-between"
                                align="center"
                                p={4}
                                borderRadius="xl"
                                borderWidth="1px"
                                borderColor={
                                    alert.severity === 'error'
                                        ? '#FECACA'
                                        : alert.severity === 'warning'
                                        ? '#FDE68A'
                                        : '#BFDBFE'
                                }
                                bg={
                                    alert.severity === 'error'
                                        ? '#FEF2F2'
                                        : alert.severity === 'warning'
                                        ? '#FFFBEB'
                                        : '#EFF6FF'
                                }
                            >
                                <Box>
                                    <HStack gap={2} mb={1}>
                                        <Badge
                                            bg={
                                                alert.severity === 'error'
                                                    ? '#EF4444'
                                                    : alert.severity === 'warning'
                                                    ? '#F59E0B'
                                                    : '#3B82F6'
                                            }
                                            color="white"
                                            fontSize="2xs"
                                            px={1.5}
                                            py={0.5}
                                            borderRadius="sm"
                                            fontWeight="800"
                                        >
                                            {alert.category}
                                        </Badge>
                                        <Text fontWeight="800" fontSize="sm" color="#0A1128">
                                            {alert.title}
                                        </Text>
                                        <Text fontSize="2xs" color="gray.500">
                                            &bull; {alert.timestamp}
                                        </Text>
                                    </HStack>
                                    <Text fontSize="xs" color="gray.700">
                                        {alert.message}
                                    </Text>
                                </Box>

                                <HStack gap={2}>
                                    <Button
                                        size="xs"
                                        bg="#0A1128"
                                        color="white"
                                        onClick={() => onNavigate && onNavigate(alert.target)}
                                        borderRadius="md"
                                    >
                                        Inspect {alert.target}
                                    </Button>
                                    <Button
                                        size="xs"
                                        variant="ghost"
                                        color="gray.500"
                                        onClick={() => handleDismiss(alert.id)}
                                    >
                                        ✕
                                    </Button>
                                </HStack>
                            </Flex>
                        ))}
                    </VStack>
                )}
            </DashboardCard>
        </VStack>
    );
};

export default SystemAlertsModule;
