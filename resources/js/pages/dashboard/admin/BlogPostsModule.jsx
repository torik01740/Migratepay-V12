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

export const BlogPostsModule = () => {
    const [posts, setPosts] = useState([]);
    const [loading, setLoading] = useState(true);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingPost, setEditingPost] = useState(null);
    const [formData, setFormData] = useState({
        title: '',
        slug: '',
        category: 'Guides',
        summary: '',
        content: '',
        image_url: '',
        is_published: 1,
    });
    const [submitting, setSubmitting] = useState(false);
    const [feedback, setFeedback] = useState(null);

    const fetchPosts = async () => {
        setLoading(true);
        try {
            const data = await adminApi.getBlogPosts();
            if (data && Array.isArray(data.posts)) {
                setPosts(data.posts);
            } else if (Array.isArray(data)) {
                setPosts(data);
            }
        } catch (err) {
            console.error('Failed to load blog posts:', err);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchPosts();
    }, []);

    const openCreateModal = () => {
        setEditingPost(null);
        setFormData({
            title: '',
            slug: '',
            category: 'Escrow Security',
            summary: '',
            content: '',
            image_url: '',
            is_published: 1,
        });
        setIsModalOpen(true);
    };

    const openEditModal = (p) => {
        setEditingPost(p);
        setFormData({
            title: p.title || '',
            slug: p.slug || '',
            category: p.category || 'Guides',
            summary: p.summary || '',
            content: p.content || '',
            image_url: p.image_url || '',
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
            slug: editingPost ? prev.slug : slug,
        }));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setSubmitting(true);
        try {
            await adminApi.saveBlogPost(formData, editingPost ? editingPost.id : null);
            setFeedback({ type: 'success', text: `Article "${formData.title}" published.` });
            setIsModalOpen(false);
            await fetchPosts();
        } catch (err) {
            setFeedback({
                type: 'error',
                text: err.response?.data?.message || 'Failed to save blog post.',
            });
        } finally {
            setSubmitting(false);
        }
    };

    const handleDelete = async (id, title) => {
        if (!window.confirm(`Delete article "${title}"?`)) return;
        try {
            await adminApi.deleteBlogPost(id);
            setFeedback({ type: 'success', text: `Article "${title}" deleted.` });
            await fetchPosts();
        } catch (err) {
            setFeedback({
                type: 'error',
                text: err.response?.data?.message || 'Failed to delete article.',
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
                title="Blog Engine & Editorial Desk"
                subtitle="Publish escrow education, transaction safety guides, and platform product announcements"
                headerAction={
                    <Button
                        size="sm"
                        bg="#1769FF"
                        color="white"
                        _hover={{ bg: '#0D52D6' }}
                        onClick={openCreateModal}
                        borderRadius="lg"
                    >
                        + Write Article
                    </Button>
                }
            >
                {loading ? (
                    <Flex justify="center" align="center" py={12}>
                        <Spinner size="lg" color="#1769FF" />
                    </Flex>
                ) : posts.length === 0 ? (
                    <Box textAlign="center" py={12} color="gray.500">
                        <Text fontSize="lg" fontWeight="700">No Blog Articles Found</Text>
                        <Text fontSize="sm" mt={1}>Write insights and transaction best practices for your community.</Text>
                    </Box>
                ) : (
                    <Box overflowX="auto">
                        <table style={{ width: '100%', borderCollapse: 'collapse', textAlign: 'left' }}>
                            <thead>
                                <tr style={{ borderBottom: '1px solid #E2E8F0', backgroundColor: '#F8FAFC' }}>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Article</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Category</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Status</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Date</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {posts.map((post) => (
                                    <tr key={post.id} style={{ borderBottom: '1px solid #F1F5F9' }}>
                                        <td style={{ padding: '14px 16px' }}>
                                            <Text fontWeight="800" color="#0A1128" fontSize="sm">
                                                {post.title}
                                            </Text>
                                            <Text fontSize="xs" color="gray.500">
                                                /blog/{post.slug}
                                            </Text>
                                        </td>
                                        <td style={{ padding: '14px 16px' }}>
                                            <Badge bg="#EFF6FF" color="#1E40AF" fontSize="xs" px={2} py={0.5} borderRadius="md">
                                                {post.category || 'General'}
                                            </Badge>
                                        </td>
                                        <td style={{ padding: '14px 16px' }}>
                                            <Badge
                                                bg={post.is_published ? '#DEF7EC' : '#FEE2E2'}
                                                color={post.is_published ? '#03543F' : '#991B1B'}
                                                fontSize="xs"
                                                px={2}
                                                py={0.5}
                                                borderRadius="md"
                                                fontWeight="700"
                                            >
                                                {post.is_published ? 'Published' : 'Draft'}
                                            </Badge>
                                        </td>
                                        <td style={{ padding: '14px 16px', fontSize: 'xs', color: 'gray.500' }}>
                                            {post.created_at ? new Date(post.created_at).toLocaleDateString() : 'N/A'}
                                        </td>
                                        <td style={{ padding: '14px 16px' }}>
                                            <HStack gap={2}>
                                                <Button size="xs" variant="outline" borderColor="gray.300" onClick={() => openEditModal(post)}>
                                                    Edit
                                                </Button>
                                                <Button size="xs" colorScheme="red" variant="ghost" color="#DC2626" onClick={() => handleDelete(post.id, post.title)}>
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
                    <Box bg="white" borderRadius="2xl" maxW="640px" w="100%" p={6} boxShadow="2xl">
                        <Flex justify="space-between" align="center" mb={4}>
                            <Heading size="md" color="#0A1128">
                                {editingPost ? 'Edit Blog Article' : 'Draft New Article'}
                            </Heading>
                            <Button size="sm" variant="ghost" onClick={() => setIsModalOpen(false)}>
                                ✕
                            </Button>
                        </Flex>

                        <form onSubmit={handleSubmit}>
                            <VStack gap={4} align="stretch">
                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                        Article Headline *
                                    </Text>
                                    <Input
                                        placeholder="e.g. How Escrow Protects High-Value Freelance Contracts"
                                        value={formData.title}
                                        onChange={handleTitleChange}
                                        borderRadius="lg"
                                        required
                                    />
                                </Box>

                                <Flex gap={3}>
                                    <Box flex={1}>
                                        <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                            Category
                                        </Text>
                                        <Input
                                            placeholder="Security & Guides"
                                            value={formData.category}
                                            onChange={(e) => setFormData({ ...formData, category: e.target.value })}
                                            borderRadius="lg"
                                        />
                                    </Box>
                                    <Box flex={1}>
                                        <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                            Cover Image URL
                                        </Text>
                                        <Input
                                            placeholder="https://.../cover.png"
                                            value={formData.image_url}
                                            onChange={(e) => setFormData({ ...formData, image_url: e.target.value })}
                                            borderRadius="lg"
                                        />
                                    </Box>
                                </Flex>

                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                        Summary Excerpt
                                    </Text>
                                    <Input
                                        placeholder="Brief 1-2 sentence overview for social cards..."
                                        value={formData.summary}
                                        onChange={(e) => setFormData({ ...formData, summary: e.target.value })}
                                        borderRadius="lg"
                                    />
                                </Box>

                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                        Article Body *
                                    </Text>
                                    <Textarea
                                        rows={7}
                                        placeholder="Write your article in Markdown..."
                                        value={formData.content}
                                        onChange={(e) => setFormData({ ...formData, content: e.target.value })}
                                        borderRadius="lg"
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
                                        {editingPost ? 'Update Post' : 'Publish Post'}
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

export default BlogPostsModule;
