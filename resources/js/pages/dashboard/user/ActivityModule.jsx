import React, { useState, useEffect } from 'react';
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
    Spinner,
} from '@chakra-ui/react';
import { useAuth } from '../../../hooks/useAuth';
import { financeApi, dealsApi } from '../../../services/dashboardApi';
import authService from '../../../services/auth';
import { PageHeader } from '../../../components/common/PageHeader';
import { DashboardCard } from '../../../components/common/DashboardCard';
import { StatCard } from '../../../components/common/StatCard';
import { EmptyState } from '../../../components/common/EmptyState';
import {
    WalletIcon,
    DepositIcon,
    WithdrawalIcon,
    ArrowUpRightIcon,
    ArrowDownLeftIcon,
} from '../../../components/common/Icons';

export const ActivityModule = () => {
    const { user, currency, refreshUser } = useAuth();
    const [transactions, setTransactions] = useState([]);
    const [loading, setLoading] = useState(true);
    const [filter, setFilter] = useState('all'); // 'all' | 'credit' | 'debit'

    const loadLedger = async () => {
        try {
            const [trxRes, depRes, withRes, dealRes] = await Promise.all([
                financeApi.getTransactions().catch(() => ({ transactions: [] })),
                financeApi.getDeposits().catch(() => ({ deposits: [] })),
                financeApi.getWithdrawals().catch(() => ({ withdrawals: [] })),
                dealsApi.getAll().catch(() => ({ deals: [] })),
            ]);

            const aggregated = [];

            // 1. Direct database transactions if any
            if (trxRes.success && Array.isArray(trxRes.transactions) && trxRes.transactions.length > 0) {
                trxRes.transactions.forEach((t) => {
                    aggregated.push({
                        id: `TRX-${t.id}`,
                        trxId: t.transaction_id || `TRX-${t.id}`,
                        date: t.created_at,
                        description: t.description || t.type,
                        type: t.type === 'credit' ? 'credit' : 'debit',
                        amount: parseFloat(t.amount || 0),
                        balanceBefore: t.balance_before,
                        balanceAfter: t.balance_after,
                        status: 'Completed',
                    });
                });
            } else {
                // 2. Synthesize unified ledger from deposits, withdrawals, and escrow deals
                (depRes.deposits || []).forEach((d) => {
                    aggregated.push({
                        id: `DEP-${d.id}`,
                        trxId: d.transaction_id || `DEP-${d.id}`,
                        date: d.created_at,
                        description: `Deposit via ${d.gateway_title || d.gateway}`,
                        type: 'credit',
                        amount: parseFloat(d.amount || 0),
                        status: d.status,
                    });
                });

                (withRes.withdrawals || []).forEach((w) => {
                    aggregated.push({
                        id: `WTH-${w.id}`,
                        trxId: w.reference_id || `WTH-${w.id}`,
                        date: w.created_at,
                        description: `Payout via ${w.method_name || 'Bank/Mobile'}`,
                        type: 'debit',
                        amount: parseFloat(w.amount || 0),
                        status: w.status,
                    });
                });

                (dealRes.deals || []).forEach((dl) => {
                    const isBuyer = dl.initiator_role === 'buyer'
                        ? dl.initiator_id === user?.id
                        : dl.counterparty_id === user?.id;

                    if (isBuyer) {
                        aggregated.push({
                            id: `ESC-LCK-${dl.id}`,
                            trxId: `ESC-${dl.deal_id || dl.id}`,
                            date: dl.created_at,
                            description: `Escrow Fund Locked: ${dl.title}`,
                            type: 'debit',
                            amount: parseFloat(dl.buyer_pays || dl.amount || 0),
                            status: dl.status === 'awaiting_acceptance' ? 'Pending Acceptance' : 'Escrow Secured',
                        });
                    }

                    if (['completed', 'released'].includes(dl.status)) {
                        aggregated.push({
                            id: `ESC-REL-${dl.id}`,
                            trxId: `REL-${dl.deal_id || dl.id}`,
                            date: dl.updated_at || dl.created_at,
                            description: `Escrow Payment Released: ${dl.title}`,
                            type: isBuyer ? 'debit' : 'credit',
                            amount: parseFloat(dl.seller_gets || dl.amount || 0),
                            status: 'Completed',
                        });
                    }
                });
            }

            // Sort chronologically newest first
            aggregated.sort((a, b) => new Date(b.date) - new Date(a.date));
            setTransactions(aggregated);
        } catch (e) {
            console.error('Error loading ledger:', e);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        loadLedger();
        refreshUser();
    }, []);

    const filtered = transactions.filter((t) => {
        if (filter === 'credit') return t.type === 'credit';
        if (filter === 'debit') return t.type === 'debit';
        return true;
    });

    const walletBalance = parseFloat(user?.wallet_balance || 0);

    return (
        <Box>
            <PageHeader
                title="Financial Ledger & Audit Trail"
                subtitle="Immutable chronological history of all wallet debits, credits, and escrow transactions."
            />

            {/* Filter buttons */}
            <Flex gap={2} mb={6}>
                <Button
                    size="xs"
                    variant={filter === 'all' ? 'solid' : 'outline'}
                    bg={filter === 'all' ? '#002147' : 'white'}
                    color={filter === 'all' ? 'white' : 'gray.700'}
                    borderRadius="lg"
                    px={3}
                    onClick={() => setFilter('all')}
                >
                    All Entries ({transactions.length})
                </Button>
                <Button
                    size="xs"
                    variant={filter === 'credit' ? 'solid' : 'outline'}
                    bg={filter === 'credit' ? '#006A4E' : 'white'}
                    color={filter === 'credit' ? 'white' : 'gray.700'}
                    borderRadius="lg"
                    px={3}
                    onClick={() => setFilter('credit')}
                >
                    Credits Only
                </Button>
                <Button
                    size="xs"
                    variant={filter === 'debit' ? 'solid' : 'outline'}
                    bg={filter === 'debit' ? '#DC2626' : 'white'}
                    color={filter === 'debit' ? 'white' : 'gray.700'}
                    borderRadius="lg"
                    px={3}
                    onClick={() => setFilter('debit')}
                >
                    Debits / Locks
                </Button>
            </Flex>

            {loading ? (
                <Flex justify="center" align="center" minH="240px">
                    <Spinner size="lg" color="#1769FF" />
                </Flex>
            ) : filtered.length === 0 ? (
                <EmptyState
                    icon={WalletIcon}
                    title="No financial records found"
                    description="You do not have any transaction entries matching your filter."
                    minH="200px"
                />
            ) : (
                <DashboardCard title="Ledger Transactions" subtitle="Chronological accounting records.">
                    <Box overflowX="auto">
                        <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: '13px' }}>
                            <thead>
                                <tr style={{ borderBottom: '1px solid #E2E8F0', textAlign: 'left', color: '#64748B' }}>
                                    <th style={{ padding: '10px 12px' }}>Timestamp</th>
                                    <th style={{ padding: '10px 12px' }}>Reference</th>
                                    <th style={{ padding: '10px 12px' }}>Description</th>
                                    <th style={{ padding: '10px 12px' }}>Type</th>
                                    <th style={{ padding: '10px 12px' }}>Amount</th>
                                    <th style={{ padding: '10px 12px' }}>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                {filtered.map((t) => {
                                    const isCredit = t.type === 'credit';
                                    return (
                                        <tr key={t.id} style={{ borderBottom: '1px solid #F1F5F9' }}>
                                            <td style={{ padding: '12px', color: '#64748B', fontSize: '12px', whiteSpace: 'nowrap' }}>
                                                {new Date(t.date).toLocaleString([], { dateStyle: 'short', timeStyle: 'short' })}
                                            </td>
                                            <td style={{ padding: '12px', fontFamily: 'mono', fontWeight: '700' }}>
                                                {t.trxId}
                                            </td>
                                            <td style={{ padding: '12px', color: '#0A1128', fontWeight: '600' }}>
                                                {t.description}
                                            </td>
                                            <td style={{ padding: '12px' }}>
                                                <Badge
                                                    colorPalette={isCredit ? 'green' : 'orange'}
                                                    variant="subtle"
                                                    fontSize="2xs"
                                                    px={2}
                                                    borderRadius="md"
                                                >
                                                    {isCredit ? 'CREDIT' : 'DEBIT'}
                                                </Badge>
                                            </td>
                                            <td
                                                style={{
                                                    padding: '12px',
                                                    fontWeight: '800',
                                                    color: isCredit ? '#006A4E' : '#DC2626',
                                                    whiteSpace: 'nowrap',
                                                }}
                                            >
                                                {isCredit ? '+' : '-'} {authService.formatCurrency(t.amount, currency)}
                                            </td>
                                            <td style={{ padding: '12px' }}>
                                                <Badge
                                                    colorPalette={t.status?.toLowerCase().includes('reject') ? 'red' : t.status?.toLowerCase().includes('pend') ? 'yellow' : 'green'}
                                                    variant="subtle"
                                                    fontSize="2xs"
                                                    px={2}
                                                    borderRadius="md"
                                                >
                                                    {t.status || 'Success'}
                                                </Badge>
                                            </td>
                                        </tr>
                                    );
                                })}
                            </tbody>
                        </table>
                    </Box>
                </DashboardCard>
            )}
        </Box>
    );
};

export default ActivityModule;

