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

export const KnowledgeBaseModule = () => {
    const [articles, setArticles] = useState([]);
    const [loading, setLoading] = useState(true);
    const [search, setSearch] = useState('');
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingArticle, setEditingArticle] = useState(null);
    const [form, setForm] = useState({ category: 'Escrow Operations', question: '', answer: '' });
    const [submitting, setSubmitting] = useState(false);
    const [feedback, setFeedback] = useState(null);

    const fetchArticles = async () => {
        setLoading(true);
        try {
            const data = await adminApi.getKnowledgeBase();
            const list = data.articles || data.knowledge_base || data;
            setArticles(Array.isArray(list) ? list : []);
        } catch (err) {
            console.error('Failed to load KB articles:', err);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchArticles();
    }, []);

    const openCreateModal = () => {
        setEditingArticle(null);
        setForm({ category: 'Escrow Operations', question: '', answer: '' });
        setIsModalOpen(true);
    };

    const openEditModal = (a) => {
        setEditingArticle(a);
        setForm({
            category: a.category || 'Escrow Operations',
            question: a.question || a.title || '',
            answer: a.answer || a.content || '',
        });
        setIsModalOpen(true);
    };

    const handleSave = async (e) => {
        e.preventDefault();
        setSubmitting(true);
        try {
            await adminApi.saveKnowledgeBase(form, editingArticle ? editingArticle.id : null);
            setFeedback({ type: 'success', text: 'Knowledge base entry saved.' });
            setIsModalOpen(false);
            await fetchArticles();
        } catch (err) {
            setFeedback({ type: 'error', text: 'Failed to save article.' });
        } finally {
            setSubmitting(false);
        }
    };

    const handleDelete = async (id) => {
        if (!window.confirm('Delete this knowledge base entry?')) return;
        try {
            await adminApi.deleteKnowledgeBase(id);
            setFeedback({ type: 'success', text: 'Article deleted.' });
            setArticles((prev) => prev.filter((a) => a.id !== id));
        } catch (err) {
            setFeedback({ type: 'error', text: 'Failed to delete article.' });
        }
    };

    const filtered = articles.filter((a) =>
        (a.question || a.title || '').toLowerCase().includes(search.toLowerCase()) ||
        (a.category || '').toLowerCase().includes(search.toLowerCase())
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
                title="Knowledge Base & Client Documentation"
                subtitle="Author self-service tutorials, dispute checklists, and deposit guides for buyers & sellers"
                headerAction={
                    <Button
                        size="sm"
                        bg="#1769FF"
                        color="white"
                        _hover={{ bg: '#0D52D6' }}
                        onClick={openCreateModal}
                        borderRadius="lg"
                    >
                        + Add Article
                    </Button>
                }
            >
                <Box mb={4} maxW="360px">
                    <Input
                        placeholder="Search articles by title or keyword..."
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
                        <Text fontSize="lg" fontWeight="700">No Articles in Knowledge Base</Text>
                        <Text fontSize="sm" mt={1}>Add guides to empower users to resolve common issues independently.</Text>
                    </Box>
                ) : (
                    <Box overflowX="auto">
                        <table style={{ width: '100%', borderCollapse: 'collapse', textAlign: 'left' }}>
                            <thead>
                                <tr style={{ borderBottom: '1px solid #E2E8F0', backgroundColor: '#F8FAFC' }}>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Category</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Article Question / Topic</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Body Summary</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {filtered.map((a) => (
                                    <tr key={a.id} style={{ borderBottom: '1px solid #F1F5F9' }}>
                                        <td style={{ padding: '14px 16px' }}>
                                            <Badge bg="#EFF6FF" color="#1E40AF" fontSize="xs" px={2} py={0.5} borderRadius="md" fontWeight="700">
                                                {a.category || 'General'}
                                            </Badge>
                                        </td>
                                        <td style={{ padding: '14px 16px', fontWeight: '800', color: '#0A1128', fontSize: 'sm' }}>
                                            {a.question || a.title}
                                        </td>
                                        <td style={{ padding: '14px 16px', fontSize: 'xs', color: 'gray.600', maxWidth: '340px' }}>
                                            {a.answer || a.content}
                                        </td>
                                        <td style={{ padding: '14px 16px' }}>
                                            <HStack gap={2}>
                                                <Button size="xs" variant="outline" borderColor="gray.300" onClick={() => openEditModal(a)}>
                                                    Edit
                                                </Button>
                                                <Button size="xs" variant="ghost" color="#DC2626" onClick={() => handleDelete(a.id)}>
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
                    <Box bg="white" borderRadius="2xl" maxW="540px" w="100%" p={6} boxShadow="2xl">
                        <Flex justify="space-between" align="center" mb={4}>
                            <Heading size="md" color="#0A1128">
                                {editingArticle ? 'Edit KB Article' : 'Author Knowledge Base Article'}
                            </Heading>
                            <Button size="sm" variant="ghost" onClick={() => setIsModalOpen(false)}>
                                ✕
                            </Button>
                        </Flex>

                        <form onSubmit={handleSave}>
                            <VStack gap={4} align="stretch">
                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                        Section Category *
                                    </Text>
                                    <Input
                                        placeholder="Escrow Protection, bKash Deposits, Withdrawal Limits..."
                                        value={form.category}
                                        onChange={(e) => setForm({ ...form, category: e.target.value })}
                                        borderRadius="lg"
                                        required
                                    />
                                </Box>

                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                        Question / Article Title *
                                    </Text>
                                    <Input
                                        placeholder="How do I initiate a domain escrow contract?"
                                        value={form.question}
                                        onChange={(e) => setForm({ ...form, question: e.target.value })}
                                        borderRadius="lg"
                                        required
                                    />
                                </Box>

                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                        Article Content &amp; Resolution Steps *
                                    </Text>
                                    <Textarea
                                        rows={6}
                                        placeholder="Step 1: Go to Initiate New Deal... Step 2: Choose seller... Step 3: Fund vault..."
                                        value={form.answer}
                                        onChange={(e) => setForm({ ...form, answer: e.target.value })}
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
                                        Save Article
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

export default KnowledgeBaseModule;
