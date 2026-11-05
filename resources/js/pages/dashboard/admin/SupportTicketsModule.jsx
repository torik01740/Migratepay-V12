import React, { useState, useEffect, useRef } from 'react';
import {
    Box,
    Flex,
    Grid,
    HStack,
    VStack,
    Text,
    Heading,
    Button,
    Badge,
    Input,
    Spinner,
    Textarea,
} from '@chakra-ui/react';
import adminApi from '../../../services/adminApi';
import { PageHeader } from '../../../components/common/PageHeader';
import { DashboardCard } from '../../../components/common/DashboardCard';
import { EmptyState } from '../../../components/common/EmptyState';
import { toaster } from '../../../components/feedback/toaster';
import {
    SupportIcon,
    CheckCircleIcon,
    AlertCircleIcon,
    CloseIcon,
} from '../../../components/common/Icons';

export const SupportTicketsModule = () => {
    const [tickets, setTickets] = useState([]);
    const [selectedTicket, setSelectedTicket] = useState(null);
    const [replyMsg, setReplyMsg] = useState('');
    const [loading, setLoading] = useState(true);
    const [submitting, setSubmitting] = useState(false);
    const messagesEndRef = useRef(null);

    const fetchTickets = async () => {
        try {
            const data = await adminApi.getTickets();
            const list = Array.isArray(data) ? data : data.tickets || [];
            setTickets(list);
            if (selectedTicket) {
                const updated = list.find((t) => t.id === selectedTicket.id);
                if (updated) setSelectedTicket(updated);
            }
        } catch (e) {
            console.error('Failed to fetch support tickets:', e);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchTickets();
    }, []);

    const handleSendReply = async () => {
        if (!replyMsg.trim() || !selectedTicket) return;
        setSubmitting(true);
        try {
            const formData = new FormData();
            formData.append('message', replyMsg);

            const res = await adminApi.replyTicket(selectedTicket.id, formData);
            if (res.success || res) {
                toaster.create({
                    title: 'Reply Delivered',
                    description: 'Your response was posted to customer ticket.',
                    type: 'success',
                });
                setReplyMsg('');
                fetchTickets();
            }
        } catch (e) {
            console.error(e);
            toaster.create({
                title: 'Reply Failed',
                description: 'Server failed to deliver reply.',
                type: 'error',
            });
        } finally {
            setSubmitting(false);
        }
    };

    const handleCloseTicket = async () => {
        if (!selectedTicket || !window.confirm('Close and resolve this support ticket?')) return;
        try {
            await adminApi.closeTicket(selectedTicket.id);
            toaster.create({
                title: 'Ticket Resolved',
                description: 'Support ticket marked as closed.',
                type: 'info',
            });
            fetchTickets();
        } catch (e) {
            console.error(e);
        }
    };

    return (
        <Box>
            <PageHeader
                title="Customer Support Desk Queue"
                subtitle="Address customer assistance requests, handle escalated inquiries, and communicate directly with users."
                actions={
                    <Button
                        size="sm"
                        variant="outline"
                        borderColor="gray.300"
                        borderRadius="lg"
                        onClick={fetchTickets}
                    >
                        Refresh Queue
                    </Button>
                }
            />

            <Grid templateColumns={{ base: '1fr', lg: '380px 1fr' }} gap={6} alignItems="flex-start">
                {/* Tickets List */}
                <DashboardCard title={`Active Tickets (${tickets.length})`} p={0}>
                    {loading ? (
                        <Flex justify="center" py={12}>
                            <Spinner size="lg" color="#1769FF" />
                        </Flex>
                    ) : tickets.length === 0 ? (
                        <Box p={6}>
                            <EmptyState
                                title="No Tickets Open"
                                description="Customer support requests will arrive here automatically."
                            />
                        </Box>
                    ) : (
                        <VStack gap={0} align="stretch" maxH="600px" overflowY="auto">
                            {tickets.map((t) => {
                                const isSelected = selectedTicket?.id === t.id;
                                return (
                                    <Box
                                        key={t.id}
                                        p={4}
                                        borderBottomWidth="1px"
                                        borderColor="gray.100"
                                        bg={isSelected ? 'blue.50' : 'white'}
                                        cursor="pointer"
                                        _hover={{ bg: isSelected ? 'blue.50' : 'gray.50' }}
                                        onClick={() => setSelectedTicket(t)}
                                        transition="background 0.15s"
                                    >
                                        <Flex justify="space-between" align="flex-start" mb={1}>
                                            <Text fontSize="xs" fontWeight="700" color="#0A1128" lineClamp={1}>
                                                {t.subject}
                                            </Text>
                                            <Badge
                                                colorPalette={
                                                    t.status === 'open' ? 'green' : t.status === 'closed' ? 'gray' : 'blue'
                                                }
                                                variant="subtle"
                                                fontSize="2xs"
                                                textTransform="uppercase"
                                            >
                                                {t.status}
                                            </Badge>
                                        </Flex>
                                        <Text fontSize="2xs" color="gray.500" mb={2} lineClamp={2}>
                                            {t.message || t.latest_message?.message || 'No preview available'}
                                        </Text>
                                        <Flex justify="space-between" align="center">
                                            <Text fontSize="2xs" color="gray.400">
                                                {t.user?.email || 'Guest User'}
                                            </Text>
                                            <Badge colorPalette="gray" variant="outline" fontSize="2xs">
                                                {t.priority || 'medium'}
                                            </Badge>
                                        </Flex>
                                    </Box>
                                );
                            })}
                        </VStack>
                    )}
                </DashboardCard>

                {/* Conversation Thread */}
                <DashboardCard
                    title={selectedTicket ? selectedTicket.subject : 'Ticket Thread'}
                    subtitle={selectedTicket ? `From ${selectedTicket.user?.email || 'User'} • Status: ${selectedTicket.status}` : 'Select a ticket to review conversation history.'}
                    actions={
                        selectedTicket && selectedTicket.status !== 'closed' ? (
                            <Button
                                size="xs"
                                colorPalette="red"
                                variant="outline"
                                onClick={handleCloseTicket}
                            >
                                Mark as Resolved
                            </Button>
                        ) : null
                    }
                >
                    {!selectedTicket ? (
                        <Flex justify="center" align="center" minH="300px" color="gray.400">
                            <Text fontSize="sm">Select a ticket from the left panel to inspect details.</Text>
                        </Flex>
                    ) : (
                        <Flex direction="column" h="500px">
                            {/* Message Thread */}
                            <Box flex={1} overflowY="auto" p={4} bg="gray.50" borderRadius="xl" mb={4}>
                                <VStack gap={3} align="stretch">
                                    {/* Initial Ticket Message */}
                                    <Box bg="white" p={3.5} borderRadius="lg" borderWidth="1px" borderColor="gray.200" maxW="85%" alignSelf="flex-start">
                                        <Text fontSize="2xs" fontWeight="700" color="gray.500" mb={1}>
                                            {selectedTicket.user?.name || selectedTicket.user?.email || 'Client'}
                                        </Text>
                                        <Text fontSize="xs" color="#0A1128">
                                            {selectedTicket.message}
                                        </Text>
                                    </Box>

                                    {/* Replies */}
                                    {(selectedTicket.messages || []).map((m) => {
                                        const isAdmin = m.is_admin || m.sender_type === 'admin' || m.user_id !== selectedTicket.user_id;
                                        return (
                                            <Box
                                                key={m.id}
                                                bg={isAdmin ? '#0A1128' : 'white'}
                                                color={isAdmin ? 'white' : '#0A1128'}
                                                p={3.5}
                                                borderRadius="lg"
                                                borderWidth={isAdmin ? '0' : '1px'}
                                                borderColor="gray.200"
                                                maxW="85%"
                                                alignSelf={isAdmin ? 'flex-end' : 'flex-start'}
                                            >
                                                <Text fontSize="2xs" fontWeight="700" color={isAdmin ? 'gray.300' : 'gray.500'} mb={1}>
                                                    {isAdmin ? 'Staff Support' : 'Customer'}
                                                </Text>
                                                <Text fontSize="xs">{m.message}</Text>
                                            </Box>
                                        );
                                    })}
                                    <div ref={messagesEndRef} />
                                </VStack>
                            </Box>

                            {/* Reply Box */}
                            {selectedTicket.status !== 'closed' ? (
                                <HStack gap={3} align="flex-end">
                                    <Textarea
                                        flex={1}
                                        size="sm"
                                        placeholder="Type administrative reply..."
                                        rows={2}
                                        value={replyMsg}
                                        onChange={(e) => setReplyMsg(e.target.value)}
                                        borderRadius="xl"
                                    />
                                    <Button
                                        size="md"
                                        bg="#1769FF"
                                        color="white"
                                        _hover={{ bg: '#0F52BA' }}
                                        borderRadius="xl"
                                        px={6}
                                        loading={submitting}
                                        onClick={handleSendReply}
                                    >
                                        Send Reply
                                    </Button>
                                </HStack>
                            ) : (
                                <Box p={3} bg="gray.100" borderRadius="lg" textAlign="center">
                                    <Text fontSize="xs" color="gray.600">
                                        This ticket has been marked as resolved and closed.
                                    </Text>
                                </Box>
                            )}
                        </Flex>
                    )}
                </DashboardCard>
            </Grid>
        </Box>
    );
};

export default SupportTicketsModule;

