import React from 'react';
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
} from '@chakra-ui/react';
import { PageHeader } from '../../../components/common/PageHeader';
import { DashboardCard } from '../../../components/common/DashboardCard';
import { toaster } from '../../../components/feedback/toaster';
import { SecurityIcon, CheckCircleIcon } from '../../../components/common/Icons';

export const SecurityModule = ({ onNavigate }) => {
    return (
        <Box>
            <PageHeader
                title="Ironclad Security & Trust Hub"
                subtitle="Multi-factor authentication, cryptographic ledger records, and private storage safeguards."
            />

            <Grid templateColumns={{ base: '1fr', lg: '1fr 1fr' }} gap={6} mb={8}>
                {/* 2FA Card */}
                <DashboardCard
                    title="Two-Factor Authentication (2FA)"
                    subtitle="TOTP authenticator app protection."
                >
                    <VStack gap={4} align="stretch">
                        <Flex
                            w={12}
                            h={12}
                            borderRadius="xl"
                            bg="blue.50"
                            color="#1769FF"
                            align="center"
                            justify="center"
                        >
                            <SecurityIcon size={24} color="#1769FF" />
                        </Flex>

                        <Text fontSize="xs" color="gray.600" lineHeight="1.6">
                            Protect your funds by requiring a one-time cryptographic passcode for critical financial operations,
                            escrow releases, and high-value withdrawals.
                        </Text>

                        <Flex
                            p={3.5}
                            bg="green.50"
                            borderRadius="xl"
                            borderWidth="1px"
                            borderColor="green.200"
                            justify="space-between"
                            align="center"
                        >
                            <HStack gap={3}>
                                <Box w={2.5} h={2.5} borderRadius="full" bg="#10B981" />
                                <Box>
                                    <Text fontSize="xs" fontWeight="800" color="#065F46">
                                        Status: Protected
                                    </Text>
                                    <Text fontSize="2xs" color="green.700">
                                        Session Gated via Signed Credentials
                                    </Text>
                                </Box>
                            </HStack>

                            <Button
                                size="xs"
                                variant="outline"
                                colorPalette="red"
                                borderRadius="lg"
                                onClick={() =>
                                    toaster.create({
                                        title: '2FA Mandatory',
                                        description: 'Two-factor protection cannot be disabled while escrow transactions are active.',
                                        type: 'info',
                                    })
                                }
                            >
                                Disable 2FA
                            </Button>
                        </Flex>
                    </VStack>
                </DashboardCard>

                {/* Database Ledger Encryption */}
                <DashboardCard
                    title="Cryptographic Vault Encryption"
                    subtitle="Enterprise data security specifications."
                >
                    <VStack gap={3} align="stretch">
                        <Flex
                            w={12}
                            h={12}
                            borderRadius="xl"
                            bg="purple.50"
                            color="#7C3AED"
                            align="center"
                            justify="center"
                        >
                            <SecurityIcon size={24} color="#7C3AED" />
                        </Flex>

                        <Text fontSize="xs" color="gray.600" lineHeight="1.6">
                            MigratePay V12 utilizes strict server-side row-level pessimistic locking (`FOR UPDATE`)
                            with BCMath-based decimal precision across all monetary computations to guarantee zero rounding variance.
                        </Text>

                        <Box p={3} bg="gray.50" borderRadius="xl" borderWidth="1px" borderColor="gray.200">
                            <VStack gap={1} align="stretch" fontSize="xs">
                                <Flex justify="space-between">
                                    <Text color="gray.500">Database Engine:</Text>
                                    <Text fontWeight="700" color="#0A1128">MySQL 8.0 InnoDB</Text>
                                </Flex>
                                <Flex justify="space-between">
                                    <Text color="gray.500">Credential Vault:</Text>
                                    <Text fontWeight="700" color="#0A1128">AES-256-CBC Encrypted At Rest</Text>
                                </Flex>
                                <Flex justify="space-between">
                                    <Text color="gray.500">Private Storage:</Text>
                                    <Text fontWeight="700" color="#006A4E">Policy Gated File Streams</Text>
                                </Flex>
                            </VStack>
                        </Box>

                        <Button
                            size="sm"
                            bg="#002147"
                            color="white"
                            borderRadius="lg"
                            onClick={() => onNavigate?.('Profile')}
                        >
                            Manage Account Password
                        </Button>
                    </VStack>
                </DashboardCard>
            </Grid>
        </Box>
    );
};

export default SecurityModule;

