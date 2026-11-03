import React, { useState } from 'react';
import { Box, Flex, Container } from '@chakra-ui/react';
import { Sidebar } from '../components/navigation/Sidebar';
import { Topbar } from '../components/navigation/Topbar';

export const DashboardLayout = ({
    portal = 'user',
    activeNav = 'Dashboard',
    onNavSelect,
    children,
}) => {
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

    return (
        <Flex minH="100vh" bg="#F8FAFC" position="relative">
            {/* Desktop Sidebar */}
            <Box display={{ base: 'none', md: 'block' }}>
                <Sidebar
                    portal={portal}
                    activeNav={activeNav}
                    onNavSelect={onNavSelect}
                    isMobile={false}
                />
            </Box>

            {/* Mobile Sidebar Overlay */}
            <Sidebar
                portal={portal}
                activeNav={activeNav}
                onNavSelect={onNavSelect}
                isOpen={mobileMenuOpen}
                onClose={() => setMobileMenuOpen(false)}
                isMobile={true}
            />

            {/* Main Application Area */}
            <Flex direction="column" flex="1" minW={0} overflow="hidden">
                <Topbar
                    portal={portal}
                    activeNav={activeNav}
                    onMenuToggle={() => setMobileMenuOpen(!mobileMenuOpen)}
                    onNavSelect={onNavSelect}
                />

                <Box
                    as="main"
                    flex="1"
                    p={{ base: 4, sm: 6, lg: 8 }}
                    overflowY="auto"
                >
                    <Container maxW="1400px" p={0}>
                        {children}
                    </Container>
                </Box>
            </Flex>
        </Flex>
    );
};

export default DashboardLayout;

