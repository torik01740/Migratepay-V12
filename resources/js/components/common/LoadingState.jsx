import React from 'react';
import { Flex, Spinner, Text } from '@chakra-ui/react';

export const LoadingState = ({ message = 'Loading details...', minH = '240px', ...props }) => {
    return (
        <Flex
            direction="column"
            align="center"
            justify="center"
            minH={minH}
            gap={3}
            p={6}
            {...props}
        >
            <Spinner size="lg" color="#1769FF" borderWidth="3px" />
            <Text fontSize="sm" color="gray.500" fontWeight="500">
                {message}
            </Text>
        </Flex>
    );
};

export default LoadingState;

