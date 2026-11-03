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
import { dealsApi } from '../../../services/dashboardApi';
import authService from '../../../services/auth';
import { PageHeader } from '../../../components/common/PageHeader';
import { DashboardCard } from '../../../components/common/DashboardCard';
import { EmptyState } from '../../../components/common/EmptyState';
import { UserAvatar } from '../../../components/common/UserAvatar';
import { SupportIcon } from '../../../components/common/Icons';

export const MessagesModule = () => {
    const { user, currency } = useAuth();
    const [deals, setDeals] = useState([]);
    const [selectedDeal, setSelectedDeal] = useState(null);
    const [messageText, setMessageText] = useState('');
    const [loading, setLoading] = useState(true);
    const [sending, setSending] = useState(false);
    const messagesEndRef = useRef(null);

    const fetchDeals = async () => {
        try {
            const data = await dealsApi.getAll();
            if (data.success && Array.isArray(data.deals)) {
                setDeals(data.deals);
                if (selectedDeal) {
                    const fresh = data.deals.find((d) => d.id === selectedDeal.id);
                    if (fresh) setSelectedDeal(fresh);
                } else if (data.deals.length > 0) {
                    setSelectedDeal(data.deals[0]);
                }
            }
        } catch (e) {
            console.error('Error fetching deals:', e);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchDeals();
        const interval = setInterval(fetchDeals, 6000);
        return () => clearInterval(interval);
    }, []);

    useEffect(() => {
        messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    }, [selectedDeal?.workspace?.messages]);

    const handleSendMessage = async (e) => {
        e.preventDefault();
        if (!selectedDeal || !messageText.trim()) return;

        setSending(true);
        try {
            const res = await dealsApi.sendMessage(selectedDeal.id, messageText.trim());
            if (res.success) {
                setMessageText('');
                fetchDeals();
            }
        } catch (e) {
            console.error('Error sending message:', e);
        } finally {
            setSending(false);
        }
    };

    const currentUserId = user?.id;

    return (
        <Box>
            <PageHeader
                title="Deal Communications Hub"
                subtitle="Direct bilateral communication recorded in the deal audit workspace."
            />

            {loading ? (
                <Flex justify="center" align="center" minH="300px">
                    <Spinner size="lg" color="#1769FF" />
                </Flex>
            ) : deals.length === 0 ? (
                <EmptyState
                    icon={SupportIcon}
                    title="No deal conversations"
                    description="You must participate in at least one escrow deal to start discussions."
                    minH="260px"
                />
            ) : (
                <Grid templateColumns={{ base: '1fr', md: '320px 1fr' }} gap={6} minH="560px">
                    {/* Left Conversations List */}
                    <DashboardCard title="Active Discussions" p={3}>
                        <VStack gap={2} align="stretch" maxH="500px" overflowY="auto">
                            {deals.map((d) => {
                                const isSel = selectedDeal?.id === d.id;
                                const messages = d.workspace?.messages || [];
                                const lastMsg = messages.length > 0 ? messages[messages.length - 1].text : 'No messages yet';

                                return (
                                    <Box
                                        key={d.id}
                                        p={3}
                                        borderRadius="xl"
                                        borderWidth="1px"
                                        borderColor={isSel ? '#1769FF' : 'gray.200'}
                                        bg={isSel ? '#EEF6FF' : 'white'}
                                        cursor="pointer"
                                        transition="all 0.15s"
                                        onClick={() => setSelectedDeal(d)}
                                    >
                                        <Flex gap={2.5} align="center">
                                            <UserAvatar name={d.counterparty_email} size="sm" />
                                            <Box flex={1} overflow="hidden">
                                                <Text fontSize="xs" fontWeight="700" color="#0A1128" truncate>
                                                    {d.title}
                                                </Text>
                                                <Text fontSize="2xs" color="gray.500" truncate>
                                                    {lastMsg}
                                                </Text>
                                            </Box>
                                        </Flex>
                                    </Box>
                                );
                            })}
                        </VStack>
                    </DashboardCard>

                    {/* Right Conversation Window */}
                    {selectedDeal ? (
                        <DashboardCard
                            title={selectedDeal.title}
                            subtitle={`Counterparty: ${selectedDeal.counterparty_email} • Deal #${selectedDeal.deal_id || selectedDeal.id}`}
                        >
                            <VStack gap={4} align="stretch" h="100%">
                                {/* Messages Container */}
                                <Box
                                    flex={1}
                                    minH="360px"
                                    maxH="420px"
                                    overflowY="auto"
                                    p={4}
                                    bg="gray.50"
                                    borderRadius="xl"
                                    borderWidth="1px"
                                    borderColor="gray.200"
                                >
                                    {(!selectedDeal.workspace?.messages || selectedDeal.workspace.messages.length === 0) ? (
                                        <Flex justify="center" align="center" h="100%">
                                            <Text fontSize="xs" color="gray.400" fontStyle="italic">
                                                Conversation thread initialized. Send a message to coordinate work.
                                            </Text>
                                        </Flex>
                                    ) : (
                                        <VStack gap={2.5} align="stretch">
                                            {selectedDeal.workspace.messages.map((m, idx) => {
                                                const isMe = m.sender_id === currentUserId;
                                                return (
                                                    <Box
                                                        key={idx}
                                                        alignSelf={isMe ? 'flex-end' : 'flex-start'}
                                                        maxW="70%"
                                                        bg={isMe ? '#002147' : 'white'}
                                                        color={isMe ? 'white' : '#0A1128'}
                                                        p={3}
                                                        borderRadius="xl"
                                                        shadow="xs"
                                                        borderWidth={isMe ? '0px' : '1px'}
                                                        borderColor="gray.200"
                                                    >
                                                        <Text fontSize="xs">{m.text}</Text>
                                                        <Text fontSize="3xs" opacity={0.7} mt={1} textAlign="right">
                                                            {m.created_at ? new Date(m.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : ''}
                                                        </Text>
                                                    </Box>
                                                );
                                            })}
                                            <div ref={messagesEndRef} />
                                        </VStack>
                                    )}
                                </Box>

                                {/* Input Form */}
                                <form onSubmit={handleSendMessage}>
                                    <HStack gap={2}>
                                        <input
                                            type="text"
                                            value={messageText}
                                            onChange={(e) => setMessageText(e.target.value)}
                                            placeholder="Write message to counterparty..."
                                            style={{
                                                flex: 1,
                                                padding: '10px 14px',
                                                borderRadius: '8px',
                                                border: '1px solid #E2E8F0',
                                                fontSize: '13px',
                                                outline: 'none',
                                            }}
                                        />
                                        <Button size="md" bg="#002147" color="white" type="submit" loading={sending} px={6}>
                                            Send
                                        </Button>
                                    </HStack>
                                </form>
                            </VStack>
                        </DashboardCard>
                    ) : null}
                </Grid>
            )}
        </Box>
    );
};

export default MessagesModule;

