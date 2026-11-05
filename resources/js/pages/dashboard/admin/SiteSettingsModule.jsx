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
import { DashboardCard } from '../../../components/common/DashboardCard';

export const SiteSettingsModule = () => {
    const [settings, setSettings] = useState(() => {
        try {
            const saved = localStorage.getItem('migratepay_site_settings');
            if (saved) return JSON.parse(saved);
        } catch (e) {}
        return {
            siteName: 'MigratePay',
            tagline: 'Enterprise Digital Escrow & Milestone Protection',
            currency: '৳',
            supportEmail: 'support@migratepay.com',
            supportPhone: '+880 1700-000000',
            maintenanceMode: false,
            address: 'Gulshan-2, Dhaka 1212, Bangladesh',
            fbUrl: 'https://facebook.com/migratepay',
            twitterUrl: 'https://twitter.com/migratepay',
            telegramUrl: 'https://t.me/migratepay',
        };
    });

    const [saving, setSaving] = useState(false);
    const [feedback, setFeedback] = useState(null);

    const updateField = (key, value) => {
        setSettings((prev) => ({ ...prev, [key]: value }));
    };

    const handleSave = (e) => {
        e.preventDefault();
        setSaving(true);
        try {
            localStorage.setItem('migratepay_site_settings', JSON.stringify(settings));
            setFeedback({ type: 'success', text: 'Platform parameters successfully saved.' });
        } catch (err) {
            setFeedback({ type: 'error', text: 'Failed to update settings.' });
        } finally {
            setSaving(false);
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

            <form onSubmit={handleSave}>
                <VStack gap={6} align="stretch">
                    {/* General Settings */}
                    <DashboardCard
                        title="General Branding & Regional Localization"
                        subtitle="Primary platform metadata, title tags, and localized monetary symbol"
                    >
                        <SimpleGrid columns={{ base: 1, md: 2 }} gap={4}>
                            <Box>
                                <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                    Platform Legal Name *
                                </Text>
                                <Input
                                    value={settings.siteName}
                                    onChange={(e) => updateField('siteName', e.target.value)}
                                    borderRadius="lg"
                                    required
                                />
                            </Box>
                            <Box>
                                <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                    Primary Currency Symbol
                                </Text>
                                <select
                                    style={{
                                        width: '100%',
                                        padding: '8px 12px',
                                        borderRadius: '8px',
                                        border: '1px solid #CBD5E1',
                                        fontSize: '14px',
                                    }}
                                    value={settings.currency}
                                    onChange={(e) => updateField('currency', e.target.value)}
                                >
                                    <option value="৳">BDT - Bangladeshi Taka (৳)</option>
                                    <option value="$">USD - US Dollar ($)</option>
                                    <option value="€">EUR - Euro (€)</option>
                                    <option value="£">GBP - British Pound (£)</option>
                                </select>
                            </Box>
                            <Box gridColumn={{ base: 'span 1', md: 'span 2' }}>
                                <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                    Site Tagline
                                </Text>
                                <Input
                                    value={settings.tagline}
                                    onChange={(e) => updateField('tagline', e.target.value)}
                                    borderRadius="lg"
                                />
                            </Box>
                        </SimpleGrid>
                    </DashboardCard>

                    {/* Operational Contacts */}
                    <DashboardCard
                        title="Contact & Support Channels"
                        subtitle="Public helpline, escalated support email, and headquarters location"
                    >
                        <SimpleGrid columns={{ base: 1, md: 2 }} gap={4}>
                            <Box>
                                <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                    Support Email
                                </Text>
                                <Input
                                    type="email"
                                    value={settings.supportEmail}
                                    onChange={(e) => updateField('supportEmail', e.target.value)}
                                    borderRadius="lg"
                                />
                            </Box>
                            <Box>
                                <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                    Emergency Helpline
                                </Text>
                                <Input
                                    value={settings.supportPhone}
                                    onChange={(e) => updateField('supportPhone', e.target.value)}
                                    borderRadius="lg"
                                />
                            </Box>
                            <Box gridColumn={{ base: 'span 1', md: 'span 2' }}>
                                <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                    Registered Office Address
                                </Text>
                                <Input
                                    value={settings.address}
                                    onChange={(e) => updateField('address', e.target.value)}
                                    borderRadius="lg"
                                />
                            </Box>
                        </SimpleGrid>
                    </DashboardCard>

                    {/* Maintenance Mode */}
                    <DashboardCard
                        title="Platform Maintenance Barrier"
                        subtitle="Temporarily restrict deal creations and transactions during core system upgrades"
                    >
                        <Flex justify="space-between" align="center" p={2}>
                            <Box>
                                <Text fontWeight="800" fontSize="sm" color="#0A1128">
                                    System Maintenance Mode
                                </Text>
                                <Text fontSize="xs" color="gray.500">
                                    When enabled, only administrators can access transactional pages.
                                </Text>
                            </Box>
                            <Button
                                size="sm"
                                bg={settings.maintenanceMode ? '#DC2626' : '#10B981'}
                                color="white"
                                onClick={() => updateField('maintenanceMode', !settings.maintenanceMode)}
                                borderRadius="full"
                            >
                                {settings.maintenanceMode ? 'Maintenance ACTIVE' : 'Operational (Live)'}
                            </Button>
                        </Flex>
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
                            Save Site Settings
                        </Button>
                    </Flex>
                </VStack>
            </form>
        </VStack>
    );
};

export default SiteSettingsModule;
