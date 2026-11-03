/**
 * MigratePay V12 - Central Chakra UI 3 System Configuration
 */

import { createSystem, defaultConfig, defineConfig } from '@chakra-ui/react';
import { brandTokens } from './tokens';

const config = defineConfig({
    theme: {
        tokens: brandTokens,
    },
});

export const system = createSystem(defaultConfig, config);
export default system;

