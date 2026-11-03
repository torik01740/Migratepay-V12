import React from 'react';
import { Card, Box, Flex, Text, Heading, Badge } from '@chakra-ui/react';

export const StatCard = ({
    label,
    value,
    helpText,
    icon: IconComponent,
    trend = null, // 'up' | 'down' | null
    trendValue = null,
    colorScheme = 'blue', // 'blue' | 'emerald' | 'amber' | 'navy'
    ...props
}) => {
    // Scheme colors matching MigratePay brand
    const colorStyles = {
        blue: {
            bg: 'blue.50',
            text: '#1769FF',
            iconBg: '#EEF6FF',
        },
        emerald: {
            bg: 'green.50',
            text: '#006A4E',
            iconBg: '#ECFDF5',
        },
        amber: {
            bg: 'orange.50',
            text: '#D97706',
            iconBg: '#FEF3C7',
        },
        navy: {
            bg: 'slate.50',
            text: '#002147',
            iconBg: '#F1F5F9',
        },
    };

    const style = colorStyles[colorScheme] || colorStyles.blue;

    return (
        <Card.Root
            bg="white"
            borderRadius="xl"
            borderWidth="1px"
            borderColor="gray.200"
            shadow="xs"
            p={{ base: 4, md: 5 }}
            transition="all 0.2s"
            _hover={{ shadow: 'sm', transform: 'translateY(-2px)' }}
            {...props}
        >
            <Flex justify="space-between" align="flex-start" mb={3}>
                <Box>
                    <Text fontSize="xs" fontWeight="700" color="gray.500" textTransform="uppercase" letterSpacing="0.05em">
                        {label}
                    </Text>
                    <Heading size="xl" fontWeight="800" color="#0A1128" mt={2} letterSpacing="-0.02em">
                        {value}
                    </Heading>
                </Box>
                {IconComponent && (
                    <Flex
                        w={12}
                        h={12}
                        align="center"
                        justify="center"
                        borderRadius="xl"
                        bg={style.iconBg}
                        color={style.text}
                    >
                        <IconComponent size={22} color={style.text} />
                    </Flex>
                )}
            </Flex>

            {(helpText || trendValue) && (
                <Flex align="center" gap={2} mt={1}>
                    {trendValue && (
                        <Badge
                            colorPalette={trend === 'up' ? 'green' : trend === 'down' ? 'red' : 'gray'}
                            variant="subtle"
                            fontSize="xs"
                            px={2}
                            py={0.5}
                            borderRadius="md"
                        >
                            {trend === 'up' ? '↑ ' : trend === 'down' ? '↓ ' : ''}
                            {trendValue}
                        </Badge>
                    )}
                    {helpText && (
                        <Text fontSize="xs" color="gray.500">
                            {helpText}
                        </Text>
                    )}
                </Flex>
            )}
        </Card.Root>
    );
};

export default StatCard;

