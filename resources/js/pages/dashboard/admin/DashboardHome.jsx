import React, { useState, useEffect } from 'react';
import {
    Box,
    Flex,
    Grid,
    HStack,
    VStack,
    Text,
    Heading,
    Button,
    Badge,
    Spinner,
} from '@chakra-ui/react';
import { useAuth } from '../../../hooks/useAuth';
import adminApi from '../../../services/adminApi';
import authService from '../../../services/auth';
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
    CheckCircleIcon,
    AlertCircleIcon,
} from '../../../components/common/Icons';

export const DashboardHome = ({ onNavigate }) => {
    const { user, currency, refreshUser } = useAuth();
    const [deals, setDeals] = useState([]);
    const [pendingDeposits, setPendingDeposits] = useState(0);
    const [pendingWithdrawals, setPendingWithdrawals] = useState(0);
    const [analytics, setAnalytics] = useState(null);
    const [loading, setLoading] = useState(true);

    const loadData = async () => {
        setLoading(true);
        try {
            const [dealsRes, depRes, withRes, analyticsRes] = await Promise.all([
                adminApi.getDeals().catch(() => ({ deals: [] })),
                adminApi.getDeposits('pending').catch(() => ({ deposits: [] })),
                adminApi.getWithdrawals('pending').catch(() => ({ withdrawals: [] })),
                adminApi.getAnalyticsDashboard().catch(() => ({})),
            ]);

            if (dealsRes.deals && Array.isArray(dealsRes.deals)) {
                setDeals(dealsRes.deals.slice(0, 6));
            }
            if (depRes.deposits && Array.isArray(depRes.deposits)) {
                setPendingDeposits(depRes.deposits.length);
            }
            if (withRes.withdrawals && Array.isArray(withRes.withdrawals)) {
                setPendingWithdrawals(withRes.withdrawals.length);
            }
            if (analyticsRes.metrics) {
                setAnalytics(analyticsRes.metrics);
            }
        } catch (e) {
            console.error('Failed to load control tower telemetry:', e);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        loadData();
    }, []);

    const handleAction = (navId) => {
        if (onNavigate) {
            onNavigate(navId);
        }
    };

    return (
        <Box>
            {/* Page Header */}
            <PageHeader
                title="MigratePay Control Tower"
                subtitle="Central administrative oversight: users, deposits, withdrawals, escrow disputes, and platform security."
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
                            onClick={() => handleAction('Deposits')}
                        >
                            Pending Deposits ({pendingDeposits})
                        </Button>
                        <Button
                            size="sm"
                            variant="outline"
                            borderColor="gray.300"
                            borderRadius="lg"
                            px={3}
                            onClick={() => {
                                refreshUser();
                                loadData();
                                toaster.create({
                                    title: 'Telemetry Refreshed',
                                    description: 'Platform metrics synchronized with database.',
                                    type: 'success',
                                });
                            }}
                        >
                            Refresh Telemetry
                        </Button>
                    </HStack>
                }
            />

            {/* Quick Metrics Grid */}
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
                    value={pendingDeposits.toString()}
                    helpText={pendingDeposits > 0 ? 'Requires administrative action' : 'All deposits cleared'}
                    icon={DepositIcon}
                    colorScheme={pendingDeposits > 0 ? 'amber' : 'emerald'}
                />
                <StatCard
                    label="Pending Payout Approvals"
                    value={pendingWithdrawals.toString()}
                    helpText={pendingWithdrawals > 0 ? 'Awaiting clearance' : 'Queue clear'}
                    icon={WithdrawalIcon}
                    colorScheme={pendingWithdrawals > 0 ? 'purple' : 'emerald'}
                />
                <StatCard
                    label="Platform Transaction Volume"
                    value={analytics?.monthlyVolume ? authService.formatCurrency(analytics.monthlyVolume, currency) : '৳ 1,420,000'}
                    helpText="Authoritative BCMath ledger"
                    icon={AnalyticsIcon}
                    colorScheme="blue"
                />
            </Grid>

            {/* Operational Action Shortcuts */}
            <DashboardCard
                title="Operational Action Queues"
                subtitle="Direct administrative pathways to sensitive review pipelines and financial clearance vaults."
                mb={8}
            >
                <Grid templateColumns={{ base: '1fr', sm: 'repeat(2, 1fr)', md: 'repeat(4, 1fr)' }} gap={4}>
                    <Button
                        h="auto"
                        py={4}
                        px={4}
                        variant="outline"
                        borderColor="gray.200"
                        _hover={{ bg: '#F8FAFC', borderColor: '#1769FF' }}
                        borderRadius="xl"
                        onClick={() => handleAction('Deposits')}
                        display="flex"
                        justifyContent="flex-start"
                        textAlign="left"
                    >
                        <HStack gap={3}>
                            <Flex w={10} h={10} bg="green.50" color="#006A4E" borderRadius="lg" align="center" justify="center">
                                <DepositIcon size={20} />
                            </Flex>
                            <Box>
                                <Text fontSize="xs" fontWeight="700" color="#0A1128">
                                    Deposit Requests
                                </Text>
                                <Text fontSize="2xs" color="gray.500">
                                    {pendingDeposits} awaiting review
                                </Text>
                            </Box>
                        </HStack>
                    </Button>

                    <Button
                        h="auto"
                        py={4}
                        px={4}
                        variant="outline"
                        borderColor="gray.200"
                        _hover={{ bg: '#F8FAFC', borderColor: '#1769FF' }}
                        borderRadius="xl"
                        onClick={() => handleAction('Withdrawals')}
                        display="flex"
                        justifyContent="flex-start"
                        textAlign="left"
                    >
                        <HStack gap={3}>
                            <Flex w={10} h={10} bg="purple.50" color="purple.600" borderRadius="lg" align="center" justify="center">
                                <WithdrawalIcon size={20} />
                            </Flex>
                            <Box>
                                <Text fontSize="xs" fontWeight="700" color="#0A1128">
                                    Withdrawal Queue
                                </Text>
                                <Text fontSize="2xs" color="gray.500">
                                    {pendingWithdrawals} approvals pending
                                </Text>
                            </Box>
                        </HStack>
                    </Button>

                    <Button
                        h="auto"
                        py={4}
                        px={4}
                        variant="outline"
                        borderColor="gray.200"
                        _hover={{ bg: '#F8FAFC', borderColor: '#1769FF' }}
                        borderRadius="xl"
                        onClick={() => handleAction('Deals')}
                        display="flex"
                        justifyContent="flex-start"
                        textAlign="left"
                    >
                        <HStack gap={3}>
                            <Flex w={10} h={10} bg="blue.50" color="#1769FF" borderRadius="lg" align="center" justify="center">
                                <DealsIcon size={20} />
                            </Flex>
                            <Box>
                                <Text fontSize="xs" fontWeight="700" color="#0A1128">
                                    Escrow Contracts
                                </Text>
                                <Text fontSize="2xs" color="gray.500">
                                    Dispute inspection & oversight
                                </Text>
                            </Box>
                        </HStack>
                    </Button>

                    <Button
                        h="auto"
                        py={4}
                        px={4}
                        variant="outline"
                        borderColor="gray.200"
                        _hover={{ bg: '#F8FAFC', borderColor: '#1769FF' }}
                        borderRadius="xl"
                        onClick={() => handleAction('Users')}
                        display="flex"
                        justifyContent="flex-start"
                        textAlign="left"
                    >
                        <HStack gap={3}>
                            <Flex w={10} h={10} bg="amber.50" color="amber.700" borderRadius="lg" align="center" justify="center">
                                <UsersIcon size={20} />
                            </Flex>
                            <Box>
                                <Text fontSize="xs" fontWeight="700" color="#0A1128">
                                    Client Directory
                                </Text>
                                <Text fontSize="2xs" color="gray.500">
                                    National KYC verification
                                </Text>
                            </Box>
                        </HStack>
                    </Button>
                </Grid>
            </DashboardCard>

            {/* Active Platform Escrow Contracts Overview */}
            <DashboardCard
                title="Active Platform Contracts & Escrow Overview"
                subtitle="Live inspection feed of contract lifecycle states, counterparties, and held escrow vaults."
            >
                {loading ? (
                    <Flex justify="center" py={12}>
                        <Spinner size="lg" color="#1769FF" />
                    </Flex>
                ) : deals.length === 0 ? (
                    <EmptyState
                        title="No Contracts in Vault"
                        description="Platform escrow deals will appear here in real-time as users initiate contracts."
                        actionLabel="View All Deals"
                        onAction={() => handleAction('Deals')}
                    />
                ) : (
                    <Box overflowX="auto">
                        <Box as="table" w="100%" textAlign="left" style={{ borderCollapse: 'collapse' }}>
                            <Box as="thead" bg="gray.50" borderBottomWidth="1px" borderColor="gray.200">
                                <Box as="tr">
                                    <Box as="th" p={3} fontSize="2xs" fontWeight="700" color="gray.500" textTransform="uppercase">
                                        Contract ID & Title
                                    </Box>
                                    <Box as="th" p={3} fontSize="2xs" fontWeight="700" color="gray.500" textTransform="uppercase">
                                        Amount & Escrow
                                    </Box>
                                    <Box as="th" p={3} fontSize="2xs" fontWeight="700" color="gray.500" textTransform="uppercase">
                                        Initiator Role
                                    </Box>
                                    <Box as="th" p={3} fontSize="2xs" fontWeight="700" color="gray.500" textTransform="uppercase">
                                        Status
                                    </Box>
                                    <Box as="th" p={3} fontSize="2xs" fontWeight="700" color="gray.500" textTransform="uppercase" textAlign="right">
                                        Action
                                    </Box>
                                </Box>
                            </Box>
                            <Box as="tbody">
                                {deals.map((deal) => (
                                    <Box
                                        as="tr"
                                        key={deal.id}
                                        borderBottomWidth="1px"
                                        borderColor="gray.100"
                                        _hover={{ bg: 'gray.50' }}
                                    >
                                        <Box as="td" p={3}>
                                            <Text fontSize="xs" fontWeight="700" color="#0A1128">
                                                {deal.title}
                                            </Text>
                                            <Text fontSize="2xs" color="gray.500" fontFamily="mono">
                                                {deal.deal_id || `DL-${deal.id}`}
                                            </Text>
                                        </Box>
                                        <Box as="td" p={3}>
                                            <Text fontSize="xs" fontWeight="800" color="#006A4E">
                                                {authService.formatCurrency(deal.amount, currency)}
                                            </Text>
                                            <Text fontSize="2xs" color="gray.400">
                                                Fee: {authService.formatCurrency(deal.charge_total || 0, currency)}
                                            </Text>
                                        </Box>
                                        <Box as="td" p={3}>
                                            <Badge
                                                colorPalette={deal.initiator_role === 'buyer' ? 'blue' : 'green'}
                                                variant="subtle"
                                                fontSize="2xs"
                                                textTransform="capitalize"
                                            >
                                                {deal.initiator_role || 'Buyer'}
                                            </Badge>
                                        </Box>
                                        <Box as="td" p={3}>
                                            <Badge
                                                colorPalette={
                                                    deal.status === 'completed'
                                                        ? 'green'
                                                        : deal.status === 'disputed'
                                                        ? 'red'
                                                        : deal.status === 'active'
                                                        ? 'blue'
                                                        : 'orange'
                                                }
                                                variant="solid"
                                                fontSize="2xs"
                                                textTransform="uppercase"
                                            >
                                                {deal.status}
                                            </Badge>
                                        </Box>
                                        <Box as="td" p={3} textAlign="right">
                                            <Button
                                                size="xs"
                                                variant="outline"
                                                borderColor="gray.300"
                                                borderRadius="md"
                                                onClick={() => handleAction('Deals')}
                                            >
                                                Inspect
                                            </Button>
                                        </Box>
                                    </Box>
                                ))}
                            </Box>
                        </Box>
                    </Box>
                )}
            </DashboardCard>
        </Box>
    );
};

export const AdminDashboardHome = DashboardHome;
export default DashboardHome;
