import React, { useState } from 'react';
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
    SimpleGrid,
} from '@chakra-ui/react';
import { adminApi } from '../../../services/adminApi';
import { DashboardCard } from '../../../components/common/DashboardCard';

export const AnnouncementsModule = () => {
    const [formData, setFormData] = useState({
        to: 'all',
        specificEmail: '',
        title: '',
        message: '',
        type: 'announcement', // 'announcement' | 'security' | 'maintenance'
    });
    const [sending, setSending] = useState(false);
    const [feedback, setFeedback] = useState(null);
    const [recentBroadcasts, setRecentBroadcasts] = useState([
        {
            id: 1,
            title: 'bKash Merchant Rail Maintenance Window',
            message: 'Scheduled gateway maintenance completed with zero downtime.',
            audience: 'All Members',
            type: 'maintenance',
            sent_at: '2026-11-02 04:00',
        },
        {
            id: 2,
            title: 'National Identity KYC Policy Update',
            message: 'All sellers exceeding ৳ 50,000 threshold must submit NID verification.',
            audience: 'Unverified Members',
            type: 'security',
            sent_at: '2026-10-28 12:30',
        },
    ]);

    const handleSend = async (e) => {
        e.preventDefault();
        if (!formData.title.trim() || !formData.message.trim()) {
            setFeedback({ type: 'error', text: 'Title and message are required.' });
            return;
        }

        setSending(true);
        try {
            await adminApi.sendBroadcastAnnouncement({
                to: formData.to === 'specific' ? formData.specificEmail : formData.to,
                title: formData.title,
                message: formData.message,
                type: formData.type,
            });

            const newBroadcast = {
                id: Date.now(),
                title: formData.title,
                message: formData.message,
                audience: formData.to === 'all' ? 'All Members' : formData.to === 'specific' ? formData.specificEmail : 'Target Group',
                type: formData.type,
                sent_at: new Date().toLocaleString(),
            };

            setRecentBroadcasts((prev) => [newBroadcast, ...prev]);
            setFeedback({ type: 'success', text: `Broadcast announcement dispatched to "${newBroadcast.audience}".` });
            setFormData({
                to: 'all',
                specificEmail: '',
                title: '',
                message: '',
                type: 'announcement',
            });
        } catch (err) {
            setFeedback({
                type: 'error',
                text: err.response?.data?.message || 'Failed to dispatch broadcast announcement.',
            });
        } finally {
            setSending(false);
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

            <SimpleGrid columns={{ base: 1, lg: 12 }} gap={6}>
                {/* Send Broadcast Form */}
                <Box gridColumn={{ base: 'span 1', lg: 'span 7' }}>
                    <DashboardCard
                        title="Broadcast Notification Dispatcher"
                        subtitle="Send high-priority in-app alerts and notifications to buyers, sellers, or specific accounts"
                    >
                        <form onSubmit={handleSend}>
                            <VStack gap={4} align="stretch">
                                <Flex gap={4}>
                                    <Box flex={1}>
                                        <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                            Target Audience
                                        </Text>
                                        <select
                                            style={{
                                                width: '100%',
                                                padding: '8px 12px',
                                                borderRadius: '8px',
                                                border: '1px solid #CBD5E1',
                                                fontSize: '14px',
                                            }}
                                            value={formData.to}
                                            onChange={(e) => setFormData({ ...formData, to: e.target.value })}
                                        >
                                            <option value="all">All Registered Users</option>
                                            <option value="verified">Verified KYC Only</option>
                                            <option value="specific">Specific User Email</option>
                                        </select>
                                    </Box>

                                    <Box flex={1}>
                                        <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                            Alert Category
                                        </Text>
                                        <select
                                            style={{
                                                width: '100%',
                                                padding: '8px 12px',
                                                borderRadius: '8px',
                                                border: '1px solid #CBD5E1',
                                                fontSize: '14px',
                                            }}
                                            value={formData.type}
                                            onChange={(e) => setFormData({ ...formData, type: e.target.value })}
                                        >
                                            <option value="announcement">Platform Announcement</option>
                                            <option value="security">Security Alert</option>
                                            <option value="maintenance">Maintenance Notice</option>
                                        </select>
                                    </Box>
                                </Flex>

                                {formData.to === 'specific' && (
                                    <Box>
                                        <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                            Recipient Email Address *
                                        </Text>
                                        <Input
                                            type="email"
                                            placeholder="client@migratepay.com"
                                            value={formData.specificEmail}
                                            onChange={(e) => setFormData({ ...formData, specificEmail: e.target.value })}
                                            borderRadius="lg"
                                            required
                                        />
                                    </Box>
                                )}

                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                        Notification Title *
                                    </Text>
                                    <Input
                                        placeholder="e.g. Scheduled Network Upgrade this Friday"
                                        value={formData.title}
                                        onChange={(e) => setFormData({ ...formData, title: e.target.value })}
                                        borderRadius="lg"
                                        required
                                    />
                                </Box>

                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                        Detailed Message *
                                    </Text>
                                    <Textarea
                                        rows={4}
                                        placeholder="Write notice instructions or advisory here..."
                                        value={formData.message}
                                        onChange={(e) => setFormData({ ...formData, message: e.target.value })}
                                        borderRadius="lg"
                                        required
                                    />
                                </Box>

                                <Flex justify="flex-end" mt={2}>
                                    <Button
                                        size="sm"
                                        bg="#1769FF"
                                        color="white"
                                        _hover={{ bg: '#0D52D6' }}
                                        type="submit"
                                        isLoading={sending}
                                        borderRadius="lg"
                                    >
                                        Dispatch Broadcast
                                    </Button>
                                </Flex>
                            </VStack>
                        </form>
                    </DashboardCard>
                </Box>

                {/* Broadcast History */}
                <Box gridColumn={{ base: 'span 1', lg: 'span 5' }}>
                    <DashboardCard
                        title="Broadcast Dispatch History"
                        subtitle="Recent announcements broadcast across the network"
                    >
                        <VStack gap={3} align="stretch">
                            {recentBroadcasts.map((b) => (
                                <Box
                                    key={b.id}
                                    p={3}
                                    borderRadius="xl"
                                    borderWidth="1px"
                                    borderColor="gray.200"
                                    bg="#F8FAFC"
                                >
                                    <Flex justify="space-between" align="center" mb={1}>
                                        <Badge
                                            bg={
                                                b.type === 'security'
                                                    ? '#FEE2E2'
                                                    : b.type === 'maintenance'
                                                    ? '#FEF3C7'
                                                    : '#EFF6FF'
                                            }
                                            color={
                                                b.type === 'security'
                                                    ? '#991B1B'
                                                    : b.type === 'maintenance'
                                                    ? '#92400E'
                                                    : '#1E40AF'
                                            }
                                            fontSize="2xs"
                                            px={1.5}
                                            py={0.5}
                                            borderRadius="sm"
                                        >
                                            {b.type}
                                        </Badge>
                                        <Text fontSize="2xs" color="gray.400">
                                            {b.sent_at}
                                        </Text>
                                    </Flex>
                                    <Text fontWeight="800" fontSize="xs" color="#0A1128">
                                        {b.title}
                                    </Text>
                                    <Text fontSize="xs" color="gray.600" mt={1} noOfLines={2}>
                                        {b.message}
                                    </Text>
                                    <Text fontSize="2xs" color="gray.500" mt={2}>
                                        Audience: <strong>{b.audience}</strong>
                                    </Text>
                                </Box>
                            ))}
                        </VStack>
                    </DashboardCard>
                </Box>
            </SimpleGrid>
        </VStack>
    );
};

export default AnnouncementsModule;
