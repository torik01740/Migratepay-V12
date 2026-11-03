import React from 'react';
import { ChakraProvider } from '@chakra-ui/react';
import { system } from '../../theme';
import { AppToaster } from '../../components/feedback/toaster';
import { AuthProvider } from '../../hooks/useAuth';

export const AppProviders = ({ children, initialUser = null, portal = 'user' }) => {
    return (
        <ChakraProvider value={system}>
            <AuthProvider initialUser={initialUser} portal={portal}>
                {children}
                <AppToaster />
            </AuthProvider>
        </ChakraProvider>
    );
};

export default AppProviders;

