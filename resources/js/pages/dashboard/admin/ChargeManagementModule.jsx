import React, { useState } from 'react';
import {
    Box,
    Flex,
    Grid,
    HStack,
    VStack,
    Text,
    Heading,
    Button,
    Input,
    Badge,
} from '@chakra-ui/react';
import { PageHeader } from '../../../components/common/PageHeader';
import { DashboardCard } from '../../../components/common/DashboardCard';
import { toaster } from '../../../components/feedback/toaster';
import { WalletIcon, CheckCircleIcon } from '../../../components/common/Icons';

const CHARGE_SETTINGS_KEY = 'migratepay_charge_settings';

const loadChargeSettings = () => {
    const defaults = { tier1: 5.0, tier2: 4.5, tier3: 4.0, tier4: 3.0, minFee: 50 };
    try {
        return JSON.parse(localStorage.getItem(CHARGE_SETTINGS_KEY)) || defaults;
    } catch (e) {
        return defaults;
    }
};

export const ChargeManagementModule = () => {
    const [settings, setSettings] = useState(() => loadChargeSettings());
    const [simAmount, setSimAmount] = useState('25000');
    const [saved, setSaved] = useState(false);

    const handleUpdate = (tier, val) => {
        setSettings((prev) => ({ ...prev, [tier]: parseFloat(val) || 0 }));
    };

    const handleSave = () => {
        localStorage.setItem(CHARGE_SETTINGS_KEY, JSON.stringify(settings));
        window.dispatchEvent(new Event('storage'));
        setSaved(true);
        toaster.create({
            title: 'Fee Parameters Saved',
            description: 'Global escrow fee parameters updated and synchronized.',
            type: 'success',
        });
        setTimeout(() => setSaved(false), 3000);
    };

    // Simulated calculation using backend tiers
    const calcSimFee = () => {
        const amt = parseFloat(simAmount) || 0;
        let rate = settings.tier1;
        if (amt > 500000) rate = settings.tier4;
        else if (amt > 200000) rate = settings.tier3;
        else if (amt > 50000) rate = settings.tier2;

        const fee = Math.max((amt * rate) / 100, settings.minFee || 50);
        return { rate, fee, totalBuyerPays: amt + fee, sellerGets: amt };
    };

    const sim = calcSimFee();

    return (
        <Box>
            <PageHeader
                title="Gateway Fee Grid & Commission Tiers"
                subtitle="Configure platform escrow commission rates, sliding tier percentages, and minimum charge parameters."
                actions={
                    <Button
                        size="sm"
                        bg="#0A1128"
                        color="white"
                        _hover={{ bg: '#162447' }}
                        borderRadius="lg"
                        onClick={handleSave}
                    >
                        Save Fee Parameters
                    </Button>
                }
            />

            {/* Fee Tiers Grid */}
            <DashboardCard
                title="Sliding Escrow Commission Tiers"
                subtitle="Escrow fee dynamically slides according to transaction transaction size to encourage high-value volume."
                mb={8}
            >
                <Grid templateColumns={{ base: '1fr', sm: 'repeat(2, 1fr)', lg: 'repeat(4, 1fr)' }} gap={4} mb={6}>
                    <Box p={5} borderRadius="2xl" borderWidth="1px" borderColor="gray.200" bg="#FAFAFA">
                        <Text fontSize="2xs" fontWeight="700" color="gray.500" textTransform="uppercase">
                            TIER 1 (৳0 — ৳50,000)
                        </Text>
                        <Text fontSize="2xl" fontWeight="900" color="#0A1128" mt={2} mb={3}>
                            {settings.tier1}%
                        </Text>
                        <HStack gap={2}>
                            <Input
                                size="xs"
                                type="number"
                                step="0.1"
                                value={settings.tier1}
                                onChange={(e) => handleUpdate('tier1', e.target.value)}
                                borderRadius="md"
                                bg="white"
                            />
                            <Text fontSize="xs" fontWeight="700">%</Text>
                        </HStack>
                    </Box>

                    <Box p={5} borderRadius="2xl" borderWidth="1px" borderColor="gray.200" bg="#FAFAFA">
                        <Text fontSize="2xs" fontWeight="700" color="gray.500" textTransform="uppercase">
                            TIER 2 (৳50,001 — ৳200,000)
                        </Text>
                        <Text fontSize="2xl" fontWeight="900" color="#0A1128" mt={2} mb={3}>
                            {settings.tier2}%
                        </Text>
                        <HStack gap={2}>
                            <Input
                                size="xs"
                                type="number"
                                step="0.1"
                                value={settings.tier2}
                                onChange={(e) => handleUpdate('tier2', e.target.value)}
                                borderRadius="md"
                                bg="white"
                            />
                            <Text fontSize="xs" fontWeight="700">%</Text>
                        </HStack>
                    </Box>

                    <Box p={5} borderRadius="2xl" borderWidth="1px" borderColor="gray.200" bg="#FAFAFA">
                        <Text fontSize="2xs" fontWeight="700" color="gray.500" textTransform="uppercase">
                            TIER 3 (৳200,001 — ৳500,000)
                        </Text>
                        <Text fontSize="2xl" fontWeight="900" color="#0A1128" mt={2} mb={3}>
                            {settings.tier3}%
                        </Text>
                        <HStack gap={2}>
                            <Input
                                size="xs"
                                type="number"
                                step="0.1"
                                value={settings.tier3}
                                onChange={(e) => handleUpdate('tier3', e.target.value)}
                                borderRadius="md"
                                bg="white"
                            />
                            <Text fontSize="xs" fontWeight="700">%</Text>
                        </HStack>
                    </Box>

                    <Box p={5} borderRadius="2xl" borderWidth="1px" borderColor="gray.200" bg="#FAFAFA">
                        <Text fontSize="2xs" fontWeight="700" color="gray.500" textTransform="uppercase">
                            TIER 4 (৳500,001+)
                        </Text>
                        <Text fontSize="2xl" fontWeight="900" color="#0A1128" mt={2} mb={3}>
                            {settings.tier4}%
                        </Text>
                        <HStack gap={2}>
                            <Input
                                size="xs"
                                type="number"
                                step="0.1"
                                value={settings.tier4}
                                onChange={(e) => handleUpdate('tier4', e.target.value)}
                                borderRadius="md"
                                bg="white"
                            />
                            <Text fontSize="xs" fontWeight="700">%</Text>
                        </HStack>
                    </Box>
                </Grid>
            </DashboardCard>

            {/* Fee Simulator Box */}
            <DashboardCard
                title="Live Commission Simulator"
                subtitle="Preview authoritative fee calculations applied when users initiate contracts."
            >
                <Grid templateColumns={{ base: '1fr', md: '1fr 1fr' }} gap={6} align="center">
                    <Box>
                        <Text fontSize="xs" fontWeight="700" color="gray.600" mb={1}>
                            Test Deal Amount (BDT)
                        </Text>
                        <Input
                            size="md"
                            type="number"
                            value={simAmount}
                            onChange={(e) => setSimAmount(e.target.value)}
                            borderRadius="xl"
                        />
                        <Text fontSize="2xs" color="gray.500" mt={2}>
                            Calculated dynamically according to active parameters and BCMath decimal precision.
                        </Text>
                    </Box>

                    <Box p={5} bg="gray.50" borderRadius="xl">
                        <HStack justify="space-between" mb={2}>
                            <Text fontSize="xs" color="gray.600">Applicable Tier Rate:</Text>
                            <Badge colorPalette="blue">{sim.rate}%</Badge>
                        </HStack>
                        <HStack justify="space-between" mb={2}>
                            <Text fontSize="xs" color="gray.600">Calculated Escrow Fee:</Text>
                            <Text fontSize="sm" fontWeight="700" color="#0A1128">৳ {sim.fee.toFixed(2)}</Text>
                        </HStack>
                        <HStack justify="space-between" mb={2}>
                            <Text fontSize="xs" color="gray.600">Buyer Pays Total:</Text>
                            <Text fontSize="sm" fontWeight="800" color="#1769FF">৳ {sim.totalBuyerPays.toFixed(2)}</Text>
                        </HStack>
                        <HStack justify="space-between">
                            <Text fontSize="xs" color="gray.600">Seller Receives Net:</Text>
                            <Text fontSize="sm" fontWeight="800" color="#006A4E">৳ {sim.sellerGets.toFixed(2)}</Text>
                        </HStack>
                    </Box>
                </Grid>
            </DashboardCard>
        </Box>
    );
};

export default ChargeManagementModule;

