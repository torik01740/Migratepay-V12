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
    Input,
    SimpleGrid,
    Spinner,
} from '@chakra-ui/react';
import { DashboardCard } from '../../../components/common/DashboardCard';
import adminApi from '../../../services/adminApi';

export const SecurityPoliciesModule = () => {
    const [policies, setPolicies] = useState({
        enforce2FA: false,
        sessionTimeoutMinutes: 30,
        maxFailedLogins: 5,
        requireKycForWithdrawal: true,
        ipWhitelist: '',
        autoLockSuspiciousDeals: true,
    });

    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [feedback, setFeedback] = useState(null);

    useEffect(() => {
        let isMounted = true;
        const fetchPolicies = async () => {
            try {
                const res = await adminApi.getSecurityPolicies();
                if (isMounted && res.success && res.policies) {
                    setPolicies(res.policies);
                }
            } catch (err) {
                // Fall back to localStorage if available
                try {
                    const saved = localStorage.getItem('migratepay_security_policies');
                    if (isMounted && saved) setPolicies(JSON.parse(saved));
                } catch (e) {}
            } finally {
                if (isMounted) setLoading(false);
            }
        };

        fetchPolicies();
        return () => {
            isMounted = false;
        };
    }, []);

    const updatePolicy = (key, value) => {
        setPolicies((prev) => ({ ...prev, [key]: value }));
    };

    const handleSave = async (e) => {
        e.preventDefault();
        setSaving(true);
        setFeedback(null);
        try {
            const res = await adminApi.updateSecurityPolicies(policies);
            if (res.success) {
                if (res.policies) setPolicies(res.policies);
                try {
                    localStorage.setItem('migratepay_security_policies', JSON.stringify(policies));
                } catch (e) {}
                setFeedback({ type: 'success', text: res.message || 'Ironclad security policies applied and saved.' });
            } else {
                setFeedback({ type: 'error', text: res.message || 'Failed to update security policies.' });
            }
        } catch (err) {
            // Fall back to local persistence
            try {
                localStorage.setItem('migratepay_security_policies', JSON.stringify(policies));
                setFeedback({ type: 'success', text: 'Security policies updated locally (offline mode).' });
            } catch (storageErr) {
                setFeedback({ type: 'error', text: err?.response?.data?.message || 'Failed to update security policies.' });
            }
        } finally {
            setSaving(false);
        }
    };

    if (loading) {
        return (
            <Flex justify="center" align="center" minH="300px">
                <Spinner size="lg" color="#1769FF" />
            </Flex>
        );
    }

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

            <form onSubmit={handleSave}>
                <VStack gap={6} align="stretch">
                    <DashboardCard
                        title="Cryptographic & Session Governance"
                        subtitle="Platform-wide authentication thresholds, token lifetimes, and credential protections"
                    >
                        <SimpleGrid columns={{ base: 1, md: 2 }} gap={5}>
                            <Box p={4} borderRadius="xl" borderWidth="1px" borderColor="gray.200" bg="#F8FAFC">
                                <Flex justify="space-between" align="center">
                                    <Box pr={2}>
                                        <Flex align="center" gap={2} mb={1}>
                                            <Text fontWeight="800" fontSize="sm" color="#0A1128">
                                                Mandatory 2FA for Administrators
                                            </Text>
                                            <Badge colorPalette="teal" size="xs" px={2} py={0.5} borderRadius="md">
                                                TOTP Active
                                            </Badge>
                                        </Flex>
                                        <Text fontSize="xs" color="gray.500">
                                            Require verified TOTP authenticator challenge on all administrative sessions
                                        </Text>
                                    </Box>
                                    <Button
                                        size="xs"
                                        bg={policies.enforce2FA ? '#10B981' : '#E2E8F0'}
                                        color={policies.enforce2FA ? 'white' : 'gray.700'}
                                        onClick={() => updatePolicy('enforce2FA', !policies.enforce2FA)}
                                        borderRadius="full"
                                    >
                                        {policies.enforce2FA ? 'Enforced' : 'Optional'}
                                    </Button>
                                </Flex>
                            </Box>

                            <Box p={4} borderRadius="xl" borderWidth="1px" borderColor="gray.200" bg="#F8FAFC">
                                <Flex justify="space-between" align="center">
                                    <Box>
                                        <Text fontWeight="800" fontSize="sm" color="#0A1128">
                                            KYC Gate on Payout Withdrawals
                                        </Text>
                                        <Text fontSize="xs" color="gray.500">
                                            Require approved national identity verification before payout clearance
                                        </Text>
                                    </Box>
                                    <Button
                                        size="xs"
                                        bg={policies.requireKycForWithdrawal ? '#10B981' : '#E2E8F0'}
                                        color={policies.requireKycForWithdrawal ? 'white' : 'gray.700'}
                                        onClick={() => updatePolicy('requireKycForWithdrawal', !policies.requireKycForWithdrawal)}
                                        borderRadius="full"
                                    >
                                        {policies.requireKycForWithdrawal ? 'Enforced' : 'Disabled'}
                                    </Button>
                                </Flex>
                            </Box>

                            <Box>
                                <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                    Session Idle Timeout (Minutes)
                                </Text>
                                <Input
                                    type="number"
                                    value={policies.sessionTimeoutMinutes}
                                    onChange={(e) => updatePolicy('sessionTimeoutMinutes', Number(e.target.value))}
                                    borderRadius="lg"
                                />
                                <Text fontSize="2xs" color="gray.500" mt={1}>
                                    Sessions idle longer than this limit will automatically terminate.
                                </Text>
                            </Box>

                            <Box>
                                <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                    Max Failed Login Attempts
                                </Text>
                                <Input
                                    type="number"
                                    value={policies.maxFailedLogins}
                                    onChange={(e) => updatePolicy('maxFailedLogins', Number(e.target.value))}
                                    borderRadius="lg"
                                />
                                <Text fontSize="2xs" color="gray.500" mt={1}>
                                    IP address lock triggers after exceeding this limit.
                                </Text>
                            </Box>

                            <Box gridColumn={{ base: 'span 1', md: 'span 2' }}>
                                <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                    Administrative IP Whitelist (Optional)
                                </Text>
                                <Input
                                    placeholder="e.g. 192.168.1.1, 103.205.180.4 (Leave blank to permit all authenticated admins)"
                                    value={policies.ipWhitelist || ''}
                                    onChange={(e) => updatePolicy('ipWhitelist', e.target.value)}
                                    borderRadius="lg"
                                />
                            </Box>
                        </SimpleGrid>
                    </DashboardCard>

                    <Flex justify="flex-end">
                        <Button
                            size="md"
                            bg="#1769FF"
                            color="white"
                            _hover={{ bg: '#0D52D6' }}
                            type="submit"
                            isLoading={saving}
                            borderRadius="xl"
                            px={8}
                        >
                            Save Security Policies
                        </Button>
                    </Flex>
                </VStack>
            </form>
        </VStack>
    );
};

export default SecurityPoliciesModule;
