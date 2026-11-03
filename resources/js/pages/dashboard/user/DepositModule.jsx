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
    DepositIcon,
    WalletIcon,
    CheckCircleIcon,
    AlertCircleIcon,
    ArrowUpRightIcon,
} from '../../../components/common/Icons';

export const DepositModule = () => {
    const { user, currency, refreshUser } = useAuth();
    const [deposits, setDeposits] = useState([]);
    const [gateways, setGateways] = useState([]);
    const [loading, setLoading] = useState(true);
    const [submitting, setSubmitting] = useState(false);
    const [uploadingImage, setUploadingImage] = useState(false);

    // Form State
    const [selectedGateway, setSelectedGateway] = useState(null);
    const [amount, setAmount] = useState('');
    const [trxId, setTrxId] = useState('');
    const [notes, setNotes] = useState('');
    const [screenshotFile, setScreenshotFile] = useState(null);
    const [screenshotPath, setScreenshotPath] = useState('');

    const loadData = async () => {
        try {
            const [depData, gwData] = await Promise.all([
                financeApi.getDeposits().catch(() => ({ deposits: [] })),
                financeApi.getGateways().catch(() => []),
            ]);

            if (depData.success) {
                setDeposits(depData.deposits || []);
            }
            if (Array.isArray(gwData)) {
                const active = gwData.filter((g) => g.isEnabled);
                setGateways(active);
                if (active.length > 0 && !selectedGateway) {
                    setSelectedGateway(active[0]);
                }
            }
        } catch (e) {
            console.error('Error loading deposit data:', e);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        loadData();
    }, []);

    const handleScreenshotChange = async (e) => {
        const file = e.target.files[0];
        if (!file) return;

        setScreenshotFile(file);
        setUploadingImage(true);

        try {
            const formData = new FormData();
            formData.append('screenshot', file);

            const res = await financeApi.uploadScreenshot(formData);
            if (res.success && res.path) {
                setScreenshotPath(res.path);
                toaster.create({
                    title: 'Receipt Uploaded',
                    description: 'Screenshot stored securely in private vault.',
                    type: 'success',
                });
            }
        } catch (err) {
            toaster.create({
                title: 'Upload Failed',
                description: err?.response?.data?.message || err.message || 'Error uploading receipt image.',
                type: 'error',
            });
        } finally {
            setUploadingImage(false);
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        const numAmount = parseFloat(amount) || 0;
        if (numAmount <= 0) {
            toaster.create({ title: 'Invalid Amount', description: 'Please specify a valid deposit amount.', type: 'error' });
            return;
        }
        if (!selectedGateway) {
            toaster.create({ title: 'Select Gateway', description: 'Please choose a deposit method.', type: 'error' });
            return;
        }

        setSubmitting(true);
        try {
            const payload = {
                gateway: selectedGateway.id || selectedGateway.name,
                gateway_title: selectedGateway.name || selectedGateway.title,
                amount: numAmount,
                transaction_id: trxId.trim() || `MAN-${Date.now()}`,
                screenshot_path: screenshotPath || null,
                notes: notes.trim() || null,
            };

            const data = await financeApi.submitDeposit(payload);

            if (data.success) {
                toaster.create({
                    title: 'Deposit Submitted!',
                    description: 'Our verification team has received your request and will approve it shortly.',
                    type: 'success',
                });
                setAmount('');
                setTrxId('');
                setNotes('');
                setScreenshotFile(null);
                setScreenshotPath('');
                refreshUser();
                loadData();
            } else {
                toaster.create({ title: 'Error', description: data.message || 'Deposit submission failed.', type: 'error' });
            }
        } catch (err) {
            toaster.create({
                title: 'Deposit Error',
                description: err?.response?.data?.message || err.message || 'Server error submitting deposit.',
                type: 'error',
            });
        } finally {
            setSubmitting(false);
        }
    };

    const walletBalance = parseFloat(user?.wallet_balance || 0);
    const pendingDeposits = deposits.filter((d) => d.status === 'Pending' || d.status === 'pending');
    const totalDeposited = deposits
        .filter((d) => d.status === 'Approved' || d.status === 'approved')
        .reduce((sum, d) => sum + parseFloat(d.amount || 0), 0);

    return (
        <Box>
            <PageHeader
                title="Add Balance to Wallet"
                subtitle="Instant or manual deposits via bKash, Nagad, Rocket, or direct Bank Transfer."
            />

            {/* Metric Row */}
            <Grid templateColumns={{ base: '1fr', sm: 'repeat(3, 1fr)' }} gap={4} mb={8}>
                <StatCard
                    label="Current Wallet Balance"
                    value={authService.formatCurrency(walletBalance, currency)}
                    helpText="Verified by MySQL Ledger"
                    icon={WalletIcon}
                    colorScheme="emerald"
                />
                <StatCard
                    label="Total Approved Deposits"
                    value={authService.formatCurrency(totalDeposited, currency)}
                    helpText="Lifetime ledger credits"
                    icon={DepositIcon}
                    colorScheme="blue"
                />
                <StatCard
                    label="Pending Reviews"
                    value={`${pendingDeposits.length} Requests`}
                    helpText="Currently under verification"
                    icon={DepositIcon}
                    colorScheme="amber"
                />
            </Grid>

            {/* Deposit Request & Gateways */}
            <Grid templateColumns={{ base: '1fr', lg: '1.8fr 1.2fr' }} gap={6} mb={8}>
                {/* Form */}
                <DashboardCard title="New Deposit Submission" subtitle="Complete the transfer, then submit your transaction details below.">
                    <form onSubmit={handleSubmit}>
                        <VStack gap={4} align="stretch">
                            {/* Gateway Selector */}
                            <Box>
                                <Text fontSize="xs" fontWeight="700" color="#0A1128" mb={2}>
                                    Select Payment Method *
                                </Text>
                                <Grid templateColumns={{ base: 'repeat(2, 1fr)', sm: 'repeat(4, 1fr)' }} gap={2}>
                                    {(gateways.length > 0 ? gateways : [
                                        { id: 'bkash', name: 'bKash', number: '01700-000000', type: 'Personal' },
                                        { id: 'nagad', name: 'Nagad', number: '01800-000000', type: 'Merchant' },
                                        { id: 'rocket', name: 'Rocket', number: '01900-000000', type: 'Personal' },
                                        { id: 'bank', name: 'Bank Transfer', number: 'City Bank Ltd', type: 'Bank' },
                                    ]).map((gw) => {
                                        const isSel = selectedGateway?.id === gw.id || selectedGateway?.name === gw.name;
                                        return (
                                            <Button
                                                key={gw.id}
                                                type="button"
                                                size="sm"
                                                variant={isSel ? 'solid' : 'outline'}
                                                bg={isSel ? '#002147' : 'white'}
                                                color={isSel ? 'white' : '#0A1128'}
                                                borderColor={isSel ? '#002147' : 'gray.200'}
                                                borderRadius="xl"
                                                p={3}
                                                h="auto"
                                                onClick={() => setSelectedGateway(gw)}
                                            >
                                                <VStack gap={0.5}>
                                                    <Text fontSize="xs" fontWeight="800">{gw.name || gw.title}</Text>
                                                    <Text fontSize="3xs" opacity={0.8}>{gw.type || 'Manual'}</Text>
                                                </VStack>
                                            </Button>
                                        );
                                    })}
                                </Grid>
                            </Box>

                            {/* Gateway Instructions Display */}
                            {selectedGateway && (
                                <Box p={4} bg="blue.50" borderRadius="xl" borderWidth="1px" borderColor="blue.100">
                                    <Text fontSize="xs" fontWeight="800" color="#002147" mb={1}>
                                        Payment Instructions ({selectedGateway.name || selectedGateway.title})
                                    </Text>
                                    <Text fontSize="2xs" color="gray.700" mb={2}>
                                        {selectedGateway.instruction || `Please Send Money or Transfer to our official ${selectedGateway.name} account:`}
                                    </Text>
                                    <HStack gap={4} p={2.5} bg="white" borderRadius="lg" borderWidth="1px" borderColor="blue.200">
                                        <Box>
                                            <Text fontSize="3xs" color="gray.500" textTransform="uppercase">Account Number / Name</Text>
                                            <Text fontSize="sm" fontWeight="800" color="#002147">
                                                {selectedGateway.accountNumber || selectedGateway.number || '01700-123456'}
                                            </Text>
                                        </Box>
                                        {selectedGateway.branch && (
                                            <Box>
                                                <Text fontSize="3xs" color="gray.500" textTransform="uppercase">Branch</Text>
                                                <Text fontSize="xs" fontWeight="700" color="#0A1128">{selectedGateway.branch}</Text>
                                            </Box>
                                        )}
                                    </HStack>
                                </Box>
                            )}

                            {/* Amount & TrxID */}
                            <Grid templateColumns={{ base: '1fr', sm: '1fr 1fr' }} gap={4}>
                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="#0A1128" mb={1}>
                                        Deposited Amount (BDT ৳) *
                                    </Text>
                                    <input
                                        type="number"
                                        min="10"
                                        step="0.01"
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
                                </Box>

                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="#0A1128" mb={1}>
                                        Transaction ID / TrxID *
                                    </Text>
                                    <input
                                        type="text"
                                        value={trxId}
                                        onChange={(e) => setTrxId(e.target.value)}
                                        placeholder="e.g. 9M8K7L6J5"
                                        style={{
                                            width: '100%',
                                            padding: '10px 14px',
                                            borderRadius: '8px',
                                            border: '1px solid #E2E8F0',
                                            fontSize: '13px',
                                            textTransform: 'uppercase',
                                            outline: 'none',
                                        }}
                                        required
                                    />
                                </Box>
                            </Grid>

                            {/* Screenshot Upload */}
                            <Box>
                                <Text fontSize="xs" fontWeight="700" color="#0A1128" mb={1}>
                                    Payment Receipt / SMS Screenshot (Optional)
                                </Text>
                                <HStack gap={3}>
                                    <input
                                        type="file"
                                        accept="image/*"
                                        onChange={handleScreenshotChange}
                                        style={{ fontSize: '12px' }}
                                    />
                                    {uploadingImage && <Spinner size="xs" color="#1769FF" />}
                                    {screenshotPath && (
                                        <Badge colorPalette="green" fontSize="3xs">Receipt Uploaded</Badge>
                                    )}
                                </HStack>
                            </Box>

                            {/* Notes */}
                            <Box>
                                <Text fontSize="xs" fontWeight="700" color="#0A1128" mb={1}>
                                    Sender Phone / Notes
                                </Text>
                                <input
                                    type="text"
                                    value={notes}
                                    onChange={(e) => setNotes(e.target.value)}
                                    placeholder="e.g. Sent from bKash number 017xxxxxxxx"
                                    style={{
                                        width: '100%',
                                        padding: '9px 14px',
                                        borderRadius: '8px',
                                        border: '1px solid #E2E8F0',
                                        fontSize: '13px',
                                        outline: 'none',
                                    }}
                                />
                            </Box>

                            <Button
                                type="submit"
                                size="lg"
                                bg="#006A4E"
                                color="white"
                                _hover={{ bg: '#00503A' }}
                                loading={submitting}
                                borderRadius="xl"
                                fontWeight="800"
                                fontSize="sm"
                            >
                                <DepositIcon size={18} style={{ marginRight: '8px' }} />
                                Submit Deposit Request
                            </Button>
                        </VStack>
                    </form>
                </DashboardCard>

                {/* Info Card */}
                <VStack gap={4} align="stretch">
                    <DashboardCard title="Deposit Verification Policy" subtitle="How MigratePay protects funds.">
                        <VStack gap={3} align="stretch" fontSize="xs" color="gray.600">
                            <Flex gap={2}>
                                <CheckCircleIcon size={16} color="#006A4E" />
                                <Text>Manual deposits are audited and credited within 10–30 minutes.</Text>
                            </Flex>
                            <Flex gap={2}>
                                <CheckCircleIcon size={16} color="#006A4E" />
                                <Text>All credited funds receive atomic ledger row protection.</Text>
                            </Flex>
                            <Flex gap={2}>
                                <CheckCircleIcon size={16} color="#006A4E" />
                                <Text>Receipt screenshots are stored in private disks protected by policy authorization.</Text>
                            </Flex>
                        </VStack>
                    </DashboardCard>
                </VStack>
            </Grid>

            {/* Deposit History */}
            <DashboardCard title="Deposit History" subtitle="Your recent deposit submissions and verification statuses.">
                {deposits.length === 0 ? (
                    <EmptyState
                        icon={DepositIcon}
                        title="No deposit requests found"
                        description="You haven't submitted any deposit requests yet."
                        minH="180px"
                    />
                ) : (
                    <Box overflowX="auto">
                        <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: '13px' }}>
                            <thead>
                                <tr style={{ borderBottom: '1px solid #E2E8F0', textAlign: 'left', color: '#64748B' }}>
                                    <th style={{ padding: '10px 12px' }}>Trx ID</th>
                                    <th style={{ padding: '10px 12px' }}>Method</th>
                                    <th style={{ padding: '10px 12px' }}>Amount</th>
                                    <th style={{ padding: '10px 12px' }}>Status</th>
                                    <th style={{ padding: '10px 12px' }}>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                {deposits.map((d) => {
                                    const st = d.status?.toLowerCase();
                                    const badgePalette = st === 'approved' ? 'green' : st === 'rejected' ? 'red' : 'yellow';

                                    return (
                                        <tr key={d.id} style={{ borderBottom: '1px solid #F1F5F9' }}>
                                            <td style={{ padding: '12px', fontWeight: '700', fontFamily: 'mono' }}>
                                                {d.transaction_id || `DEP-${d.id}`}
                                            </td>
                                            <td style={{ padding: '12px' }}>{d.gateway_title || d.gateway}</td>
                                            <td style={{ padding: '12px', fontWeight: '800', color: '#006A4E' }}>
                                                {authService.formatCurrency(d.amount, currency)}
                                            </td>
                                            <td style={{ padding: '12px' }}>
                                                <Badge colorPalette={badgePalette} variant="subtle" fontSize="2xs" px={2} py={0.5} borderRadius="md">
                                                    {d.status}
                                                </Badge>
                                            </td>
                                            <td style={{ padding: '12px', color: '#64748B', fontSize: '12px' }}>
                                                {new Date(d.created_at).toLocaleDateString()}
                                            </td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                    </Box>
                )}
            </DashboardCard>
        </Box>
    );
};

export default DepositModule;

