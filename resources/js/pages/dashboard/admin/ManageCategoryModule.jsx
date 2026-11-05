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
    Textarea,
    Spinner,
} from '@chakra-ui/react';
import { adminApi } from '../../../services/adminApi';
import { DashboardCard } from '../../../components/common/DashboardCard';

export const ManageCategoryModule = () => {
    const [categories, setCategories] = useState([]);
    const [loading, setLoading] = useState(true);
    const [search, setSearch] = useState('');
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingCategory, setEditingCategory] = useState(null);
    const [formData, setFormData] = useState({ name: '', slug: '', description: '', is_active: 1 });
    const [submitting, setSubmitting] = useState(false);
    const [feedback, setFeedback] = useState(null);

    const fetchCategories = async () => {
        setLoading(true);
        try {
            const data = await adminApi.getCategories();
            if (data && Array.isArray(data.categories)) {
                setCategories(data.categories);
            } else if (Array.isArray(data)) {
                setCategories(data);
            }
        } catch (err) {
            console.error('Failed to fetch categories:', err);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchCategories();
    }, []);

    const openCreateModal = () => {
        setEditingCategory(null);
        setFormData({ name: '', slug: '', description: '', is_active: 1 });
        setIsModalOpen(true);
    };

    const openEditModal = (cat) => {
        setEditingCategory(cat);
        setFormData({
            name: cat.name || '',
            slug: cat.slug || '',
            description: cat.description || '',
            is_active: cat.is_active !== undefined ? (cat.is_active ? 1 : 0) : 1,
        });
        setIsModalOpen(true);
    };

    const handleNameChange = (e) => {
        const val = e.target.value;
        const slug = val.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
        setFormData((prev) => ({
            ...prev,
            name: val,
            slug: editingCategory ? prev.slug : slug,
        }));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setSubmitting(true);
        try {
            if (editingCategory) {
                await adminApi.updateCategory(editingCategory.id, formData);
                setFeedback({ type: 'success', text: `Category "${formData.name}" successfully updated.` });
            } else {
                await adminApi.createCategory(formData);
                setFeedback({ type: 'success', text: `Category "${formData.name}" successfully created.` });
            }
            setIsModalOpen(false);
            await fetchCategories();
        } catch (err) {
            setFeedback({
                type: 'error',
                text: err.response?.data?.message || 'Failed to save category.',
            });
        } finally {
            setSubmitting(false);
        }
    };

    const handleDelete = async (id, name) => {
        if (!window.confirm(`Are you sure you want to delete category "${name}"?`)) return;
        try {
            await adminApi.deleteCategory(id);
            setFeedback({ type: 'success', text: `Category "${name}" deleted.` });
            await fetchCategories();
        } catch (err) {
            setFeedback({
                type: 'error',
                text: err.response?.data?.message || 'Failed to delete category.',
            });
        }
    };

    const filtered = categories.filter((c) =>
        c.name?.toLowerCase().includes(search.toLowerCase()) ||
        c.slug?.toLowerCase().includes(search.toLowerCase())
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
                title="Deal & Project Categories"
                subtitle="Organize digital products, domain escrow, freelancing, and physical goods classifications"
                headerAction={
                    <Button
                        size="sm"
                        bg="#1769FF"
                        color="white"
                        _hover={{ bg: '#0D52D6' }}
                        onClick={openCreateModal}
                        borderRadius="lg"
                    >
                        + Add Category
                    </Button>
                }
            >
                <Box mb={4} maxW="360px">
                    <Input
                        placeholder="Search category name or slug..."
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
                        <Text fontSize="lg" fontWeight="700">No Categories Found</Text>
                        <Text fontSize="sm" mt={1}>Create your first deal classification to get started.</Text>
                    </Box>
                ) : (
                    <Box overflowX="auto">
                        <table style={{ width: '100%', borderCollapse: 'collapse', textAlign: 'left' }}>
                            <thead>
                                <tr style={{ borderBottom: '1px solid #E2E8F0', backgroundColor: '#F8FAFC' }}>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Name</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Slug</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Description</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Status</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {filtered.map((cat) => (
                                    <tr key={cat.id} style={{ borderBottom: '1px solid #F1F5F9' }}>
                                        <td style={{ padding: '14px 16px', fontWeight: '800', color: '#0A1128' }}>
                                            {cat.name}
                                        </td>
                                        <td style={{ padding: '14px 16px', fontFamily: 'mono', fontSize: 'xs', color: 'gray.600' }}>
                                            {cat.slug}
                                        </td>
                                        <td style={{ padding: '14px 16px', fontSize: 'xs', color: 'gray.600', maxWidth: '300px' }}>
                                            {cat.description || 'No description provided'}
                                        </td>
                                        <td style={{ padding: '14px 16px' }}>
                                            <Badge
                                                bg={cat.is_active !== 0 ? '#DEF7EC' : '#FEE2E2'}
                                                color={cat.is_active !== 0 ? '#03543F' : '#991B1B'}
                                                fontSize="xs"
                                                px={2}
                                                py={0.5}
                                                borderRadius="md"
                                                fontWeight="700"
                                            >
                                                {cat.is_active !== 0 ? 'Active' : 'Disabled'}
                                            </Badge>
                                        </td>
                                        <td style={{ padding: '14px 16px' }}>
                                            <HStack gap={2}>
                                                <Button
                                                    size="xs"
                                                    variant="outline"
                                                    borderColor="gray.300"
                                                    onClick={() => openEditModal(cat)}
                                                >
                                                    Edit
                                                </Button>
                                                <Button
                                                    size="xs"
                                                    colorScheme="red"
                                                    variant="ghost"
                                                    color="#DC2626"
                                                    onClick={() => handleDelete(cat.id, cat.name)}
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
                    <Box
                        bg="white"
                        borderRadius="2xl"
                        maxW="480px"
                        w="100%"
                        p={6}
                        boxShadow="2xl"
                    >
                        <Flex justify="space-between" align="center" mb={4}>
                            <Heading size="md" color="#0A1128">
                                {editingCategory ? 'Edit Category' : 'Create Deal Category'}
                            </Heading>
                            <Button size="sm" variant="ghost" onClick={() => setIsModalOpen(false)}>
                                ✕
                            </Button>
                        </Flex>

                        <form onSubmit={handleSubmit}>
                            <VStack gap={4} align="stretch">
                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                        Category Title *
                                    </Text>
                                    <Input
                                        placeholder="e.g. Domain Names & Digital Assets"
                                        value={formData.name}
                                        onChange={handleNameChange}
                                        borderRadius="lg"
                                        required
                                    />
                                </Box>

                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                        URL Slug *
                                    </Text>
                                    <Input
                                        placeholder="domain-names"
                                        value={formData.slug}
                                        onChange={(e) => setFormData({ ...formData, slug: e.target.value })}
                                        borderRadius="lg"
                                        required
                                    />
                                </Box>

                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                        Description
                                    </Text>
                                    <Textarea
                                        rows={3}
                                        placeholder="Details regarding what falls under this escrow classification..."
                                        value={formData.description}
                                        onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                                        borderRadius="lg"
                                    />
                                </Box>

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
                                        {editingCategory ? 'Update Category' : 'Create Category'}
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

export default ManageCategoryModule;
