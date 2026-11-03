# MigratePay V12: Phase 2 Frontend Architecture & Chakra UI 3 Migration Blueprint

> **Document ID:** `DOC-P2-08`  
> **Source Project:** `F:\laragon\www\migratepay` (React 19.2.8, Chakra UI 2.10.10, Emotion, Vite 5, Tailwind 3)  
> **Destination Project:** `F:\laragon\www\Migratepay-V12` (React 19.3.1, Chakra UI 3.37.0, Vite 7.3.7, Tailwind CSS 4.3.3)  
> **Phase:** 2 — Architecture Decisions & Blueprint  
> **Status:** APPROVED FRONTEND DESIGN & CHAKRA 3 MIGRATION BLUEPRINT  

---

## 1. Executive Summary

The MigratePay frontend is characterized by its institutional fintech appearance, characterized by deep navy palettes, crisp card geometry, and responsive transaction tables. 

The primary frontend modernization objective in V12 is upgrading from **Chakra UI 2.10.10** to **Chakra UI 3.37.0** while preserving 100% of the visual identity and user workflows across the 14 public Blade views, 10 User Portal modules, and 23 Admin Control Tower modules.

---

## 2. Visual Brand Identity & Design Tokens

MigratePay's visual tokens must be strictly preserved in V12's `createSystem` theme definition:

### 2.1 Color Token Palette
```javascript
export const brandTokens = {
    colors: {
        navy: {
            900: { value: '#071A3B' }, // Deep Midnight Navy (Sidebar & Headers)
            800: { value: '#002147' }, // Oxford Blue (Brand Accent & Buttons)
            700: { value: '#0B2450' },
        },
        sapphire: {
            500: { value: '#1769FF' }, // Primary Action Blue / Active Link
            600: { value: '#1358d8' }, // Hover Blue
        },
        emerald: {
            700: { value: '#006A4E' }, // Bangladesh Dark Green (Escrow Verified)
            500: { value: '#10b981' }, // Positive Metrics
        },
        crimson: {
            700: { value: '#DC4C64' }, // Dispute Alert / Rejection
            500: { value: '#f43f5e' }, // Destructive Delete
        },
        slate: {
            bg: { value: '#F5F8FF' },     // Canvas Background
            card: { value: '#FFFFFF' },   // Surface Background
            border: { value: '#E3EAF5' }, // Card & Divider Border
        }
    }
};
```

### 2.2 Typography Tokens
- **Headings & Body:** `'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif`
- **Monospace Financial Numbers:** `'JetBrains Mono', SFMono-Regular, Menlo, Monaco, Consolas, monospace` (mandatory for transaction hashes, deal IDs, wallet addresses, and currency figures).

### 2.3 Component Geometry & Elevation
- **Cards:** `borderRadius: '2xl'` (16px), `borderWidth: '1px'`, `borderColor: '#E3EAF5'`, `boxShadow: '0 1px 3px 0 rgba(0, 0, 0, 0.05)'`.
- **Buttons:** `borderRadius: 'xl'` (12px), `fontWeight: '700'`, `px: 5`, `py: 2.5`.
- **Badges:** `borderRadius: 'full'` (9999px), `px: 3`, `py: 0.5`, `fontSize: 'xs'`, `fontWeight: '800'`, uppercase.

---

## 3. Chakra UI 2 to Chakra UI 3 Migration Specification

Chakra UI 3 introduces major architectural changes by replacing Emotion CSS-in-JS with zero-runtime static token generation and Ark UI primitives:

### 3.1 Theme Setup: `extendTheme` vs `createSystem`
- **Chakra UI 2 (Legacy):**
  ```javascript
  import { extendTheme, ChakraProvider } from '@chakra-ui/react';
  const theme = extendTheme({ colors: { ... } });
  <ChakraProvider theme={theme}><App /></ChakraProvider>
  ```
- **Chakra UI 3 (Target V12):**
  ```javascript
  import { createSystem, defaultConfig, ChakraProvider } from '@chakra-ui/react';
  const system = createSystem(defaultConfig, {
      theme: {
          tokens: brandTokens
      }
  });
  <ChakraProvider value={system}><App /></ChakraProvider>
  ```

---

### 3.2 Modal & Dialog Primitive Migration
All confirmation dialogs across 33 modules must be refactored from Chakra 2 composite modals to Chakra 3 Ark UI trees:

