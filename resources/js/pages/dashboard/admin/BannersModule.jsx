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
    Spinner,
} from '@chakra-ui/react';
import { adminApi } from '../../../services/adminApi';
import { DashboardCard } from '../../../components/common/DashboardCard';

export const BannersModule = () => {
    const [banners, setBanners] = useState([]);
    const [loading, setLoading] = useState(true);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingBanner, setEditingBanner] = useState(null);
    const [formData, setFormData] = useState({
        title: '',
        image_url: '',
        link_url: '',
        position: 'top',
        is_active: 1,
    });
    const [submitting, setSubmitting] = useState(false);
    const [feedback, setFeedback] = useState(null);

    const fetchBanners = async () => {
        setLoading(true);
        try {
            const data = await adminApi.getBanners();
            if (data && Array.isArray(data.banners)) {
                setBanners(data.banners);
            } else if (Array.isArray(data)) {
                setBanners(data);
            }
        } catch (err) {
            console.error('Failed to load banners:', err);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchBanners();
    }, []);

    const openCreateModal = () => {
        setEditingBanner(null);
        setFormData({
            title: '',
            image_url: '',
            link_url: '',
            position: 'top',
            is_active: 1,
        });
        setIsModalOpen(true);
    };

    const openEditModal = (b) => {
        setEditingBanner(b);
        setFormData({
            title: b.title || '',
            image_url: b.image_url || '',
            link_url: b.link_url || '',
            position: b.position || 'top',
            is_active: b.is_active ? 1 : 0,
        });
        setIsModalOpen(true);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setSubmitting(true);
        try {
            if (editingBanner) {
                await adminApi.updateBanner(editingBanner.id, formData);
                setFeedback({ type: 'success', text: `Banner "${formData.title}" updated.` });
            } else {
                await adminApi.createBanner(formData);
                setFeedback({ type: 'success', text: `Banner "${formData.title}" created.` });
            }
            setIsModalOpen(false);
            await fetchBanners();
        } catch (err) {
            setFeedback({
                type: 'error',
                text: err.response?.data?.message || 'Failed to save banner.',
            });
        } finally {
            setSubmitting(false);
        }
    };

    const handleDelete = async (id, title) => {
        if (!window.confirm(`Delete banner "${title}"?`)) return;
        try {
            await adminApi.deleteBanner(id);
            setFeedback({ type: 'success', text: `Banner "${title}" deleted.` });
            await fetchBanners();
        } catch (err) {
            setFeedback({
                type: 'error',
                text: err.response?.data?.message || 'Failed to delete banner.',
            });
        }
    };

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
                title="Promotional Banners & Broadcast Alerts"
                subtitle="Manage homepage banners, dashboard notice strips, and special campaign highlights"
                headerAction={
                    <Button
                        size="sm"
                        bg="#1769FF"
                        color="white"
                        _hover={{ bg: '#0D52D6' }}
                        onClick={openCreateModal}
                        borderRadius="lg"
                    >
                        + Add Banner
                    </Button>
                }
            >
                {loading ? (
                    <Flex justify="center" align="center" py={12}>
                        <Spinner size="lg" color="#1769FF" />
                    </Flex>
                ) : banners.length === 0 ? (
                    <Box textAlign="center" py={12} color="gray.500">
                        <Text fontSize="lg" fontWeight="700">No Banners Published</Text>
                        <Text fontSize="sm" mt={1}>Add promotional graphics or notices for users.</Text>
                    </Box>
                ) : (
                    <Box overflowX="auto">
                        <table style={{ width: '100%', borderCollapse: 'collapse', textAlign: 'left' }}>
                            <thead>
                                <tr style={{ borderBottom: '1px solid #E2E8F0', backgroundColor: '#F8FAFC' }}>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Preview</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Title</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Destination Link</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Position</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Status</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {banners.map((b) => (
                                    <tr key={b.id} style={{ borderBottom: '1px solid #F1F5F9' }}>
                                        <td style={{ padding: '14px 16px' }}>
                                            {b.image_url ? (
                                                <img
                                                    src={b.image_url}
                                                    alt={b.title}
                                                    style={{ width: '80px', height: '40px', objectFit: 'cover', borderRadius: '6px' }}
                                                />
                                            ) : (
                                                <Box w="80px" h="40px" bg="gray.100" borderRadius="md" display="flex" alignItems="center" justifyContent="center">
                                                    <Text fontSize="2xs" color="gray.400">No Image</Text>
                                                </Box>
                                            )}
                                        </td>
                                        <td style={{ padding: '14px 16px', fontWeight: '700', color: '#0A1128' }}>
                                            {b.title}
                                        </td>
                                        <td style={{ padding: '14px 16px', fontSize: 'xs', color: 'gray.600', maxWidth: '200px' }}>
                                            <a href={b.link_url} target="_blank" rel="noopener noreferrer" style={{ color: '#1769FF', textDecoration: 'underline' }}>
                                                {b.link_url || 'Internal'}
                                            </a>
                                        </td>
                                        <td style={{ padding: '14px 16px', fontSize: 'xs', textTransform: 'capitalize' }}>
                                            {b.position || 'Top'}
                                        </td>
                                        <td style={{ padding: '14px 16px' }}>
                                            <Badge
                                                bg={b.is_active ? '#DEF7EC' : '#FEE2E2'}
                                                color={b.is_active ? '#03543F' : '#991B1B'}
                                                fontSize="xs"
                                                px={2}
                                                py={0.5}
                                                borderRadius="md"
                                                fontWeight="700"
                                            >
                                                {b.is_active ? 'Active' : 'Hidden'}
                                            </Badge>
                                        </td>
                                        <td style={{ padding: '14px 16px' }}>
                                            <HStack gap={2}>
                                                <Button size="xs" variant="outline" borderColor="gray.300" onClick={() => openEditModal(b)}>
                                                    Edit
                                                </Button>
                                                <Button size="xs" colorScheme="red" variant="ghost" color="#DC2626" onClick={() => handleDelete(b.id, b.title)}>
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
                                {editingBanner ? 'Edit Banner' : 'Publish Banner'}
                            </Heading>
                            <Button size="sm" variant="ghost" onClick={() => setIsModalOpen(false)}>
                                ✕
                            </Button>
                        </Flex>

                        <form onSubmit={handleSubmit}>
                            <VStack gap={4} align="stretch">
                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                        Banner Title *
                                    </Text>
                                    <Input
                                        placeholder="e.g. 0% Escrow Fee Weekend"
                                        value={formData.title}
                                        onChange={(e) => setFormData({ ...formData, title: e.target.value })}
                                        borderRadius="lg"
                                        required
                                    />
                                </Box>

                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                        Image URL *
                                    </Text>
                                    <Input
                                        placeholder="https://example.com/banner.png or /images/banner.jpg"
                                        value={formData.image_url}
                                        onChange={(e) => setFormData({ ...formData, image_url: e.target.value })}
                                        borderRadius="lg"
                                        required
                                    />
                                </Box>

                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                        Target URL / Route
                                    </Text>
                                    <Input
                                        placeholder="/deals/create or https://migratepay.com/promo"
                                        value={formData.link_url}
                                        onChange={(e) => setFormData({ ...formData, link_url: e.target.value })}
                                        borderRadius="lg"
                                    />
                                </Box>

                                <Flex justify="flex-end" gap={3} mt={4}>
                                    <Button size="sm" variant="outline" onClick={() => setIsModalOpen(false)} borderRadius="lg">
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
                                        {editingBanner ? 'Update Banner' : 'Create Banner'}
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

export default BannersModule;
