import React, { useState } from 'react';
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
} from '@chakra-ui/react';
import { DashboardCard } from '../../../components/common/DashboardCard';

const defaultMenuItems = [
    { id: 1, label: 'Home', url: '/', order: 1 },
    { id: 2, label: 'About Us', url: '/about', order: 2 },
    { id: 3, label: 'Escrow Calculator', url: '/charge-calculator', order: 3 },
    { id: 4, label: 'Safety & Guides', url: '/blog', order: 4 },
    { id: 5, label: 'Contact Support', url: '/contact', order: 5 },
];

export const MenusModule = () => {
    const [items, setItems] = useState(() => {
        try {
            const saved = localStorage.getItem('migratepay_menu_config');
            if (saved) return JSON.parse(saved);
        } catch (e) {}
        return defaultMenuItems;
    });

    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingItem, setEditingItem] = useState(null);
    const [form, setForm] = useState({ label: '', url: '', order: 1 });
    const [feedback, setFeedback] = useState(null);

    const openCreateModal = () => {
        setEditingItem(null);
        setForm({ label: '', url: '/', order: items.length + 1 });
        setIsModalOpen(true);
    };

    const openEditModal = (item) => {
        setEditingItem(item);
        setForm({ label: item.label, url: item.url, order: item.order });
        setIsModalOpen(true);
    };

    const handleSave = (e) => {
        e.preventDefault();
        let updated;
        if (editingItem) {
            updated = items.map((i) => (i.id === editingItem.id ? { ...i, ...form } : i));
            setFeedback({ type: 'success', text: `Menu item "${form.label}" updated.` });
        } else {
            const newItem = { id: Date.now(), ...form };
            updated = [...items, newItem];
            setFeedback({ type: 'success', text: `Menu item "${form.label}" added.` });
        }

        updated.sort((a, b) => Number(a.order) - Number(b.order));
        setItems(updated);
        localStorage.setItem('migratepay_menu_config', JSON.stringify(updated));
        setIsModalOpen(false);
    };

    const handleDelete = (id, label) => {
        if (!window.confirm(`Remove menu item "${label}"?`)) return;
        const updated = items.filter((i) => i.id !== id);
        setItems(updated);
        localStorage.setItem('migratepay_menu_config', JSON.stringify(updated));
        setFeedback({ type: 'success', text: `Removed "${label}".` });
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
                title="Navigation & Header Menu Builder"
                subtitle="Configure the public header bar and footer navigation links"
                headerAction={
                    <Button
                        size="sm"
                        bg="#1769FF"
                        color="white"
                        _hover={{ bg: '#0D52D6' }}
                        onClick={openCreateModal}
                        borderRadius="lg"
                    >
                        + Add Navigation Link
                    </Button>
                }
            >
                <Box overflowX="auto">
                    <table style={{ width: '100%', borderCollapse: 'collapse', textAlign: 'left' }}>
                        <thead>
                            <tr style={{ borderBottom: '1px solid #E2E8F0', backgroundColor: '#F8FAFC' }}>
                                <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Position</th>
                                <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Navigation Label</th>
                                <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Destination URL</th>
                                <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {items.map((item) => (
                                <tr key={item.id} style={{ borderBottom: '1px solid #F1F5F9' }}>
                                    <td style={{ padding: '14px 16px' }}>
                                        <Badge bg="#F1F5F9" color="#0A1128" fontSize="xs" px={2} py={0.5} borderRadius="md" fontWeight="800">
                                            #{item.order}
                                        </Badge>
                                    </td>
                                    <td style={{ padding: '14px 16px', fontWeight: '800', color: '#0A1128', fontSize: 'sm' }}>
                                        {item.label}
                                    </td>
                                    <td style={{ padding: '14px 16px', fontFamily: 'mono', fontSize: 'xs', color: '#1769FF' }}>
                                        {item.url}
                                    </td>
                                    <td style={{ padding: '14px 16px' }}>
                                        <HStack gap={2}>
                                            <Button size="xs" variant="outline" borderColor="gray.300" onClick={() => openEditModal(item)}>
                                                Edit
                                            </Button>
                                            <Button size="xs" variant="ghost" color="#DC2626" onClick={() => handleDelete(item.id, item.label)}>
                                                Delete
                                            </Button>
                                        </HStack>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </Box>
            </DashboardCard>

            {/* Modal */}
            {isModalOpen && (
                <Box
                    position="fixed"
                    top="0"
                    left="0"
                    right="0"
                    bottom="0"
                    bg="blackAlpha.600"
                    zIndex="9999"
                    display="flex"
                    alignItems="center"
                    justifyContent="center"
                    p={4}
                >
                    <Box bg="white" borderRadius="2xl" maxW="440px" w="100%" p={6} boxShadow="2xl">
                        <Flex justify="space-between" align="center" mb={4}>
                            <Heading size="md" color="#0A1128">
                                {editingItem ? 'Edit Navigation Link' : 'Add Navigation Link'}
                            </Heading>
                            <Button size="sm" variant="ghost" onClick={() => setIsModalOpen(false)}>
                                ✕
                            </Button>
                        </Flex>

                        <form onSubmit={handleSave}>
                            <VStack gap={4} align="stretch">
                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                        Link Label *
                                    </Text>
                                    <Input
                                        placeholder="e.g. Developer APIs"
                                        value={form.label}
                                        onChange={(e) => setForm({ ...form, label: e.target.value })}
                                        borderRadius="lg"
                                        required
                                    />
                                </Box>

                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                        Destination URL / Route *
                                    </Text>
                                    <Input
                                        placeholder="/docs/api or https://..."
                                        value={form.url}
                                        onChange={(e) => setForm({ ...form, url: e.target.value })}
                                        borderRadius="lg"
                                        required
                                    />
                                </Box>

                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                        Display Order
                                    </Text>
                                    <Input
                                        type="number"
                                        value={form.order}
                                        onChange={(e) => setForm({ ...form, order: e.target.value })}
                                        borderRadius="lg"
                                    />
                                </Box>

                                <Flex justify="flex-end" gap={3} mt={4}>
                                    <Button size="sm" variant="outline" onClick={() => setIsModalOpen(false)} borderRadius="lg">
                                        Cancel
                                    </Button>
                                    <Button size="sm" bg="#1769FF" color="white" _hover={{ bg: '#0D52D6' }} type="submit" borderRadius="lg">
                                        Save Link
                                    </Button>
                                </Flex>
                            </VStack>
                        </form>
                    </Box>
                </Box>
            )}
        </VStack>
    );
};

export default MenusModule;
