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
    Textarea,
} from '@chakra-ui/react';
import adminApi from '../../../services/adminApi';
import { PageHeader } from '../../../components/common/PageHeader';
import { DashboardCard } from '../../../components/common/DashboardCard';
import { toaster } from '../../../components/feedback/toaster';
import {
    GatewaysIcon,
    CheckCircleIcon,
    AlertCircleIcon,
    CloseIcon,
} from '../../../components/common/Icons';

const DEFAULT_GATEWAYS = [
    {
        slug: 'bkash',
        name: 'bKash Merchant Gateway',
        type: 'mobile_banking',
        is_active: true,
        instructions: 'Send money to our verified bKash merchant number.',
        account_number: '01700000000',
    },
    {
        slug: 'nagad',
        name: 'Nagad Merchant Gateway',
        type: 'mobile_banking',
        is_active: true,
        instructions: 'Send money to our verified Nagad merchant number.',
        account_number: '01800000000',
    },
    {
        slug: 'bank_transfer',
        name: 'Corporate Bank Transfer',
        type: 'bank',
        is_active: true,
        instructions: 'Deposit directly to our corporate bank account (City Bank / DBBL).',
        bank_name: 'The City Bank Ltd',
        account_name: 'MigratePay Technologies Ltd',
        account_number: '1102938475001',
        routing_number: '22527234',
        branch_name: 'Gulshan Branch',
    },
    {
        slug: 'card_stripe',
        name: 'Stripe International Cards',
        type: 'card',
        is_active: false,
        instructions: 'Accept Visa, Mastercard, AMEX with automatic conversion.',
    },
];

