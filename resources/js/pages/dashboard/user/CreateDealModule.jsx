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
} from '@chakra-ui/react';
import { useAuth } from '../../../hooks/useAuth';
import { dealsApi } from '../../../services/dashboardApi';
import authService from '../../../services/auth';
import { PageHeader } from '../../../components/common/PageHeader';
import { DashboardCard } from '../../../components/common/DashboardCard';
import { toaster } from '../../../components/feedback/toaster';
import {
    DealsIcon,
    WalletIcon,
    CheckCircleIcon,
    AlertCircleIcon,
    ArrowUpRightIcon,
} from '../../../components/common/Icons';

export const CreateDealModule = ({ onNavigate }) => {
    const { user, currency, refreshUser } = useAuth();
    const [categories, setCategories] = useState([]);
    const [submitting, setSubmitting] = useState(false);
    const [calculating, setCalculating] = useState(false);

    const [form, setForm] = useState({
        title: '',
        role: 'buyer', // 'buyer' or 'seller'
        category_id: '',
        custom_category: '',
        amount: '',
        counterparty_email: '',
        charge_payer: 'shared_50', // 'shared_50' | 'buyer_100' | 'seller_100'
        deadline: '',
        coupon_code: '',
        description: '',
    });

    const [chargePreview, setChargePreview] = useState(null);

    // Fetch active categories
    useEffect(() => {
        const fetchCategories = async () => {
            try {
                const data = await dealsApi.getCategories();
                if (data.success && Array.isArray(data.categories)) {
                    setCategories(data.categories.filter((c) => c.is_active));
                }
            } catch (e) {
                console.error('Error fetching categories:', e);
            }
        };
        fetchCategories();
        refreshUser();
    }, []);

    // Live Fee Calculation
    const calculateFee = async (codeToUse = null) => {
        const amt = parseFloat(form.amount) || 0;
        if (amt <= 0) {
            setChargePreview(null);
            return;
        }

        setCalculating(true);
        try {
            const params = {
                amount: amt,
                charge_payer: form.charge_payer,
            };
            if (form.category_id) params.category_id = form.category_id;
            const cCode = codeToUse !== null ? codeToUse : form.coupon_code;
            if (cCode) params.coupon_code = cCode.trim();

            const res = await dealsApi.calculateCharge(params);
            if (res.success && res.data) {
                setChargePreview(res.data);
            }
        } catch (e) {
            console.error('Error calculating charge:', e);
        } finally {
            setCalculating(false);
        }
    };

    useEffect(() => {
        const timer = setTimeout(() => {
            if (form.amount > 0) {
                calculateFee();
            }
        }, 400);
        return () => clearTimeout(timer);
    }, [form.amount, form.charge_payer, form.category_id]);

    const walletBalance = parseFloat(user?.wallet_balance || 0);
    const dealAmount = parseFloat(form.amount) || 0;
    const buyerPays = chargePreview ? parseFloat(chargePreview.buyer_pays || 0) : dealAmount;
    const isBuyer = form.role === 'buyer';
    const hasInsufficientBalance = isBuyer && walletBalance < buyerPays;

    const handleSubmit = async (e) => {
        e.preventDefault();

        if (!form.title.trim()) {
            toaster.create({ title: 'Validation Error', description: 'Please enter a deal title.', type: 'error' });
            return;
        }
        if (!dealAmount || dealAmount <= 0) {
            toaster.create({ title: 'Validation Error', description: 'Please specify a valid deal amount.', type: 'error' });
            return;
        }
        if (!form.counterparty_email.trim()) {
            toaster.create({ title: 'Validation Error', description: 'Counterparty email is required.', type: 'error' });
            return;
        }
        if (form.counterparty_email.toLowerCase() === (user?.email || '').toLowerCase()) {
            toaster.create({ title: 'Validation Error', description: 'You cannot initiate a deal with your own email.', type: 'error' });
            return;
        }
        if (hasInsufficientBalance) {
            toaster.create({
                title: 'Insufficient Balance',
                description: `You need ${authService.formatCurrency(buyerPays, currency)} to lock escrow. Current balance: ${authService.formatCurrency(walletBalance, currency)}.`,
                type: 'error',
            });
            return;
        }

        setSubmitting(true);
        try {
            const payload = {
                title: form.title,
                role: form.role,
                amount: dealAmount,
                email: form.counterparty_email.trim().toLowerCase(),
                category_id: form.category_id || null,
                custom_category: form.custom_category || null,
                chargePayer: form.charge_payer,
                deadline: form.deadline || null,
                coupon_code: form.coupon_code || null,
                description: form.description || null,
            };

            const data = await dealsApi.create(payload);

            if (data.success) {
                toaster.create({
                    title: 'Escrow Deal Initiated!',
                    description: `Deal #${data.deal?.deal_id || ''} created. Funds secured in mediator escrow.`,
                    type: 'success',
                });
                refreshUser();
                onNavigate?.('Projects');
            } else {
                toaster.create({
                    title: 'Creation Failed',
                    description: data.message || 'Could not initiate escrow deal.',
                    type: 'error',
                });
            }
        } catch (err) {
            toaster.create({
                title: 'Deal Error',
                description: err?.response?.data?.message || err.message || 'An unexpected error occurred.',
                type: 'error',
            });
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <Box>
            <PageHeader
                title="Initiate Escrow Deal"
                subtitle="Create a secure, mediated transaction protected by MigratePay escrow."
                badge={
                    <Badge colorPalette="blue" variant="subtle" fontSize="xs" px={2.5} py={1} borderRadius="md">
                        Pessimistic Row Lock Protection
                    </Badge>
                }
            />

            <Grid templateColumns={{ base: '1fr', lg: '1.8fr 1.2fr' }} gap={6}>
                {/* Left: Deal Initiation Form */}
                <DashboardCard title="Agreement Parameters" subtitle="Define the terms for you and your counterparty.">
                    <form onSubmit={handleSubmit}>
                        <VStack gap={4} align="stretch">
                            {/* Role Selection */}
                            <Box>
                                <Text fontSize="xs" fontWeight="700" color="#0A1128" mb={2}>
                                    Your Role in this Deal *
                                </Text>
                                <HStack gap={3}>
                                    <Button
                                        type="button"
                                        size="sm"
                                        flex={1}
                                        bg={form.role === 'buyer' ? '#002147' : 'gray.50'}
                                        color={form.role === 'buyer' ? 'white' : 'gray.700'}
                                        borderWidth="1px"
                                        borderColor={form.role === 'buyer' ? '#002147' : 'gray.200'}
                                        borderRadius="xl"
                                        py={5}
                                        onClick={() => setForm({ ...form, role: 'buyer' })}
                                    >
                                        <VStack gap={0}>
                                            <Text fontSize="xs" fontWeight="800">I am Buying</Text>
                                            <Text fontSize="3xs" opacity={0.8}>I will deposit & fund the escrow</Text>
                                        </VStack>
                                    </Button>
                                    <Button
                                        type="button"
                                        size="sm"
                                        flex={1}
                                        bg={form.role === 'seller' ? '#002147' : 'gray.50'}
                                        color={form.role === 'seller' ? 'white' : 'gray.700'}
                                        borderWidth="1px"
                                        borderColor={form.role === 'seller' ? '#002147' : 'gray.200'}
                                        borderRadius="xl"
                                        py={5}
                                        onClick={() => setForm({ ...form, role: 'seller' })}
                                    >
                                        <VStack gap={0}>
                                            <Text fontSize="xs" fontWeight="800">I am Selling</Text>
                                            <Text fontSize="3xs" opacity={0.8}>I will provide goods or services</Text>
                                        </VStack>
                                    </Button>
                                </HStack>
                            </Box>

                            {/* Deal Title */}
                            <Box>
                                <Text fontSize="xs" fontWeight="700" color="#0A1128" mb={1}>
                                    Deal / Project Title *
                                </Text>
                                <input
                                    type="text"
                                    value={form.title}
                                    onChange={(e) => setForm({ ...form, title: e.target.value })}
                                    placeholder="e.g. Full-Stack Web Development, iPhone 15 Pro Purchase"
                                    style={{
                                        width: '100%',
                                        padding: '10px 14px',
                                        borderRadius: '8px',
                                        border: '1px solid #E2E8F0',
                                        fontSize: '13px',
                                        outline: 'none',
                                    }}
                                    required
                                />
                            </Box>

                            {/* Category & Amount */}
                            <Grid templateColumns={{ base: '1fr', sm: '1fr 1fr' }} gap={4}>
                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="#0A1128" mb={1}>
                                        Category
                                    </Text>
                                    <select
                                        value={form.category_id}
                                        onChange={(e) => setForm({ ...form, category_id: e.target.value })}
                                        style={{
                                            width: '100%',
                                            padding: '10px 12px',
                                            borderRadius: '8px',
                                            border: '1px solid #E2E8F0',
                                            fontSize: '13px',
                                            outline: 'none',
                                            background: 'white',
                                        }}
                                    >
                                        <option value="">Select Category (Optional)</option>
                                        {categories.map((c) => (
                                            <option key={c.id} value={c.id}>{c.name}</option>
                                        ))}
                                    </select>
                                </Box>

                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="#0A1128" mb={1}>
                                        Deal Value (BDT ৳) *
                                    </Text>
                                    <input
                                        type="number"
                                        min="1"
                                        step="0.01"
                                        value={form.amount}
                                        onChange={(e) => setForm({ ...form, amount: e.target.value })}
                                        placeholder="0.00"
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
                            </Grid>

                            {/* Counterparty Email */}
                            <Box>
                                <Text fontSize="xs" fontWeight="700" color="#0A1128" mb={1}>
                                    Counterparty Registered Email *
                                </Text>
                                <input
                                    type="email"
                                    value={form.counterparty_email}
                                    onChange={(e) => setForm({ ...form, counterparty_email: e.target.value })}
                                    placeholder="partner@example.com"
                                    style={{
                                        width: '100%',
                                        padding: '10px 14px',
                                        borderRadius: '8px',
                                        border: '1px solid #E2E8F0',
                                        fontSize: '13px',
                                        outline: 'none',
                                    }}
                                    required
                                />
                                <Text fontSize="3xs" color="gray.500" mt={1}>
                                    They will receive an invitation to inspect and accept the escrow terms.
                                </Text>
                            </Box>

                            {/* Commission Split */}
                            <Box>
                                <Text fontSize="xs" fontWeight="700" color="#0A1128" mb={2}>
                                    Who Pays the Protection Fee?
                                </Text>
                                <HStack gap={2} wrap="wrap">
                                    {[
                                        { id: 'shared_50', label: 'Split 50/50' },
                                        { id: 'buyer_100', label: 'Buyer Pays 100%' },
                                        { id: 'seller_100', label: 'Seller Pays 100%' },
                                    ].map((opt) => (
                                        <Button
                                            key={opt.id}
                                            type="button"
                                            size="xs"
                                            variant={form.charge_payer === opt.id ? 'solid' : 'outline'}
                                            bg={form.charge_payer === opt.id ? '#1769FF' : 'transparent'}
                                            color={form.charge_payer === opt.id ? 'white' : 'gray.700'}
                                            borderColor={form.charge_payer === opt.id ? '#1769FF' : 'gray.200'}
                                            borderRadius="lg"
                                            px={3}
                                            onClick={() => setForm({ ...form, charge_payer: opt.id })}
                                        >
                                            {opt.label}
                                        </Button>
                                    ))}
                                </HStack>
                            </Box>

                            {/* Deadline & Coupon */}
                            <Grid templateColumns={{ base: '1fr', sm: '1fr 1fr' }} gap={4}>
                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="#0A1128" mb={1}>
                                        Deadline (Optional)
                                    </Text>
                                    <input
                                        type="date"
                                        value={form.deadline}
                                        onChange={(e) => setForm({ ...form, deadline: e.target.value })}
                                        style={{
                                            width: '100%',
                                            padding: '9px 12px',
                                            borderRadius: '8px',
                                            border: '1px solid #E2E8F0',
                                            fontSize: '13px',
                                            outline: 'none',
                                            background: 'white',
                                        }}
                                    />
                                </Box>

                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="#0A1128" mb={1}>
                                        Promo Coupon Code
                                    </Text>
                                    <HStack gap={2}>
                                        <input
                                            type="text"
                                            value={form.coupon_code}
                                            onChange={(e) => setForm({ ...form, coupon_code: e.target.value.toUpperCase() })}
                                            placeholder="PROMO2026"
                                            style={{
                                                flex: 1,
                                                padding: '9px 12px',
                                                borderRadius: '8px',
                                                border: '1px solid #E2E8F0',
                                                fontSize: '13px',
                                                textTransform: 'uppercase',
                                                outline: 'none',
                                            }}
                                        />
                                        <Button
                                            type="button"
                                            size="sm"
                                            variant="outline"
                                            onClick={() => calculateFee(form.coupon_code)}
                                            loading={calculating}
                                            borderRadius="lg"
                                        >
                                            Apply
                                        </Button>
                                    </HStack>
                                </Box>
                            </Grid>

                            {/* Description */}
                            <Box>
                                <Text fontSize="xs" fontWeight="700" color="#0A1128" mb={1}>
                                    Deliverable Specifications / Terms
                                </Text>
                                <textarea
                                    rows={3}
                                    value={form.description}
                                    onChange={(e) => setForm({ ...form, description: e.target.value })}
                                    placeholder="Outline the scope of work, deliverable milestones, inspection conditions..."
                                    style={{
                                        width: '100%',
                                        padding: '10px 14px',
                                        borderRadius: '8px',
                                        border: '1px solid #E2E8F0',
                                        fontSize: '13px',
                                        outline: 'none',
                                        fontFamily: 'inherit',
                                    }}
                                />
                            </Box>

                            {/* Submit Button */}
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
                                mt={2}
                            >
                                <DealsIcon size={18} style={{ marginRight: '8px' }} />
                                Lock Escrow Agreement
                            </Button>
                        </VStack>
                    </form>
                </DashboardCard>

                {/* Right: Live Fee Calculation & Wallet Guard */}
                <VStack gap={6} align="stretch">
                    {/* Live Financial Ledger Preview */}
                    <DashboardCard title="Escrow Financial Breakdown" subtitle="Authoritative server-side calculation.">
                        <VStack gap={3} align="stretch">
                            <Flex justify="space-between" fontSize="sm">
                                <Text color="gray.600">Base Deal Value:</Text>
                                <Text fontWeight="700" color="#0A1128">
                                    {authService.formatCurrency(dealAmount, currency)}
                                </Text>
                            </Flex>

                            <Flex justify="space-between" fontSize="sm">
                                <Text color="gray.600">MigratePay Escrow Fee:</Text>
                                <Text fontWeight="700" color="#1769FF">
                                    {authService.formatCurrency(chargePreview?.total_charge || 0, currency)}
                                </Text>
                            </Flex>

                            {chargePreview?.discount > 0 && (
                                <Flex justify="space-between" fontSize="sm" color="#006A4E">
                                    <Text>Coupon Discount:</Text>
                                    <Text fontWeight="700">- {authService.formatCurrency(chargePreview.discount, currency)}</Text>
                                </Flex>
                            )}

                            <Box h="1px" bg="gray.200" my={1} />

                            <Flex justify="space-between" fontSize="sm" bg="blue.50" p={2.5} borderRadius="lg">
                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="#0A1128">Buyer Total Escrow</Text>
                                    <Text fontSize="3xs" color="gray.500">Amount deducted from Buyer</Text>
                                </Box>
                                <Text fontSize="md" fontWeight="800" color="#1769FF">
                                    {authService.formatCurrency(chargePreview?.buyer_pays || dealAmount, currency)}
                                </Text>
                            </Flex>

                            <Flex justify="space-between" fontSize="sm" bg="green.50" p={2.5} borderRadius="lg">
                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="#006A4E">Seller Net Payout</Text>
                                    <Text fontSize="3xs" color="gray.500">Amount credited upon completion</Text>
                                </Box>
                                <Text fontSize="md" fontWeight="800" color="#006A4E">
                                    {authService.formatCurrency(chargePreview?.seller_gets || dealAmount, currency)}
                                </Text>
                            </Flex>
                        </VStack>
                    </DashboardCard>

                    {/* Escrow Wallet Protection Banner */}
                    <DashboardCard title="Wallet Escrow Safeguard" subtitle="Real-time balance verification.">
                        <VStack gap={3} align="stretch">
                            <Flex justify="space-between" align="center">
                                <Text fontSize="xs" color="gray.600">Available Wallet Balance:</Text>
                                <Text fontSize="sm" fontWeight="800" color="#006A4E">
                                    {authService.formatCurrency(walletBalance, currency)}
                                </Text>
                            </Flex>

                            {isBuyer && (
                                hasInsufficientBalance ? (
                                    <Box p={3} bg="red.50" borderWidth="1px" borderColor="red.200" borderRadius="xl">
                                        <HStack gap={2} mb={2}>
                                            <AlertCircleIcon size={18} color="#DC2626" />
                                            <Text fontSize="xs" fontWeight="700" color="#DC2626">
                                                Insufficient Escrow Balance
                                            </Text>
                                        </HStack>
                                        <Text fontSize="2xs" color="red.700" mb={3}>
                                            You need an additional {authService.formatCurrency(buyerPays - walletBalance, currency)} in your wallet to initiate this deal.
                                        </Text>
                                        <Button
                                            size="xs"
                                            bg="#DC2626"
                                            color="white"
                                            _hover={{ bg: '#B91C1C' }}
                                            borderRadius="lg"
                                            onClick={() => onNavigate?.('Deposit')}
                                        >
                                            Deposit Funds Now <ArrowUpRightIcon size={12} style={{ marginLeft: '4px' }} />
                                        </Button>
                                    </Box>
                                ) : (
                                    <Box p={3} bg="green.50" borderWidth="1px" borderColor="green.200" borderRadius="xl">
                                        <HStack gap={2}>
                                            <CheckCircleIcon size={18} color="#10B981" />
                                            <Text fontSize="xs" fontWeight="700" color="#006A4E">
                                                Wallet Balance Verified
                                            </Text>
                                        </HStack>
                                        <Text fontSize="2xs" color="green.700" mt={1}>
                                            Your wallet has sufficient funds to immediately lock escrow upon creation.
                                        </Text>
                                    </Box>
                                )
                            )}
                        </VStack>
                    </DashboardCard>
                </VStack>
            </Grid>
        </Box>
    );
};

export default CreateDealModule;

