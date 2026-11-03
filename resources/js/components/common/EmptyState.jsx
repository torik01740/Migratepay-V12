import React from 'react';
import { Box, Flex, Heading, Text, Button } from '@chakra-ui/react';

export const EmptyState = ({
    icon: IconComponent,
    title = 'No items found',
    description = 'There are no records to display at this moment.',
    actionLabel = null,
    onAction = null,
    minH = '240px',
    ...props
}) => {
    return (
        <Flex
            direction="column"
            align="center"
            justify="center"
            textAlign="center"
            minH={minH}
            p={6}
            bg="white"
            borderRadius="xl"
            borderWidth="1px"
            borderColor="gray.200"
            borderStyle="dashed"
            {...props}
        >
            {IconComponent && (
                <Flex
                    w={14}
                    h={14}
                    borderRadius="full"
                    bg="gray.50"
                    align="center"
                    justify="center"
                    color="gray.400"
                    mb={4}
                >
                    <IconComponent size={28} />
                </Flex>
            )}
            <Heading size="md" color="#0A1128" fontWeight="700" mb={1}>
                {title}
            </Heading>
            <Text fontSize="sm" color="gray.500" maxW="400px" mb={actionLabel ? 4 : 0}>
                {description}
            </Text>
            {actionLabel && onAction && (
                <Button
                    size="sm"
                    bg="#002147"
                    color="white"
                    _hover={{ bg: '#0A1128' }}
                    onClick={onAction}
                    borderRadius="lg"
                    px={4}
                >
                    {actionLabel}
                </Button>
            )}
        </Flex>
    );
};

export default EmptyState;