```jsx
// Chakra UI 2 (Legacy)
<Modal isOpen={isOpen} onClose={onClose}>
    <ModalOverlay />
    <ModalContent>
        <ModalHeader>Approve Release</ModalHeader>
        <ModalCloseButton />
        <ModalBody>Are you sure you want to release funds?</ModalBody>
        <ModalFooter>
            <Button onClick={onClose}>Cancel</Button>
            <Button colorScheme="blue" onClick={handleApprove}>Confirm</Button>
        </ModalFooter>
    </ModalContent>
</Modal>

// Chakra UI 3 (Target V12)
<Dialog.Root open={isOpen} onOpenChange={({ open }) => setIsOpen(open)}>
    <Dialog.Backdrop />
    <Dialog.Positioner>
        <Dialog.Content borderRadius="2xl">
            <Dialog.Header>
                <Dialog.Title>Approve Release</Dialog.Title>
            </Dialog.Header>
            <Dialog.CloseTrigger />
            <Dialog.Body>Are you sure you want to release funds?</Dialog.Body>
            <Dialog.Footer gap={3}>
                <Button variant="outline" onClick={() => setIsOpen(false)}>Cancel</Button>
                <Button colorPalette="blue" onClick={handleApprove}>Confirm</Button>
            </Dialog.Footer>
        </Dialog.Content>
    </Dialog.Positioner>
</Dialog.Root>
```

---

### 3.3 Form Control to Field Root Migration
```jsx
// Chakra UI 2 (Legacy)
<FormControl isInvalid={!!error}>
    <FormLabel>Deposit Amount</FormLabel>
    <Input value={amount} onChange={e => setAmount(e.target.value)} />
    <FormErrorMessage>{error}</FormErrorMessage>
</FormControl>

// Chakra UI 3 (Target V12)
<Field.Root invalid={!!error}>
    <Field.Label>Deposit Amount</Field.Label>
    <Input value={amount} onChange={e => setAmount(e.target.value)} borderRadius="xl" />
    <Field.ErrorText>{error}</Field.ErrorText>
</Field.Root>
```

---

### 3.4 Table Primitive Migration
```jsx
// Chakra UI 2 (Legacy)
<Table variant="simple">
    <Thead><Tr><Th>Reference</Th><Th>Amount</Th></Tr></Thead>
    <Tbody><Tr><Td>{tx.ref}</Td><Td>৳{tx.amount}</Td></Tr></Tbody>
</Table>

// Chakra UI 3 (Target V12)
<Table.Root variant="outline" size="sm">
    <Table.Header>
        <Table.Row>
            <Table.ColumnHeader>Reference</Table.ColumnHeader>
            <Table.ColumnHeader>Amount</Table.ColumnHeader>
        </Table.Row>
    </Table.Header>
    <Table.Body>
        <Table.Row>
            <Table.Cell>{tx.ref}</Table.Cell>
            <Table.Cell>৳{tx.amount}</Table.Cell>
        </Table.Row>
    </Table.Body>
</Table.Root>
```

---

### 3.5 Toast Notification Modernization
- In Chakra UI 2, toasts required invoking the hook `const toast = useToast()`.
- In Chakra UI 3, toasts use a standalone imperative singleton:
  ```javascript
  import { toaster } from '@/components/ui/toaster';
  
  toaster.create({
      title: "Deposit Submitted",
      description: "Admin will review your slip within 15 minutes.",
      type: "success",
      duration: 4000,
  });
  ```
- Deploy `<Toaster />` globally in `UserLayout.jsx` and `AdminLayout.jsx`.

---

## 4. Dashboard State Architecture & Backend Synchronization

### 4.1 User Portal State (`UserContext.jsx`)
- In V10, `UserContext` read currency symbol from `localStorage`.
- In V12, `UserContext` bootstraps from `GET /api/user/bootstrap`, receiving:
  - Authenticated user details (`id`, `name`, `email`, `role`, `wallet_balance`, `avatar_path`).
  - Active currency symbol and fee schedule.
  - Active pipeline counters (pending deals, awaiting approvals, unread tickets).

### 4.2 Admin Control Tower State (`AdminContext.jsx`)
- In V10, 8 modules saved state to browser `localStorage` (`ARCH-01`).
- In V12, `AdminContext` communicates with real RESTful endpoints:
  - Announcements → `NotificationController`
  - Charge Tiers & Site Meta → `SystemSettingsController`
  - CMS Pages → `PagesController`
  - Blog Posts → `BlogPostsController`
  - Staff Accounts → `AdminUserController`
  - Database Backups → `BackupController`
