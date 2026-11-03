import React from 'react';
import { Box, Flex, Text } from '@chakra-ui/react';
import { CheckCircleIcon } from './Icons';

export const UserAvatar = ({
    name = '',
    src = null,
    size = 'md', // 'sm' | 'md' | 'lg'
    isVerified = false,
    ...props
}) => {
    const sizeMap = {
        sm: { box: 8, font: 'xs', badge: 14 },
        md: { box: 10, font: 'sm', badge: 16 },
        lg: { box: 14, font: 'md', badge: 20 },
    };

    const s = sizeMap[size] || sizeMap.md;

    const getInitials = (str) => {
        if (!str) return 'U';
        const parts = str.trim().split(/\s+/);
        if (parts.length >= 2) {
            return (parts[0][0] + parts[1][0]).toUpperCase();
        }
        return str.substring(0, 2).toUpperCase();
    };

    return (
        <Box position="relative" display="inline-block" {...props}>
            <Flex
                w={s.box}
                h={s.box}
                borderRadius="full"
                bg="#002147"
                color="white"
                align="center"
                justify="center"
                fontWeight="700"
                fontSize={s.font}
                overflow="hidden"
                borderWidth="2px"
                borderColor="white"
                shadow="xs"
            >
                {src ? (
                    <img
                        src={src}
                        alt={name}
                        style={{ width: '100%', height: '100%', objectFit: 'cover' }}
                    />
                ) : (
                    <Text as="span">{getInitials(name)}</Text>
                )}
            </Flex>
            {isVerified && (
                <Box
                    position="absolute"
                    bottom="-2px"
                    right="-2px"
                    bg="white"
                    borderRadius="full"
                    p="1px"
                    color="#10B981"
                    shadow="xs"
                >
                    <CheckCircleIcon size={s.badge} color="#10B981" />
                </Box>
            )}
        </Box>
    );
};

export default UserAvatar;

