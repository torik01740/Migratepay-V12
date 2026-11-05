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
    UsersIcon,
    CheckCircleIcon,
    AlertCircleIcon,
    CloseIcon,
} from '../../../components/common/Icons';

export const UsersManagementModule = () => {
    const { currency } = useAuth();
    const [users, setUsers] = useState([]);
    const [loading, setLoading] = useState(true);
    const [search, setSearch] = useState('');
    const [statusFilter, setStatusFilter] = useState('All');
    const [kycFilter, setKycFilter] = useState('All');
    const [selectedUser, setSelectedUser] = useState(null);
    const [verifyingUser, setVerifyingUser] = useState(null);
    const [rejectionReason, setRejectionReason] = useState('');
    const [actionLoading, setActionLoading] = useState(false);

    const fetchUsers = async () => {
        setLoading(true);
        try {
            const data = await adminApi.getUsers();
            if (data.success && Array.isArray(data.users)) {
                setUsers(data.users);
            }
        } catch (e) {
            console.error('Failed to fetch users:', e);
            toaster.create({
                title: 'Error Loading Users',
                description: 'Unable to retrieve user directory from backend.',
                type: 'error',
            });
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchUsers();
    }, []);

    const handleStatusToggle = async (targetUser) => {
        const newStatus = targetUser.status === 'Active' ? 'Suspended' : 'Active';
        if (!window.confirm(`Are you sure you want to change status of ${targetUser.email} to ${newStatus}?`)) {
            return;
        }

        setActionLoading(true);
        try {
            const data = await adminApi.updateUserStatus(targetUser.id, newStatus);
            if (data.success) {
                toaster.create({
                    title: `User ${newStatus}`,
                    description: `Account for ${targetUser.email} is now ${newStatus}.`,
                    type: 'info',
                });
                fetchUsers();
            }
        } catch (e) {
            console.error(e);
            toaster.create({
                title: 'Status Update Failed',
                description: 'Server rejected user status change.',
                type: 'error',
            });
        } finally {
            setActionLoading(false);
        }
    };

    const handleVerificationDecision = async (status) => {
        if (!verifyingUser) return;
        setActionLoading(true);
        try {
            const data = await adminApi.updateUserVerification(
                verifyingUser.id,
                status,
                status === 'REJECTED' ? rejectionReason : ''
            );
            if (data.success) {
                toaster.create({
                    title: `KYC ${status === 'VERIFIED' ? 'Approved' : 'Rejected'}`,
                    description: `User ${verifyingUser.email} KYC status updated.`,
                    type: status === 'VERIFIED' ? 'success' : 'warning',
                });
                setVerifyingUser(null);
                setRejectionReason('');
                fetchUsers();
            }
        } catch (e) {
            console.error(e);
            toaster.create({
                title: 'Decision Error',
                description: 'Failed to record KYC verification decision.',
                type: 'error',
            });
        } finally {
            setActionLoading(false);
        }
    };

    const filteredUsers = users.filter((u) => {
        const matchesSearch =
            (u.name || '').toLowerCase().includes(search.toLowerCase()) ||
            (u.email || '').toLowerCase().includes(search.toLowerCase()) ||
            (u.username || '').toLowerCase().includes(search.toLowerCase()) ||
            (u.user_id_string || '').toLowerCase().includes(search.toLowerCase());

        const matchesStatus = statusFilter === 'All' || u.status === statusFilter;
        const matchesKyc = kycFilter === 'All' || u.verification_status === kycFilter;

        return matchesSearch && matchesStatus && matchesKyc;
    });

    return (
        <Box>
            <PageHeader
                title="Client Directory & KYC Verification"
                subtitle="Inspect user accounts, manage account standing, and adjudicate National KYC verification requests."
                actions={
                    <Button
                        size="sm"
                        variant="outline"
                        borderColor="gray.300"
                        borderRadius="lg"
                        onClick={fetchUsers}
                    >
                        Refresh Directory
                    </Button>
                }
            />

            {/* Filter Controls */}
            <DashboardCard mb={6}>
                <Grid templateColumns={{ base: '1fr', md: 'repeat(3, 1fr)' }} gap={4}>
                    <Box>
                        <Text fontSize="xs" fontWeight="700" color="gray.600" mb={1}>
                            Search Directory
                        </Text>
                        <Input
                            placeholder="Name, email, username, or User ID..."
                            size="sm"
                            borderRadius="lg"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                        />
                    </Box>

                    <Box>
                        <Text fontSize="xs" fontWeight="700" color="gray.600" mb={1}>
                            Account Status
                        </Text>
                        <HStack gap={2}>
                            {['All', 'Active', 'Suspended'].map((status) => (
                                <Button
                                    key={status}
                                    size="xs"
                                    variant={statusFilter === status ? 'solid' : 'outline'}
                                    bg={statusFilter === status ? '#0A1128' : 'transparent'}
                                    color={statusFilter === status ? 'white' : 'gray.600'}
                                    borderRadius="md"
                                    onClick={() => setStatusFilter(status)}
                                >
                                    {status}
                                </Button>
                            ))}
                        </HStack>
                    </Box>

                    <Box>
                        <Text fontSize="xs" fontWeight="700" color="gray.600" mb={1}>
                            KYC Verification Status
                        </Text>
                        <HStack gap={2}>
                            {['All', 'VERIFIED', 'PENDING', 'NOT_VERIFIED'].map((k) => (
                                <Button
                                    key={k}
                                    size="xs"
                                    variant={kycFilter === k ? 'solid' : 'outline'}
                                    bg={kycFilter === k ? '#1769FF' : 'transparent'}
                                    color={kycFilter === k ? 'white' : 'gray.600'}
                                    borderRadius="md"
                                    onClick={() => setKycFilter(k)}
                                >
                                    {k === 'NOT_VERIFIED' ? 'Unverified' : k}
                                </Button>
                            ))}
                        </HStack>
                    </Box>
                </Grid>
            </DashboardCard>

            {/* User Directory Table */}
            <DashboardCard
                title={`Platform Users (${filteredUsers.length})`}
                subtitle="Complete register of customer and administrator profiles with live balance and identity status."
            >
                {loading ? (
                    <Flex justify="center" py={12}>
                        <Spinner size="lg" color="#1769FF" />
                    </Flex>
                ) : filteredUsers.length === 0 ? (
                    <EmptyState
                        title="No Users Found"
                        description="Try adjusting your search criteria or filters."
                    />
                ) : (
                    <Box overflowX="auto">
                        <Box as="table" w="100%" textAlign="left" style={{ borderCollapse: 'collapse' }}>
                            <Box as="thead" bg="gray.50" borderBottomWidth="1px" borderColor="gray.200">
                                <Box as="tr">
                                    <Box as="th" p={3} fontSize="2xs" fontWeight="700" color="gray.500" textTransform="uppercase">
                                        User Details
                                    </Box>
                                    <Box as="th" p={3} fontSize="2xs" fontWeight="700" color="gray.500" textTransform="uppercase">
                                        Role & Balance
                                    </Box>
                                    <Box as="th" p={3} fontSize="2xs" fontWeight="700" color="gray.500" textTransform="uppercase">
                                        Account Status
                                    </Box>
                                    <Box as="th" p={3} fontSize="2xs" fontWeight="700" color="gray.500" textTransform="uppercase">
                                        KYC Status
                                    </Box>
                                    <Box as="th" p={3} fontSize="2xs" fontWeight="700" color="gray.500" textTransform="uppercase" textAlign="right">
                                        Actions
                                    </Box>
                                </Box>
                            </Box>
                            <Box as="tbody">
                                {filteredUsers.map((u) => (
                                    <Box as="tr" key={u.id} borderBottomWidth="1px" borderColor="gray.100" _hover={{ bg: 'gray.50' }}>
                                        <Box as="td" p={3}>
                                            <Text fontSize="xs" fontWeight="700" color="#0A1128">
                                                {u.name || u.username}
                                            </Text>
                                            <Text fontSize="2xs" color="gray.500">
                                                {u.email}
                                            </Text>
                                            <Text fontSize="2xs" color="gray.400" fontFamily="mono">
                                                {u.user_id_string || `ID: ${u.id}`}
                                            </Text>
                                        </Box>
                                        <Box as="td" p={3}>
                                            <Badge
                                                colorPalette={u.role === 'admin' ? 'red' : 'blue'}
                                                variant="subtle"
                                                fontSize="2xs"
                                                mb={1}
                                            >
                                                {u.role.toUpperCase()}
                                            </Badge>
                                            <Text fontSize="xs" fontWeight="800" color="#006A4E">
                                                {authService.formatCurrency(u.wallet_balance || 0, currency)}
                                            </Text>
                                        </Box>
                                        <Box as="td" p={3}>
                                            <Badge
                                                colorPalette={u.status === 'Active' ? 'green' : 'red'}
                                                variant="solid"
                                                fontSize="2xs"
                                            >
                                                {u.status}
                                            </Badge>
                                        </Box>
                                        <Box as="td" p={3}>
                                            <Badge
                                                colorPalette={
                                                    u.verification_status === 'VERIFIED'
                                                        ? 'green'
                                                        : u.verification_status === 'PENDING'
                                                        ? 'orange'
                                                        : 'gray'
                                                }
                                                variant="subtle"
                                                fontSize="2xs"
                                            >
                                                {u.verification_status}
                                            </Badge>
                                        </Box>
                                        <Box as="td" p={3} textAlign="right">
                                            <HStack gap={2} justify="flex-end">
                                                <Button
                                                    size="xs"
                                                    variant="outline"
                                                    borderColor="gray.300"
                                                    borderRadius="md"
                                                    onClick={() => setSelectedUser(u)}
                                                >
                                                    View Details
                                                </Button>

                                                {u.role !== 'admin' && (
                                                    <Button
                                                        size="xs"
                                                        colorPalette={u.status === 'Active' ? 'red' : 'green'}
                                                        variant="ghost"
                                                        onClick={() => handleStatusToggle(u)}
                                                    >
                                                        {u.status === 'Active' ? 'Suspend' : 'Activate'}
                                                    </Button>
                                                )}

                                                {u.verification_status === 'PENDING' && (
                                                    <Button
                                                        size="xs"
                                                        bg="#1769FF"
                                                        color="white"
                                                        _hover={{ bg: '#0F52BA' }}
                                                        borderRadius="md"
                                                        onClick={() => setVerifyingUser(u)}
                                                    >
                                                        Review KYC
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

            {/* Inspect User Modal */}
            {selectedUser && (
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
                                User Profile Inspection
                            </Heading>
                            <Button size="sm" variant="ghost" onClick={() => setSelectedUser(null)}>
                                <CloseIcon size={18} />
                            </Button>
                        </Flex>

                        <VStack gap={3} align="stretch">
                            <Grid templateColumns="repeat(2, 1fr)" gap={3} p={3} bg="gray.50" borderRadius="lg">
                                <Box>
                                    <Text fontSize="2xs" color="gray.500" fontWeight="700">FULL NAME</Text>
                                    <Text fontSize="xs" fontWeight="700">{selectedUser.name || 'Not provided'}</Text>
                                </Box>
                                <Box>
                                    <Text fontSize="2xs" color="gray.500" fontWeight="700">USERNAME</Text>
                                    <Text fontSize="xs" fontWeight="700">{selectedUser.username || 'Not set'}</Text>
                                </Box>
                                <Box>
                                    <Text fontSize="2xs" color="gray.500" fontWeight="700">EMAIL</Text>
                                    <Text fontSize="xs" fontWeight="700">{selectedUser.email}</Text>
                                </Box>
                                <Box>
                                    <Text fontSize="2xs" color="gray.500" fontWeight="700">PHONE</Text>
                                    <Text fontSize="xs" fontWeight="700">{selectedUser.phone || 'Not provided'}</Text>
                                </Box>
                                <Box>
                                    <Text fontSize="2xs" color="gray.500" fontWeight="700">WALLET BALANCE</Text>
                                    <Text fontSize="xs" fontWeight="800" color="#006A4E">
                                        {authService.formatCurrency(selectedUser.wallet_balance || 0, currency)}
                                    </Text>
                                </Box>
                                <Box>
                                    <Text fontSize="2xs" color="gray.500" fontWeight="700">MEMBER SINCE</Text>
                                    <Text fontSize="xs" fontWeight="700">
                                        {new Date(selectedUser.created_at).toLocaleDateString()}
                                    </Text>
                                </Box>
                            </Grid>

                            <Box p={3} borderWidth="1px" borderColor="gray.200" borderRadius="lg">
                                <Text fontSize="xs" fontWeight="700" color="#0A1128" mb={1}>
                                    National KYC Verification Status
                                </Text>
                                <HStack gap={2} mb={2}>
                                    <Badge colorPalette={selectedUser.verification_status === 'VERIFIED' ? 'green' : 'orange'}>
                                        {selectedUser.verification_status}
                                    </Badge>
                                    {selectedUser.id_type && (
                                        <Badge variant="outline">{selectedUser.id_type}</Badge>
                                    )}
                                    {selectedUser.nid_number && (
                                        <Text fontSize="2xs" fontFamily="mono">NID: {selectedUser.nid_number}</Text>
                                    )}
                                </HStack>
                                {selectedUser.rejection_reason && (
                                    <Text fontSize="2xs" color="red.600">
                                        Rejection Note: {selectedUser.rejection_reason}
                                    </Text>
                                )}
                            </Box>
                        </VStack>

                        <Flex justify="flex-end" gap={3} mt={6}>
                            <Button size="sm" variant="outline" onClick={() => setSelectedUser(null)}>
                                Close
                            </Button>
                        </Flex>
                    </Box>
                </Box>
            )}

            {/* KYC Adjudication Modal */}
            {verifyingUser && (
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
                    <Box bg="white" borderRadius="2xl" maxW="500px" w="100%" p={6} shadow="2xl">
                        <Flex justify="space-between" align="center" mb={4}>
                            <Heading size="md" color="#0A1128">
                                Review KYC Submission
                            </Heading>
                            <Button size="sm" variant="ghost" onClick={() => setVerifyingUser(null)}>
                                <CloseIcon size={18} />
                            </Button>
                        </Flex>

                        <VStack gap={3} align="stretch" mb={4}>
                            <Text fontSize="xs" color="gray.600">
                                Adjudicate National ID verification for <strong>{verifyingUser.email}</strong>.
                            </Text>

                            <Box p={3} bg="gray.50" borderRadius="lg">
                                <Text fontSize="2xs" color="gray.500" fontWeight="700">DOCUMENT DETAILS</Text>
                                <Text fontSize="xs">ID Type: {verifyingUser.id_type || 'National ID'}</Text>
                                <Text fontSize="xs">Document Number: {verifyingUser.nid_number || 'N/A'}</Text>
                            </Box>

                            <Box>
                                <Text fontSize="xs" fontWeight="700" color="gray.600" mb={1}>
                                    Rejection Reason (Required if declining)
                                </Text>
                                <Textarea
                                    size="sm"
                                    placeholder="Explain why the document was unacceptable..."
                                    value={rejectionReason}
                                    onChange={(e) => setRejectionReason(e.target.value)}
                                    borderRadius="lg"
                                />
                            </Box>
                        </VStack>

                        <Flex justify="space-between" gap={3} mt={4}>
                            <Button
                                size="sm"
                                colorPalette="red"
                                variant="outline"
                                loading={actionLoading}
                                onClick={() => handleVerificationDecision('REJECTED')}
                            >
                                Reject Submission
                            </Button>

                            <Button
                                size="sm"
                                bg="#10B981"
                                color="white"
                                _hover={{ bg: '#059669' }}
                                loading={actionLoading}
                                onClick={() => handleVerificationDecision('VERIFIED')}
                            >
                                Approve & Verify
                            </Button>
                        </Flex>
                    </Box>
                </Box>
            )}
        </Box>
    );
};

export default UsersManagementModule;

