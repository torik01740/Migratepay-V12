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
import { dealsApi, financeApi } from '../../../services/dashboardApi';
import authService from '../../../services/auth';
import { PageHeader } from '../../../components/common/PageHeader';
import { StatCard } from '../../../components/common/StatCard';
import { DashboardCard } from '../../../components/common/DashboardCard';
import { EmptyState } from '../../../components/common/EmptyState';
import { toaster } from '../../../components/feedback/toaster';
import {
    WalletIcon,
    DealsIcon,
    DepositIcon,
    WithdrawalIcon,
    SecurityIcon,
    SupportIcon,
    CheckCircleIcon,
    ArrowUpRightIcon,
    PlusIcon,
} from '../../../components/common/Icons';

export const DashboardHome = ({ onNavigate }) => {
    const { user, currency, refreshUser } = useAuth();
    const [deals, setDeals] = useState([]);
    const [deposits, setDeposits] = useState([]);
    const [loading, setLoading] = useState(true);

    const loadData = async () => {
        try {
            const [dealRes, depRes] = await Promise.all([
                dealsApi.getAll().catch(() => ({ deals: [] })),
                financeApi.getDeposits().catch(() => ({ deposits: [] })),
                refreshUser(),
            ]);

            if (dealRes.success && Array.isArray(dealRes.deals)) {
                setDeals(dealRes.deals);
            }
            if (depRes.success && Array.isArray(depRes.deposits)) {
                setDeposits(depRes.deposits);
            }
        } catch (e) {
            console.error('Error loading dashboard home:', e);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        loadData();
        const interval = setInterval(loadData, 8000);
        return () => clearInterval(interval);
    }, []);

    const walletBalance = parseFloat(user?.wallet_balance || 0);
    const isVerified = user?.verification_status === 'VERIFIED';

    const activeDeals = deals.filter((d) =>
        ['awaiting_acceptance', 'funds_secured', 'in_progress', 'buyer_review', 'delivered'].includes(d.status)
    );

    const totalDeposited = deposits
        .filter((d) => d.status === 'Approved' || d.status === 'approved')
        .reduce((sum, d) => sum + parseFloat(d.amount || 0), 0);

    const getStatusStyle = (status) => {
        switch (status) {
            case 'completed':
            case 'released':
                return { palette: 'green', label: 'Completed' };
            case 'funds_secured':
            case 'in_progress':
                return { palette: 'blue', label: 'Funds Secured' };
            case 'awaiting_acceptance':
                return { palette: 'orange', label: 'Awaiting Acceptance' };
            case 'buyer_review':
            case 'delivered':
                return { palette: 'purple', label: 'In Review' };
            default:
                return { palette: 'gray', label: status };
        }
    };

    return (
        <Box>
            {/* Page Header */}
            <PageHeader
                title={`Welcome back, ${user?.name || user?.username || 'Member'}!`}
                subtitle="Manage your secure escrow transactions, wallet funds, and account security."
                badge={
                    <Badge
                        colorPalette={isVerified ? 'green' : 'orange'}
                        variant="subtle"
                        fontSize="xs"
                        px={2.5}
                        py={1}
                        borderRadius="md"
                    >
                        {isVerified ? 'Verified Counterparty' : 'Verification Required'}
                    </Badge>
                }
                actions={
                    <HStack gap={3}>
                        <Button
                            size="sm"
                            bg="#1769FF"
                            color="white"
                            _hover={{ bg: '#1358D8' }}
                            borderRadius="lg"
                            px={4}
                            onClick={() => onNavigate?.('CreateDeal')}
                        >
                            <PlusIcon size={16} style={{ marginRight: '6px' }} />
                            New Escrow Deal
                        </Button>
                        <Button
                            size="sm"
                            variant="outline"
                            borderColor="gray.300"
                            borderRadius="lg"
                            px={3}
                            onClick={() => {
                                loadData();
                                toaster.create({
                                    title: 'Wallet Synchronized',
                                    description: 'Latest balance and deals retrieved from server.',
                                    type: 'success',
                                });
                            }}
                        >
                            Sync Wallet
                        </Button>
                    </HStack>
                }
            />

            {/* Quick Stats Grid */}
            <Grid templateColumns={{ base: '1fr', sm: 'repeat(2, 1fr)', lg: 'repeat(4, 1fr)' }} gap={4} mb={8}>
                <StatCard
                    label="Wallet Available Balance"
                    value={authService.formatCurrency(walletBalance, currency)}
                    helpText="Verified by MySQL Ledger"
                    icon={WalletIcon}
                    colorScheme="emerald"
                />
                <StatCard
                    label="Active Escrow Deals"
                    value={`${activeDeals.length} Deals`}
                    helpText="Funds locked in mediator escrow"
                    icon={DealsIcon}
                    colorScheme="blue"
                />
                <StatCard
                    label="Lifetime Approved Deposits"
                    value={authService.formatCurrency(totalDeposited, currency)}
                    helpText="Total credited wallet funds"
                    icon={DepositIcon}
                    colorScheme="navy"
                />
                <StatCard
                    label="Account Trust Tier"
                    value={isVerified ? 'Tier 1 Verified' : 'Unverified'}
                    helpText="Tier 1 KYC Escrow Protection"
                    icon={SecurityIcon}
                    colorScheme={isVerified ? 'emerald' : 'amber'}
                />
            </Grid>

            {/* Middle Section: Active Deals & Quick Actions */}
            <Grid templateColumns={{ base: '1fr', lg: '2fr 1fr' }} gap={6} mb={8}>
                {/* Left: Active Escrow Deals */}
                <DashboardCard
                    title="Active Escrow Deals"
                    subtitle="Transactions currently protected under MigratePay escrow."
                    headerRight={
                        <Button
                            size="xs"
                            variant="ghost"
                            color="#1769FF"
                            onClick={() => onNavigate?.('Projects')}
                        >
                            View Workspace <ArrowUpRightIcon size={14} style={{ marginLeft: '4px' }} />
                        </Button>
                    }
                >
                    {loading ? (
                        <Flex justify="center" p={8}><Spinner size="md" color="#1769FF" /></Flex>
                    ) : activeDeals.length === 0 ? (
                        <EmptyState
                            icon={DealsIcon}
                            title="No active escrow deals"
                            description="You do not have any pending or active deal agreements right now. Initiate a new escrow deal to begin safe trading."
                            actionLabel="Initiate Escrow Deal"
                            onAction={() => onNavigate?.('CreateDeal')}
                            minH="200px"
                        />
                    ) : (
                        <Box overflowX="auto">
                            <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: '13px' }}>
                                <thead>
                                    <tr style={{ borderBottom: '1px solid #E2E8F0', textAlign: 'left', color: '#64748B' }}>
                                        <th style={{ padding: '8px 10px' }}>Deal ID</th>
                                        <th style={{ padding: '8px 10px' }}>Title</th>
                                        <th style={{ padding: '8px 10px' }}>Value</th>
                                        <th style={{ padding: '8px 10px' }}>Status</th>
                                        <th style={{ padding: '8px 10px' }}>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {activeDeals.slice(0, 5).map((d) => {
                                        const st = getStatusStyle(d.status);
                                        return (
                                            <tr key={d.id} style={{ borderBottom: '1px solid #F1F5F9' }}>
                                                <td style={{ padding: '10px', fontFamily: 'mono', fontWeight: '700' }}>
                                                    #{d.deal_id || d.id}
                                                </td>
                                                <td style={{ padding: '10px', fontWeight: '600', color: '#0A1128' }}>
                                                    {d.title}
                                                </td>
                                                <td style={{ padding: '10px', fontWeight: '800', color: '#006A4E' }}>
                                                    {authService.formatCurrency(d.amount, currency)}
                                                </td>
                                                <td style={{ padding: '10px' }}>
                                                    <Badge colorPalette={st.palette} variant="subtle" fontSize="3xs" px={2} py={0.5} borderRadius="md">
                                                        {st.label}
                                                    </Badge>
                                                </td>
                                                <td style={{ padding: '10px' }}>
                                                    <Button
                                                        size="2xs"
                                                        bg="#002147"
                                                        color="white"
                                                        onClick={() => onNavigate?.('Projects')}
                                                    >
                                                        Open
                                                    </Button>
                                                </td>
                                            </tr>
                                        );
                                    })}
                                </tbody>
                            </table>
                        </Box>
                    )}
                </DashboardCard>

                {/* Right: Quick Operations */}
                <DashboardCard title="Quick Operations" subtitle="Frequent account actions">
                    <VStack gap={3} align="stretch">
                        <Button
                            variant="outline"
                            justifyContent="flex-start"
                            py={5}
                            px={4}
                            borderRadius="xl"
                            borderColor="gray.200"
                            _hover={{ borderColor: '#1769FF', bg: 'blue.50' }}
                            onClick={() => onNavigate?.('Deposit')}
                        >
                            <HStack gap={3} w="100%">
                                <Flex w={8} h={8} borderRadius="lg" bg="#ECFDF5" align="center" justify="center" color="#006A4E">
                                    <DepositIcon size={16} />
                                </Flex>
                                <Box textAlign="left">
                                    <Text fontSize="xs" fontWeight="700" color="#0A1128">Deposit Funds</Text>
                                    <Text fontSize="2xs" color="gray.500">Manual / Instant bKash, Nagad, Card</Text>
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
                            onClick={() => onNavigate?.('Withdraw')}
                        >
                            <HStack gap={3} w="100%">
                                <Flex w={8} h={8} borderRadius="lg" bg="#EEF6FF" align="center" justify="center" color="#1769FF">
                                    <WithdrawalIcon size={16} />
                                </Flex>
                                <Box textAlign="left">
                                    <Text fontSize="xs" fontWeight="700" color="#0A1128">Withdraw to Bank</Text>
                                    <Text fontSize="2xs" color="gray.500">Direct payout to verified method</Text>
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
                            onClick={() => onNavigate?.('Support')}
                        >
                            <HStack gap={3} w="100%">
                                <Flex w={8} h={8} borderRadius="lg" bg="#FEF3C7" align="center" justify="center" color="#D97706">
                                    <SupportIcon size={16} />
                                </Flex>
                                <Box textAlign="left">
                                    <Text fontSize="xs" fontWeight="700" color="#0A1128">Dispute & Support Desk</Text>
                                    <Text fontSize="2xs" color="gray.500">Escalate issues to official mediators</Text>
                                </Box>
                            </HStack>
                        </Button>
                    </VStack>
                </DashboardCard>
            </Grid>

            {/* Bottom Row: Account Verification Banner */}
            <DashboardCard title="Account Security & Compliance" subtitle="Bangladeshi National KYC and transaction protection status.">
                <Flex
                    direction={{ base: 'column', md: 'row' }}
                    align={{ base: 'flex-start', md: 'center' }}
                    justify="space-between"
                    gap={4}
                    p={4}
                    bg={isVerified ? '#ECFDF5' : '#FEF3C7'}
                    borderRadius="xl"
                    borderWidth="1px"
                    borderColor={isVerified ? '#A7F3D0' : '#FDE68A'}
                >
                    <HStack gap={3}>
                        <Flex
                            w={10}
                            h={10}
                            borderRadius="full"
                            bg={isVerified ? '#10B981' : '#D97706'}
                            color="white"
                            align="center"
                            justify="center"
                            flexShrink={0}
                        >
                            {isVerified ? <CheckCircleIcon size={20} /> : <SecurityIcon size={20} />}
                        </Flex>
                        <Box>
                            <Text fontSize="sm" fontWeight="700" color="#0A1128">
                                {isVerified ? 'Your account is fully KYC-verified' : 'Verification Required for High-Volume Escrow'}
                            </Text>
                            <Text fontSize="xs" color="gray.600">
                                {isVerified
                                    ? 'You have unlimited access to escrow deals, instant payouts, and dispute mediation.'
                                    : 'Please submit your National ID (NID) or Passport to unlock higher transaction limits.'}
                            </Text>
                        </Box>
                    </HStack>

                    {!isVerified && (
                        <Button
                            size="sm"
                            bg="#0A1128"
                            color="white"
                            _hover={{ bg: '#162447' }}
                            borderRadius="lg"
                            onClick={() => onNavigate?.('Profile')}
                        >
                            Submit Verification
                        </Button>
                    )}
                </Flex>
            </DashboardCard>
        </Box>
    );
};

export default DashboardHome;
