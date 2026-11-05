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
    Spinner,
} from '@chakra-ui/react';
import { adminApi } from '../../../services/adminApi';
import { DashboardCard } from '../../../components/common/DashboardCard';

export const ProjectSubmissionModule = () => {
    const [submissions, setSubmissions] = useState([]);
    const [loading, setLoading] = useState(true);
    const [selectedSubmission, setSelectedSubmission] = useState(null);

    const fetchSubmissions = async () => {
        setLoading(true);
        try {
            const data = await adminApi.getDeals();
            const deals = Array.isArray(data.deals) ? data.deals : Array.isArray(data) ? data : [];
            const subs = [];

            deals.forEach((d) => {
                const wsSubs = d.workspace?.submissions || d.milestones || [];
                if (Array.isArray(wsSubs) && wsSubs.length > 0) {
                    wsSubs.forEach((s) => {
                        subs.push({
                            ...s,
                            id: s.id || `${d.id}-${s.title || 'sub'}`,
                            deal_id: d.id,
                            deal_title: d.title,
                            seller: d.seller?.name || d.counterparty_name || 'Seller',
                            buyer: d.buyer?.name || d.creator?.name || 'Buyer',
                            amount: d.amount,
                            submitted_at: s.created_at || d.updated_at,
                        });
                    });
                } else if (d.status === 'in_escrow' || d.status === 'completed') {
                    subs.push({
                        id: `deal-${d.id}`,
                        deal_id: d.id,
                        deal_title: d.title,
                        seller: d.seller?.name || 'Assigned Seller',
                        buyer: d.buyer?.name || 'Assigned Buyer',
                        title: 'Final Project Deliverable & Asset Archive',
                        notes: d.description || 'Deliverables verified in workspace escrow.',
                        status: d.status === 'completed' ? 'approved' : 'submitted',
                        amount: d.amount,
                        submitted_at: d.updated_at,
                    });
                }
            });
            setSubmissions(subs);
        } catch (err) {
            console.error('Failed to load project deliverables:', err);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchSubmissions();
    }, []);

    return (
        <VStack gap={6} align="stretch" pb={12}>
            <DashboardCard
                title="Project Deliverables & Work Submissions"
                subtitle="Inspect deliverables, code archives, domain transfer proofs, and milestones submitted by sellers"
                headerAction={
                    <Button size="xs" variant="outline" onClick={fetchSubmissions} borderRadius="md">
                        Refresh Queue
                    </Button>
                }
            >
                {loading ? (
                    <Flex justify="center" align="center" py={12}>
                        <Spinner size="lg" color="#1769FF" />
                    </Flex>
                ) : submissions.length === 0 ? (
                    <Box textAlign="center" py={12} color="gray.500">
                        <Text fontSize="lg" fontWeight="700">No Submissions Recorded</Text>
                        <Text fontSize="sm" mt={1}>When sellers upload deliverable files to escrow workspaces, they will appear here.</Text>
                    </Box>
                ) : (
                    <Box overflowX="auto">
                        <table style={{ width: '100%', borderCollapse: 'collapse', textAlign: 'left' }}>
                            <thead>
                                <tr style={{ borderBottom: '1px solid #E2E8F0', backgroundColor: '#F8FAFC' }}>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Deal / Contract</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Seller / Creator</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Deliverable</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Escrow Value</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Status</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Inspect</th>
                                </tr>
                            </thead>
                            <tbody>
                                {submissions.map((sub) => (
                                    <tr key={sub.id} style={{ borderBottom: '1px solid #F1F5F9' }}>
                                        <td style={{ padding: '14px 16px' }}>
                                            <Text fontWeight="800" color="#0A1128" fontSize="sm">
                                                {sub.deal_title}
                                            </Text>
                                            <Text fontSize="2xs" color="gray.500">
                                                Contract #{sub.deal_id}
                                            </Text>
                                        </td>
                                        <td style={{ padding: '14px 16px', fontSize: 'xs', color: 'gray.800', fontWeight: '600' }}>
                                            {sub.seller}
                                        </td>
                                        <td style={{ padding: '14px 16px', fontSize: 'xs', color: 'gray.700', maxWidth: '280px' }}>
                                            <Text fontWeight="700" noOfLines={1}>{sub.title || 'Work Milestone'}</Text>
                                            <Text fontSize="2xs" color="gray.500" noOfLines={1}>{sub.notes || 'Files attached in workspace'}</Text>
                                        </td>
                                        <td style={{ padding: '14px 16px', fontWeight: '800', color: '#0A1128', fontSize: 'sm' }}>
                                            ৳ {Number(sub.amount || 0).toLocaleString()}
                                        </td>
                                        <td style={{ padding: '14px 16px' }}>
                                            <Badge
                                                bg={sub.status === 'approved' ? '#DEF7EC' : '#EFF6FF'}
                                                color={sub.status === 'approved' ? '#03543F' : '#1E40AF'}
                                                fontSize="xs"
                                                px={2}
                                                py={0.5}
                                                borderRadius="md"
                                                fontWeight="700"
                                            >
                                                {sub.status || 'Submitted'}
                                            </Badge>
                                        </td>
                                        <td style={{ padding: '14px 16px' }}>
                                            <Button
                                                size="xs"
                                                variant="outline"
                                                borderColor="gray.300"
                                                onClick={() => setSelectedSubmission(sub)}
                                            >
                                                View Details
                                            </Button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </Box>
                )}
            </DashboardCard>

            {/* Inspection Modal */}
            {selectedSubmission && (
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
                    <Box bg="white" borderRadius="2xl" maxW="500px" w="100%" p={6} boxShadow="2xl">
                        <Flex justify="space-between" align="center" mb={4}>
                            <Heading size="md" color="#0A1128">
                                Submission Inspection
                            </Heading>
                            <Button size="sm" variant="ghost" onClick={() => setSelectedSubmission(null)}>
                                ✕
                            </Button>
                        </Flex>

                        <VStack gap={3} align="stretch" fontSize="sm">
                            <Box bg="#F8FAFC" p={3} borderRadius="lg" border="1px solid #E2E8F0">
                                <Text fontSize="xs" fontWeight="700" color="gray.500">CONTRACT / DEAL:</Text>
                                <Text fontWeight="800" color="#0A1128">{selectedSubmission.deal_title}</Text>
                                <Text fontSize="xs" color="gray.600">ID #{selectedSubmission.deal_id} &bull; Escrow Amount: ৳ {Number(selectedSubmission.amount || 0).toLocaleString()}</Text>
                            </Box>

                            <Box>
                                <Text fontSize="xs" fontWeight="700" color="gray.500">SELLER DETAILS:</Text>
                                <Text fontWeight="700" color="gray.800">{selectedSubmission.seller}</Text>
                            </Box>

                            <Box>
                                <Text fontSize="xs" fontWeight="700" color="gray.500">DELIVERABLE SUMMARY:</Text>
                                <Text color="gray.800">{selectedSubmission.notes || 'Files and assets submitted directly into the escrow milestone workspace.'}</Text>
                            </Box>
                        </VStack>

                        <Flex justify="flex-end" mt={6}>
                            <Button size="sm" bg="#1769FF" color="white" onClick={() => setSelectedSubmission(null)} borderRadius="lg">
                                Close Inspection
                            </Button>
                        </Flex>
                    </Box>
                </Box>
            )}
        </VStack>
    );
};

export default ProjectSubmissionModule;
