import React, { useState, useEffect } from 'react';
import {
    Box,
    Flex,
    Heading,
    Text,
    Button,
    Input,
    Textarea,
    VStack,
    HStack,
    Badge,
    Spinner,
    SimpleGrid,
} from '@chakra-ui/react';
import { adminApi } from '../../../services/adminApi';
import { DashboardCard } from '../../../components/common/DashboardCard';

export const AiLiveChatModule = () => {
    const [activeTab, setActiveTab] = useState('config'); // 'config' | 'kb' | 'history'
    const [settings, setSettings] = useState({
        ai_widget_active: '1',
        ai_api_key: '',
        ai_api_endpoint: '',
        ai_api_model: 'gemini-1.5-flash',
        ai_system_instructions: 'You are MigratePay AI Assistant, helping users navigate escrow deposits, deal creation, and security.',
    });
    const [history, setHistory] = useState([]);
    const [selectedSession, setSelectedSession] = useState(null);
    const [kbItems, setKbItems] = useState([]);
    const [kbModalOpen, setKbModalOpen] = useState(false);
    const [editingKb, setEditingKb] = useState(null);
    const [kbForm, setKbForm] = useState({ category: 'Escrow', question: '', answer: '' });
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [feedback, setFeedback] = useState(null);

    const loadData = async () => {
        setLoading(true);
        try {
            const [settingsRes, historyRes, kbRes] = await Promise.allSettled([
                adminApi.getAiSettings(),
                adminApi.getAiChatHistory(),
                adminApi.getKnowledgeBase(),
            ]);

            if (settingsRes.status === 'fulfilled' && settingsRes.value) {
                setSettings((prev) => ({ ...prev, ...settingsRes.value }));
            }
            if (historyRes.status === 'fulfilled' && historyRes.value) {
                const hData = historyRes.value.history || historyRes.value.sessions || historyRes.value;
                setHistory(Array.isArray(hData) ? hData : []);
            }
            if (kbRes.status === 'fulfilled' && kbRes.value) {
                const kData = kbRes.value.articles || kbRes.value.knowledge_base || kbRes.value;
                setKbItems(Array.isArray(kData) ? kData : []);
            }
        } catch (err) {
            console.error('Failed to load AI data:', err);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        loadData();
    }, []);

    const handleSaveSettings = async (e) => {
        e.preventDefault();
        setSaving(true);
        try {
            await adminApi.saveAiSettings(settings);
            setFeedback({ type: 'success', text: 'AI Assistant settings saved successfully.' });
        } catch (err) {
            setFeedback({ type: 'error', text: 'Failed to update AI settings.' });
        } finally {
            setSaving(false);
        }
    };

    const handleSaveKb = async (e) => {
        e.preventDefault();
        setSaving(true);
        try {
            await adminApi.saveKnowledgeBase(kbForm, editingKb ? editingKb.id : null);
            setFeedback({ type: 'success', text: 'Knowledge base entry saved.' });
            setKbModalOpen(false);
            const kbRes = await adminApi.getKnowledgeBase();
            const kData = kbRes.articles || kbRes.knowledge_base || kbRes;
            setKbItems(Array.isArray(kData) ? kData : []);
        } catch (err) {
            setFeedback({ type: 'error', text: 'Failed to save KB entry.' });
        } finally {
            setSaving(false);
        }
    };

    const handleDeleteKb = async (id) => {
        if (!window.confirm('Delete this knowledge base entry?')) return;
        try {
            await adminApi.deleteKnowledgeBase(id);
            setFeedback({ type: 'success', text: 'Knowledge base entry removed.' });
            setKbItems((prev) => prev.filter((k) => k.id !== id));
        } catch (err) {
            setFeedback({ type: 'error', text: 'Failed to delete KB entry.' });
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
                title="AI Live Assistant & Knowledge Engine"
                subtitle="Configure automated customer response models, training documents, and visitor chat audits"
                headerAction={
                    <HStack gap={2}>
                        <Button
                            size="xs"
                            variant={activeTab === 'config' ? 'solid' : 'outline'}
                            bg={activeTab === 'config' ? '#0A1128' : 'transparent'}
                            color={activeTab === 'config' ? 'white' : 'gray.600'}
                            onClick={() => setActiveTab('config')}
                            borderRadius="md"
                        >
                            LLM Model &amp; Config
                        </Button>
                        <Button
                            size="xs"
                            variant={activeTab === 'kb' ? 'solid' : 'outline'}
                            bg={activeTab === 'kb' ? '#0A1128' : 'transparent'}
                            color={activeTab === 'kb' ? 'white' : 'gray.600'}
                            onClick={() => setActiveTab('kb')}
                            borderRadius="md"
                        >
                            Knowledge Base ({kbItems.length})
                        </Button>
                        <Button
                            size="xs"
                            variant={activeTab === 'history' ? 'solid' : 'outline'}
                            bg={activeTab === 'history' ? '#0A1128' : 'transparent'}
                            color={activeTab === 'history' ? 'white' : 'gray.600'}
                            onClick={() => setActiveTab('history')}
                            borderRadius="md"
                        >
                            Chat History ({history.length})
                        </Button>
                    </HStack>
                }
            >
                {loading ? (
                    <Flex justify="center" align="center" py={12}>
                        <Spinner size="lg" color="#1769FF" />
                    </Flex>
                ) : activeTab === 'config' ? (
                    /* Config Form */
                    <form onSubmit={handleSaveSettings}>
                        <VStack gap={5} align="stretch" maxW="700px">
                            <Box
                                p={4}
                                borderRadius="xl"
                                borderWidth="1px"
                                borderColor="gray.200"
                                bg="#F8FAFC"
                            >
                                <Flex justify="space-between" align="center">
                                    <Box>
                                        <Text fontWeight="800" fontSize="sm" color="#0A1128">
                                            Enable AI Live Chat Widget
                                        </Text>
                                        <Text fontSize="xs" color="gray.500">
                                            Display floating AI support agent on marketing and user portal pages
                                        </Text>
                                    </Box>
                                    <Button
                                        size="sm"
                                        bg={settings.ai_widget_active === '1' ? '#10B981' : '#E2E8F0'}
                                        color={settings.ai_widget_active === '1' ? 'white' : 'gray.700'}
                                        onClick={() =>
                                            setSettings({
                                                ...settings,
                                                ai_widget_active: settings.ai_widget_active === '1' ? '0' : '1',
                                            })
                                        }
                                        borderRadius="full"
                                    >
                                        {settings.ai_widget_active === '1' ? 'Active' : 'Disabled'}
                                    </Button>
                                </Flex>
                            </Box>

                            <SimpleGrid columns={{ base: 1, md: 2 }} gap={4}>
                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                        API Key
                                    </Text>
                                    <Input
                                        type="password"
                                        placeholder="••••••••••••••••••••••"
                                        value={settings.ai_api_key || ''}
                                        onChange={(e) => setSettings({ ...settings, ai_api_key: e.target.value })}
                                        borderRadius="lg"
                                        fontSize="xs"
                                    />
                                    <Text fontSize="2xs" color="gray.400" mt={1}>
                                        Leave empty to use local Rule Engine + Knowledge Base
                                    </Text>
                                </Box>
                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                        LLM Model Name
                                    </Text>
                                    <Input
                                        placeholder="gemini-1.5-flash or gpt-4o-mini"
                                        value={settings.ai_api_model || ''}
                                        onChange={(e) => setSettings({ ...settings, ai_api_model: e.target.value })}
                                        borderRadius="lg"
                                        fontSize="xs"
                                    />
                                </Box>
                            </SimpleGrid>

                            <Box>
                                <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                    API Endpoint URL
                                </Text>
                                <Input
                                    placeholder="https://generativelanguage.googleapis.com/v1beta/openai"
                                    value={settings.ai_api_endpoint || ''}
                                    onChange={(e) => setSettings({ ...settings, ai_api_endpoint: e.target.value })}
                                    borderRadius="lg"
                                    fontSize="xs"
                                />
                            </Box>

                            <Box>
                                <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                    AI Persona &amp; System Guardrails
                                </Text>
                                <Textarea
                                    rows={4}
                                    placeholder="Instructions for the agent..."
                                    value={settings.ai_system_instructions || ''}
                                    onChange={(e) =>
                                        setSettings({ ...settings, ai_system_instructions: e.target.value })
                                    }
                                    borderRadius="lg"
                                    fontSize="xs"
                                />
                            </Box>

                            <Flex justify="flex-start" mt={2}>
                                <Button
                                    size="sm"
                                    bg="#1769FF"
                                    color="white"
                                    _hover={{ bg: '#0D52D6' }}
                                    type="submit"
                                    isLoading={saving}
                                    borderRadius="lg"
                                >
                                    Save AI Configuration
                                </Button>
                            </Flex>
                        </VStack>
                    </form>
                ) : activeTab === 'kb' ? (
                    /* Knowledge Base Tab */
                    <Box>
                        <Flex justify="space-between" align="center" mb={4}>
                            <Text fontSize="xs" color="gray.600">
                                Define answers to common questions so the AI can resolve queries accurately without hallucination.
                            </Text>
                            <Button
                                size="xs"
                                bg="#1769FF"
                                color="white"
                                _hover={{ bg: '#0D52D6' }}
                                onClick={() => {
                                    setEditingKb(null);
                                    setKbForm({ category: 'Escrow', question: '', answer: '' });
                                    setKbModalOpen(true);
                                }}
                                borderRadius="md"
                            >
                                + Add FAQ / Rule
                            </Button>
                        </Flex>

                        {kbItems.length === 0 ? (
                            <Box textAlign="center" py={8} color="gray.400">
                                <Text fontSize="sm">No training rules yet. Add knowledge base articles.</Text>
                            </Box>
                        ) : (
                            <Box overflowX="auto">
                                <table style={{ width: '100%', borderCollapse: 'collapse', textAlign: 'left' }}>
                                    <thead>
                                        <tr style={{ borderBottom: '1px solid #E2E8F0', backgroundColor: '#F8FAFC' }}>
                                            <th style={{ padding: '10px 14px', fontSize: '11px', fontWeight: '800', color: '#64748B' }}>CATEGORY</th>
                                            <th style={{ padding: '10px 14px', fontSize: '11px', fontWeight: '800', color: '#64748B' }}>QUESTION / TOPIC</th>
                                            <th style={{ padding: '10px 14px', fontSize: '11px', fontWeight: '800', color: '#64748B' }}>ANSWER / INSTRUCTION</th>
                                            <th style={{ padding: '10px 14px', fontSize: '11px', fontWeight: '800', color: '#64748B' }}>ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {kbItems.map((k) => (
                                            <tr key={k.id} style={{ borderBottom: '1px solid #F1F5F9' }}>
                                                <td style={{ padding: '12px 14px' }}>
                                                    <Badge bg="#EFF6FF" color="#1E40AF" fontSize="2xs" px={1.5} py={0.5} borderRadius="sm">
                                                        {k.category || 'General'}
                                                    </Badge>
                                                </td>
                                                <td style={{ padding: '12px 14px', fontWeight: '700', fontSize: 'xs', color: '#0A1128' }}>
                                                    {k.question}
                                                </td>
                                                <td style={{ padding: '12px 14px', fontSize: 'xs', color: 'gray.700', maxWidth: '380px' }}>
                                                    {k.answer}
                                                </td>
                                                <td style={{ padding: '12px 14px' }}>
                                                    <HStack gap={2}>
                                                        <Button
                                                            size="2xs"
                                                            variant="outline"
                                                            onClick={() => {
                                                                setEditingKb(k);
                                                                setKbForm({ category: k.category || 'Escrow', question: k.question, answer: k.answer });
                                                                setKbModalOpen(true);
                                                            }}
                                                        >
                                                            Edit
                                                        </Button>
                                                        <Button
                                                            size="2xs"
                                                            variant="ghost"
                                                            color="#DC2626"
                                                            onClick={() => handleDeleteKb(k.id)}
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
                    </Box>
                ) : (
                    /* History Tab */
                    <Flex gap={4} direction={{ base: 'column', md: 'row' }} minH="400px">
                        <Box w={{ base: '100%', md: '35%' }} borderRight="1px solid #E2E8F0" pr={4}>
                            <Text fontSize="xs" fontWeight="800" color="gray.500" mb={3} textTransform="uppercase">
                                Chat Sessions
                            </Text>
                            {history.length === 0 ? (
                                <Text fontSize="xs" color="gray.400" py={6} textAlign="center">
                                    No chat sessions recorded.
                                </Text>
                            ) : (
                                <VStack gap={2} align="stretch" maxH="380px" overflowY="auto">
                                    {history.map((h) => (
                                        <Box
                                            key={h.session_id || h.id}
                                            p={3}
                                            borderRadius="lg"
                                            borderWidth="1px"
                                            borderColor={selectedSession?.session_id === h.session_id ? '#1769FF' : 'gray.200'}
                                            bg={selectedSession?.session_id === h.session_id ? '#EFF6FF' : 'white'}
                                            cursor="pointer"
                                            onClick={() => setSelectedSession(h)}
                                        >
                                            <Flex justify="space-between" align="center">
                                                <Text fontWeight="700" fontSize="xs" color="#0A1128">
                                                    {h.visitor_name || h.session_id?.slice(0, 8) || 'Visitor'}
                                                </Text>
                                                <Badge fontSize="2xs">{h.message_count || 1} msgs</Badge>
                                            </Flex>
                                            <Text fontSize="2xs" color="gray.500" mt={1}>
                                                {h.created_at ? new Date(h.created_at).toLocaleString() : 'Recent'}
                                            </Text>
                                        </Box>
                                    ))}
                                </VStack>
                            )}
                        </Box>

                        <Box flex={1} pl={2}>
                            {selectedSession ? (
                                <VStack gap={3} align="stretch">
                                    <Box pb={2} borderBottom="1px solid #E2E8F0">
                                        <Text fontWeight="800" fontSize="sm" color="#0A1128">
                                            {selectedSession.visitor_name || 'Visitor'} Session
                                        </Text>
                                        <Text fontSize="2xs" color="gray.500">
                                            Session ID: {selectedSession.session_id}
                                        </Text>
                                    </Box>
                                    <Box maxH="320px" overflowY="auto" p={2}>
                                        {(selectedSession.messages || []).map((m, idx) => (
                                            <Flex
                                                key={idx}
                                                justify={m.sender_type === 'user' ? 'flex-end' : 'flex-start'}
                                                mb={2}
                                            >
                                                <Box
                                                    maxW="80%"
                                                    p={3}
                                                    borderRadius="xl"
                                                    bg={m.sender_type === 'user' ? '#1769FF' : '#F1F5F9'}
                                                    color={m.sender_type === 'user' ? 'white' : '#0A1128'}
                                                    fontSize="xs"
                                                >
                                                    {m.message}
                                                </Box>
                                            </Flex>
                                        ))}
                                    </Box>
                                </VStack>
                            ) : (
                                <Flex justify="center" align="center" h="100%" color="gray.400" fontSize="xs">
                                    Select a chat session on the left to audit conversation messages
                                </Flex>
                            )}
                        </Box>
                    </Flex>
                )}
            </DashboardCard>

            {/* Knowledge Base Modal */}
            {kbModalOpen && (
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
                    <Box bg="white" borderRadius="2xl" maxW="500px" w="100%" p={6} boxShadow="2xl">
                        <Flex justify="space-between" align="center" mb={4}>
                            <Heading size="md" color="#0A1128">
                                {editingKb ? 'Edit Knowledge Base Rule' : 'Add Knowledge Base Rule'}
                            </Heading>
                            <Button size="sm" variant="ghost" onClick={() => setKbModalOpen(false)}>
                                ✕
                            </Button>
                        </Flex>

                        <form onSubmit={handleSaveKb}>
                            <VStack gap={4} align="stretch">
                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                        Category
                                    </Text>
                                    <Input
                                        placeholder="Escrow, Payments, Security, KYC..."
                                        value={kbForm.category}
                                        onChange={(e) => setKbForm({ ...kbForm, category: e.target.value })}
                                        borderRadius="lg"
                                        required
                                    />
                                </Box>
                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                        User Question / Topic *
                                    </Text>
                                    <Input
                                        placeholder="How do I verify bKash payment receipt?"
                                        value={kbForm.question}
                                        onChange={(e) => setKbForm({ ...kbForm, question: e.target.value })}
                                        borderRadius="lg"
                                        required
                                    />
                                </Box>
                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                        Expected AI Answer / Resolution *
                                    </Text>
                                    <Textarea
                                        rows={4}
                                        placeholder="Explain the step-by-step procedure..."
                                        value={kbForm.answer}
                                        onChange={(e) => setKbForm({ ...kbForm, answer: e.target.value })}
                                        borderRadius="lg"
                                        fontSize="xs"
                                        required
                                    />
                                </Box>

                                <Flex justify="flex-end" gap={3} mt={4}>
                                    <Button size="sm" variant="outline" onClick={() => setKbModalOpen(false)} borderRadius="lg">
                                        Cancel
                                    </Button>
                                    <Button
                                        size="sm"
                                        bg="#1769FF"
                                        color="white"
                                        _hover={{ bg: '#0D52D6' }}
                                        type="submit"
                                        isLoading={saving}
                                        borderRadius="lg"
                                    >
                                        Save Rule
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

export default AiLiveChatModule;
