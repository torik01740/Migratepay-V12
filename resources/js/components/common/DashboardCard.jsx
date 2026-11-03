import React from 'react';
import { Card, Box, Flex, Heading, Text } from '@chakra-ui/react';

export const DashboardCard = ({
    title,
    subtitle,
    action,
    children,
    headerRight,
    p = { base: 4, md: 6 },
    className = '',
    ...props
}) => {
    return (
        <Card.Root
            bg="white"
            borderRadius="xl"
            borderWidth="1px"
            borderColor="gray.200"
            shadow="xs"
            transition="all 0.2s ease-in-out"
            _hover={{ shadow: 'sm', borderColor: 'gray.300' }}
            {...props}
        >
            {(title || subtitle || action || headerRight) && (
                <Card.Header px={p} pt={p} pb={3} borderBottomWidth="1px" borderColor="gray.100">
                    <Flex justify="space-between" align="center" wrap="wrap" gap={3}>
                        <Box>
                            {title && (
                                <Heading size="md" color="#0A1128" fontWeight="700">
                                    {title}
                                </Heading>
                            )}
                            {subtitle && (
                                <Text fontSize="xs" color="gray.500" mt={1}>
                                    {subtitle}
                                </Text>
                            )}
                        </Box>
                        {(action || headerRight) && (
                            <Box>{action || headerRight}</Box>
                        )}
                    </Flex>
                </Card.Header>
            )}
            <Card.Body p={p}>{children}</Card.Body>
        </Card.Root>
    );
};

export default DashboardCard;

