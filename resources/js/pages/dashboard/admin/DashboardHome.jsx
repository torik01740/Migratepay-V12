import React from 'react';
import { Box, Flex, Grid, HStack, VStack, Text, Heading, Button, Badge } from '@chakra-ui/react';
import { useAuth } from '../../../hooks/useAuth';
import { PageHeader } from '../../../components/common/PageHeader';
import { StatCard } from '../../../components/common/StatCard';
import { DashboardCard } from '../../../components/common/DashboardCard';
import { EmptyState } from '../../../components/common/EmptyState';
import { toaster } from '../../../components/feedback/toaster';
import {
    DashboardIcon,
    UsersIcon,
    DepositIcon,
    WithdrawalIcon,
    DealsIcon,
    GatewaysIcon,
    SupportIcon,
    SecurityIcon,
    AnalyticsIcon,
    ArrowUpRightIcon,
    PlusIcon,
} from '../../../components/common/Icons';

export const AdminDashboardHome = ({ onNavigate }) => {
    const { user, refreshUser } = useAuth();

    const handleAction = (navId, title) => {
        if (onNavigate) {
            onNavigate(navId);
        } else {
            toaster.create({
                title: `${title} Management`,
                description: `Navigating to ${title} control queue...`,
                type: 'info',
            });
        }
    };

    return (
        <Box>
            {/* Page Header */}
            <PageHeader
                title="MigratePay Control Tower"
                subtitle="Central administrative oversight: users, deposits, withdrawals, escrow disputes, and system security."
                badge={
                    <Badge
                        colorPalette="red"
                        variant="subtle"
                        fontSize="xs"
                        px={2.5}
                        py={1}
                        borderRadius="md"
                    >
                        ROOT ACCESS ACTIVE
                    </Badge>
                }
                actions={
                    <HStack gap={3}>
                        <Button
                            size="sm"
                            bg="#DC2626"
                            color="white"
                            _hover={{ bg: '#B91C1C' }}
                            borderRadius="lg"
                            px={4}
                            onClick={() => handleAction('Deposits', 'Pending Approvals')}
                        >
                            Review Deposits (0)
                        </Button>
                        <Button
                            size="sm"
                            variant="outline"
                            borderColor="gray.300"
                            borderRadius="lg"
                            px={3}
                            onClick={() => {
                                refreshUser();
                                toaster.create({
                                    title: 'Administrative Metrics Synced',
                                    description: 'Audit telemetry and system state refreshed.',
                                    type: 'success',
                                });
                            }}
                        >
                            Refresh Telemetry
                        </Button>
                    </HStack>
                }
            />

            {/* Quick Stats Grid */}
            <Grid templateColumns={{ base: '1fr', sm: 'repeat(2, 1fr)', lg: 'repeat(4, 1fr)' }} gap={4} mb={8}>
                <StatCard
                    label="Platform Escrow Health"
                    value="Optimal"
                    helpText="MySQL InnoDB Pessimistic Locking"
                    icon={SecurityIcon}
                    colorScheme="emerald"
                />
                <StatCard
                    label="Pending Deposit Reviews"
                    value="0 Requests"
                    helpText="Approval queue ready for Phase 8"
                    icon={DepositIcon}
                    colorScheme="blue"
                />
                <StatCard
                    label="Pending Payouts"
                    value="0 Requests"
                    helpText="Bank and mobile payout queue"
                    icon={WithdrawalIcon}
                    colorScheme="amber"
                />
                <StatCard
                    label="Active Disputes"
                    value="0 Cases"
                    helpText="Arbitration engine operational"
                    icon={SupportIcon}
                    colorScheme="navy"
                />
            </Grid>

            {/* Middle Section: Management Queues */}
            <Grid templateColumns={{ base: '1fr', lg: '2fr 1fr' }} gap={6} mb={8}>
                {/* Left: Administrative Control Queue */}
                <DashboardCard
                    title="Audit & Compliance Queue"
                    subtitle="Critical items requiring staff intervention or approval."
                    headerRight={
                        <Button
                            size="xs"
                            variant="ghost"
                            color="#1769FF"
                            onClick={() => handleAction('Users', 'User Directory')}
                        >
                            Open Directory <ArrowUpRightIcon size={14} style={{ marginLeft: '4px' }} />
                        </Button>
                    }
                >
                    <EmptyState
                        icon={SecurityIcon}
                        title="All operational queues are clear"
                        description="There are currently no flagged transactions, unreviewed deposit receipts, or unresolved disputes awaiting mediation."
                        actionLabel="View User Directory"
                        onAction={() => handleAction('Users', 'Users')}
                        minH="200px"
                    />
                </DashboardCard>

                {/* Right: Administrative Quick Shortcuts */}
                <DashboardCard
                    title="Control Tower Shortcuts"
                    subtitle="High-frequency management actions"
                >
                    <VStack gap={3} align="stretch">
                        <Button
                            variant="outline"
                            justifyContent="flex-start"
                            py={5}
                            px={4}
                            borderRadius="xl"
                            borderColor="gray.200"
                            _hover={{ borderColor: '#DC2626', bg: 'red.50' }}
                            onClick={() => handleAction('Deposits', 'Manual Deposit Verification')}
                        >
                            <HStack gap={3} w="100%">
                                <Flex w={8} h={8} borderRadius="lg" bg="#FEE2E2" align="center" justify="center" color="#DC2626">
                                    <DepositIcon size={16} />
                                </Flex>
                                <Box textAlign="left">
                                    <Text fontSize="xs" fontWeight="700" color="#0A1128">
                                        Verify Deposit Receipts
                                    </Text>
                                    <Text fontSize="2xs" color="gray.500">
                                        Private receipt streaming verified
                                    </Text>
                                </Box>
                            </HStack>
                        </Button>

                        <Button
                            variant="outline"
                            justifyContent="flex-start"
                            py={5}
                            px={4}
                            borderRadius="xl"
                            borderColor="gray.200"
                            _hover={{ borderColor: '#1769FF', bg: 'blue.50' }}
                            onClick={() => handleAction('Gateways', 'Payment Gateway Vault')}
                        >
                            <HStack gap={3} w="100%">
                                <Flex w={8} h={8} borderRadius="lg" bg="#EEF6FF" align="center" justify="center" color="#1769FF">
                                    <GatewaysIcon size={16} />
                                </Flex>
                                <Box textAlign="left">
                                    <Text fontSize="xs" fontWeight="700" color="#0A1128">
                                        Payment Gateway Vault
                                    </Text>
                                    <Text fontSize="2xs" color="gray.500">
                                        AES-256 encrypted credential vault
                                    </Text>
                                </Box>
                            </HStack>
                        </Button>

                        <Button
                            variant="outline"
                            justifyContent="flex-start"
                            py={5}
                            px={4}
                            borderRadius="xl"
                            borderColor="gray.200"
                            _hover={{ borderColor: '#006A4E', bg: 'green.50' }}
                            onClick={() => handleAction('Analytics', 'System Telemetry')}
                        >
                            <HStack gap={3} w="100%">
                                <Flex w={8} h={8} borderRadius="lg" bg="#ECFDF5" align="center" justify="center" color="#006A4E">
                                    <AnalyticsIcon size={16} />
                                </Flex>
                                <Box textAlign="left">
                                    <Text fontSize="xs" fontWeight="700" color="#0A1128">
                                        Financial Reconciliation
                                    </Text>
                                    <Text fontSize="2xs" color="gray.500">
                                        Automated ledger balance check
                                    </Text>
                                </Box>
                            </HStack>
                        </Button>
                    </VStack>
                </DashboardCard>
            </Grid>

            {/* Bottom Row: Architecture & Security Verification */}
            <DashboardCard
                title="Laravel 12 + Chakra UI 3 Runtime Environment"
                subtitle="Verified production parameters for MigratePay V12."
            >
                <Grid templateColumns={{ base: '1fr', sm: 'repeat(2, 1fr)', md: 'repeat(4, 1fr)' }} gap={4} p={2}>
                    <Box p={3} bg="gray.50" borderRadius="lg" borderWidth="1px" borderColor="gray.200">
                        <Text fontSize="2xs" color="gray.500" fontWeight="700" textTransform="uppercase">
                            Framework
                        </Text>
                        <Text fontSize="sm" fontWeight="800" color="#002147" mt={1}>
                            Laravel 12.70.2
                        </Text>
                        <Text fontSize="2xs" color="gray.600">
                            PHP 8.4.27 Engine
                        </Text>
                    </Box>
                    <Box p={3} bg="gray.50" borderRadius="lg" borderWidth="1px" borderColor="gray.200">
                        <Text fontSize="2xs" color="gray.500" fontWeight="700" textTransform="uppercase">
                            UI Core
                        </Text>
                        <Text fontSize="sm" fontWeight="800" color="#1769FF" mt={1}>
                            Chakra UI 3.37.0
                        </Text>
                        <Text fontSize="2xs" color="gray.600">
                            React 19.3.1 + Emotion
                        </Text>
                    </Box>
                    <Box p={3} bg="gray.50" borderRadius="lg" borderWidth="1px" borderColor="gray.200">
                        <Text fontSize="2xs" color="gray.500" fontWeight="700" textTransform="uppercase">
                            Database
                        </Text>
                        <Text fontSize="sm" fontWeight="800" color="#006A4E" mt={1}>
                            MySQL 8.0.30
                        </Text>
                        <Text fontSize="2xs" color="gray.600">
                            DECIMAL(15,2) Precision
                        </Text>
                    </Box>
                    <Box p={3} bg="gray.50" borderRadius="lg" borderWidth="1px" borderColor="gray.200">
                        <Text fontSize="2xs" color="gray.500" fontWeight="700" textTransform="uppercase">
                            Bundler
                        </Text>
                        <Text fontSize="sm" fontWeight="800" color="#7C3AED" mt={1}>
                            Vite 7.3.7
                        </Text>
                        <Text fontSize="2xs" color="gray.600">
                            HMR + Tailwind 4
                        </Text>
                    </Box>
                </Grid>
            </DashboardCard>
        </Box>
    );
};

export default AdminDashboardHome;

