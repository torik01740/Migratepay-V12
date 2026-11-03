import React from 'react';
import { Box, Flex, Heading, Text } from '@chakra-ui/react';

export const PageHeader = ({
    title,
    subtitle,
    badge,
    actions,
    breadcrumbs,
    ...props
}) => {
    return (
        <Box mb={{ base: 6, md: 8 }} {...props}>
            {breadcrumbs && (
                <Flex align="center" gap={2} mb={2} fontSize="xs" color="gray.500">
                    {breadcrumbs}
                </Flex>
            )}
            <Flex
                justify="space-between"
                align={{ base: 'flex-start', sm: 'center' }}
                direction={{ base: 'column', sm: 'row' }}
                gap={4}
            >
                <Box>
                    <Flex align="center" gap={3}>
                        <Heading size="xl" color="#0A1128" fontWeight="800" letterSpacing="-0.02em">
                            {title}
                        </Heading>
                        {badge}
                    </Flex>
                    {subtitle && (
                        <Text fontSize="sm" color="gray.600" mt={1}>
                            {subtitle}
                        </Text>
                    )}
                </Box>
                {actions && <Flex gap={3} align="center" wrap="wrap">{actions}</Flex>}
            </Flex>
        </Box>
    );
};

export default PageHeader;

