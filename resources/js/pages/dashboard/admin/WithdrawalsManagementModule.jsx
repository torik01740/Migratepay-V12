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
    Input,
    Spinner,
    Textarea,
} from '@chakra-ui/react';
import { useAuth } from '../../../hooks/useAuth';
import adminApi from '../../../services/adminApi';
import authService from '../../../services/auth';
import { PageHeader } from '../../../components/common/PageHeader';
import { DashboardCard } from '../../../components/common/DashboardCard';
import { EmptyState } from '../../../components/common/EmptyState';
import { toaster } from '../../../components/feedback/toaster';
import {
    WithdrawalIcon,
    CheckCircleIcon,
    AlertCircleIcon,
    CloseIcon,
} from '../../../components/common/Icons';

export const WithdrawalsManagementModule = () => {
    const { currency } = useAuth();
    const [withdrawals, setWithdrawals] = useState([]);
    const [loading, setLoading] = useState(true);
    const [filter, setFilter] = useState('All');
    const [selected, setSelected] = useState(null);
    const [adminNote, setAdminNote] = useState('');
    const [actionLoading, setActionLoading] = useState(false);

    const fetchWithdrawals = async () => {
        setLoading(true);
        try {
            const statusParam = filter === 'All' ? '' : filter.toLowerCase();
            const data = await adminApi.getWithdrawals(statusParam);
            if (data.success && Array.isArray(data.withdrawals)) {
                setWithdrawals(data.withdrawals);
            }
        } catch (e) {
            console.error('Failed to fetch withdrawals:', e);
            toaster.create({
                title: 'Error Loading Payouts',
                description: 'Unable to retrieve withdrawal queue from backend.',
                type: 'error',
            });
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchWithdrawals();
    }, [filter]);

    const handleApprove = async (withdrawal) => {
        if (!window.confirm(`Confirm payment clearance of ${authService.formatCurrency(withdrawal.net_amount || withdrawal.amount, currency)} for ${withdrawal.user?.email || 'user'}?`)) {
            return;
        }

        setActionLoading(true);
        try {
            const data = await adminApi.approveWithdrawal(withdrawal.id, adminNote);
            if (data.success) {
                toaster.create({
                    title: 'Withdrawal Approved',
                    description: 'Payout marked as cleared and executed.',
                    type: 'success',
                });
                setSelected(null);
                setAdminNote('');
                fetchWithdrawals();
            }
        } catch (e) {
            console.error(e);
            toaster.create({
                title: 'Approval Failed',
                description: e.response?.data?.message || 'Server rejected payout approval.',
                type: 'error',
            });
        } finally {
            setActionLoading(false);
        }
    };

    const handleReject = async (withdrawal) => {
        if (!adminNote.trim()) {
            toaster.create({
                title: 'Note Required',
                description: 'Please provide an administrative note. Funds will be refunded to user wallet.',
                type: 'warning',
            });
            return;
        }

        if (!window.confirm(`Reject withdrawal? This will immediately refund ${authService.formatCurrency(withdrawal.amount, currency)} back to user's wallet balance.`)) {
            return;
        }

        setActionLoading(true);
        try {
            const data = await adminApi.rejectWithdrawal(withdrawal.id, adminNote);
            if (data.success) {
                toaster.create({
                    title: 'Withdrawal Rejected & Refunded',
                    description: 'Funds restored to client wallet balance atomically.',
                    type: 'info',
                });
                setSelected(null);
                setAdminNote('');
                fetchWithdrawals();
            }
        } catch (e) {
            console.error(e);
            toaster.create({
                title: 'Rejection Failed',
                description: e.response?.data?.message || 'Server rejected rejection.',
                type: 'error',
            });
        } finally {
            setActionLoading(false);
        }
    };

    return (
        <Box>
            <PageHeader
                title="Withdrawals Approval Queue"
                subtitle="Review outgoing withdrawal requests, inspect bank details, and execute payout disbursal."
                actions={
                    <Button
                        size="sm"
                        variant="outline"
                        borderColor="gray.300"
                        borderRadius="lg"
                        onClick={fetchWithdrawals}
                    >
                        Refresh Queue
                    </Button>
                }
            />

            {/* Filter Tabs */}
            <DashboardCard mb={6}>
                <HStack gap={2}>
                    {['All', 'Pending', 'Approved', 'Rejected'].map((status) => (
                        <Button
                            key={status}
                            size="sm"
                            variant={filter === status ? 'solid' : 'outline'}
                            bg={filter === status ? '#0A1128' : 'transparent'}
                            color={filter === status ? 'white' : 'gray.600'}
                            borderRadius="lg"
                            onClick={() => setFilter(status)}
                        >
                            {status} Approvals
                        </Button>
                    ))}
                </HStack>
            </DashboardCard>

            {/* Withdrawals Table */}
            <DashboardCard
                title={`Withdrawal Requests (${withdrawals.length})`}
                subtitle="Authoritative queue of customer balance withdrawal requests."
            >
                {loading ? (
                    <Flex justify="center" py={12}>
                        <Spinner size="lg" color="#1769FF" />
                    </Flex>
                ) : withdrawals.length === 0 ? (
                    <EmptyState
                        title="No Withdrawal Requests Found"
                        description={`There are currently no ${filter.toLowerCase()} withdrawal requests.`}
                    />
                ) : (
                    <Box overflowX="auto">
                        <Box as="table" w="100%" textAlign="left" style={{ borderCollapse: 'collapse' }}>
                            <Box as="thead" bg="gray.50" borderBottomWidth="1px" borderColor="gray.200">
                                <Box as="tr">
                                    <Box as="th" p={3} fontSize="2xs" fontWeight="700" color="gray.500" textTransform="uppercase">
                                        Client
                                    </Box>
                                    <Box as="th" p={3} fontSize="2xs" fontWeight="700" color="gray.500" textTransform="uppercase">
                                        Payout Method
                                    </Box>
                                    <Box as="th" p={3} fontSize="2xs" fontWeight="700" color="gray.500" textTransform="uppercase">
                                        Amount & Fee
                                    </Box>
                                    <Box as="th" p={3} fontSize="2xs" fontWeight="700" color="gray.500" textTransform="uppercase">
                                        Net Payout
                                    </Box>
                                    <Box as="th" p={3} fontSize="2xs" fontWeight="700" color="gray.500" textTransform="uppercase">
                                        Status
                                    </Box>
                                    <Box as="th" p={3} fontSize="2xs" fontWeight="700" color="gray.500" textTransform="uppercase" textAlign="right">
                                        Actions
                                    </Box>
                                </Box>
                            </Box>
                            <Box as="tbody">
                                {withdrawals.map((w) => (
                                    <Box as="tr" key={w.id} borderBottomWidth="1px" borderColor="gray.100" _hover={{ bg: 'gray.50' }}>
                                        <Box as="td" p={3}>
                                            <Text fontSize="xs" fontWeight="700" color="#0A1128">
                                                {w.user?.name || w.user?.username || 'Client'}
                                            </Text>
                                            <Text fontSize="2xs" color="gray.500">
                                                {w.user?.email}
                                            </Text>
                                            <Text fontSize="2xs" color="gray.400">
                                                {new Date(w.created_at).toLocaleString()}
                                            </Text>
                                        </Box>
                                        <Box as="td" p={3}>
                                            <Badge colorPalette="purple" variant="subtle" fontSize="2xs" textTransform="capitalize">
                                                {w.method_type || 'Payout Method'}
                                            </Badge>
                                            <Text fontSize="2xs" color="gray.700" mt={1}>
                                                {w.account_number || w.account_details?.account_number || 'N/A'}
                                            </Text>
                                            {w.bank_name && (
                                                <Text fontSize="2xs" color="gray.500">{w.bank_name}</Text>
                                            )}
                                        </Box>
                                        <Box as="td" p={3}>
                                            <Text fontSize="xs" fontWeight="700" color="#0A1128">
                                                {authService.formatCurrency(w.amount, currency)}
                                            </Text>
                                            <Text fontSize="2xs" color="red.500">
                                                Fee: {authService.formatCurrency(w.fee_amount || 0, currency)}
                                            </Text>
                                        </Box>
                                        <Box as="td" p={3}>
                                            <Text fontSize="xs" fontWeight="900" color="#006A4E">
                                                {authService.formatCurrency(w.net_amount || w.amount, currency)}
                                            </Text>
                                        </Box>
                                        <Box as="td" p={3}>
                                            <Badge
                                                colorPalette={
                                                    w.status === 'approved'
                                                        ? 'green'
                                                        : w.status === 'pending'
                                                        ? 'orange'
                                                        : 'red'
                                                }
                                                variant="solid"
                                                fontSize="2xs"
                                                textTransform="uppercase"
                                            >
                                                {w.status}
                                            </Badge>
                                        </Box>
                                        <Box as="td" p={3} textAlign="right">
                                            <HStack gap={2} justify="flex-end">
                                                <Button
                                                    size="xs"
                                                    variant="outline"
                                                    borderColor="gray.300"
                                                    borderRadius="md"
                                                    onClick={() => setSelected(w)}
                                                >
                                                    Inspect
                                                </Button>

                                                {w.status === 'pending' && (
                                                    <Button
                                                        size="xs"
                                                        bg="#10B981"
                                                        color="white"
                                                        _hover={{ bg: '#059669' }}
                                                        borderRadius="md"
                                                        onClick={() => handleApprove(w)}
                                                    >
                                                        Approve
                                                    </Button>
                                                )}
                                            </HStack>
                                        </Box>
                                    </Box>
                                ))}
                            </Box>
                        </Box>
                    </Box>
                )}
            </DashboardCard>

            {/* Inspect / Process Payout Modal */}
            {selected && (
                <Box
                    position="fixed"
                    top={0}
                    left={0}
                    right={0}
                    bottom={0}
                    bg="blackAlpha.600"
                    zIndex={1000}
                    display="flex"
                    alignItems="center"
                    justifyContent="center"
                    p={4}
                >
                    <Box bg="white" borderRadius="2xl" maxW="560px" w="100%" p={6} shadow="2xl">
                        <Flex justify="space-between" align="center" mb={4}>
                            <Heading size="md" color="#0A1128">
                                Withdrawal Clearance
                            </Heading>
                            <Button size="sm" variant="ghost" onClick={() => setSelected(null)}>
                                <CloseIcon size={18} />
                            </Button>
                        </Flex>

                        <VStack gap={4} align="stretch">
                            <Grid templateColumns="repeat(2, 1fr)" gap={3} p={3} bg="gray.50" borderRadius="lg">
                                <Box>
                                    <Text fontSize="2xs" color="gray.500" fontWeight="700">USER</Text>
                                    <Text fontSize="xs" fontWeight="700">{selected.user?.email}</Text>
                                </Box>
                                <Box>
                                    <Text fontSize="2xs" color="gray.500" fontWeight="700">NET PAYOUT</Text>
                                    <Text fontSize="sm" fontWeight="800" color="#006A4E">
                                        {authService.formatCurrency(selected.net_amount || selected.amount, currency)}
                                    </Text>
                                </Box>
                                <Box>
                                    <Text fontSize="2xs" color="gray.500" fontWeight="700">METHOD</Text>
                                    <Text fontSize="xs">{selected.method_type}</Text>
                                </Box>
                                <Box>
                                    <Text fontSize="2xs" color="gray.500" fontWeight="700">ACCOUNT NUMBER</Text>
                                    <Text fontSize="xs" fontFamily="mono">{selected.account_number || 'N/A'}</Text>
                                </Box>
                                {selected.bank_name && (
                                    <Box>
                                        <Text fontSize="2xs" color="gray.500" fontWeight="700">BANK / BRANCH</Text>
                                        <Text fontSize="xs">{selected.bank_name} {selected.branch_name ? `(${selected.branch_name})` : ''}</Text>
                                    </Box>
                                )}
                                <Box>
                                    <Text fontSize="2xs" color="gray.500" fontWeight="700">REQUEST DATE</Text>
                                    <Text fontSize="xs">{new Date(selected.created_at).toLocaleString()}</Text>
                                </Box>
                            </Grid>

                            {selected.status === 'pending' && (
                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="gray.600" mb={1}>
                                        Administrative Note
                                    </Text>
                                    <Textarea
                                        size="sm"
                                        placeholder="Add transaction clearance note or reason for rejection..."
                                        value={adminNote}
                                        onChange={(e) => setAdminNote(e.target.value)}
                                        borderRadius="lg"
                                    />
                                </Box>
                            )}
                        </VStack>

                        <Flex justify="space-between" mt={6}>
                            <Button size="sm" variant="outline" onClick={() => setSelected(null)}>
                                Close
                            </Button>

                            {selected.status === 'pending' && (
                                <HStack gap={3}>
                                    <Button
                                        size="sm"
                                        colorPalette="red"
                                        variant="outline"
                                        loading={actionLoading}
                                        onClick={() => handleReject(selected)}
                                    >
                                        Reject & Refund Balance
                                    </Button>

                                    <Button
                                        size="sm"
                                        bg="#10B981"
                                        color="white"
                                        _hover={{ bg: '#059669' }}
                                        loading={actionLoading}
                                        onClick={() => handleApprove(selected)}
                                    >
                                        Confirm Payout Disbursed
                                    </Button>
                                </HStack>
                            )}
                        </Flex>
                    </Box>
                </Box>
            )}
        </Box>
    );
};

export default WithdrawalsManagementModule;

