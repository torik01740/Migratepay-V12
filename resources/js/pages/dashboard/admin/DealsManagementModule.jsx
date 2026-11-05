import React, { useState, useEffect } from 'react';
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
} from '@chakra-ui/react';
import { useAuth } from '../../../hooks/useAuth';
import adminApi from '../../../services/adminApi';
import authService from '../../../services/auth';
import { PageHeader } from '../../../components/common/PageHeader';
import { DashboardCard } from '../../../components/common/DashboardCard';
import { EmptyState } from '../../../components/common/EmptyState';
import { toaster } from '../../../components/feedback/toaster';
import {
    DealsIcon,
    SecurityIcon,
    AlertCircleIcon,
    CloseIcon,
} from '../../../components/common/Icons';

export const DealsManagementModule = () => {
    const { currency } = useAuth();
    const [deals, setDeals] = useState([]);
    const [loading, setLoading] = useState(true);
    const [filter, setFilter] = useState('All');
    const [selectedDeal, setSelectedDeal] = useState(null);
    const [processing, setProcessing] = useState(false);

    const fetchDeals = async () => {
        setLoading(true);
        try {
            const statusParam = filter === 'All' ? '' : filter.toLowerCase();
            const data = await adminApi.getDeals(statusParam);
            if (data.success && Array.isArray(data.deals)) {
                setDeals(data.deals);
                if (selectedDeal) {
                    const updated = data.deals.find((d) => d.id === selectedDeal.id);
                    if (updated) setSelectedDeal(updated);
                }
            }
        } catch (e) {
            console.error('Failed to fetch deals:', e);
            toaster.create({
                title: 'Error Loading Deals',
                description: 'Unable to retrieve contracts from backend.',
                type: 'error',
            });
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchDeals();
    }, [filter]);

    const handleReleaseFunds = async (deal) => {
        if (!window.confirm(`Disburse escrow funds of ${authService.formatCurrency(deal.seller_gets || deal.amount, currency)} to seller (${deal.seller?.email || deal.counterparty_email})?`)) {
            return;
        }

        setProcessing(true);
        try {
            const data = await adminApi.approveDealFunds(deal.id);
            if (data.success) {
                toaster.create({
                    title: 'Funds Disbursed',
                    description: 'Escrow funds credited to seller account atomically.',
                    type: 'success',
                });
                fetchDeals();
            }
        } catch (e) {
            console.error(e);
            toaster.create({
                title: 'Disbursal Failed',
                description: e.response?.data?.message || 'Server rejected escrow disbursal.',
                type: 'error',
            });
        } finally {
            setProcessing(false);
        }
    };

    return (
        <Box>
            <PageHeader
                title="Platform Escrow Contracts & Disputes"
                subtitle="Complete visibility over active, delivered, and disputed escrow contracts across all platform users."
                actions={
                    <Button
                        size="sm"
                        variant="outline"
                        borderColor="gray.300"
                        borderRadius="lg"
                        onClick={fetchDeals}
                    >
                        Refresh Contracts
                    </Button>
                }
            />

            {/* Filter Tabs */}
            <DashboardCard mb={6}>
                <HStack gap={2} wrap="wrap">
                    {['All', 'Active', 'Disputed', 'Completed'].map((status) => (
                        <Button
                            key={status}
                            size="sm"
                            variant={filter === status ? 'solid' : 'outline'}
                            bg={filter === status ? '#0A1128' : 'transparent'}
                            color={filter === status ? 'white' : 'gray.600'}
                            borderRadius="lg"
                            onClick={() => setFilter(status)}
                        >
                            {status} Contracts
                        </Button>
                    ))}
                </HStack>
            </DashboardCard>

            {/* Contracts Table */}
            <DashboardCard
                title={`Escrow Deals (${deals.length})`}
                subtitle="Authoritative audit of all platform agreements and cryptographic escrow locks."
            >
                {loading ? (
                    <Flex justify="center" py={12}>
                        <Spinner size="lg" color="#1769FF" />
                    </Flex>
                ) : deals.length === 0 ? (
                    <EmptyState
                        title="No Contracts Found"
                        description={`There are currently no ${filter.toLowerCase()} escrow deals in the platform.`}
                    />
                ) : (
                    <Box overflowX="auto">
                        <Box as="table" w="100%" textAlign="left" style={{ borderCollapse: 'collapse' }}>
                            <Box as="thead" bg="gray.50" borderBottomWidth="1px" borderColor="gray.200">
                                <Box as="tr">
                                    <Box as="th" p={3} fontSize="2xs" fontWeight="700" color="gray.500" textTransform="uppercase">
                                        Contract & Title
                                    </Box>
                                    <Box as="th" p={3} fontSize="2xs" fontWeight="700" color="gray.500" textTransform="uppercase">
                                        Parties
                                    </Box>
                                    <Box as="th" p={3} fontSize="2xs" fontWeight="700" color="gray.500" textTransform="uppercase">
                                        Amount & Escrow
                                    </Box>
                                    <Box as="th" p={3} fontSize="2xs" fontWeight="700" color="gray.500" textTransform="uppercase">
                                        Status
                                    </Box>
                                    <Box as="th" p={3} fontSize="2xs" fontWeight="700" color="gray.500" textTransform="uppercase" textAlign="right">
                                        Actions
                                    </Box>
                                </Box>
                            </Box>
                            <Box as="tbody">
                                {deals.map((deal) => (
                                    <Box as="tr" key={deal.id} borderBottomWidth="1px" borderColor="gray.100" _hover={{ bg: 'gray.50' }}>
                                        <Box as="td" p={3}>
                                            <Text fontSize="xs" fontWeight="700" color="#0A1128">
                                                {deal.title}
                                            </Text>
                                            <Text fontSize="2xs" color="gray.500" fontFamily="mono">
                                                {deal.deal_id || `DL-${deal.id}`}
                                            </Text>
                                            <Text fontSize="2xs" color="gray.400">
                                                {new Date(deal.created_at).toLocaleDateString()}
                                            </Text>
                                        </Box>
                                        <Box as="td" p={3}>
                                            <Text fontSize="2xs" color="gray.700">
                                                <strong>Initiator:</strong> {deal.initiator?.email || 'N/A'} ({deal.initiator_role || 'buyer'})
                                            </Text>
                                            <Text fontSize="2xs" color="gray.700">
                                                <strong>Counterparty:</strong> {deal.counterparty?.email || deal.counterparty_email}
                                            </Text>
                                        </Box>
                                        <Box as="td" p={3}>
                                            <Text fontSize="xs" fontWeight="800" color="#006A4E">
                                                {authService.formatCurrency(deal.amount, currency)}
                                            </Text>
                                            <Text fontSize="2xs" color="gray.500">
                                                Seller Gets: {authService.formatCurrency(deal.seller_gets || deal.amount, currency)}
                                            </Text>
                                            <Text fontSize="2xs" color="gray.400">
                                                Fee: {authService.formatCurrency(deal.charge_total || 0, currency)}
                                            </Text>
                                        </Box>
                                        <Box as="td" p={3}>
                                            <Badge
                                                colorPalette={
                                                    deal.status === 'completed'
                                                        ? 'green'
                                                        : deal.status === 'disputed'
                                                        ? 'red'
                                                        : deal.status === 'active'
                                                        ? 'blue'
                                                        : 'orange'
                                                }
                                                variant="solid"
                                                fontSize="2xs"
                                                textTransform="uppercase"
                                            >
                                                {deal.status}
                                            </Badge>
                                        </Box>
                                        <Box as="td" p={3} textAlign="right">
                                            <HStack gap={2} justify="flex-end">
                                                <Button
                                                    size="xs"
                                                    variant="outline"
                                                    borderColor="gray.300"
                                                    borderRadius="md"
                                                    onClick={() => setSelectedDeal(deal)}
                                                >
                                                    Inspect
                                                </Button>

                                                {deal.status !== 'completed' && (
                                                    <Button
                                                        size="xs"
                                                        colorPalette="green"
                                                        variant="subtle"
                                                        loading={processing}
                                                        onClick={() => handleReleaseFunds(deal)}
                                                    >
                                                        Release Escrow
                                                    </Button>
                                                )}
                                            </HStack>
                                        </Box>
                                    </Box>
                                ))}
                            </Box>
                        </Box>
                    </Box>
                )}
            </DashboardCard>

            {/* Inspect Deal Modal */}
            {selectedDeal && (
                <Box
                    position="fixed"
                    top={0}
                    left={0}
                    right={0}
                    bottom={0}
                    bg="blackAlpha.600"
                    zIndex={1000}
                    display="flex"
                    alignItems="center"
                    justifyContent="center"
                    p={4}
                >
                    <Box bg="white" borderRadius="2xl" maxW="600px" w="100%" p={6} shadow="2xl">
                        <Flex justify="space-between" align="center" mb={4}>
                            <Heading size="md" color="#0A1128">
                                Contract Inspection: {selectedDeal.title}
                            </Heading>
                            <Button size="sm" variant="ghost" onClick={() => setSelectedDeal(null)}>
                                <CloseIcon size={18} />
                            </Button>
                        </Flex>

                        <VStack gap={4} align="stretch">
                            <Grid templateColumns="repeat(2, 1fr)" gap={3} p={3} bg="gray.50" borderRadius="lg">
                                <Box>
                                    <Text fontSize="2xs" color="gray.500" fontWeight="700">CONTRACT REF</Text>
                                    <Text fontSize="xs" fontWeight="700" fontFamily="mono">{selectedDeal.deal_id || `DL-${selectedDeal.id}`}</Text>
                                </Box>
                                <Box>
                                    <Text fontSize="2xs" color="gray.500" fontWeight="700">STATUS</Text>
                                    <Badge colorPalette="blue" textTransform="uppercase">{selectedDeal.status}</Badge>
                                </Box>
                                <Box>
                                    <Text fontSize="2xs" color="gray.500" fontWeight="700">CONTRACT AMOUNT</Text>
                                    <Text fontSize="sm" fontWeight="800" color="#006A4E">
                                        {authService.formatCurrency(selectedDeal.amount, currency)}
                                    </Text>
                                </Box>
                                <Box>
                                    <Text fontSize="2xs" color="gray.500" fontWeight="700">ESCROW FEE</Text>
                                    <Text fontSize="xs">{authService.formatCurrency(selectedDeal.charge_total || 0, currency)}</Text>
                                </Box>
                            </Grid>

                            {selectedDeal.description && (
                                <Box p={3} borderWidth="1px" borderColor="gray.200" borderRadius="lg">
                                    <Text fontSize="2xs" color="gray.500" fontWeight="700">CONTRACT DESCRIPTION & TERMS</Text>
                                    <Text fontSize="xs" color="gray.700" mt={1}>{selectedDeal.description}</Text>
                                </Box>
                            )}

                            {selectedDeal.workspace && (
                                <Box p={3} bg="blue.50" borderRadius="lg">
                                    <Text fontSize="2xs" color="blue.700" fontWeight="700">WORKSPACE DELIVERABLES NOTE</Text>
                                    <Text fontSize="xs" color="blue.900" mt={1}>{selectedDeal.workspace}</Text>
                                </Box>
                            )}
                        </VStack>

                        <Flex justify="space-between" mt={6}>
                            <Button size="sm" variant="outline" onClick={() => setSelectedDeal(null)}>
                                Close
                            </Button>

                            {selectedDeal.status !== 'completed' && (
                                <Button
                                    size="sm"
                                    bg="#10B981"
                                    color="white"
                                    _hover={{ bg: '#059669' }}
                                    loading={processing}
                                    onClick={() => handleReleaseFunds(selectedDeal)}
                                >
                                    Disburse Held Escrow to Seller
                                </Button>
                            )}
                        </Flex>
                    </Box>
                </Box>
            )}
        </Box>
    );
};

export default DealsManagementModule;

