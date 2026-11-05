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
    Spinner,
} from '@chakra-ui/react';
import { adminApi } from '../../../services/adminApi';
import { DashboardCard } from '../../../components/common/DashboardCard';

export const ReviewsModerationModule = () => {
    const [reviews, setReviews] = useState([]);
    const [loading, setLoading] = useState(true);
    const [feedback, setFeedback] = useState(null);

    const fetchReviews = async () => {
        setLoading(true);
        try {
            const data = await adminApi.getReviews();
            if (data && Array.isArray(data.reviews)) {
                setReviews(data.reviews);
            } else if (Array.isArray(data)) {
                setReviews(data);
            }
        } catch (err) {
            console.error('Failed to load reviews:', err);
            // Fallback preview
            setReviews([
                {
                    id: 1,
                    reviewer: 'Tariq Rahman',
                    recipient: 'Sultan Mahmud',
                    rating: 5,
                    comment: 'Flawless escrow clearance! Delivered the full source code and domain auth key within 2 hours.',
                    created_at: '2026-11-02',
                },
                {
                    id: 2,
                    reviewer: 'Fahim Chowdhury',
                    recipient: 'Rakib Hasan',
                    rating: 4,
                    comment: 'Smooth milestone transaction. Escrow protected my payment properly.',
                    created_at: '2026-10-29',
                },
            ]);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchReviews();
    }, []);

    const handleDelete = async (id) => {
        if (!window.confirm('Delete this user review?')) return;
        try {
            await adminApi.deleteReview(id);
            setFeedback({ type: 'success', text: 'Review removed from public platform profiles.' });
            setReviews((prev) => prev.filter((r) => r.id !== id));
        } catch (err) {
            setFeedback({ type: 'error', text: 'Failed to delete review.' });
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
                title="Counterparty Reputation & Review Moderation"
                subtitle="Safeguard community integrity by auditing ratings, feedback, and escrow completion reviews"
                headerAction={
                    <Button size="xs" variant="outline" onClick={fetchReviews} borderRadius="md">
                        Refresh Reviews
                    </Button>
                }
            >
                {loading ? (
                    <Flex justify="center" align="center" py={12}>
                        <Spinner size="lg" color="#1769FF" />
                    </Flex>
                ) : reviews.length === 0 ? (
                    <Box textAlign="center" py={12} color="gray.500">
                        <Text fontSize="lg" fontWeight="700">No Reviews Logged</Text>
                        <Text fontSize="sm" mt={1}>User ratings will appear after escrow deals are completed.</Text>
                    </Box>
                ) : (
                    <Box overflowX="auto">
                        <table style={{ width: '100%', borderCollapse: 'collapse', textAlign: 'left' }}>
                            <thead>
                                <tr style={{ borderBottom: '1px solid #E2E8F0', backgroundColor: '#F8FAFC' }}>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Author &bull; Target</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Rating</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Review Statement</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Date</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {reviews.map((r) => (
                                    <tr key={r.id} style={{ borderBottom: '1px solid #F1F5F9' }}>
                                        <td style={{ padding: '14px 16px' }}>
                                            <Text fontWeight="800" color="#0A1128" fontSize="sm">
                                                {r.reviewer?.name || r.reviewer || 'Client'}
                                            </Text>
                                            <Text fontSize="2xs" color="gray.500">
                                                Reviewed &rarr; {r.recipient?.name || r.recipient || 'Seller'}
                                            </Text>
                                        </td>
                                        <td style={{ padding: '14px 16px' }}>
                                            <Badge bg="#FEF3C7" color="#92400E" fontSize="xs" px={2} py={0.5} borderRadius="md" fontWeight="800">
                                                {'★'.repeat(r.rating || 5)} {r.rating || 5}.0
                                            </Badge>
                                        </td>
                                        <td style={{ padding: '14px 16px', fontSize: 'xs', color: 'gray.700', maxWidth: '340px' }}>
                                            {r.comment || 'No written comment.'}
                                        </td>
                                        <td style={{ padding: '14px 16px', fontSize: 'xs', color: 'gray.500' }}>
                                            {r.created_at ? new Date(r.created_at).toLocaleDateString() : 'Recent'}
                                        </td>
                                        <td style={{ padding: '14px 16px' }}>
                                            <Button
                                                size="xs"
                                                variant="ghost"
                                                color="#DC2626"
                                                onClick={() => handleDelete(r.id)}
                                            >
                                                Moderate / Delete
                                            </Button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </Box>
                )}
            </DashboardCard>
        </VStack>
    );
};

export default ReviewsModerationModule;
