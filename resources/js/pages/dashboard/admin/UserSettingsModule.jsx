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
    SimpleGrid,
} from '@chakra-ui/react';
import { DashboardCard } from '../../../components/common/DashboardCard';

const STAFF_USERS_KEY = 'migratepay_staff_accounts';

const defaultStaff = [
    {
        id: 'STAFF-001',
        name: 'Principal Administrator',
        email: 'admin@migratepay.com',
        role: 'Admin',
        status: 'Active',
        created: '2026-01-01',
    },
    {
        id: 'STAFF-002',
        name: 'Escrow Dispute Specialist',
        email: 'disputes@migratepay.com',
        role: 'Support Team',
        status: 'Active',
        created: '2026-03-15',
    },
];

export const UserSettingsModule = () => {
    const [staff, setStaff] = useState(() => {
        try {
            const saved = localStorage.getItem(STAFF_USERS_KEY);
            if (saved) return JSON.parse(saved);
        } catch (e) {}
        return defaultStaff;
    });

    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingStaff, setEditingStaff] = useState(null);
    const [form, setForm] = useState({
        name: '',
        email: '',
        role: 'Support Team',
        status: 'Active',
    });
    const [feedback, setFeedback] = useState(null);

    const openCreateModal = () => {
        setEditingStaff(null);
        setForm({ name: '', email: '', role: 'Support Team', status: 'Active' });
        setIsModalOpen(true);
    };

    const openEditModal = (member) => {
        setEditingStaff(member);
        setForm({
            name: member.name,
            email: member.email,
            role: member.role,
            status: member.status,
        });
        setIsModalOpen(true);
    };

    const handleSave = (e) => {
        e.preventDefault();
        let updatedList;
        if (editingStaff) {
            updatedList = staff.map((s) =>
                s.id === editingStaff.id ? { ...s, ...form } : s
            );
            setFeedback({ type: 'success', text: `Staff account "${form.name}" updated.` });
        } else {
            const newMember = {
                id: `STAFF-${String(staff.length + 1).padStart(3, '0')}`,
                ...form,
                created: new Date().toISOString().split('T')[0],
            };
            updatedList = [...staff, newMember];
            setFeedback({ type: 'success', text: `Staff account "${form.name}" added.` });
        }

        setStaff(updatedList);
        localStorage.setItem(STAFF_USERS_KEY, JSON.stringify(updatedList));
        setIsModalOpen(false);
    };

    const handleDelete = (id, name) => {
        if (id === 'STAFF-001') {
            alert('Root Super Administrator cannot be removed.');
            return;
        }
        if (!window.confirm(`Revoke staff access for ${name}?`)) return;
        const updated = staff.filter((s) => s.id !== id);
        setStaff(updated);
        localStorage.setItem(STAFF_USERS_KEY, JSON.stringify(updated));
        setFeedback({ type: 'success', text: `Access revoked for ${name}.` });
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
                title="Staff Accounts & Role Privileges"
                subtitle="Assign administrative access levels, arbitration capabilities, and support desk permissions"
                headerAction={
                    <Button
                        size="sm"
                        bg="#1769FF"
                        color="white"
                        _hover={{ bg: '#0D52D6' }}
                        onClick={openCreateModal}
                        borderRadius="lg"
                    >
                        + Add Staff Member
                    </Button>
                }
            >
                <Box overflowX="auto">
                    <table style={{ width: '100%', borderCollapse: 'collapse', textAlign: 'left' }}>
                        <thead>
                            <tr style={{ borderBottom: '1px solid #E2E8F0', backgroundColor: '#F8FAFC' }}>
                                <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Staff Member</th>
                                <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Assigned Role</th>
                                <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Status</th>
                                <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Member Since</th>
                                <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {staff.map((s) => (
                                <tr key={s.id} style={{ borderBottom: '1px solid #F1F5F9' }}>
                                    <td style={{ padding: '14px 16px' }}>
                                        <Text fontWeight="800" color="#0A1128" fontSize="sm">
                                            {s.name}
                                        </Text>
                                        <Text fontSize="xs" color="gray.500">
                                            {s.email} &bull; {s.id}
                                        </Text>
                                    </td>
                                    <td style={{ padding: '14px 16px' }}>
                                        <Badge
                                            bg={
                                                s.role === 'Admin'
                                                    ? '#FEE2E2'
                                                    : s.role === 'Co-Admin'
                                                    ? '#F3E8FF'
                                                    : '#EFF6FF'
                                            }
                                            color={
                                                s.role === 'Admin'
                                                    ? '#991B1B'
                                                    : s.role === 'Co-Admin'
                                                    ? '#6B21A8'
                                                    : '#1E40AF'
                                            }
                                            fontSize="xs"
                                            px={2}
                                            py={0.5}
                                            borderRadius="md"
                                            fontWeight="700"
                                        >
                                            {s.role}
                                        </Badge>
                                    </td>
                                    <td style={{ padding: '14px 16px' }}>
                                        <Badge
                                            bg={s.status === 'Active' ? '#DEF7EC' : '#F3F4F6'}
                                            color={s.status === 'Active' ? '#03543F' : '#4B5563'}
                                            fontSize="xs"
                                            px={2}
                                            py={0.5}
                                            borderRadius="md"
                                        >
                                            {s.status}
                                        </Badge>
                                    </td>
                                    <td style={{ padding: '14px 16px', fontSize: 'xs', color: 'gray.500' }}>
                                        {s.created}
                                    </td>
                                    <td style={{ padding: '14px 16px' }}>
                                        <HStack gap={2}>
                                            <Button size="xs" variant="outline" borderColor="gray.300" onClick={() => openEditModal(s)}>
                                                Edit
                                            </Button>
                                            {s.id !== 'STAFF-001' && (
                                                <Button size="xs" variant="ghost" color="#DC2626" onClick={() => handleDelete(s.id, s.name)}>
                                                    Revoke
                                                </Button>
                                            )}
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
                    <Box bg="white" borderRadius="2xl" maxW="480px" w="100%" p={6} boxShadow="2xl">
                        <Flex justify="space-between" align="center" mb={4}>
                            <Heading size="md" color="#0A1128">
                                {editingStaff ? 'Edit Staff Privileges' : 'Invite Staff Account'}
                            </Heading>
                            <Button size="sm" variant="ghost" onClick={() => setIsModalOpen(false)}>
                                ✕
                            </Button>
                        </Flex>

                        <form onSubmit={handleSave}>
                            <VStack gap={4} align="stretch">
                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                        Full Name *
                                    </Text>
                                    <Input
                                        placeholder="e.g. Tariq Rahman"
                                        value={form.name}
                                        onChange={(e) => setForm({ ...form, name: e.target.value })}
                                        borderRadius="lg"
                                        required
                                    />
                                </Box>

                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                        Official Work Email *
                                    </Text>
                                    <Input
                                        type="email"
                                        placeholder="staff@migratepay.com"
                                        value={form.email}
                                        onChange={(e) => setForm({ ...form, email: e.target.value })}
                                        borderRadius="lg"
                                        required
                                    />
                                </Box>

                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                        Role Privilege *
                                    </Text>
                                    <select
                                        style={{
                                            width: '100%',
                                            padding: '8px 12px',
                                            borderRadius: '8px',
                                            border: '1px solid #CBD5E1',
                                            fontSize: '14px',
                                        }}
                                        value={form.role}
                                        onChange={(e) => setForm({ ...form, role: e.target.value })}
                                    >
                                        <option value="Admin">Super Admin (Full Platform Control)</option>
                                        <option value="Co-Admin">Co-Admin (Operations & Financial Approval)</option>
                                        <option value="Editorial Team">Editorial Team (CMS, Blogs, Categories)</option>
                                        <option value="Support Team">Support Specialist (Tickets & Disputes)</option>
                                    </select>
                                </Box>

                                <Flex justify="flex-end" gap={3} mt={4}>
                                    <Button size="sm" variant="outline" onClick={() => setIsModalOpen(false)} borderRadius="lg">
                                        Cancel
                                    </Button>
                                    <Button size="sm" bg="#1769FF" color="white" _hover={{ bg: '#0D52D6' }} type="submit" borderRadius="lg">
                                        {editingStaff ? 'Save Changes' : 'Confirm & Authorize'}
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

export default UserSettingsModule;
