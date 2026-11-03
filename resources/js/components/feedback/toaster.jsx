import React from 'react';
import {
    createToaster,
    Toaster as ChakraToaster,
    ToastRoot,
    ToastTitle,
    ToastDescription,
    ToastIndicator,
    ToastCloseTrigger,
    Stack,
    Portal,
} from '@chakra-ui/react';

export const toaster = createToaster({
    placement: 'bottom-end',
    pauseOnPageIdle: true,
});

export const AppToaster = () => {
    return (
        <Portal>
            <ChakraToaster toaster={toaster} insetInline={{ mdDown: '4' }}>
                {(toast) => (
                    <ToastRoot key={toast.id}>
                        <ToastIndicator />
                        <Stack gap="1" flex="1" maxWidth="100%">
                            {toast.title && <ToastTitle>{toast.title}</ToastTitle>}
                            {toast.description && (
                                <ToastDescription>{toast.description}</ToastDescription>
                            )}
                        </Stack>
                        <ToastCloseTrigger />
                    </ToastRoot>
                )}
            </ChakraToaster>
        </Portal>
    );
};

export default AppToaster;

