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
    Input,
    Select,
    Spinner,
} from '@chakra-ui/react';
import { adminApi } from '../../../services/adminApi';
import { DashboardCard } from '../../../components/common/DashboardCard';

export const CouponsModule = () => {
    const [coupons, setCoupons] = useState([]);
    const [loading, setLoading] = useState(true);
    const [search, setSearch] = useState('');
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingCoupon, setEditingCoupon] = useState(null);
    const [formData, setFormData] = useState({
        code: '',
        discount_type: 'percentage',
        discount_value: '',
        total_usage_limit: 100,
        usage_per_user: 1,
        expiry_date: '',
        is_active: 1,
    });
    const [submitting, setSubmitting] = useState(false);
    const [feedback, setFeedback] = useState(null);

    const fetchCoupons = async () => {
        setLoading(true);
        try {
            const data = await adminApi.getCoupons();
            if (data && Array.isArray(data.coupons)) {
                setCoupons(data.coupons);
            } else if (Array.isArray(data)) {
                setCoupons(data);
            }
        } catch (err) {
            console.error('Failed to fetch coupons:', err);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchCoupons();
    }, []);

    const openCreateModal = () => {
        setEditingCoupon(null);
        setFormData({
            code: '',
            discount_type: 'percentage',
            discount_value: '10',
            total_usage_limit: 100,
            usage_per_user: 1,
            expiry_date: '',
            is_active: 1,
        });
        setIsModalOpen(true);
    };

    const openEditModal = (c) => {
        setEditingCoupon(c);
        setFormData({
            code: c.code || '',
            discount_type: c.discount_type || 'percentage',
            discount_value: c.discount_value || '',
            total_usage_limit: c.total_usage_limit || 100,
            usage_per_user: c.usage_per_user || 1,
            expiry_date: c.expiry_date ? c.expiry_date.split('T')[0] : '',
            is_active: c.is_active !== undefined ? (c.is_active ? 1 : 0) : 1,
        });
        setIsModalOpen(true);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setSubmitting(true);
        try {
            if (editingCoupon) {
                await adminApi.updateCoupon(editingCoupon.id, formData);
                setFeedback({ type: 'success', text: `Coupon "${formData.code}" updated.` });
            } else {
                await adminApi.createCoupon(formData);
                setFeedback({ type: 'success', text: `Coupon "${formData.code}" created.` });
            }
            setIsModalOpen(false);
            await fetchCoupons();
        } catch (err) {
            setFeedback({
                type: 'error',
                text: err.response?.data?.message || 'Failed to save coupon.',
            });
        } finally {
            setSubmitting(false);
        }
    };

    const handleDelete = async (id, code) => {
        if (!window.confirm(`Delete coupon "${code}"?`)) return;
        try {
            await adminApi.deleteCoupon(id);
            setFeedback({ type: 'success', text: `Coupon "${code}" deleted.` });
            await fetchCoupons();
        } catch (err) {
            setFeedback({
                type: 'error',
                text: err.response?.data?.message || 'Failed to delete coupon.',
            });
        }
    };

    const filtered = coupons.filter((c) =>
        c.code?.toLowerCase().includes(search.toLowerCase())
    );

    return (
        <VStack gap={6} align="stretch" pb={12}>
            {feedback && (
                <Box
                    p={4}
                    borderRadius="xl"
                    bg={feedback.type === 'success' ? '#ECFDF5' : '#FEF2F2'}
                    borderWidth="1px"
                    borderColor={feedback.type === 'success' ? '#10B981' : '#EF4444'}
                    color={feedback.type === 'success' ? '#065F46' : '#991B1B'}
                    fontSize="sm"
                    fontWeight="600"
                >
                    <Flex justify="space-between" align="center">
                        <Text>{feedback.text}</Text>
                        <Button size="xs" variant="ghost" onClick={() => setFeedback(null)} color="inherit">
                            ✕
                        </Button>
                    </Flex>
                </Box>
            )}

            <DashboardCard
                title="Promotional Fee Coupons"
                subtitle="Offer fee waivers and commission discounts to attract high-volume escrow clients"
                headerAction={
                    <Button
                        size="sm"
                        bg="#1769FF"
                        color="white"
                        _hover={{ bg: '#0D52D6' }}
                        onClick={openCreateModal}
                        borderRadius="lg"
                    >
                        + Create Coupon
                    </Button>
                }
            >
                <Box mb={4} maxW="360px">
                    <Input
                        placeholder="Search promo code..."
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        fontSize="sm"
                        borderRadius="lg"
                        borderColor="gray.300"
                    />
                </Box>

                {loading ? (
                    <Flex justify="center" align="center" py={12}>
                        <Spinner size="lg" color="#1769FF" />
                    </Flex>
                ) : filtered.length === 0 ? (
                    <Box textAlign="center" py={12} color="gray.500">
                        <Text fontSize="lg" fontWeight="700">No Active Promotional Codes</Text>
                        <Text fontSize="sm" mt={1}>Generate a discount code to incentivize platform deal volume.</Text>
                    </Box>
                ) : (
                    <Box overflowX="auto">
                        <table style={{ width: '100%', borderCollapse: 'collapse', textAlign: 'left' }}>
                            <thead>
                                <tr style={{ borderBottom: '1px solid #E2E8F0', backgroundColor: '#F8FAFC' }}>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Promo Code</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Discount</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Usage Limit</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Expires</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Status</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {filtered.map((c) => (
                                    <tr key={c.id} style={{ borderBottom: '1px solid #F1F5F9' }}>
                                        <td style={{ padding: '14px 16px' }}>
                                            <Badge
                                                bg="#EFF6FF"
                                                color="#1E40AF"
                                                fontSize="sm"
                                                px={2.5}
                                                py={1}
                                                borderRadius="md"
                                                fontFamily="mono"
                                                fontWeight="800"
                                            >
                                                {c.code}
                                            </Badge>
                                        </td>
                                        <td style={{ padding: '14px 16px', fontWeight: '700', color: '#0A1128' }}>
                                            {c.discount_type === 'percentage'
                                                ? `${c.discount_value}% OFF`
                                                : `৳ ${Number(c.discount_value).toLocaleString()} OFF`}
                                        </td>
                                        <td style={{ padding: '14px 16px', fontSize: 'xs', color: 'gray.600' }}>
                                            {c.used_count || 0} / {c.total_usage_limit || '∞'} uses
                                        </td>
                                        <td style={{ padding: '14px 16px', fontSize: 'xs', color: 'gray.600' }}>
                                            {c.expiry_date ? new Date(c.expiry_date).toLocaleDateString() : 'Never'}
                                        </td>
                                        <td style={{ padding: '14px 16px' }}>
                                            <Badge
                                                bg={c.is_active ? '#DEF7EC' : '#FEE2E2'}
                                                color={c.is_active ? '#03543F' : '#991B1B'}
                                                fontSize="xs"
                                                px={2}
                                                py={0.5}
                                                borderRadius="md"
                                                fontWeight="700"
                                            >
                                                {c.is_active ? 'Active' : 'Inactive'}
                                            </Badge>
                                        </td>
                                        <td style={{ padding: '14px 16px' }}>
                                            <HStack gap={2}>
                                                <Button
                                                    size="xs"
                                                    variant="outline"
                                                    borderColor="gray.300"
                                                    onClick={() => openEditModal(c)}
                                                >
                                                    Edit
                                                </Button>
                                                <Button
                                                    size="xs"
                                                    colorScheme="red"
                                                    variant="ghost"
                                                    color="#DC2626"
                                                    onClick={() => handleDelete(c.id, c.code)}
                                                >
                                                    Delete
                                                </Button>
                                            </HStack>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </Box>
                )}
            </DashboardCard>

            {/* Modal */}
            {isModalOpen && (
                <Box
                    position="fixed"
                    top="0"
                    left="0"
                    right="0"
                    bottom="0"
                    bg="blackAlpha.600"
                    zIndex="9999"
                    display="flex"
                    alignItems="center"
                    justifyContent="center"
                    p={4}
                >
                    <Box bg="white" borderRadius="2xl" maxW="480px" w="100%" p={6} boxShadow="2xl">
                        <Flex justify="space-between" align="center" mb={4}>
                            <Heading size="md" color="#0A1128">
                                {editingCoupon ? 'Edit Coupon' : 'Create Promotional Coupon'}
                            </Heading>
                            <Button size="sm" variant="ghost" onClick={() => setIsModalOpen(false)}>
                                ✕
                            </Button>
                        </Flex>

                        <form onSubmit={handleSubmit}>
                            <VStack gap={4} align="stretch">
                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                        Coupon Code *
                                    </Text>
                                    <Input
                                        placeholder="e.g. LAUNCH50 or SUMMER2026"
                                        value={formData.code}
                                        onChange={(e) =>
                                            setFormData({ ...formData, code: e.target.value.toUpperCase() })
                                        }
                                        borderRadius="lg"
                                        fontFamily="mono"
                                        fontWeight="700"
                                        required
                                    />
                                </Box>

                                <Flex gap={3}>
                                    <Box flex={1}>
                                        <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                            Type
                                        </Text>
                                        <select
                                            style={{
                                                width: '100%',
                                                padding: '8px 12px',
                                                borderRadius: '8px',
                                                border: '1px solid #CBD5E1',
                                                fontSize: '14px',
                                            }}
                                            value={formData.discount_type}
                                            onChange={(e) =>
                                                setFormData({ ...formData, discount_type: e.target.value })
                                            }
                                        >
                                            <option value="percentage">Percentage (%)</option>
                                            <option value="fixed">Fixed BDT (৳)</option>
                                        </select>
                                    </Box>
                                    <Box flex={1}>
                                        <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                            Value *
                                        </Text>
                                        <Input
                                            type="number"
                                            placeholder="10"
                                            value={formData.discount_value}
                                            onChange={(e) =>
                                                setFormData({ ...formData, discount_value: e.target.value })
                                            }
                                            borderRadius="lg"
                                            required
                                        />
                                    </Box>
                                </Flex>

                                <Flex gap={3}>
                                    <Box flex={1}>
                                        <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                            Total Uses Limit
                                        </Text>
                                        <Input
                                            type="number"
                                            value={formData.total_usage_limit}
                                            onChange={(e) =>
                                                setFormData({ ...formData, total_usage_limit: e.target.value })
                                            }
                                            borderRadius="lg"
                                        />
                                    </Box>
                                    <Box flex={1}>
                                        <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                            Expiry Date
                                        </Text>
                                        <Input
                                            type="date"
                                            value={formData.expiry_date}
                                            onChange={(e) =>
                                                setFormData({ ...formData, expiry_date: e.target.value })
                                            }
                                            borderRadius="lg"
                                        />
                                    </Box>
                                </Flex>

                                <Flex justify="flex-end" gap={3} mt={4}>
                                    <Button
                                        size="sm"
                                        variant="outline"
                                        onClick={() => setIsModalOpen(false)}
                                        borderRadius="lg"
                                    >
                                        Cancel
                                    </Button>
                                    <Button
                                        size="sm"
                                        bg="#1769FF"
                                        color="white"
                                        _hover={{ bg: '#0D52D6' }}
                                        type="submit"
                                        isLoading={submitting}
                                        borderRadius="lg"
                                    >
                                        {editingCoupon ? 'Update Coupon' : 'Create Coupon'}
                                    </Button>
                                </Flex>
                            </VStack>
                        </form>
                    </Box>
                </Box>
            )}
        </VStack>
    );
};

export default CouponsModule;
