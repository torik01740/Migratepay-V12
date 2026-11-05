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

export const ManagePagesModule = () => {
    const [pages, setPages] = useState([]);
    const [loading, setLoading] = useState(true);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingPage, setEditingPage] = useState(null);
    const [formData, setFormData] = useState({
        title: '',
        slug: '',
        content: '',
        is_published: 1,
    });
    const [submitting, setSubmitting] = useState(false);
    const [feedback, setFeedback] = useState(null);

    const fetchPages = async () => {
        setLoading(true);
        try {
            const data = await adminApi.getCmsPages();
            if (data && Array.isArray(data.pages)) {
                setPages(data.pages);
            } else if (Array.isArray(data)) {
                setPages(data);
            }
        } catch (err) {
            console.error('Failed to load CMS pages:', err);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchPages();
    }, []);

    const openCreateModal = () => {
        setEditingPage(null);
        setFormData({ title: '', slug: '', content: '', is_published: 1 });
        setIsModalOpen(true);
    };

    const openEditModal = (p) => {
        setEditingPage(p);
        setFormData({
            title: p.title || '',
            slug: p.slug || '',
            content: p.content || '',
            is_published: p.is_published !== undefined ? (p.is_published ? 1 : 0) : 1,
        });
        setIsModalOpen(true);
    };

    const handleTitleChange = (e) => {
        const val = e.target.value;
        const slug = val.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
        setFormData((prev) => ({
            ...prev,
            title: val,
            slug: editingPage ? prev.slug : slug,
        }));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setSubmitting(true);
        try {
            await adminApi.saveCmsPage(formData, editingPage ? editingPage.id : null);
            setFeedback({ type: 'success', text: `Page "${formData.title}" saved successfully.` });
            setIsModalOpen(false);
            await fetchPages();
        } catch (err) {
            setFeedback({
                type: 'error',
                text: err.response?.data?.message || 'Failed to save page.',
            });
        } finally {
            setSubmitting(false);
        }
    };

    const handleDelete = async (id, title) => {
        if (!window.confirm(`Delete page "${title}"?`)) return;
        try {
            await adminApi.deleteCmsPage(id);
            setFeedback({ type: 'success', text: `Page "${title}" deleted.` });
            await fetchPages();
        } catch (err) {
            setFeedback({
                type: 'error',
                text: err.response?.data?.message || 'Failed to delete page.',
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
                title="CMS Static Pages Management"
                subtitle="Author and maintain system documentation, Terms of Service, Privacy Policy, and Escrow FAQ"
                headerAction={
                    <Button
                        size="sm"
                        bg="#1769FF"
                        color="white"
                        _hover={{ bg: '#0D52D6' }}
                        onClick={openCreateModal}
                        borderRadius="lg"
                    >
                        + Create Page
                    </Button>
                }
            >
                {loading ? (
                    <Flex justify="center" align="center" py={12}>
                        <Spinner size="lg" color="#1769FF" />
                    </Flex>
                ) : pages.length === 0 ? (
                    <Box textAlign="center" py={12} color="gray.500">
                        <Text fontSize="lg" fontWeight="700">No CMS Pages Configured</Text>
                        <Text fontSize="sm" mt={1}>Create legal or informational pages for the public portal.</Text>
                    </Box>
                ) : (
                    <Box overflowX="auto">
                        <table style={{ width: '100%', borderCollapse: 'collapse', textAlign: 'left' }}>
                            <thead>
                                <tr style={{ borderBottom: '1px solid #E2E8F0', backgroundColor: '#F8FAFC' }}>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Page Title</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Slug Route</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Status</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Updated</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {pages.map((p) => (
                                    <tr key={p.id} style={{ borderBottom: '1px solid #F1F5F9' }}>
                                        <td style={{ padding: '14px 16px', fontWeight: '800', color: '#0A1128' }}>
                                            {p.title}
                                        </td>
                                        <td style={{ padding: '14px 16px', fontFamily: 'mono', fontSize: 'xs', color: '#1769FF' }}>
                                            /{p.slug}
                                        </td>
                                        <td style={{ padding: '14px 16px' }}>
                                            <Badge
                                                bg={p.is_published ? '#DEF7EC' : '#FEE2E2'}
                                                color={p.is_published ? '#03543F' : '#991B1B'}
                                                fontSize="xs"
                                                px={2}
                                                py={0.5}
                                                borderRadius="md"
                                                fontWeight="700"
                                            >
                                                {p.is_published ? 'Published' : 'Draft'}
                                            </Badge>
                                        </td>
                                        <td style={{ padding: '14px 16px', fontSize: 'xs', color: 'gray.500' }}>
                                            {p.updated_at ? new Date(p.updated_at).toLocaleDateString() : 'N/A'}
                                        </td>
                                        <td style={{ padding: '14px 16px' }}>
                                            <HStack gap={2}>
                                                <Button size="xs" variant="outline" borderColor="gray.300" onClick={() => openEditModal(p)}>
                                                    Edit
                                                </Button>
                                                <Button size="xs" colorScheme="red" variant="ghost" color="#DC2626" onClick={() => handleDelete(p.id, p.title)}>
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
                    <Box bg="white" borderRadius="2xl" maxW="600px" w="100%" p={6} boxShadow="2xl">
                        <Flex justify="space-between" align="center" mb={4}>
                            <Heading size="md" color="#0A1128">
                                {editingPage ? 'Edit Page' : 'Author CMS Page'}
                            </Heading>
                            <Button size="sm" variant="ghost" onClick={() => setIsModalOpen(false)}>
                                ✕
                            </Button>
                        </Flex>

                        <form onSubmit={handleSubmit}>
                            <VStack gap={4} align="stretch">
                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                        Page Title *
                                    </Text>
                                    <Input
                                        placeholder="e.g. Terms & Escrow Agreement"
                                        value={formData.title}
                                        onChange={handleTitleChange}
                                        borderRadius="lg"
                                        required
                                    />
                                </Box>

                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                        Route Slug *
                                    </Text>
                                    <Input
                                        placeholder="terms-and-conditions"
                                        value={formData.slug}
                                        onChange={(e) => setFormData({ ...formData, slug: e.target.value })}
                                        borderRadius="lg"
                                        required
                                    />
                                </Box>

                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                        Page Markdown / HTML Body *
                                    </Text>
                                    <Textarea
                                        rows={8}
                                        placeholder="# Terms of Service&#10;&#10;Welcome to MigratePay escrow..."
                                        value={formData.content}
                                        onChange={(e) => setFormData({ ...formData, content: e.target.value })}
                                        borderRadius="lg"
                                        fontFamily="mono"
                                        fontSize="xs"
                                        required
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
                                        {editingPage ? 'Update Page' : 'Save Page'}
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

export default ManagePagesModule;
