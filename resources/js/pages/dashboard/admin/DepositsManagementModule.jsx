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
    DepositIcon,
    CheckCircleIcon,
    AlertCircleIcon,
    CloseIcon,
} from '../../../components/common/Icons';

export const DepositsManagementModule = () => {
    const { currency } = useAuth();
    const [deposits, setDeposits] = useState([]);
    const [loading, setLoading] = useState(true);
    const [filter, setFilter] = useState('All');
    const [selected, setSelected] = useState(null);
    const [adminNote, setAdminNote] = useState('');
    const [actionLoading, setActionLoading] = useState(false);

    const fetchDeposits = async () => {
        setLoading(true);
        try {
            const statusParam = filter === 'All' ? '' : filter.toLowerCase();
            const data = await adminApi.getDeposits(statusParam);
            if (data.success && Array.isArray(data.deposits)) {
                setDeposits(data.deposits);
            }
        } catch (e) {
            console.error('Failed to fetch deposits:', e);
            toaster.create({
                title: 'Error Loading Deposits',
                description: 'Unable to retrieve deposit requests from backend.',
                type: 'error',
            });
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchDeposits();
    }, [filter]);

    const handleApprove = async (deposit) => {
        if (!window.confirm(`Approve deposit of ${authService.formatCurrency(deposit.amount, currency)} for ${deposit.user?.email || 'user'}? This will immediately credit their wallet balance.`)) {
            return;
        }

        setActionLoading(true);
        try {
            const data = await adminApi.approveDeposit(deposit.id, adminNote);
            if (data.success) {
                toaster.create({
                    title: 'Deposit Approved',
                    description: `Credited ${authService.formatCurrency(deposit.amount, currency)} to user wallet atomically.`,
                    type: 'success',
                });
                setSelected(null);
                setAdminNote('');
                fetchDeposits();
            }
        } catch (e) {
            console.error(e);
            toaster.create({
                title: 'Approval Failed',
                description: e.response?.data?.message || 'Server rejected deposit approval.',
                type: 'error',
            });
        } finally {
            setActionLoading(false);
        }
    };

    const handleReject = async (deposit) => {
        if (!adminNote.trim()) {
            toaster.create({
                title: 'Note Required',
                description: 'Please provide an administrative note explaining the rejection.',
                type: 'warning',
            });
            return;
        }

        if (!window.confirm(`Reject deposit of ${authService.formatCurrency(deposit.amount, currency)}?`)) {
            return;
        }

        setActionLoading(true);
        try {
            const data = await adminApi.rejectDeposit(deposit.id, adminNote);
            if (data.success) {
                toaster.create({
                    title: 'Deposit Rejected',
                    description: 'Deposit marked as rejected and user informed.',
                    type: 'info',
                });
                setSelected(null);
                setAdminNote('');
                fetchDeposits();
            }
        } catch (e) {
            console.error(e);
            toaster.create({
                title: 'Rejection Failed',
                description: e.response?.data?.message || 'Server rejected operation.',
                type: 'error',
            });
        } finally {
            setActionLoading(false);
        }
    };

    return (
        <Box>
            <PageHeader
                title="Deposits Management Vault"
                subtitle="Review inbound payments, verify gateway reference notes, and credit customer balances."
                actions={
                    <Button
                        size="sm"
                        variant="outline"
                        borderColor="gray.300"
                        borderRadius="lg"
                        onClick={fetchDeposits}
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
                            {status} Requests
                        </Button>
                    ))}
                </HStack>
            </DashboardCard>

            {/* Deposits Ledger */}
            <DashboardCard
                title={`Deposit Requests (${deposits.length})`}
                subtitle="Authoritative queue of manual and automated balance deposit intents."
            >
                {loading ? (
                    <Flex justify="center" py={12}>
                        <Spinner size="lg" color="#1769FF" />
                    </Flex>
                ) : deposits.length === 0 ? (
                    <EmptyState
                        title="No Deposit Requests Found"
                        description={`There are currently no ${filter.toLowerCase()} deposit requests.`}
                    />
                ) : (
                    <Box overflowX="auto">
                        <Box as="table" w="100%" textAlign="left" style={{ borderCollapse: 'collapse' }}>
                            <Box as="thead" bg="gray.50" borderBottomWidth="1px" borderColor="gray.200">
                                <Box as="tr">
                                    <Box as="th" p={3} fontSize="2xs" fontWeight="700" color="gray.500" textTransform="uppercase">
                                        User / Client
                                    </Box>
                                    <Box as="th" p={3} fontSize="2xs" fontWeight="700" color="gray.500" textTransform="uppercase">
                                        Gateway & Reference
                                    </Box>
                                    <Box as="th" p={3} fontSize="2xs" fontWeight="700" color="gray.500" textTransform="uppercase">
                                        Amount
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
                                {deposits.map((dep) => (
                                    <Box as="tr" key={dep.id} borderBottomWidth="1px" borderColor="gray.100" _hover={{ bg: 'gray.50' }}>
                                        <Box as="td" p={3}>
                                            <Text fontSize="xs" fontWeight="700" color="#0A1128">
                                                {dep.user?.name || dep.user?.username || 'Client'}
                                            </Text>
                                            <Text fontSize="2xs" color="gray.500">
                                                {dep.user?.email}
                                            </Text>
                                            <Text fontSize="2xs" color="gray.400">
                                                {new Date(dep.created_at).toLocaleString()}
                                            </Text>
                                        </Box>
                                        <Box as="td" p={3}>
                                            <Badge colorPalette="blue" variant="subtle" fontSize="2xs" textTransform="uppercase">
                                                {dep.payment_gateway?.name || dep.gateway_slug || 'Gateway'}
                                            </Badge>
                                            <Text fontSize="2xs" color="gray.600" fontFamily="mono" mt={1}>
                                                TrxID: {dep.transaction_ref || dep.deposit_id || 'N/A'}
                                            </Text>
                                            {dep.sender_number && (
                                                <Text fontSize="2xs" color="gray.500">
                                                    Sender: {dep.sender_number}
                                                </Text>
                                            )}
                                        </Box>
                                        <Box as="td" p={3}>
                                            <Text fontSize="xs" fontWeight="800" color="#006A4E">
                                                {authService.formatCurrency(dep.amount, currency)}
                                            </Text>
                                        </Box>
                                        <Box as="td" p={3}>
                                            <Badge
                                                colorPalette={
                                                    dep.status === 'approved'
                                                        ? 'green'
                                                        : dep.status === 'pending'
                                                        ? 'orange'
                                                        : 'red'
                                                }
                                                variant="solid"
                                                fontSize="2xs"
                                                textTransform="uppercase"
                                            >
                                                {dep.status}
                                            </Badge>
                                        </Box>
                                        <Box as="td" p={3} textAlign="right">
                                            <HStack gap={2} justify="flex-end">
                                                <Button
                                                    size="xs"
                                                    variant="outline"
                                                    borderColor="gray.300"
                                                    borderRadius="md"
                                                    onClick={() => setSelected(dep)}
                                                >
                                                    Inspect
                                                </Button>

                                                {dep.status === 'pending' && (
                                                    <Button
                                                        size="xs"
                                                        bg="#10B981"
                                                        color="white"
                                                        _hover={{ bg: '#059669' }}
                                                        borderRadius="md"
                                                        onClick={() => handleApprove(dep)}
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

            {/* Inspect / Process Deposit Modal */}
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
                                Deposit Request Clearance
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
                                    <Text fontSize="2xs" color="gray.500" fontWeight="700">AMOUNT</Text>
                                    <Text fontSize="sm" fontWeight="800" color="#006A4E">
                                        {authService.formatCurrency(selected.amount, currency)}
                                    </Text>
                                </Box>
                                <Box>
                                    <Text fontSize="2xs" color="gray.500" fontWeight="700">GATEWAY</Text>
                                    <Text fontSize="xs">{selected.payment_gateway?.name || selected.gateway_slug}</Text>
                                </Box>
                                <Box>
                                    <Text fontSize="2xs" color="gray.500" fontWeight="700">TRANSACTION REF</Text>
                                    <Text fontSize="xs" fontFamily="mono">{selected.transaction_ref || 'N/A'}</Text>
                                </Box>
                                {selected.sender_number && (
                                    <Box>
                                        <Text fontSize="2xs" color="gray.500" fontWeight="700">SENDER MOBILE</Text>
                                        <Text fontSize="xs">{selected.sender_number}</Text>
                                    </Box>
                                )}
                                <Box>
                                    <Text fontSize="2xs" color="gray.500" fontWeight="700">DATE</Text>
                                    <Text fontSize="xs">{new Date(selected.created_at).toLocaleString()}</Text>
                                </Box>
                            </Grid>

                            {selected.proof_notes && (
                                <Box p={3} borderWidth="1px" borderColor="gray.200" borderRadius="lg">
                                    <Text fontSize="2xs" color="gray.500" fontWeight="700">USER PROOF NOTES</Text>
                                    <Text fontSize="xs" color="gray.700" mt={1}>{selected.proof_notes}</Text>
                                </Box>
                            )}

                            {selected.receipt_path && (
                                <Box p={3} bg="blue.50" borderRadius="lg">
                                    <Text fontSize="2xs" color="blue.700" fontWeight="700">RECEIPT ATTACHMENT</Text>
                                    <Text fontSize="xs" color="blue.800" mt={1}>Receipt file uploaded on private disk.</Text>
                                    <Button
                                        size="xs"
                                        mt={2}
                                        variant="outline"
                                        borderColor="blue.300"
                                        onClick={() => window.open(`/api/private/deposits/${selected.id}`, '_blank')}
                                    >
                                        Open Receipt Stream
                                    </Button>
                                </Box>
                            )}

                            {selected.status === 'pending' && (
                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="gray.600" mb={1}>
                                        Administrative Note
                                    </Text>
                                    <Textarea
                                        size="sm"
                                        placeholder="Add an internal note or explanation for approval/rejection..."
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
                                        Reject
                                    </Button>

                                    <Button
                                        size="sm"
                                        bg="#10B981"
                                        color="white"
                                        _hover={{ bg: '#059669' }}
                                        loading={actionLoading}
                                        onClick={() => handleApprove(selected)}
                                    >
                                        Approve & Credit Balance
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

export default DepositsManagementModule;

