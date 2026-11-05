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
    Textarea,
    Spinner,
} from '@chakra-ui/react';
import { adminApi } from '../../../services/adminApi';
import { DashboardCard } from '../../../components/common/DashboardCard';

export const ReportsManagementModule = () => {
    const [reports, setReports] = useState([]);
    const [loading, setLoading] = useState(true);
    const [filterStatus, setFilterStatus] = useState('all');
    const [resolvingReport, setResolvingReport] = useState(null);
    const [resolutionNote, setResolutionNote] = useState('');
    const [submitting, setSubmitting] = useState(false);
    const [feedback, setFeedback] = useState(null);

    const fetchReports = async () => {
        setLoading(true);
        try {
            const data = await adminApi.getReports(filterStatus === 'all' ? '' : filterStatus);
            if (data && (Array.isArray(data.reports) || Array.isArray(data))) {
                setReports(data.reports || data);
            } else if (data && data.data && Array.isArray(data.data)) {
                setReports(data.data);
            }
        } catch (err) {
            console.error('Failed to load reports:', err);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchReports();
    }, [filterStatus]);

    const handleResolve = async (e) => {
        e.preventDefault();
        if (!resolvingReport || !resolutionNote.trim()) {
            setFeedback({ type: 'error', text: 'Resolution note is required.' });
            return;
        }

        setSubmitting(true);
        try {
            await adminApi.resolveReport(resolvingReport.id, resolutionNote.trim());
            setFeedback({ type: 'success', text: `Report #${resolvingReport.id} successfully resolved.` });
            setResolvingReport(null);
            setResolutionNote('');
            await fetchReports();
        } catch (err) {
            setFeedback({
                type: 'error',
                text: err.response?.data?.message || 'Failed to resolve report.',
            });
        } finally {
            setSubmitting(false);
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
                        <Button
                            size="xs"
                            variant="ghost"
                            onClick={() => setFeedback(null)}
                            color="inherit"
                        >
                            ✕
                        </Button>
                    </Flex>
                </Box>
            )}

            <DashboardCard
                title="Dispute & User Reports Queue"
                subtitle="Review client flags, contract violations, and settle deal disputes"
                headerAction={
                    <HStack gap={2}>
                        {['all', 'pending', 'resolved', 'dismissed'].map((status) => (
                            <Button
                                key={status}
                                size="xs"
                                variant={filterStatus === status ? 'solid' : 'outline'}
                                bg={filterStatus === status ? '#0A1128' : 'transparent'}
                                color={filterStatus === status ? 'white' : 'gray.600'}
                                borderColor="gray.300"
                                borderRadius="md"
                                textTransform="capitalize"
                                onClick={() => setFilterStatus(status)}
                            >
                                {status}
                            </Button>
                        ))}
                    </HStack>
                }
            >
                {loading ? (
                    <Flex justify="center" align="center" py={12}>
                        <Spinner size="lg" color="#1769FF" />
                    </Flex>
                ) : reports.length === 0 ? (
                    <Box textAlign="center" py={12} color="gray.500">
                        <Text fontSize="lg" fontWeight="700">No Reports in this Queue</Text>
                        <Text fontSize="sm" mt={1}>All contracts and user submissions are in good standing.</Text>
                    </Box>
                ) : (
                    <Box overflowX="auto">
                        <table style={{ width: '100%', borderCollapse: 'collapse', textAlign: 'left' }}>
                            <thead>
                                <tr style={{ borderBottom: '1px solid #E2E8F0', backgroundColor: '#F8FAFC' }}>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>ID / Deal</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Reporter</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Reason</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Description</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Status</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {reports.map((report) => (
                                    <tr key={report.id} style={{ borderBottom: '1px solid #F1F5F9' }}>
                                        <td style={{ padding: '14px 16px', verticalAlign: 'top' }}>
                                            <Text fontWeight="800" fontSize="sm" color="#0A1128">
                                                #{report.id}
                                            </Text>
                                            <Text fontSize="xs" color="gray.500">
                                                Deal: {report.deal?.title || report.deal_id ? `#${report.deal_id}` : 'General'}
                                            </Text>
                                        </td>
                                        <td style={{ padding: '14px 16px', verticalAlign: 'top' }}>
                                            <Text fontWeight="700" fontSize="sm" color="gray.800">
                                                {report.reporter?.name || report.user?.name || 'User'}
                                            </Text>
                                            <Text fontSize="xs" color="gray.500">
                                                {report.reporter?.email || report.user?.email || 'N/A'}
                                            </Text>
                                        </td>
                                        <td style={{ padding: '14px 16px', verticalAlign: 'top' }}>
                                            <Badge
                                                bg="#FEF3C7"
                                                color="#92400E"
                                                fontSize="xs"
                                                px={2}
                                                py={0.5}
                                                borderRadius="md"
                                                fontWeight="700"
                                            >
                                                {report.reason || 'Contract Violation'}
                                            </Badge>
                                        </td>
                                        <td style={{ padding: '14px 16px', verticalAlign: 'top', maxWidth: '320px' }}>
                                            <Text fontSize="xs" color="gray.700" noOfLines={2}>
                                                {report.description || report.details || 'No details provided.'}
                                            </Text>
                                        </td>
                                        <td style={{ padding: '14px 16px', verticalAlign: 'top' }}>
                                            <Badge
                                                bg={
                                                    report.status === 'resolved'
                                                        ? '#DEF7EC'
                                                        : report.status === 'dismissed'
                                                        ? '#F3F4F6'
                                                        : '#FEE2E2'
                                                }
                                                color={
                                                    report.status === 'resolved'
                                                        ? '#03543F'
                                                        : report.status === 'dismissed'
                                                        ? '#4B5563'
                                                        : '#991B1B'
                                                }
                                                fontSize="xs"
                                                px={2}
                                                py={0.5}
                                                borderRadius="md"
                                                fontWeight="700"
                                            >
                                                {report.status || 'Pending'}
                                            </Badge>
                                        </td>
                                        <td style={{ padding: '14px 16px', verticalAlign: 'top' }}>
                                            {report.status !== 'resolved' ? (
                                                <Button
                                                    size="xs"
                                                    bg="#1769FF"
                                                    color="white"
                                                    _hover={{ bg: '#0D52D6' }}
                                                    borderRadius="md"
                                                    onClick={() => setResolvingReport(report)}
                                                >
                                                    Resolve
                                                </Button>
                                            ) : (
                                                <Text fontSize="xs" color="gray.400" fontWeight="600">
                                                    Resolved
                                                </Text>
                                            )}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </Box>
                )}
            </DashboardCard>

            {/* Modal for resolving report */}
            {resolvingReport && (
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
                    <Box
                        bg="white"
                        borderRadius="2xl"
                        maxW="540px"
                        w="100%"
                        p={6}
                        boxShadow="2xl"
                    >
                        <Flex justify="space-between" align="center" mb={4}>
                            <Heading size="md" color="#0A1128">
                                Resolve Report #{resolvingReport.id}
                            </Heading>
                            <Button
                                size="sm"
                                variant="ghost"
                                onClick={() => setResolvingReport(null)}
                            >
                                ✕
                            </Button>
                        </Flex>

                        <Text fontSize="sm" color="gray.600" mb={4}>
                            <strong>Reporter:</strong> {resolvingReport.reporter?.name || 'User'} &bull; <strong>Reason:</strong> {resolvingReport.reason}
                        </Text>

                        <Box bg="#F8FAFC" p={3} borderRadius="lg" mb={4} border="1px solid #E2E8F0">
                            <Text fontSize="xs" fontWeight="700" color="gray.500" mb={1}>REPORTER COMPLAINT:</Text>
                            <Text fontSize="sm" color="gray.800">
                                {resolvingReport.description || resolvingReport.details || 'No complaint details.'}
                            </Text>
                        </Box>

                        <form onSubmit={handleResolve}>
                            <Box mb={4}>
                                <Text fontSize="xs" fontWeight="700" color="gray.700" mb={1}>
                                    Administrative Resolution Verdict &amp; Notes *
                                </Text>
                                <Textarea
                                    rows={4}
                                    placeholder="Explain the settlement decision, compensation adjustments, or warning issued..."
                                    value={resolutionNote}
                                    onChange={(e) => setResolutionNote(e.target.value)}
                                    borderRadius="lg"
                                    borderColor="gray.300"
                                    fontSize="sm"
                                    required
                                />
                            </Box>

                            <Flex justify="flex-end" gap={3}>
                                <Button
                                    size="sm"
                                    variant="outline"
                                    onClick={() => setResolvingReport(null)}
                                    borderRadius="lg"
                                >
                                    Cancel
                                </Button>
                                <Button
                                    size="sm"
                                    bg="#10B981"
                                    color="white"
                                    _hover={{ bg: '#059669' }}
                                    type="submit"
                                    isLoading={submitting}
                                    borderRadius="lg"
                                >
                                    Confirm Resolution
                                </Button>
                            </Flex>
                        </form>
                    </Box>
                </Box>
            )}
        </VStack>
    );
};

export default ReportsManagementModule;
