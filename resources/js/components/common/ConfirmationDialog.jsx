import React from 'react';
import { Dialog, Button, Portal, Flex } from '@chakra-ui/react';

export const ConfirmationDialog = ({
    open,
    onOpenChange,
    title = 'Confirm Action',
    description = 'Are you sure you want to proceed with this operation?',
    confirmLabel = 'Confirm',
    cancelLabel = 'Cancel',
    confirmColorPalette = 'blue',
    isLoading = false,
    onConfirm,
    onCancel,
}) => {
    return (
        <Dialog.Root open={open} onOpenChange={onOpenChange}>
            <Portal>
                <Dialog.Backdrop bg="blackAlpha.600" />
                <Dialog.Positioner>
                    <Dialog.Content bg="white" borderRadius="xl" shadow="xl" maxW="460px" p={6}>
                        <Dialog.Header p={0} mb={3}>
                            <Dialog.Title fontSize="lg" fontWeight="800" color="#0A1128">
                                {title}
                            </Dialog.Title>
                        </Dialog.Header>
                        <Dialog.Body p={0} mb={6}>
                            <Dialog.Description fontSize="sm" color="gray.600">
                                {description}
                            </Dialog.Description>
                        </Dialog.Body>
                        <Dialog.Footer p={0}>
                            <Flex justify="flex-end" gap={3} w="100%">
                                <Button
                                    variant="outline"
                                    size="sm"
                                    onClick={onCancel || (() => onOpenChange?.({ open: false }))}
                                    borderRadius="lg"
                                >
                                    {cancelLabel}
                                </Button>
                                <Button
                                    size="sm"
                                    bg="#002147"
                                    color="white"
                                    _hover={{ bg: '#0A1128' }}
                                    loading={isLoading}
                                    onClick={onConfirm}
                                    borderRadius="lg"
                                >
                                    {confirmLabel}
                                </Button>
                            </Flex>
                        </Dialog.Footer>
                    </Dialog.Content>
                </Dialog.Positioner>
            </Portal>
        </Dialog.Root>
    );
};

export default ConfirmationDialog;