export const PaymentGatewaysModule = () => {
    const [gateways, setGateways] = useState(DEFAULT_GATEWAYS);
    const [loading, setLoading] = useState(true);
    const [selectedGateway, setSelectedGateway] = useState(null);
    const [saving, setSaving] = useState(false);
    const [form, setForm] = useState({});

    const fetchGateways = async () => {
        setLoading(true);
        try {
            const data = await adminApi.getGatewaySettings();
            if (Array.isArray(data) && data.length > 0) {
                setGateways(data);
            }
        } catch (e) {
            console.error('Failed to fetch gateway settings:', e);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchGateways();
    }, []);

    const openConfig = (gw) => {
        setSelectedGateway(gw);
        setForm({
            name: gw.name || '',
            is_active: gw.is_active ?? true,
            account_number: gw.account_number || '',
            instructions: gw.instructions || '',
            bank_name: gw.bank_name || '',
            account_name: gw.account_name || '',
            routing_number: gw.routing_number || '',
            branch_name: gw.branch_name || '',
            api_key: '', // secrets masked
            secret_key: '',
        });
    };

    const handleSave = async () => {
        if (!selectedGateway) return;
        setSaving(true);
        try {
            const data = await adminApi.updateGatewaySettings(selectedGateway.slug || selectedGateway.id, form);
            if (data.success || data) {
                toaster.create({
                    title: 'Gateway Configured',
                    description: `${selectedGateway.name} parameters saved securely in vault.`,
                    type: 'success',
                });
                setSelectedGateway(null);
                fetchGateways();
            }
        } catch (e) {
            console.error(e);
            toaster.create({
                title: 'Save Failed',
                description: 'Failed to update gateway settings.',
                type: 'error',
            });
        } finally {
            setSaving(false);
        }
    };

    const toggleStatus = async (gw) => {
        const nextState = !gw.is_active;
        try {
            await adminApi.updateGatewaySettings(gw.slug || gw.id, { ...gw, is_active: nextState });
            setGateways((prev) =>
                prev.map((g) => (g.slug === gw.slug ? { ...g, is_active: nextState } : g))
            );
            toaster.create({
                title: `${gw.name} ${nextState ? 'Enabled' : 'Disabled'}`,
                description: `Payment gateway status changed.`,
                type: 'info',
            });
        } catch (e) {
            console.error(e);
        }
    };

    return (
        <Box>
            <PageHeader
                title="Payment Gateways Vault"
                subtitle="Configure automated and manual deposit gateways, manage API credentials, and control active payment rails."
                actions={
                    <Button
                        size="sm"
                        variant="outline"
                        borderColor="gray.300"
                        borderRadius="lg"
                        onClick={fetchGateways}
                    >
                        Refresh Vault
                    </Button>
                }
            />

            {/* Gateway Cards Grid */}
            <DashboardCard
                title="Active Payment Gateways"
                subtitle="All gateway configurations are secured server-side with AES-256 encrypted secrets."
            >
                {loading ? (
                    <Flex justify="center" py={12}>
                        <Spinner size="lg" color="#1769FF" />
                    </Flex>
                ) : (
                    <Grid templateColumns={{ base: '1fr', md: 'repeat(2, 1fr)' }} gap={6}>
                        {gateways.map((gw) => (
                            <Box
                                key={gw.slug || gw.name}
                                p={5}
                                borderRadius="2xl"
                                borderWidth="1px"
                                borderColor={gw.is_active ? 'green.200' : 'gray.200'}
                                bg={gw.is_active ? '#FAFFFE' : 'white'}
                                shadow="sm"
                            >
                                <Flex justify="space-between" align="flex-start" mb={3}>
                                    <HStack gap={3}>
                                        <Flex w={10} h={10} bg="#0A1128" color="white" borderRadius="xl" align="center" justify="center" fontWeight="800">
                                            {gw.slug ? gw.slug.charAt(0).toUpperCase() : 'P'}
                                        </Flex>
                                        <Box>
                                            <Text fontSize="sm" fontWeight="800" color="#0A1128">
                                                {gw.name}
                                            </Text>
                                            <Badge
                                                colorPalette={gw.is_active ? 'green' : 'gray'}
                                                variant="subtle"
                                                fontSize="2xs"
                                                mt={0.5}
                                            >
                                                {gw.is_active ? 'ACTIVE RAILS' : 'OFFLINE'}
                                            </Badge>
                                        </Box>
                                    </HStack>

                                    <Button
                                        size="xs"
                                        colorPalette={gw.is_active ? 'red' : 'green'}
                                        variant="outline"
                                        onClick={() => toggleStatus(gw)}
                                    >
                                        {gw.is_active ? 'Disable' : 'Enable'}
                                    </Button>
                                </Flex>

                                <Text fontSize="xs" color="gray.600" mb={3}>
                                    {gw.instructions || 'No special instructions configured.'}
                                </Text>

                                {gw.account_number && (
                                    <Box p={2.5} bg="gray.50" borderRadius="lg" mb={3}>
                                        <Text fontSize="2xs" color="gray.500" fontWeight="700">MERCHANT ACCOUNT</Text>
                                        <Text fontSize="xs" fontWeight="700" fontFamily="mono">{gw.account_number}</Text>
                                    </Box>
                                )}

                                <Flex justify="flex-end">
                                    <Button
                                        size="sm"
                                        variant="outline"
                                        borderColor="gray.300"
                                        borderRadius="lg"
                                        onClick={() => openConfig(gw)}
                                    >
                                        Configure Parameters
                                    </Button>
                                </Flex>
                            </Box>
                        ))}
                    </Grid>
                )}
            </DashboardCard>

            {/* Configure Gateway Modal */}
            {selectedGateway && (
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
                    <Box bg="white" borderRadius="2xl" maxW="560px" w="100%" p={6} shadow="2xl">
                        <Flex justify="space-between" align="center" mb={4}>
                            <Heading size="md" color="#0A1128">
                                Configure {selectedGateway.name}
                            </Heading>
                            <Button size="sm" variant="ghost" onClick={() => setSelectedGateway(null)}>
                                <CloseIcon size={18} />
                            </Button>
                        </Flex>

                        <VStack gap={3} align="stretch">
                            <Box>
                                <Text fontSize="xs" fontWeight="700" color="gray.600" mb={1}>
                                    Display Name
                                </Text>
                                <Input
                                    size="sm"
                                    value={form.name}
                                    onChange={(e) => setForm({ ...form, name: e.target.value })}
                                    borderRadius="lg"
                                />
                            </Box>

                            <Box>
                                <Text fontSize="xs" fontWeight="700" color="gray.600" mb={1}>
                                    Account / Merchant Number
                                </Text>
                                <Input
                                    size="sm"
                                    value={form.account_number}
                                    onChange={(e) => setForm({ ...form, account_number: e.target.value })}
                                    borderRadius="lg"
                                />
                            </Box>

                            <Box>
                                <Text fontSize="xs" fontWeight="700" color="gray.600" mb={1}>
                                    Payment Instructions
                                </Text>
                                <Textarea
                                    size="sm"
                                    value={form.instructions}
                                    onChange={(e) => setForm({ ...form, instructions: e.target.value })}
                                    borderRadius="lg"
                                />
                            </Box>

                            <Box p={3} bg="amber.50" borderRadius="lg">
                                <Text fontSize="2xs" color="amber.800" fontWeight="700">
                                    CRYPTOGRAPHIC KEY VAULT
                                </Text>
                                <Text fontSize="2xs" color="amber.700" mt={1}>
                                    To update API keys or webhook secrets, enter them below. Existing keys remain stored in the AES-256 encrypted vault.
                                </Text>
                                <Input
                                    size="sm"
                                    type="password"
                                    placeholder="Enter new API Key (leave empty to keep current)"
                                    mt={2}
                                    value={form.api_key}
                                    onChange={(e) => setForm({ ...form, api_key: e.target.value })}
                                    bg="white"
                                />
                            </Box>
                        </VStack>

                        <Flex justify="space-between" mt={6}>
                            <Button size="sm" variant="outline" onClick={() => setSelectedGateway(null)}>
                                Cancel
                            </Button>

                            <Button
                                size="sm"
                                bg="#0A1128"
                                color="white"
                                _hover={{ bg: '#162447' }}
                                loading={saving}
                                onClick={handleSave}
                            >
                                Save Gateway Vault
                            </Button>
                        </Flex>
                    </Box>
                </Box>
            )}
        </Box>
    );
};

export default PaymentGatewaysModule;

