import React from 'react';
import { Flex, Heading, Text, Button } from '@chakra-ui/react';
import { AlertCircleIcon } from './Icons';

export const ErrorState = ({
    title = 'Unable to load data',
    message = 'An error occurred while communicating with the server.',
    onRetry = null,
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
            bg="red.50"
            borderRadius="xl"
            borderWidth="1px"
            borderColor="red.200"
            {...props}
        >
            <Flex
                w={14}
                h={14}
                borderRadius="full"
                bg="white"
                align="center"
                justify="center"
                color="red.500"
                mb={4}
                shadow="xs"
            >
                <AlertCircleIcon size={28} color="#DC2626" />
            </Flex>
            <Heading size="md" color="#991B1B" fontWeight="700" mb={1}>
                {title}
            </Heading>
            <Text fontSize="sm" color="red.700" maxW="400px" mb={onRetry ? 4 : 0}>
                {message}
            </Text>
            {onRetry && (
                <Button
                    size="sm"
                    bg="#DC2626"
                    color="white"
                    _hover={{ bg: '#B91C1C' }}
                    onClick={onRetry}
                    borderRadius="lg"
                    px={4}
                >
                    Retry Request
                </Button>
            )}
        </Flex>
    );
};

export default ErrorState;

