import React, { useState } from 'react';
import {
    Dialog,
    Box,
    Flex,
    VStack,
    HStack,
    Text,
    Heading,
    Button,
    Portal,
} from '@chakra-ui/react';
import { dealsApi } from '../../services/dashboardApi';
import { toaster } from '../feedback/toaster';

export const ReviewModal = ({ deal, open, onOpenChange, onSuccess }) => {
    const [communication, setCommunication] = useState(5);
    const [behavior, setBehavior] = useState(5);
    const [delivery, setDelivery] = useState(5);
    const [reliability, setReliability] = useState(5);
    const [comment, setComment] = useState('');
    const [submitting, setSubmitting] = useState(false);

    const overallRating = Math.round((communication + behavior + delivery + reliability) / 4);

    const handleSubmit = async () => {
        if (!deal) return;
        setSubmitting(true);
        try {
            const data = await dealsApi.submitReview(deal.id, overallRating, comment, {
                communication,
                behavior,
                delivery,
                reliability,
            });

            if (data.success) {
                toaster.create({
                    title: 'Review Submitted',
                    description: 'Thank you! Your ratings have been recorded on the ledger.',
                    type: 'success',
                });
                onSuccess?.();
                onOpenChange?.({ open: false });
            } else {
                toaster.create({
                    title: 'Submission Failed',
                    description: data.message || 'Could not submit review.',
                    type: 'error',
                });
            }
        } catch (e) {
            toaster.create({
                title: 'Review Error',
                description: e?.response?.data?.message || e.message || 'Error submitting review.',
                type: 'error',
            });
        } finally {
            setSubmitting(false);
        }
    };

    const renderStarSelector = (label, value, setter) => (
        <Flex justify="space-between" align="center" py={1.5} borderBottomWidth="1px" borderColor="gray.100">
            <Text fontSize="xs" fontWeight="600" color="#0A1128">
                {label}
            </Text>
            <HStack gap={1}>
                {[1, 2, 3, 4, 5].map((star) => (
                    <Box
                        key={star}
                        as="button"
                        type="button"
                        onClick={() => setter(star)}
                        color={star <= value ? '#F59E0B' : '#E2E8F0'}
                        fontSize="lg"
                        cursor="pointer"
                        transition="transform 0.1s"
                        _hover={{ transform: 'scale(1.2)' }}
                    >
                        ★
                    </Box>
                ))}
                <Text fontSize="xs" fontWeight="700" color="gray.600" ml={1} w="20px">
                    {value}/5
                </Text>
            </HStack>
        </Flex>
    );

    return (
        <Dialog.Root open={open} onOpenChange={onOpenChange}>
            <Portal>
                <Dialog.Backdrop bg="blackAlpha.600" />
                <Dialog.Positioner>
                    <Dialog.Content bg="white" borderRadius="2xl" shadow="2xl" maxW="520px" p={6}>
                        <Dialog.Header p={0} mb={4}>
                            <Dialog.Title fontSize="lg" fontWeight="800" color="#0A1128">
                                Rate Counterparty & Experience
                            </Dialog.Title>
                            <Text fontSize="xs" color="gray.500">
                                Deal #{deal?.deal_id || deal?.id} — {deal?.title}
                            </Text>
                        </Dialog.Header>

                        <Dialog.Body p={0} mb={6}>
                            <VStack gap={3} align="stretch">
                                <Box bg="blue.50" p={3} borderRadius="xl" textAlign="center">
                                    <Text fontSize="2xs" fontWeight="700" color="blue.700" textTransform="uppercase">
                                        Overall Verified Score
                                    </Text>
                                    <Heading size="lg" color="#1769FF" my={1}>
                                        {overallRating} / 5.0
                                    </Heading>
                                    <HStack justify="center" gap={1}>
                                        {[1, 2, 3, 4, 5].map((s) => (
                                            <Text key={s} color={s <= overallRating ? '#F59E0B' : '#CBD5E1'} fontSize="xl">
                                                ★
                                            </Text>
                                        ))}
                                    </HStack>
                                </Box>

                                <Box>
                                    {renderStarSelector('Communication Clarity', communication, setCommunication)}
                                    {renderStarSelector('Professional Conduct', behavior, setBehavior)}
                                    {renderStarSelector('Delivery & Punctuality', delivery, setDelivery)}
                                    {renderStarSelector('Reliability & Accuracy', reliability, setReliability)}
                                </Box>

                                <Box mt={2}>
                                    <Text fontSize="xs" fontWeight="700" color="#0A1128" mb={1}>
                                        Written Feedback (Public Counterparty Review)
                                    </Text>
                                    <textarea
                                        rows={3}
                                        value={comment}
                                        onChange={(e) => setComment(e.target.value)}
                                        placeholder="Describe the experience dealing with this party. Was the delivery as promised?"
                                        style={{
                                            width: '100%',
                                            padding: '10px',
                                            borderRadius: '8px',
                                            border: '1px solid #E2E8F0',
                                            fontSize: '13px',
                                            outline: 'none',
                                            fontFamily: 'inherit',
                                        }}
                                    />
                                </Box>
                            </VStack>
                        </Dialog.Body>

                        <Dialog.Footer p={0}>
                            <Flex justify="flex-end" gap={3} w="100%">
                                <Button
                                    variant="outline"
                                    size="sm"
                                    onClick={() => onOpenChange?.({ open: false })}
                                    borderRadius="lg"
                                >
                                    Cancel
                                </Button>
                                <Button
                                    size="sm"
                                    bg="#006A4E"
                                    color="white"
                                    _hover={{ bg: '#00503A' }}
                                    loading={submitting}
                                    onClick={handleSubmit}
                                    borderRadius="lg"
                                >
                                    Submit Verified Review
                                </Button>
                            </Flex>
                        </Dialog.Footer>
                    </Dialog.Content>
                </Dialog.Positioner>
            </Portal>
        </Dialog.Root>
    );
};

export default ReviewModal;

