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
import { ReviewModal } from '../../../components/modals/ReviewModal';
import { ConfirmationDialog } from '../../../components/common/ConfirmationDialog';
import { toaster } from '../../../components/feedback/toaster';
import {
    DealsIcon,
    CheckCircleIcon,
    AlertCircleIcon,
    ArrowUpRightIcon,
    PlusIcon,
    ShieldIcon,
    SupportIcon,
} from '../../../components/common/Icons';

export const ProjectManagementModule = ({ onNavigate }) => {
    const { user, currency, refreshUser } = useAuth();
    const [deals, setDeals] = useState([]);
    const [loading, setLoading] = useState(true);
    const [filter, setFilter] = useState('all');
    const [search, setSearch] = useState('');
    const [selectedDeal, setSelectedDeal] = useState(null);

    // Messaging state
    const [messageText, setMessageText] = useState('');
    const [sendingMsg, setSendingMsg] = useState(false);
    const chatEndRef = useRef(null);

    // Deliverable upload state
    const [deliverableFile, setDeliverableFile] = useState(null);
    const [deliverableNotes, setDeliverableNotes] = useState('');
    const [uploadingWork, setUploadingWork] = useState(false);

    // Modal states
    const [reviewModalOpen, setReviewModalOpen] = useState(false);
    const [confirmReleaseOpen, setConfirmReleaseOpen] = useState(false);
    const [releasing, setReleasing] = useState(false);
    const [disputeOpen, setDisputeOpen] = useState(false);
    const [disputeReason, setDisputeReason] = useState('Non-Delivery');
    const [disputeDesc, setDisputeDesc] = useState('');
    const [submittingDispute, setSubmittingDispute] = useState(false);

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
        const interval = setInterval(fetchDeals, 8000);
        return () => clearInterval(interval);
    }, []);

    useEffect(() => {
        chatEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    }, [selectedDeal?.workspace?.messages]);

    const currentUserId = user?.id;
    const currentUserEmail = (user?.email || '').toLowerCase();

    // Determine status badge color
    const getStatusStyle = (status) => {
        switch (status) {
            case 'completed':
            case 'released':
                return { palette: 'green', label: 'Completed' };
            case 'funds_secured':
            case 'in_progress':
                return { palette: 'blue', label: 'Funds Secured' };
            case 'awaiting_acceptance':
                return { palette: 'orange', label: 'Awaiting Acceptance' };
            case 'buyer_review':
            case 'delivered':
                return { palette: 'purple', label: 'Work In Review' };
            case 'disputed':
                return { palette: 'red', label: 'In Dispute' };
            default:
                return { palette: 'gray', label: status || 'Pending' };
        }
    };

    // Filter deals
    const filteredDeals = deals.filter((d) => {
        if (!d) return false;
        const matchesSearch =
            (d.title || '').toLowerCase().includes(search.toLowerCase()) ||
            (d.deal_id || '').toLowerCase().includes(search.toLowerCase()) ||
            (d.counterparty_email || '').toLowerCase().includes(search.toLowerCase());

        if (!matchesSearch) return false;
        if (filter === 'all') return true;
        if (filter === 'active') return ['funds_secured', 'in_progress', 'buyer_review', 'delivered'].includes(d.status);
        if (filter === 'awaiting') return d.status === 'awaiting_acceptance';
        if (filter === 'completed') return ['completed', 'released'].includes(d.status);
        if (filter === 'disputed') return d.status === 'disputed';
        return true;
    });

    const isInitiator = selectedDeal?.initiator_id === currentUserId;
    const isBuyer = selectedDeal
        ? (selectedDeal.initiator_role === 'buyer'
            ? isInitiator
            : !isInitiator)
        : false;

    // Actions
    const handleAccept = async () => {
        if (!selectedDeal) return;
        try {
            const data = await dealsApi.accept(selectedDeal.id);
            if (data.success) {
                toaster.create({ title: 'Deal Accepted!', description: 'Escrow terms confirmed.', type: 'success' });
                refreshUser();
                fetchDeals();
            } else {
                toaster.create({ title: 'Error', description: data.message || 'Could not accept deal.', type: 'error' });
            }
        } catch (e) {
            toaster.create({ title: 'Error', description: e.message, type: 'error' });
        }
    };

    const handleDecline = async () => {
        if (!selectedDeal || !window.confirm('Are you sure you want to decline this escrow agreement?')) return;
        try {
            const data = await dealsApi.decline(selectedDeal.id);
            if (data.success) {
                toaster.create({ title: 'Deal Declined', description: 'Escrow refunded to buyer.', type: 'info' });
                refreshUser();
                fetchDeals();
            }
        } catch (e) {
            toaster.create({ title: 'Error', description: e.message, type: 'error' });
        }
    };

    const handleRelease = async () => {
        if (!selectedDeal) return;
        setReleasing(true);
        try {
            const data = await dealsApi.release(selectedDeal.id);
            if (data.success) {
                toaster.create({
                    title: 'Escrow Released!',
                    description: 'Funds successfully credited to seller wallet.',
                    type: 'success',
                });
                setConfirmReleaseOpen(false);
                refreshUser();
                fetchDeals();
                setReviewModalOpen(true);
            } else {
                toaster.create({ title: 'Release Failed', description: data.message || 'Could not release escrow.', type: 'error' });
            }
        } catch (e) {
            toaster.create({ title: 'Error', description: e.message, type: 'error' });
        } finally {
            setReleasing(false);
        }
    };

    const handleSubmitWork = async (e) => {
        e.preventDefault();
        if (!selectedDeal) return;

        setUploadingWork(true);
        try {
            const formData = new FormData();
            formData.append('notes', deliverableNotes);
            if (deliverableFile) formData.append('deliverable', deliverableFile);

            const data = await dealsApi.submitWork(selectedDeal.id, formData);
            if (data.success) {
                toaster.create({ title: 'Deliverables Submitted!', description: 'Buyer notified for inspection.', type: 'success' });
                setDeliverableNotes('');
                setDeliverableFile(null);
                fetchDeals();
            }
        } catch (e) {
            toaster.create({ title: 'Upload Failed', description: e.message, type: 'error' });
        } finally {
            setUploadingWork(false);
        }
    };

    const handleReviewSubmission = async (decision) => {
        if (!selectedDeal) return;
        try {
            const data = await dealsApi.reviewSubmission(selectedDeal.id, decision);
            if (data.success) {
                toaster.create({
                    title: decision === 'approved' ? 'Submission Approved!' : 'Revision Requested',
                    description: decision === 'approved' ? 'You may now release escrow funds.' : 'Seller notified.',
                    type: decision === 'approved' ? 'success' : 'warning',
                });
                fetchDeals();
            }
        } catch (e) {
            toaster.create({ title: 'Error', description: e.message, type: 'error' });
        }
    };

    const handleSendMessage = async (e) => {
        e.preventDefault();
        if (!selectedDeal || !messageText.trim()) return;

        setSendingMsg(true);
        try {
            const data = await dealsApi.sendMessage(selectedDeal.id, messageText.trim());
            if (data.success) {
                setMessageText('');
                fetchDeals();
            }
        } catch (e) {
            toaster.create({ title: 'Message Error', description: e.message, type: 'error' });
        } finally {
            setSendingMsg(false);
        }
    };

    const handleReportDispute = async (e) => {
        e.preventDefault();
        if (!selectedDeal) return;

        setSubmittingDispute(true);
        try {
            const data = await dealsApi.reportDispute(selectedDeal.id, disputeReason, disputeDesc);
            if (data.success) {
                toaster.create({
                    title: 'Dispute Filed',
                    description: 'MigratePay arbitration team has been assigned to mediate.',
                    type: 'warning',
                });
                setDisputeOpen(false);
                setDisputeDesc('');
                fetchDeals();
            }
        } catch (e) {
            toaster.create({ title: 'Error', description: e.message, type: 'error' });
        } finally {
            setSubmittingDispute(false);
        }
    };

    return (
        <Box>
            <PageHeader
                title="Escrow Deals Workspace"
                subtitle="Track milestones, inspect deliverables, release payments, and resolve disputes."
                actions={
                    <Button
                        size="sm"
                        bg="#1769FF"
                        color="white"
                        _hover={{ bg: '#1358D8' }}
                        borderRadius="lg"
                        onClick={() => onNavigate?.('CreateDeal')}
                    >
                        <PlusIcon size={16} style={{ marginRight: '6px' }} />
                        New Escrow Deal
                    </Button>
                }
            />

            {/* Filter Tabs */}
            <Flex gap={2} mb={6} wrap="wrap">
                {[
                    { id: 'all', label: 'All Deals' },
                    { id: 'active', label: 'Active / In Progress' },
                    { id: 'awaiting', label: 'Awaiting Acceptance' },
                    { id: 'completed', label: 'Completed' },
                    { id: 'disputed', label: 'Disputed' },
                ].map((t) => (
                    <Button
                        key={t.id}
                        size="xs"
                        variant={filter === t.id ? 'solid' : 'outline'}
                        bg={filter === t.id ? '#002147' : 'white'}
                        color={filter === t.id ? 'white' : 'gray.700'}
                        borderColor={filter === t.id ? '#002147' : 'gray.200'}
                        borderRadius="lg"
                        px={3}
                        onClick={() => setFilter(t.id)}
                    >
                        {t.label}
                    </Button>
                ))}
            </Flex>

            {loading ? (
                <Flex justify="center" align="center" minH="300px">
                    <Spinner size="lg" color="#1769FF" />
                </Flex>
            ) : deals.length === 0 ? (
                <EmptyState
                    icon={DealsIcon}
                    title="No escrow agreements found"
                    description="You have not participated in any escrow deals yet. Initiate a new agreement to safely transact."
                    actionLabel="Initiate Escrow Deal"
                    onAction={() => onNavigate?.('CreateDeal')}
                    minH="320px"
                />
            ) : (
                <Grid templateColumns={{ base: '1fr', lg: '1fr 2fr' }} gap={6} minH="600px">
                    {/* Left: Deals List */}
                    <DashboardCard title={`Deals (${filteredDeals.length})`} p={{ base: 3, md: 4 }}>
                        <Box mb={3}>
                            <input
                                type="text"
                                placeholder="Search by title, ID, or email..."
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                style={{
                                    width: '100%',
                                    padding: '8px 12px',
                                    borderRadius: '8px',
                                    border: '1px solid #E2E8F0',
                                    fontSize: '12px',
                                    outline: 'none',
                                }}
                            />
                        </Box>

                        <VStack gap={2} align="stretch" maxH="540px" overflowY="auto">
                            {filteredDeals.map((d) => {
                                const st = getStatusStyle(d.status);
                                const isSelected = selectedDeal?.id === d.id;

                                return (
                                    <Box
                                        key={d.id}
                                        p={3}
                                        borderRadius="xl"
                                        borderWidth="1px"
                                        borderColor={isSelected ? '#1769FF' : 'gray.200'}
                                        bg={isSelected ? '#EEF6FF' : 'white'}
                                        cursor="pointer"
                                        transition="all 0.15s"
                                        _hover={{ borderColor: '#1769FF', shadow: 'xs' }}
                                        onClick={() => setSelectedDeal(d)}
                                    >
                                        <Flex justify="space-between" align="flex-start" mb={1}>
                                            <Text fontSize="xs" fontWeight="700" color="#0A1128" truncate maxW="160px">
                                                {d.title}
                                            </Text>
                                            <Badge colorPalette={st.palette} variant="subtle" fontSize="3xs" px={1.5} borderRadius="sm">
                                                {st.label}
                                            </Badge>
                                        </Flex>
                                        <Flex justify="space-between" align="center" fontSize="2xs" color="gray.500">
                                            <Text fontFamily="mono" fontWeight="600">
                                                #{d.deal_id || d.id}
                                            </Text>
                                            <Text fontWeight="800" color="#006A4E">
                                                {authService.formatCurrency(d.amount, currency)}
                                            </Text>
                                        </Flex>
                                    </Box>
                                );
                            })}
                        </VStack>
                    </DashboardCard>

                    {/* Right: Deal Workspace Details */}
                    {selectedDeal ? (
                        <VStack gap={6} align="stretch">
                            {/* Deal Overview Card */}
                            <DashboardCard
                                title={selectedDeal.title}
                                subtitle={`Deal ID: #${selectedDeal.deal_id || selectedDeal.id} • Created on ${new Date(selectedDeal.created_at).toLocaleDateString()}`}
                                headerRight={
                                    <Badge
                                        colorPalette={getStatusStyle(selectedDeal.status).palette}
                                        variant="subtle"
                                        fontSize="xs"
                                        px={2.5}
                                        py={1}
                                        borderRadius="md"
                                    >
                                        {getStatusStyle(selectedDeal.status).label}
                                    </Badge>
                                }
                            >
                                <Grid templateColumns={{ base: 'repeat(2, 1fr)', sm: 'repeat(4, 1fr)' }} gap={4} p={3} bg="gray.50" borderRadius="xl" mb={4}>
                                    <Box>
                                        <Text fontSize="3xs" fontWeight="700" color="gray.500" textTransform="uppercase">Your Role</Text>
                                        <Text fontSize="xs" fontWeight="800" color="#0A1128">{isBuyer ? 'Buyer (Depositor)' : 'Seller (Provider)'}</Text>
                                    </Box>
                                    <Box>
                                        <Text fontSize="3xs" fontWeight="700" color="gray.500" textTransform="uppercase">Deal Value</Text>
                                        <Text fontSize="xs" fontWeight="800" color="#0A1128">{authService.formatCurrency(selectedDeal.amount, currency)}</Text>
                                    </Box>
                                    <Box>
                                        <Text fontSize="3xs" fontWeight="700" color="gray.500" textTransform="uppercase">Escrow Fee</Text>
                                        <Text fontSize="xs" fontWeight="800" color="#1769FF">{authService.formatCurrency(selectedDeal.charge_total || 0, currency)}</Text>
                                    </Box>
                                    <Box>
                                        <Text fontSize="3xs" fontWeight="700" color="gray.500" textTransform="uppercase">Counterparty</Text>
                                        <Text fontSize="xs" fontWeight="700" color="#0A1128" truncate>{selectedDeal.counterparty_email}</Text>
                                    </Box>
                                </Grid>

                                {selectedDeal.description && (
                                    <Box mb={4} p={3} bg="white" borderWidth="1px" borderColor="gray.100" borderRadius="lg">
                                        <Text fontSize="2xs" fontWeight="700" color="gray.500" textTransform="uppercase" mb={1}>
                                            Agreed Deliverable Terms
                                        </Text>
                                        <Text fontSize="xs" color="gray.700">
                                            {selectedDeal.description}
                                        </Text>
                                    </Box>
                                )}

                                {/* Dynamic Action Controls */}
                                <Box p={4} bg="blue.50" borderRadius="xl" borderWidth="1px" borderColor="blue.100">
                                    <Text fontSize="xs" fontWeight="800" color="#002147" mb={2}>
                                        Escrow Milestone Actions
                                    </Text>

                                    {/* 1. Awaiting Acceptance Actions */}
                                    {selectedDeal.status === 'awaiting_acceptance' && (
                                        <Box>
                                            {selectedDeal.counterparty_email?.toLowerCase() === currentUserEmail ? (
                                                <HStack gap={3}>
                                                    <Button size="sm" bg="#006A4E" color="white" _hover={{ bg: '#00503A' }} onClick={handleAccept}>
                                                        Accept & Confirm Deal
                                                    </Button>
                                                    <Button size="sm" variant="outline" colorPalette="red" onClick={handleDecline}>
                                                        Decline Invitation
                                                    </Button>
                                                </HStack>
                                            ) : (
                                                <Text fontSize="xs" color="gray.600">
                                                    Waiting for counterparty (<strong>{selectedDeal.counterparty_email}</strong>) to accept the terms.
                                                </Text>
                                            )}
                                        </Box>
                                    )}

                                    {/* 2. Funds Secured / Work In Progress Actions */}
                                    {['funds_secured', 'in_progress'].includes(selectedDeal.status) && (
                                        <Box>
                                            {!isBuyer ? (
                                                <form onSubmit={handleSubmitWork}>
                                                    <VStack gap={2} align="stretch">
                                                        <Text fontSize="2xs" color="gray.600">
                                                            Submit completed deliverables or file proof for buyer inspection:
                                                        </Text>
                                                        <textarea
                                                            rows={2}
                                                            placeholder="Deliverable description or download credentials..."
                                                            value={deliverableNotes}
                                                            onChange={(e) => setDeliverableNotes(e.target.value)}
                                                            style={{
                                                                width: '100%',
                                                                padding: '8px 12px',
                                                                borderRadius: '8px',
                                                                border: '1px solid #CBD5E1',
                                                                fontSize: '12px',
                                                                background: 'white',
                                                            }}
                                                        />
                                                        <input
                                                            type="file"
                                                            onChange={(e) => setDeliverableFile(e.target.files[0])}
                                                            style={{ fontSize: '12px' }}
                                                        />
                                                        <Button size="sm" bg="#1769FF" color="white" type="submit" loading={uploadingWork} alignSelf="flex-start">
                                                            Submit Work for Review
                                                        </Button>
                                                    </VStack>
                                                </form>
                                            ) : (
                                                <Text fontSize="xs" color="gray.600">
                                                    Seller is executing the work. Once they submit the deliverables, you will inspect them here.
                                                </Text>
                                            )}
                                        </Box>
                                    )}

                                    {/* 3. Deliverables In Review Actions */}
                                    {['buyer_review', 'delivered'].includes(selectedDeal.status) && (
                                        <Box>
                                            {isBuyer ? (
                                                <VStack gap={3} align="stretch">
                                                    <Text fontSize="xs" color="gray.700">
                                                        Seller has submitted work for your review. Please inspect and decide:
                                                    </Text>
                                                    <HStack gap={3}>
                                                        <Button size="sm" bg="#006A4E" color="white" onClick={() => setConfirmReleaseOpen(true)}>
                                                            Approve & Release Payment
                                                        </Button>
                                                        <Button size="sm" variant="outline" onClick={() => handleReviewSubmission('revision')}>
                                                            Request Revision
                                                        </Button>
                                                        <Button size="sm" variant="ghost" color="red.600" onClick={() => setDisputeOpen(true)}>
                                                            File Dispute
                                                        </Button>
                                                    </HStack>
                                                </VStack>
                                            ) : (
                                                <Text fontSize="xs" color="gray.600">
                                                    Your deliverables are under review by the buyer.
                                                </Text>
                                            )}
                                        </Box>
                                    )}

                                    {/* 4. Completed / Released */}
                                    {['completed', 'released'].includes(selectedDeal.status) && (
                                        <HStack justify="space-between" align="center">
                                            <HStack gap={2} color="#006A4E">
                                                <CheckCircleIcon size={18} color="#006A4E" />
                                                <Text fontSize="xs" fontWeight="700">
                                                    Deal completed and funds released from escrow.
                                                </Text>
                                            </HStack>
                                            <Button size="xs" variant="outline" onClick={() => setReviewModalOpen(true)}>
                                                Rate Experience
                                            </Button>
                                        </HStack>
                                    )}
                                </Box>
                            </DashboardCard>

                            {/* In-Deal Discussion & Messaging */}
                            <DashboardCard title="Deal Negotiation & Communication" subtitle="All messages are recorded for escrow audit protection.">
                                <Box
                                    maxH="280px"
                                    minH="160px"
                                    overflowY="auto"
                                    p={3}
                                    bg="gray.50"
                                    borderRadius="xl"
                                    borderWidth="1px"
                                    borderColor="gray.200"
                                    mb={3}
                                >
                                    {(!selectedDeal.workspace?.messages || selectedDeal.workspace.messages.length === 0) ? (
                                        <Flex justify="center" align="center" h="140px">
                                            <Text fontSize="xs" color="gray.400" fontStyle="italic">
                                                No discussion records yet. Send a message to coordinate.
                                            </Text>
                                        </Flex>
                                    ) : (
                                        <VStack gap={2} align="stretch">
                                            {selectedDeal.workspace.messages.map((m, idx) => {
                                                const isMe = m.sender_id === currentUserId;
                                                return (
                                                    <Box
                                                        key={idx}
                                                        alignSelf={isMe ? 'flex-end' : 'flex-start'}
                                                        maxW="75%"
                                                        bg={isMe ? '#002147' : 'white'}
                                                        color={isMe ? 'white' : '#0A1128'}
                                                        p={2.5}
                                                        borderRadius="xl"
                                                        shadow="xs"
                                                        borderWidth={isMe ? '0px' : '1px'}
                                                        borderColor="gray.200"
                                                    >
                                                        <Text fontSize="xs">{m.text}</Text>
                                                        <Text fontSize="3xs" opacity={0.7} mt={0.5} textAlign="right">
                                                            {m.created_at ? new Date(m.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : ''}
                                                        </Text>
                                                    </Box>
                                                );
                                            })}
                                            <div ref={chatEndRef} />
                                        </VStack>
                                    )}
                                </Box>

                                <form onSubmit={handleSendMessage}>
                                    <HStack gap={2}>
                                        <input
                                            type="text"
                                            value={messageText}
                                            onChange={(e) => setMessageText(e.target.value)}
                                            placeholder="Write message to counterparty..."
                                            style={{
                                                flex: 1,
                                                padding: '9px 14px',
                                                borderRadius: '8px',
                                                border: '1px solid #E2E8F0',
                                                fontSize: '13px',
                                                outline: 'none',
                                            }}
                                        />
                                        <Button size="sm" bg="#002147" color="white" type="submit" loading={sendingMsg}>
                                            Send
                                        </Button>
                                    </HStack>
                                </form>
                            </DashboardCard>
                        </VStack>
                    ) : null}
                </Grid>
            )}

            {/* Confirmation Dialog for Release */}
            <ConfirmationDialog
                open={confirmReleaseOpen}
                onOpenChange={(e) => setConfirmReleaseOpen(e.open)}
                title="Release Escrow Funds"
                description={`Are you sure you want to release ${authService.formatCurrency(selectedDeal?.amount, currency)} to the seller? This action is irreversible.`}
                confirmLabel="Release Payment"
                onConfirm={handleRelease}
                isLoading={releasing}
            />

            {/* Review Modal */}
            <ReviewModal
                deal={selectedDeal}
                open={reviewModalOpen}
                onOpenChange={(e) => setReviewModalOpen(e.open)}
                onSuccess={() => fetchDeals()}
            />
        </Box>
    );
};

export default ProjectManagementModule;

