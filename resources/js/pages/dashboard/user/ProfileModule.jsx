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
import { userProfileApi } from '../../../services/dashboardApi';
import authService from '../../../services/auth';
import { PageHeader } from '../../../components/common/PageHeader';
import { DashboardCard } from '../../../components/common/DashboardCard';
import { UserAvatar } from '../../../components/common/UserAvatar';
import { EmptyState } from '../../../components/common/EmptyState';
import { toaster } from '../../../components/feedback/toaster';
import {
    UsersIcon,
    SecurityIcon,
    CheckCircleIcon,
    AlertCircleIcon,
} from '../../../components/common/Icons';

export const ProfileModule = () => {
    const { user, refreshUser } = useAuth();
    const [activeTab, setActiveTab] = useState('profile'); // 'profile' | 'kyc' | 'password' | 'reviews'
    const [saving, setSaving] = useState(false);

    // Profile Form
    const [profile, setProfile] = useState({
        name: user?.name || '',
        username: user?.username || '',
        phone: user?.phone || '',
        headline: user?.headline || '',
        bio: user?.bio || '',
        address: user?.address || '',
        skills: Array.isArray(user?.skills) ? user.skills.join(', ') : user?.skills || '',
    });

    // Password Form
    const [passwordData, setPasswordData] = useState({
        current_password: '',
        new_password: '',
        new_password_confirmation: '',
    });
    const [changingPassword, setChangingPassword] = useState(false);

    // KYC Form
    const [kycForm, setKycForm] = useState({
        id_type: 'National ID (NID)',
        id_number: user?.nid_number || '',
        notes: '',
    });
    const [submittingKyc, setSubmittingKyc] = useState(false);

    // Reviews
    const [reviews, setReviews] = useState([]);
    const [loadingReviews, setLoadingReviews] = useState(false);

    useEffect(() => {
        if (user) {
            setProfile({
                name: user.name || '',
                username: user.username || '',
                phone: user.phone || '',
                headline: user.headline || '',
                bio: user.bio || '',
                address: user.address || '',
                skills: Array.isArray(user.skills) ? user.skills.join(', ') : user.skills || '',
            });
        }
    }, [user]);

    useEffect(() => {
        if (activeTab === 'reviews' && user?.id) {
            setLoadingReviews(true);
            userProfileApi
                .getUserReviews(user.id)
                .then((res) => {
                    if (res.success) setReviews(res.reviews || []);
                })
                .catch((e) => console.error(e))
                .finally(() => setLoadingReviews(false));
        }
    }, [activeTab, user?.id]);

    const handleProfileSubmit = async (e) => {
        e.preventDefault();
        setSaving(true);
        try {
            const data = await userProfileApi.updateProfile(profile);
            if (data.success) {
                toaster.create({ title: 'Profile Updated', description: 'Your public profile information has been saved.', type: 'success' });
                refreshUser();
            } else {
                toaster.create({ title: 'Update Failed', description: data.message || 'Could not update profile.', type: 'error' });
            }
        } catch (err) {
            toaster.create({ title: 'Error', description: err?.response?.data?.message || err.message, type: 'error' });
        } finally {
            setSaving(false);
        }
    };

    const handlePasswordSubmit = async (e) => {
        e.preventDefault();
        if (passwordData.new_password !== passwordData.new_password_confirmation) {
            toaster.create({ title: 'Mismatch', description: 'New password confirmation does not match.', type: 'error' });
            return;
        }

        setChangingPassword(true);
        try {
            const data = await userProfileApi.changePassword(passwordData);
            if (data.success) {
                toaster.create({ title: 'Password Changed', description: 'Your account password has been updated.', type: 'success' });
                setPasswordData({ current_password: '', new_password: '', new_password_confirmation: '' });
            } else {
                toaster.create({ title: 'Error', description: data.message || 'Could not update password.', type: 'error' });
            }
        } catch (err) {
            toaster.create({ title: 'Error', description: err?.response?.data?.message || err.message, type: 'error' });
        } finally {
            setChangingPassword(false);
        }
    };

    const handleKycSubmit = async (e) => {
        e.preventDefault();
        if (!kycForm.id_number.trim()) {
            toaster.create({ title: 'Validation Error', description: 'Please provide your document identification number.', type: 'error' });
            return;
        }

        setSubmittingKyc(true);
        try {
            const data = await userProfileApi.requestVerification(kycForm);
            if (data.success) {
                toaster.create({
                    title: 'Verification Request Submitted',
                    description: 'Our compliance officers will review your documents.',
                    type: 'success',
                });
                refreshUser();
            }
        } catch (err) {
            toaster.create({ title: 'Error', description: err.message, type: 'error' });
        } finally {
            setSubmittingKyc(false);
        }
    };

    const isVerified = user?.verification_status === 'VERIFIED';
    const isPending = user?.verification_status === 'PENDING';

    return (
        <Box>
            <PageHeader
                title="Account Settings & Verification"
                subtitle="Manage your personal details, National KYC compliance, and security."
            />

            {/* Profile Overview Card */}
            <DashboardCard mb={6}>
                <Flex direction={{ base: 'column', sm: 'row' }} align="center" gap={4}>
                    <UserAvatar name={user?.name || user?.username} size="lg" isVerified={isVerified} />
                    <Box textAlign={{ base: 'center', sm: 'left' }} flex={1}>
                        <Flex align="center" gap={2} justify={{ base: 'center', sm: 'flex-start' }}>
                            <Heading size="md" color="#0A1128">{user?.name || user?.username}</Heading>
                            <Badge
                                colorPalette={isVerified ? 'green' : isPending ? 'yellow' : 'orange'}
                                variant="subtle"
                                fontSize="2xs"
                                px={2}
                                borderRadius="md"
                            >
                                {user?.verification_status || 'NOT_VERIFIED'}
                            </Badge>
                        </Flex>
                        <Text fontSize="xs" color="gray.500" mt={0.5}>
                            @{user?.username} • {user?.email} • Member since {new Date(user?.created_at || Date.now()).toLocaleDateString()}
                        </Text>
                    </Box>
                </Flex>
            </DashboardCard>

            {/* Navigation Tabs */}
            <Flex gap={2} mb={6} wrap="wrap">
                {[
                    { id: 'profile', label: 'Personal Information' },
                    { id: 'kyc', label: 'KYC Verification' },
                    { id: 'password', label: 'Change Password' },
                    { id: 'reviews', label: 'Customer Reviews' },
                ].map((t) => (
                    <Button
                        key={t.id}
                        size="xs"
                        variant={activeTab === t.id ? 'solid' : 'outline'}
                        bg={activeTab === t.id ? '#002147' : 'white'}
                        color={activeTab === t.id ? 'white' : 'gray.700'}
                        borderRadius="lg"
                        px={3}
                        onClick={() => setActiveTab(t.id)}
                    >
                        {t.label}
                    </Button>
                ))}
            </Flex>

            {/* Tab 1: Profile Info */}
            {activeTab === 'profile' && (
                <DashboardCard title="Edit Personal Details" subtitle="Update your profile information visible to counterparties.">
                    <form onSubmit={handleProfileSubmit}>
                        <VStack gap={4} align="stretch">
                            <Grid templateColumns={{ base: '1fr', sm: '1fr 1fr' }} gap={4}>
                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="#0A1128" mb={1}>Full Name *</Text>
                                    <input
                                        type="text"
                                        value={profile.name}
                                        onChange={(e) => setProfile({ ...profile, name: e.target.value })}
                                        style={{ width: '100%', padding: '9px 12px', borderRadius: '8px', border: '1px solid #CBD5E1', fontSize: '13px' }}
                                        required
                                    />
                                </Box>
                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="#0A1128" mb={1}>Phone Number</Text>
                                    <input
                                        type="text"
                                        value={profile.phone}
                                        onChange={(e) => setProfile({ ...profile, phone: e.target.value })}
                                        placeholder="017xxxxxxxx"
                                        style={{ width: '100%', padding: '9px 12px', borderRadius: '8px', border: '1px solid #CBD5E1', fontSize: '13px' }}
                                    />
                                </Box>
                            </Grid>

                            <Box>
                                <Text fontSize="xs" fontWeight="700" color="#0A1128" mb={1}>Professional Headline</Text>
                                <input
                                    type="text"
                                    value={profile.headline}
                                    onChange={(e) => setProfile({ ...profile, headline: e.target.value })}
                                    placeholder="e.g. Senior Software Engineer / Verified Escrow Merchant"
                                    style={{ width: '100%', padding: '9px 12px', borderRadius: '8px', border: '1px solid #CBD5E1', fontSize: '13px' }}
                                />
                            </Box>

                            <Box>
                                <Text fontSize="xs" fontWeight="700" color="#0A1128" mb={1}>Bio / Overview</Text>
                                <textarea
                                    rows={3}
                                    value={profile.bio}
                                    onChange={(e) => setProfile({ ...profile, bio: e.target.value })}
                                    placeholder="Tell counterparties about your experience and background..."
                                    style={{ width: '100%', padding: '9px 12px', borderRadius: '8px', border: '1px solid #CBD5E1', fontSize: '13px', fontFamily: 'inherit' }}
                                />
                            </Box>

                            <Grid templateColumns={{ base: '1fr', sm: '1fr 1fr' }} gap={4}>
                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="#0A1128" mb={1}>Address / Location</Text>
                                    <input
                                        type="text"
                                        value={profile.address}
                                        onChange={(e) => setProfile({ ...profile, address: e.target.value })}
                                        placeholder="Dhaka, Bangladesh"
                                        style={{ width: '100%', padding: '9px 12px', borderRadius: '8px', border: '1px solid #CBD5E1', fontSize: '13px' }}
                                    />
                                </Box>
                                <Box>
                                    <Text fontSize="xs" fontWeight="700" color="#0A1128" mb={1}>Skills / Tags (Comma separated)</Text>
                                    <input
                                        type="text"
                                        value={profile.skills}
                                        onChange={(e) => setProfile({ ...profile, skills: e.target.value })}
                                        placeholder="Web Design, Python, React, Translation"
                                        style={{ width: '100%', padding: '9px 12px', borderRadius: '8px', border: '1px solid #CBD5E1', fontSize: '13px' }}
                                    />
                                </Box>
                            </Grid>

                            <Button type="submit" size="md" bg="#002147" color="white" loading={saving} alignSelf="flex-start" borderRadius="xl">
                                Save Profile Changes
                            </Button>
                        </VStack>
                    </form>
                </DashboardCard>
            )}

            {/* Tab 2: KYC Verification */}
            {activeTab === 'kyc' && (
                <DashboardCard title="National Identity Verification (KYC)" subtitle="Government-issued credentials audit.">
                    <VStack gap={4} align="stretch">
                        <Box p={4} bg={isVerified ? '#ECFDF5' : '#FEF3C7'} borderRadius="xl" borderWidth="1px" borderColor={isVerified ? '#A7F3D0' : '#FDE68A'}>
                            <HStack gap={3}>
                                <Flex w={10} h={10} borderRadius="full" bg={isVerified ? '#10B981' : '#D97706'} color="white" align="center" justify="center">
                                    {isVerified ? <CheckCircleIcon size={20} /> : <SecurityIcon size={20} />}
                                </Flex>
                                <Box>
                                    <Text fontSize="xs" fontWeight="800" color="#0A1128">
                                        Verification Status: {user?.verification_status || 'NOT_VERIFIED'}
                                    </Text>
                                    <Text fontSize="2xs" color="gray.600">
                                        {isVerified
                                            ? 'Your National Identity has been verified by MigratePay compliance. You have unlimited escrow limits.'
                                            : isPending
                                            ? 'Your KYC documents are currently in the audit queue. Staff will verify them shortly.'
                                            : 'Please submit your National ID (NID) or Passport to unlock high-volume escrow.'}
                                    </Text>
                                </Box>
                            </HStack>
                        </Box>

                        {!isVerified && (
                            <form onSubmit={handleKycSubmit}>
                                <VStack gap={3} align="stretch">
                                    <Grid templateColumns={{ base: '1fr', sm: '1fr 1fr' }} gap={4}>
                                        <Box>
                                            <Text fontSize="xs" fontWeight="700" color="#0A1128" mb={1}>Document Type</Text>
                                            <select
                                                value={kycForm.id_type}
                                                onChange={(e) => setKycForm({ ...kycForm, id_type: e.target.value })}
                                                style={{ width: '100%', padding: '9px 12px', borderRadius: '8px', border: '1px solid #CBD5E1', fontSize: '13px' }}
                                            >
                                                <option value="National ID (NID)">National ID (Smart NID)</option>
                                                <option value="Passport">Bangladeshi Passport</option>
                                                <option value="Driving License">Driving License</option>
                                            </select>
                                        </Box>
                                        <Box>
                                            <Text fontSize="xs" fontWeight="700" color="#0A1128" mb={1}>Document Identification Number *</Text>
                                            <input
                                                type="text"
                                                value={kycForm.id_number}
                                                onChange={(e) => setKycForm({ ...kycForm, id_number: e.target.value })}
                                                placeholder="e.g. 19901234567890"
                                                style={{ width: '100%', padding: '9px 12px', borderRadius: '8px', border: '1px solid #CBD5E1', fontSize: '13px' }}
                                                required
                                            />
                                        </Box>
                                    </Grid>

                                    <Button type="submit" size="md" bg="#006A4E" color="white" loading={submittingKyc} alignSelf="flex-start" borderRadius="xl">
                                        Submit for Verification
                                    </Button>
                                </VStack>
                            </form>
                        )}
                    </VStack>
                </DashboardCard>
            )}

            {/* Tab 3: Password */}
            {activeTab === 'password' && (
                <DashboardCard title="Security & Password" subtitle="Change your login credentials.">
                    <form onSubmit={handlePasswordSubmit}>
                        <VStack gap={3} align="stretch" maxW="480px">
                            <Box>
                                <Text fontSize="xs" fontWeight="700" color="#0A1128" mb={1}>Current Password *</Text>
                                <input
                                    type="password"
                                    value={passwordData.current_password}
                                    onChange={(e) => setPasswordData({ ...passwordData, current_password: e.target.value })}
                                    style={{ width: '100%', padding: '9px 12px', borderRadius: '8px', border: '1px solid #CBD5E1', fontSize: '13px' }}
                                    required
                                />
                            </Box>
                            <Box>
                                <Text fontSize="xs" fontWeight="700" color="#0A1128" mb={1}>New Password *</Text>
                                <input
                                    type="password"
                                    value={passwordData.new_password}
                                    onChange={(e) => setPasswordData({ ...passwordData, new_password: e.target.value })}
                                    style={{ width: '100%', padding: '9px 12px', borderRadius: '8px', border: '1px solid #CBD5E1', fontSize: '13px' }}
                                    required
                                />
                            </Box>
                            <Box>
                                <Text fontSize="xs" fontWeight="700" color="#0A1128" mb={1}>Confirm New Password *</Text>
                                <input
                                    type="password"
                                    value={passwordData.new_password_confirmation}
                                    onChange={(e) => setPasswordData({ ...passwordData, new_password_confirmation: e.target.value })}
                                    style={{ width: '100%', padding: '9px 12px', borderRadius: '8px', border: '1px solid #CBD5E1', fontSize: '13px' }}
                                    required
                                />
                            </Box>
                            <Button type="submit" size="md" bg="#002147" color="white" loading={changingPassword} alignSelf="flex-start" borderRadius="xl" mt={2}>
                                Update Password
                            </Button>
                        </VStack>
                    </form>
                </DashboardCard>
            )}

            {/* Tab 4: Reviews */}
            {activeTab === 'reviews' && (
                <DashboardCard title="Counterparty Reviews" subtitle="Feedback received on completed escrow agreements.">
                    {loadingReviews ? (
                        <Flex justify="center" p={8}><Spinner size="md" color="#1769FF" /></Flex>
                    ) : reviews.length === 0 ? (
                        <EmptyState
                            icon={UsersIcon}
                            title="No reviews yet"
                            description="Complete deals to receive ratings and reviews from your trading partners."
                            minH="180px"
                        />
                    ) : (
                        <VStack gap={3} align="stretch">
                            {reviews.map((r) => (
                                <Box key={r.id} p={3} borderRadius="xl" borderWidth="1px" borderColor="gray.200" bg="gray.50">
                                    <Flex justify="space-between" align="center" mb={1}>
                                        <Text fontSize="xs" fontWeight="700" color="#0A1128">
                                            {r.reviewer?.name || 'Counterparty'}
                                        </Text>
                                        <Text color="#F59E0B" fontSize="sm">
                                            {'★'.repeat(r.rating || 5)}{'☆'.repeat(5 - (r.rating || 5))}
                                        </Text>
                                    </Flex>
                                    <Text fontSize="xs" color="gray.600">{r.comment || 'Smooth transaction.'}</Text>
                                    <Text fontSize="3xs" color="gray.400" mt={1}>
                                        {new Date(r.created_at).toLocaleDateString()}
                                    </Text>
                                </Box>
                            ))}
                        </VStack>
                    )}
                </DashboardCard>
            )}
        </Box>
    );
};

export default ProfileModule;

