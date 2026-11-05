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
    Spinner,
} from '@chakra-ui/react';
import { adminApi } from '../../../services/adminApi';
import { DashboardCard } from '../../../components/common/DashboardCard';

export const LedgerAuditModule = () => {
    const [transactions, setTransactions] = useState([]);
    const [loading, setLoading] = useState(true);
    const [search, setSearch] = useState('');
    const [typeFilter, setTypeFilter] = useState('all');

    const fetchLedger = async () => {
        setLoading(true);
        try {
            const data = await adminApi.getLedger();
            setTransactions(Array.isArray(data) ? data : []);
        } catch (err) {
            console.error('Failed to load ledger:', err);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchLedger();
    }, []);

    const filtered = transactions.filter((t) => {
        const matchesSearch =
            t.id?.toLowerCase().includes(search.toLowerCase()) ||
            t.user?.toLowerCase().includes(search.toLowerCase()) ||
            t.description?.toLowerCase().includes(search.toLowerCase());
        const matchesType = typeFilter === 'all' || t.type?.toLowerCase() === typeFilter.toLowerCase();
        return matchesSearch && matchesType;
    });

    const totalVolume = transactions.reduce((acc, curr) => acc + Math.abs(Number(curr.amount || 0)), 0);

    return (
        <VStack gap={6} align="stretch" pb={12}>
            <DashboardCard
                title="Master Financial Ledger & Audit Log"
                subtitle="Double-entry immutable record of all deposits, escrow locks, release payouts, and system fees"
                headerAction={
                    <HStack gap={2}>
                        <Button size="xs" variant="outline" onClick={fetchLedger} borderRadius="md">
                            Sync Ledger
                        </Button>
                    </HStack>
                }
            >
                <Flex
                    direction={{ base: 'column', md: 'row' }}
                    justify="space-between"
                    align={{ base: 'stretch', md: 'center' }}
                    gap={3}
                    mb={4}
                >
                    <Box maxW="360px">
                        <Input
                            placeholder="Filter by TXN reference or user..."
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            fontSize="sm"
                            borderRadius="lg"
                            borderColor="gray.300"
                        />
                    </Box>

                    <HStack gap={1} overflowX="auto">
                        {['all', 'deposit', 'withdrawal', 'escrow_hold', 'escrow_release', 'fee'].map((type) => (
                            <Button
                                key={type}
                                size="xs"
                                variant={typeFilter === type ? 'solid' : 'outline'}
                                bg={typeFilter === type ? '#0A1128' : 'transparent'}
                                color={typeFilter === type ? 'white' : 'gray.600'}
                                borderColor="gray.300"
                                borderRadius="md"
                                textTransform="capitalize"
                                onClick={() => setTypeFilter(type)}
                            >
                                {type.replace('_', ' ')}
                            </Button>
                        ))}
                    </HStack>
                </Flex>

                {loading ? (
                    <Flex justify="center" align="center" py={12}>
                        <Spinner size="lg" color="#1769FF" />
                    </Flex>
                ) : filtered.length === 0 ? (
                    <Box textAlign="center" py={12} color="gray.500">
                        <Text fontSize="lg" fontWeight="700">No Ledger Transactions</Text>
                        <Text fontSize="sm" mt={1}>Transactions will be logged automatically as escrow events occur.</Text>
                    </Box>
                ) : (
                    <Box overflowX="auto">
                        <table style={{ width: '100%', borderCollapse: 'collapse', textAlign: 'left' }}>
                            <thead>
                                <tr style={{ borderBottom: '1px solid #E2E8F0', backgroundColor: '#F8FAFC' }}>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Reference</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Account / Counterparty</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Type</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Amount (BDT)</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Resulting Balance</th>
                                    <th style={{ padding: '12px 16px', fontSize: '11px', fontWeight: '800', color: '#64748B', textTransform: 'uppercase' }}>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                {filtered.map((txn, idx) => {
                                    const isCredit = Number(txn.amount) >= 0;
                                    return (
                                        <tr key={txn.id || idx} style={{ borderBottom: '1px solid #F1F5F9' }}>
                                            <td style={{ padding: '14px 16px', fontFamily: 'mono', fontWeight: '700', fontSize: 'xs', color: '#1769FF' }}>
                                                {txn.id}
                                            </td>
                                            <td style={{ padding: '14px 16px', fontSize: 'xs', color: 'gray.800', fontWeight: '600' }}>
                                                {txn.user}
                                                <Text fontSize="2xs" color="gray.500" fontWeight="normal">
                                                    {txn.description}
                                                </Text>
                                            </td>
                                            <td style={{ padding: '14px 16px' }}>
                                                <Badge
                                                    bg={
                                                        txn.type === 'deposit'
                                                            ? '#DEF7EC'
                                                            : txn.type === 'withdrawal'
                                                            ? '#FEE2E2'
                                                            : '#EFF6FF'
                                                    }
                                                    color={
                                                        txn.type === 'deposit'
                                                            ? '#03543F'
                                                            : txn.type === 'withdrawal'
                                                            ? '#991B1B'
                                                            : '#1E40AF'
                                                    }
                                                    fontSize="xs"
                                                    px={2}
                                                    py={0.5}
                                                    borderRadius="md"
                                                    textTransform="uppercase"
                                                    fontWeight="700"
                                                >
                                                    {txn.type}
                                                </Badge>
                                            </td>
                                            <td style={{ padding: '14px 16px', fontWeight: '800', color: isCredit ? '#059669' : '#DC2626', fontSize: 'sm' }}>
                                                {isCredit ? '+' : ''}৳ {Number(txn.amount).toLocaleString(undefined, { minimumFractionDigits: 2 })}
                                            </td>
                                            <td style={{ padding: '14px 16px', fontFamily: 'mono', fontSize: 'xs', color: 'gray.700' }}>
                                                ৳ {Number(txn.balance || 0).toLocaleString(undefined, { minimumFractionDigits: 2 })}
                                            </td>
                                            <td style={{ padding: '14px 16px', fontSize: 'xs', color: 'gray.500' }}>
                                                {txn.date}
                                            </td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                    </Box>
                )}
            </DashboardCard>
        </VStack>
    );
};

export default LedgerAuditModule;
