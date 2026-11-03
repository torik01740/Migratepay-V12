import React, { useState, useEffect, useRef } from 'react';
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
import { supportDeskApi } from '../../../services/dashboardApi';
import { PageHeader } from '../../../components/common/PageHeader';
import { DashboardCard } from '../../../components/common/DashboardCard';
import { EmptyState } from '../../../components/common/EmptyState';
import { toaster } from '../../../components/feedback/toaster';
import { SupportIcon, PlusIcon, CheckCircleIcon } from '../../../components/common/Icons';

export const SupportModule = () => {
    const { user } = useAuth();
    const [tickets, setTickets] = useState([]);
    const [selectedTicket, setSelectedTicket] = useState(null);
    const [loading, setLoading] = useState(true);
    const [showNewModal, setShowNewModal] = useState(false);

    // New Ticket Form
    const [subj, setSubj] = useState('');
    const [priority, setPriority] = useState('General Inquiry');
    const [msg, setMsg] = useState('');
    const [attachment, setAttachment] = useState(null);
    const [submitting, setSubmitting] = useState(false);

    // Reply Form
    const [replyText, setReplyText] = useState('');
    const [replyFile, setReplyFile] = useState(null);
    const [sendingReply, setSendingReply] = useState(false);
    const messagesEndRef = useRef(null);

    const fetchTickets = async () => {
        try {
            const data = await supportDeskApi.getTickets();
            if (data.success && Array.isArray(data.tickets)) {
                setTickets(data.tickets);
                if (selectedTicket) {
                    const fresh = data.tickets.find((t) => t.id === selectedTicket.id);
                    if (fresh) setSelectedTicket(fresh);
                } else if (data.tickets.length > 0) {
                    setSelectedTicket(data.tickets[0]);
                }
            }
        } catch (e) {
            console.error('Error fetching tickets:', e);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchTickets();
        const interval = setInterval(fetchTickets, 6000);
        return () => clearInterval(interval);
    }, []);

    useEffect(() => {
        messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    }, [selectedTicket?.messages]);

    const handleCreateTicket = async (e) => {
        e.preventDefault();
        if (!subj.trim() || !msg.trim()) return;

        setSubmitting(true);
        try {
            const formData = new FormData();
            formData.append('subject', subj.trim());
            formData.append('priority', priority);
            formData.append('message', msg.trim());
            if (attachment) formData.append('attachment', attachment);

            const data = await supportDeskApi.createTicket(formData);
            if (data.success) {
                toaster.create({ title: 'Ticket Created', description: 'Our support team will respond shortly.', type: 'success' });
                setSubj('');
                setMsg('');
                setAttachment(null);
                setShowNewModal(false);
                fetchTickets();
            } else {
                toaster.create({ title: 'Error', description: data.error || 'Failed to create ticket.', type: 'error' });
            }
        } catch (err) {
            toaster.create({ title: 'Error', description: err.message, type: 'error' });
        } finally {
            setSubmitting(false);
        }
    };

    const handleSendReply = async (e) => {
        e.preventDefault();
        if (!selectedTicket || !replyText.trim()) return;

        setSendingReply(true);
        try {
            const formData = new FormData();
            formData.append('message', replyText.trim());
            if (replyFile) formData.append('attachment', replyFile);

            const data = await supportDeskApi.replyTicket(selectedTicket.id, formData);
            if (data.success) {
                setReplyText('');
                setReplyFile(null);
                fetchTickets();
            }
        } catch (err) {
            toaster.create({ title: 'Error', description: err.message, type: 'error' });
        } finally {
            setSendingReply(false);
        }
    };

    return (
        <Box>
            <PageHeader
                title="Customer Support Desk"
                subtitle="Official dispute arbitration, billing assistance, and technical tickets."
                actions={
                    <Button
                        size="sm"
                        bg="#002147"
                        color="white"
                        _hover={{ bg: '#0A1128' }}
                        borderRadius="lg"
                        onClick={() => setShowNewModal(true)}
                    >
                        <PlusIcon size={16} style={{ marginRight: '6px' }} />
                        Open Support Ticket
                    </Button>
                }
            />

            {loading ? (
                <Flex justify="center" align="center" minH="300px"><Spinner size="lg" color="#1769FF" /></Flex>
            ) : tickets.length === 0 ? (
                <EmptyState
                    icon={SupportIcon}
                    title="No support tickets opened"
                    description="Need help with an escrow transaction, deposit, or account verification? Open a ticket to speak with staff."
                    actionLabel="Open Support Ticket"
                    onAction={() => setShowNewModal(true)}
                    minH="260px"
                />
            ) : (
                <Grid templateColumns={{ base: '1fr', lg: '1fr 2fr' }} gap={6} minH="560px">
                    {/* Left: Tickets List */}
                    <DashboardCard title={`Support Tickets (${tickets.length})`} p={3}>
                        <VStack gap={2} align="stretch" maxH="520px" overflowY="auto">
                            {tickets.map((t) => {
                                const isSel = selectedTicket?.id === t.id;
                                const isClosed = t.status === 'Closed' || t.status === 'Resolved';
                                return (
                                    <Box
                                        key={t.id}
                                        p={3}
                                        borderRadius="xl"
                                        borderWidth="1px"
                                        borderColor={isSel ? '#1769FF' : 'gray.200'}
                                        bg={isSel ? '#EEF6FF' : 'white'}
                                        cursor="pointer"
                                        onClick={() => setSelectedTicket(t)}
                                    >
                                        <Flex justify="space-between" align="flex-start" mb={1}>
                                            <Text fontSize="xs" fontWeight="700" color="#0A1128" truncate maxW="160px">
                                                {t.subject}
                                            </Text>
                                            <Badge
                                                colorPalette={isClosed ? 'gray' : t.status === 'Replied' ? 'blue' : 'orange'}
                                                variant="subtle"
                                                fontSize="3xs"
                                                px={1.5}
                                            >
                                                {t.status}
                                            </Badge>
                                        </Flex>
                                        <Flex justify="space-between" fontSize="3xs" color="gray.500">
                                            <Text fontFamily="mono">#{t.ticket_id || t.id}</Text>
                                            <Text>{new Date(t.created_at).toLocaleDateString()}</Text>
                                        </Flex>
                                    </Box>
                                );
                            })}
                        </VStack>
                    </DashboardCard>

                    {/* Right: Ticket Conversation Thread */}
                    {selectedTicket ? (
                        <DashboardCard
                            title={selectedTicket.subject}
                            subtitle={`Ticket #${selectedTicket.ticket_id || selectedTicket.id} • Priority: ${selectedTicket.priority || 'Normal'}`}
                            headerRight={
                                <Badge colorPalette={selectedTicket.status === 'Closed' ? 'gray' : 'green'} fontSize="xs" px={2} py={0.5}>
                                    {selectedTicket.status}
                                </Badge>
                            }
                        >
                            <VStack gap={4} align="stretch">
                                {/* Message History */}
                                <Box
                                    minH="320px"
                                    maxH="400px"
                                    overflowY="auto"
                                    p={4}
                                    bg="gray.50"
                                    borderRadius="xl"
                                    borderWidth="1px"
                                    borderColor="gray.200"
                                >
                                    <VStack gap={3} align="stretch">
                                        {/* Original Message */}
                                        <Box bg="white" p={3.5} borderRadius="xl" borderWidth="1px" borderColor="gray.200">
                                            <Flex justify="space-between" mb={1}>
                                                <Text fontSize="xs" fontWeight="800" color="#0A1128">
                                                    {user?.name} (You)
                                                </Text>
                                                <Text fontSize="3xs" color="gray.400">
                                                    {new Date(selectedTicket.created_at).toLocaleString()}
                                                </Text>
                                            </Flex>
                                            <Text fontSize="xs" color="gray.700">{selectedTicket.message}</Text>
                                        </Box>

                                        {/* Replies */}
                                        {(selectedTicket.messages || []).map((m, idx) => {
                                            const isStaff = m.user_id !== user?.id;
                                            return (
                                                <Box
                                                    key={idx}
                                                    alignSelf={isStaff ? 'flex-start' : 'flex-end'}
                                                    maxW="80%"
                                                    bg={isStaff ? '#0A1128' : 'blue.50'}
                                                    color={isStaff ? 'white' : '#0A1128'}
                                                    p={3}
                                                    borderRadius="xl"
                                                    shadow="xs"
                                                >
                                                    <Flex justify="space-between" gap={4} mb={1}>
                                                        <Text fontSize="3xs" fontWeight="800" opacity={0.8}>
                                                            {isStaff ? '🛡️ MigratePay Staff' : 'You'}
                                                        </Text>
                                                        <Text fontSize="3xs" opacity={0.6}>
                                                            {new Date(m.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                                                        </Text>
                                                    </Flex>
                                                    <Text fontSize="xs">{m.message}</Text>
                                                </Box>
                                            );
                                        })}
                                        <div ref={messagesEndRef} />
                                    </VStack>
                                </Box>

                                {/* Reply Input Form */}
                                {selectedTicket.status !== 'Closed' && (
                                    <form onSubmit={handleSendReply}>
                                        <VStack gap={2} align="stretch">
                                            <textarea
                                                rows={2}
                                                value={replyText}
                                                onChange={(e) => setReplyText(e.target.value)}
                                                placeholder="Write your reply to staff..."
                                                style={{ width: '100%', padding: '9px 12px', borderRadius: '8px', border: '1px solid #CBD5E1', fontSize: '13px', outline: 'none' }}
                                                required
                                            />
                                            <HStack justify="space-between">
                                                <input type="file" onChange={(e) => setReplyFile(e.target.files[0])} style={{ fontSize: '12px' }} />
                                                <Button size="sm" bg="#002147" color="white" type="submit" loading={sendingReply} px={5}>
                                                    Reply
                                                </Button>
                                            </HStack>
                                        </VStack>
                                    </form>
                                )}
                            </VStack>
                        </DashboardCard>
                    ) : null}
                </Grid>
            )}

            {/* New Ticket Modal */}
            {showNewModal && (
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
                    <Box bg="white" borderRadius="2xl" maxW="520px" w="100%" p={6} shadow="2xl">
                        <Flex justify="space-between" align="center" mb={4}>
                            <Heading size="md" color="#0A1128">Open New Support Ticket</Heading>
                            <Button size="2xs" variant="ghost" onClick={() => setShowNewModal(false)}>✕</Button>
                        </Flex>

                        <form onSubmit={handleCreateTicket}>
                            <VStack gap={3} align="stretch">
                                <Box>
                                    <Text fontSize="2xs" fontWeight="700">Subject *</Text>
                                    <input
                                        type="text"
                                        value={subj}
                                        onChange={(e) => setSubj(e.target.value)}
                                        placeholder="e.g. Question regarding Deal #1002"
                                        style={{ width: '100%', padding: '8px 12px', border: '1px solid #CBD5E1', borderRadius: '6px' }}
                                        required
                                    />
                                </Box>

                                <Box>
                                    <Text fontSize="2xs" fontWeight="700">Priority Level</Text>
                                    <select
                                        value={priority}
                                        onChange={(e) => setPriority(e.target.value)}
                                        style={{ width: '100%', padding: '8px 12px', border: '1px solid #CBD5E1', borderRadius: '6px' }}
                                    >
                                        <option value="General Inquiry">General Inquiry</option>
                                        <option value="Billing / Payment Issue">Billing / Payment Issue</option>
                                        <option value="Dispute Mediation">Dispute Mediation</option>
                                        <option value="Urgent Escrow Hold">Urgent Escrow Hold</option>
                                    </select>
                                </Box>

                                <Box>
                                    <Text fontSize="2xs" fontWeight="700">Message Description *</Text>
                                    <textarea
                                        rows={4}
                                        value={msg}
                                        onChange={(e) => setMsg(e.target.value)}
                                        placeholder="Detail the issue with IDs, transaction references, or counterparty information..."
                                        style={{ width: '100%', padding: '8px 12px', border: '1px solid #CBD5E1', borderRadius: '6px', fontFamily: 'inherit' }}
                                        required
                                    />
                                </Box>

                                <Box>
                                    <Text fontSize="2xs" fontWeight="700">Attachment (Screenshot/Receipt)</Text>
                                    <input type="file" onChange={(e) => setAttachment(e.target.files[0])} style={{ fontSize: '12px' }} />
                                </Box>

                                <HStack justify="flex-end" gap={2} mt={3}>
                                    <Button size="xs" variant="outline" onClick={() => setShowNewModal(false)}>Cancel</Button>
                                    <Button size="xs" bg="#002147" color="white" type="submit" loading={submitting}>Submit Ticket</Button>
                                </HStack>
                            </VStack>
                        </form>
                    </Box>
                </Box>
            )}
        </Box>
    );
};

export default SupportModule;

