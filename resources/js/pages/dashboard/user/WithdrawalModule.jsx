import React, { useState, useEffect } from 'react';
import {
    Box,
    Flex,
    VStack,
    HStack,
    Heading,
    Text,
    Button,
    Badge,
    Grid,
    Spinner,
} from '@chakra-ui/react';
import { useAuth } from '../../../hooks/useAuth';
import { financeApi } from '../../../services/dashboardApi';
import authService from '../../../services/auth';
import { PageHeader } from '../../../components/common/PageHeader';
import { DashboardCard } from '../../../components/common/DashboardCard';
import { StatCard } from '../../../components/common/StatCard';
import { EmptyState } from '../../../components/common/EmptyState';
import { toaster } from '../../../components/feedback/toaster';
import {
    WithdrawalIcon,
    WalletIcon,
    CheckCircleIcon,
    AlertCircleIcon,
    PlusIcon,
} from '../../../components/common/Icons';

export const WithdrawalModule = () => {
    const { user, currency, refreshUser } = useAuth();
    const [withdrawals, setWithdrawals] = useState([]);
    const [methods, setMethods] = useState([]);
    const [loading, setLoading] = useState(true);
    const [submitting, setSubmitting] = useState(false);
    const [showMethodModal, setShowMethodModal] = useState(false);

    // Form
    const [amount, setAmount] = useState('');
    const [selectedMethodId, setSelectedMethodId] = useState('');

    // New Method Form
    const [newMethod, setNewMethod] = useState({
        type: 'mobile', // 'bank' | 'mobile'
        bank_name: '',
        branch: '',
        routing_number: '',
        account_name: '',
        account_number: '',
        mobile_provider: 'bKash',
        mobile_number: '',
    });
    const [savingMethod, setSavingMethod] = useState(false);

    const loadData = async () => {
        try {
            const [wData, mData] = await Promise.all([
                financeApi.getWithdrawals().catch(() => ({ withdrawals: [] })),
                financeApi.getWithdrawalMethods().catch(() => ({ methods: [] })),
            ]);

            if (wData.success) setWithdrawals(wData.withdrawals || []);
            if (mData.success && Array.isArray(mData.methods)) {
                setMethods(mData.methods);
                if (mData.methods.length > 0 && !selectedMethodId) {
                    setSelectedMethodId(mData.methods[0].id);
                }
            }
        } catch (e) {
            console.error('Error loading withdrawals:', e);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        loadData();
    }, []);

    const walletBalance = parseFloat(user?.wallet_balance || 0);
    const numAmount = parseFloat(amount) || 0;

    // Selected method object
    const selectedMethod = methods.find((m) => m.id === parseInt(selectedMethodId) || m.id === selectedMethodId);
    const isMobileMethod = selectedMethod?.type === 'mobile';
    const feeRate = isMobileMethod ? 0.015 : 0.0; // 1.5% for mobile, 0% for bank
    const estimatedFee = numAmount > 0 ? Math.round(numAmount * feeRate * 100) / 100 : 0;
    const netPayout = numAmount > estimatedFee ? numAmount - estimatedFee : 0;

    const handleCreateMethod = async (e) => {
        e.preventDefault();
        setSavingMethod(true);
        try {
            const payload = {
                type: newMethod.type,
                name: newMethod.type === 'bank' ? newMethod.bank_name : `${newMethod.mobile_provider} Wallet`,
                account_name: newMethod.account_name || user?.name,
                account_number: newMethod.type === 'bank' ? newMethod.account_number : newMethod.mobile_number,
                bank_name: newMethod.bank_name || null,
                branch: newMethod.branch || null,
                routing_number: newMethod.routing_number || null,
                details: {
                    provider: newMethod.mobile_provider,
                },
            };

            const data = await financeApi.saveWithdrawalMethod(payload);
            if (data.success) {
                toaster.create({ title: 'Method Added', description: 'Payout method saved successfully.', type: 'success' });
                setShowMethodModal(false);
                loadData();
            }
        } catch (err) {
            toaster.create({ title: 'Error', description: err.message, type: 'error' });
        } finally {
            setSavingMethod(false);
        }
    };

    const handleDeleteMethod = async (id) => {
        if (!window.confirm('Delete this saved payout method?')) return;
        try {
            const res = await financeApi.deleteMethod(id);
            if (res.success) {
                toaster.create({ title: 'Method Removed', description: 'Payout account removed.', type: 'info' });
                loadData();
            }
        } catch (e) {
            toaster.create({ title: 'Error', description: e.message, type: 'error' });
        }
    };

    const handleSubmitWithdrawal = async (e) => {
        e.preventDefault();

        if (numAmount <= 0) {
            toaster.create({ title: 'Validation Error', description: 'Please enter a valid payout amount.', type: 'error' });
            return;
        }
        if (numAmount > walletBalance) {
            toaster.create({ title: 'Insufficient Balance', description: 'Withdrawal amount exceeds available wallet funds.', type: 'error' });
            return;
        }
        if (!selectedMethodId) {
            toaster.create({ title: 'Select Method', description: 'Please select or add a verified payout method.', type: 'error' });
            return;
        }

        setSubmitting(true);
        try {
            const payload = {
                amount: numAmount,
                method_id: selectedMethodId,
            };

            const data = await financeApi.submitWithdrawal(payload);
            if (data.success) {
                toaster.create({
                    title: 'Withdrawal Requested!',
                    description: 'Your payout request has been registered and is pending approval.',
                    type: 'success',
                });
                setAmount('');
                refreshUser();
                loadData();
            } else {
                toaster.create({ title: 'Request Failed', description: data.message || 'Could not initiate withdrawal.', type: 'error' });
            }
        } catch (err) {
            toaster.create({
                title: 'Error',
                description: err?.response?.data?.message || err.message || 'Server error processing withdrawal.',
                type: 'error',
            });
        } finally {
            setSubmitting(false);
        }
    };

    const pendingWithdrawals = withdrawals.filter((w) => w.status === 'Pending' || w.status === 'pending');
    const totalWithdrawn = withdrawals
        .filter((w) => w.status === 'Approved' || w.status === 'completed')
        .reduce((sum, w) => sum + parseFloat(w.amount || 0), 0);

    return (
        <Box>
            <PageHeader
                title="Withdraw Funds"
                subtitle="Direct bank transfers and mobile payouts (bKash & Nagad) with zero hidden fees."
            />

            {/* Metrics */}
            <Grid templateColumns={{ base: '1fr', sm: 'repeat(3, 1fr)' }} gap={4} mb={8}>
                <StatCard
                    label="Available for Payout"
                    value={authService.formatCurrency(walletBalance, currency)}
                    helpText="Funds not locked in escrow"
                    icon={WalletIcon}
                    colorScheme="emerald"
                />
                <StatCard
                    label="Lifetime Payouts"
                    value={authService.formatCurrency(totalWithdrawn, currency)}
                    helpText="Completed disbursements"
                    icon={WithdrawalIcon}
                    colorScheme="blue"
                />
                <StatCard
                    label="Pending Withdrawals"
                    value={`${pendingWithdrawals.length} Requests`}
                    helpText="Processing in payout queue"
                    icon={WithdrawalIcon}
                    colorScheme="amber"
                />
            </Grid>

            {/* Main Form & Methods */}
            <Grid templateColumns={{ base: '1fr', lg: '1.8fr 1.2fr' }} gap={6} mb={8}>
                {/* Form */}
                <DashboardCard title="Request Payout" subtitle="Select your recipient destination and amount.">
                    <form onSubmit={handleSubmitWithdrawal}>
                        <VStack gap={4} align="stretch">
                            {/* Saved Methods Selector */}
                            <Box>
                                <Flex justify="space-between" align="center" mb={2}>
                                    <Text fontSize="xs" fontWeight="700" color="#0A1128">
                                        Select Payout Method *
                                    </Text>
                                    <Button
                                        size="xs"
                                        variant="outline"
                                        color="#1769FF"
                                        borderColor="#1769FF"
                                        borderRadius="lg"
                                        onClick={() => setShowMethodModal(true)}
                                    >
                                        <PlusIcon size={12} style={{ marginRight: '4px' }} />
                                        Add New Account
                                    </Button>
                                </Flex>

                                {methods.length === 0 ? (
                                    <Box p={4} bg="gray.50" borderRadius="xl" textAlign="center" borderWidth="1px" borderColor="gray.200">
                                        <Text fontSize="xs" color="gray.500" mb={2}>
                                            No payout methods saved. Add a bank or mobile account to proceed.
                                        </Text>
                                        <Button size="xs" bg="#002147" color="white" onClick={() => setShowMethodModal(true)}>
                                            Add Bank / Mobile Account
                                        </Button>
                                    </Box>
                                ) : (
                                    <VStack gap={2} align="stretch">
                                        {methods.map((m) => {
                                            const isSel = selectedMethodId === m.id;
                                            return (
                                                <Flex
                                                    key={m.id}
                                                    p={3}
                                                    borderRadius="xl"
                                                    borderWidth="1px"
                                                    borderColor={isSel ? '#1769FF' : 'gray.200'}
                                                    bg={isSel ? '#EEF6FF' : 'white'}
                                                    align="center"
                                                    justify="space-between"
                                                    cursor="pointer"
                                                    onClick={() => setSelectedMethodId(m.id)}
                                                >
                                                    <Box>
                                                        <Text fontSize="xs" fontWeight="800" color="#0A1128">
                                                            {m.name || m.bank_name || 'Payout Account'}
                                                        </Text>
                                                        <Text fontSize="2xs" color="gray.500">
                                                            A/C: {m.account_number} • {m.account_name}
                                                        </Text>
                                                    </Box>
                                                    <Button
                                                        size="2xs"
                                                        variant="ghost"
                                                        color="red.400"
                                                        onClick={(e) => {
                                                            e.stopPropagation();
                                                            handleDeleteMethod(m.id);
                                                        }}
                                                    >
                                                        Remove
                                                    </Button>
                                                </Flex>
                                            );
                                        })}
                                    </VStack>
                                )}
                            </Box>

                            {/* Amount */}
                            <Box>
                                <Text fontSize="xs" fontWeight="700" color="#0A1128" mb={1}>
                                    Withdrawal Amount (BDT ৳) *
                                </Text>
                                <input
                                    type="number"
                                    min="50"
                                    step="0.01"
                                    max={walletBalance}
                                    value={amount}
                                    onChange={(e) => setAmount(e.target.value)}
                                    placeholder="5000.00"
                                    style={{
                                        width: '100%',
                                        padding: '10px 14px',
                                        borderRadius: '8px',
                                        border: '1px solid #E2E8F0',
                                        fontSize: '13px',
                                        fontWeight: '700',
                                        outline: 'none',
                                    }}
                                    required
                                />
                                <Text fontSize="3xs" color="gray.500" mt={1}>
                                    Available for withdrawal: {authService.formatCurrency(walletBalance, currency)}
                                </Text>
                            </Box>

                            {/* Payout Calculation Card */}
                            <Box p={3} bg="gray.50" borderRadius="xl" borderWidth="1px" borderColor="gray.200">
                                <VStack gap={1.5} align="stretch" fontSize="xs">
                                    <Flex justify="space-between">
                                        <Text color="gray.500">Gross Amount:</Text>
                                        <Text fontWeight="700">{authService.formatCurrency(numAmount, currency)}</Text>
                                    </Flex>
                                    <Flex justify="space-between">
                                        <Text color="gray.500">Processing Fee ({isMobileMethod ? '1.5% Mobile Cashout' : '0% Bank Transfer'}):</Text>
                                        <Text fontWeight="700" color="#DC2626">- {authService.formatCurrency(estimatedFee, currency)}</Text>
                                    </Flex>
                                    <Box h="1px" bg="gray.200" my={1} />
                                    <Flex justify="space-between" fontSize="sm" fontWeight="800" color="#006A4E">
                                        <Text>Net Received Payout:</Text>
                                        <Text>{authService.formatCurrency(netPayout, currency)}</Text>
                                    </Flex>
                                </VStack>
                            </Box>

                            <Button
                                type="submit"
                                size="lg"
                                bg="#002147"
                                color="white"
                                _hover={{ bg: '#0A1128' }}
                                loading={submitting}
                                borderRadius="xl"
                                fontWeight="800"
                                fontSize="sm"
                                disabled={methods.length === 0 || numAmount <= 0 || numAmount > walletBalance}
                            >
                                <WithdrawalIcon size={18} style={{ marginRight: '8px' }} />
                                Authorize Withdrawal Request
                            </Button>
                        </VStack>
                    </form>
                </DashboardCard>

                {/* Right: Policy & Guidelines */}
                <VStack gap={4} align="stretch">
                    <DashboardCard title="Payout Timelines & Limits" subtitle="Security protocols for outward disbursements.">
                        <VStack gap={3} align="stretch" fontSize="xs" color="gray.600">
                            <Flex gap={2}>
                                <CheckCircleIcon size={16} color="#006A4E" />
                                <Text>Mobile payouts (bKash/Nagad) disburse within 1 to 4 hours on business days.</Text>
                            </Flex>
                            <Flex gap={2}>
                                <CheckCircleIcon size={16} color="#006A4E" />
                                <Text>BEFTN Bank Transfers settle within 24 business hours.</Text>
                            </Flex>
                            <Flex gap={2}>
                                <CheckCircleIcon size={16} color="#006A4E" />
                                <Text>All payouts are debit-locked atomically against the database ledger.</Text>
                            </Flex>
                        </VStack>
                    </DashboardCard>
                </VStack>
            </Grid>

            {/* History Table */}
            <DashboardCard title="Withdrawal History" subtitle="Audit trail of previous payout disbursements.">
                {withdrawals.length === 0 ? (
                    <EmptyState
                        icon={WithdrawalIcon}
                        title="No withdrawals requested yet"
                        description="Your withdrawal disbursement history will be recorded here."
                        minH="180px"
                    />
                ) : (
                    <Box overflowX="auto">
                        <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: '13px' }}>
                            <thead>
                                <tr style={{ borderBottom: '1px solid #E2E8F0', textAlign: 'left', color: '#64748B' }}>
                                    <th style={{ padding: '10px 12px' }}>Reference</th>
                                    <th style={{ padding: '10px 12px' }}>Method</th>
                                    <th style={{ padding: '10px 12px' }}>Gross</th>
                                    <th style={{ padding: '10px 12px' }}>Fee</th>
                                    <th style={{ padding: '10px 12px' }}>Net Payout</th>
                                    <th style={{ padding: '10px 12px' }}>Status</th>
                                    <th style={{ padding: '10px 12px' }}>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                {withdrawals.map((w) => {
                                    const st = w.status?.toLowerCase();
                                    const badgePalette = st === 'approved' || st === 'completed' ? 'green' : st === 'rejected' ? 'red' : 'yellow';

                                    return (
                                        <tr key={w.id} style={{ borderBottom: '1px solid #F1F5F9' }}>
                                            <td style={{ padding: '12px', fontWeight: '700', fontFamily: 'mono' }}>
                                                {w.reference_id || `WTH-${w.id}`}
                                            </td>
                                            <td style={{ padding: '12px' }}>{w.method_name || 'Bank/Mobile'}</td>
                                            <td style={{ padding: '12px', fontWeight: '700' }}>
                                                {authService.formatCurrency(w.amount, currency)}
                                            </td>
                                            <td style={{ padding: '12px', color: '#DC2626' }}>
                                                {authService.formatCurrency(w.fee || 0, currency)}
                                            </td>
                                            <td style={{ padding: '12px', fontWeight: '800', color: '#006A4E' }}>
                                                {authService.formatCurrency(w.net_amount || w.amount, currency)}
                                            </td>
                                            <td style={{ padding: '12px' }}>
                                                <Badge colorPalette={badgePalette} variant="subtle" fontSize="2xs" px={2} py={0.5} borderRadius="md">
                                                    {w.status}
                                                </Badge>
                                            </td>
                                            <td style={{ padding: '12px', color: '#64748B', fontSize: '12px' }}>
                                                {new Date(w.created_at).toLocaleDateString()}
                                            </td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                    </Box>
                )}
            </DashboardCard>

            {/* Add Method Modal */}
            {showMethodModal && (
                <Box
                    position="fixed"
                    top={0}
                    left={0}
                    w="100vw"
                    h="100vh"
                    bg="blackAlpha.600"
                    zIndex={200}
                    display="flex"
                    alignItems="center"
                    justifyContent="center"
                    p={4}
                >
                    <Box bg="white" borderRadius="2xl" maxW="480px" w="100%" p={6} shadow="2xl">
                        <Flex justify="space-between" align="center" mb={4}>
                            <Heading size="md" color="#0A1128">Add Payout Account</Heading>
                            <Button size="2xs" variant="ghost" onClick={() => setShowMethodModal(false)}>✕</Button>
                        </Flex>

                        <form onSubmit={handleCreateMethod}>
                            <VStack gap={3} align="stretch">
                                <HStack gap={2}>
                                    <Button
                                        type="button"
                                        size="xs"
                                        flex={1}
                                        bg={newMethod.type === 'mobile' ? '#002147' : 'gray.100'}
                                        color={newMethod.type === 'mobile' ? 'white' : 'gray.700'}
                                        onClick={() => setNewMethod({ ...newMethod, type: 'mobile' })}
                                    >
                                        Mobile Banking
                                    </Button>
                                    <Button
                                        type="button"
                                        size="xs"
                                        flex={1}
                                        bg={newMethod.type === 'bank' ? '#002147' : 'gray.100'}
                                        color={newMethod.type === 'bank' ? 'white' : 'gray.700'}
                                        onClick={() => setNewMethod({ ...newMethod, type: 'bank' })}
                                    >
                                        Bank Account
                                    </Button>
                                </HStack>

                                {newMethod.type === 'mobile' ? (
                                    <>
                                        <Box>
                                            <Text fontSize="2xs" fontWeight="700">Provider</Text>
                                            <select
                                                value={newMethod.mobile_provider}
                                                onChange={(e) => setNewMethod({ ...newMethod, mobile_provider: e.target.value })}
                                                style={{ width: '100%', padding: '8px', border: '1px solid #CBD5E1', borderRadius: '6px' }}
                                            >
                                                <option value="bKash">bKash Personal</option>
                                                <option value="Nagad">Nagad Personal</option>
                                                <option value="Rocket">Rocket Personal</option>
                                            </select>
                                        </Box>
                                        <Box>
                                            <Text fontSize="2xs" fontWeight="700">Mobile Account Number *</Text>
                                            <input
                                                type="text"
                                                placeholder="017xxxxxxxx"
                                                value={newMethod.mobile_number}
                                                onChange={(e) => setNewMethod({ ...newMethod, mobile_number: e.target.value })}
                                                style={{ width: '100%', padding: '8px', border: '1px solid #CBD5E1', borderRadius: '6px' }}
                                                required
                                            />
                                        </Box>
                                    </>
                                ) : (
                                    <>
                                        <Box>
                                            <Text fontSize="2xs" fontWeight="700">Bank Name *</Text>
                                            <input
                                                type="text"
                                                placeholder="e.g. Dutch-Bangla Bank, City Bank"
                                                value={newMethod.bank_name}
                                                onChange={(e) => setNewMethod({ ...newMethod, bank_name: e.target.value })}
                                                style={{ width: '100%', padding: '8px', border: '1px solid #CBD5E1', borderRadius: '6px' }}
                                                required
                                            />
                                        </Box>
                                        <Box>
                                            <Text fontSize="2xs" fontWeight="700">Account Name *</Text>
                                            <input
                                                type="text"
                                                placeholder="Account Holder Name"
                                                value={newMethod.account_name}
                                                onChange={(e) => setNewMethod({ ...newMethod, account_name: e.target.value })}
                                                style={{ width: '100%', padding: '8px', border: '1px solid #CBD5E1', borderRadius: '6px' }}
                                                required
                                            />
                                        </Box>
                                        <Box>
                                            <Text fontSize="2xs" fontWeight="700">Account Number *</Text>
                                            <input
                                                type="text"
                                                placeholder="Account Number"
                                                value={newMethod.account_number}
                                                onChange={(e) => setNewMethod({ ...newMethod, account_number: e.target.value })}
                                                style={{ width: '100%', padding: '8px', border: '1px solid #CBD5E1', borderRadius: '6px' }}
                                                required
                                            />
                                        </Box>
                                        <Box>
                                            <Text fontSize="2xs" fontWeight="700">Branch Name</Text>
                                            <input
                                                type="text"
                                                placeholder="e.g. Dhanmondi Branch"
                                                value={newMethod.branch}
                                                onChange={(e) => setNewMethod({ ...newMethod, branch: e.target.value })}
                                                style={{ width: '100%', padding: '8px', border: '1px solid #CBD5E1', borderRadius: '6px' }}
                                            />
                                        </Box>
                                    </>
                                )}

                                <HStack justify="flex-end" gap={2} mt={3}>
                                    <Button size="xs" variant="outline" onClick={() => setShowMethodModal(false)}>Cancel</Button>
                                    <Button size="xs" bg="#002147" color="white" type="submit" loading={savingMethod}>Save Account</Button>
                                </HStack>
                            </VStack>
                        </form>
                    </Box>
                </Box>
            )}
        </Box>
    );
};

export default WithdrawalModule;

