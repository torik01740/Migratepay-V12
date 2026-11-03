# DOC-P2-02 — MigratePay V12: User Module Migration Matrix
**Project:** MigratePay V10 → V12 Reconstruction  
**Phase:** 7 — User Dashboard Reconstruction  
**Document ID:** DOC-P2-02-MATRIX  
**Date:** 2026-11-04  
**Status:** Complete & Verified  

---

## 1. Executive Summary

This matrix catalogs the complete migration and modernization of all 18 V10 user dashboard components, modules, layout shells, and state controllers into MigratePay V12. All modules have been completely rebuilt to conform strictly to **React 19.3.1**, **Chakra UI 3.37.0**, and **Laravel 12.70.2** backend architecture.

---

## 2. Comprehensive 18-Module Migration Matrix

| # | V10 Source Module / Component | V12 Destination Location | Primary Responsibilities | Backend APIs / Endpoints Utilized | Chakra UI 3 Adaptations | Migration Status |
|---|-------------------------------|--------------------------|--------------------------|-----------------------------------|--------------------------|------------------|
| **1** | `user/layout/UserLayout.jsx` | `resources/js/layouts/UserLayout.jsx` | Dynamic SPA portal shell, client route synchronization, deep-linking, popstate handling | Session auth, `/dashboard/{route}` | Replaced custom layout with responsive `DashboardLayout`, flex columns, zero-flicker routing | **MIGRATED & VERIFIED** |
| **2** | `user/layout/UserSidebar.jsx` | `resources/js/components/navigation/Sidebar.jsx` | Brand navigation, desktop/mobile drawer, role badge, portal separation | Client router state | Chakra UI 3 tokens, semantic SVG icons, smooth transitions | **MIGRATED & VERIFIED** |
| **3** | `user/layout/UserHeader.jsx` | `resources/js/components/navigation/Topbar.jsx` | Sticky navigation bar, live wallet balance pill, notification bell toggle, avatar pill | `authService`, `/api/me` | Compound header flexbox, mobile drawer toggle button | **MIGRATED & VERIFIED** |
| **4** | `user/modules/UserDashboardHome.jsx` | `resources/js/pages/dashboard/user/DashboardHome.jsx` | Command center, high-level metrics, active escrow deals preview, KYC banner, quick actions | `/api/deals`, `/api/deposits`, `/api/me` | Replaced Chakra 2 Stat with `StatCard`, compound `Card.Root`, `Badge`, responsive grid | **MIGRATED & VERIFIED** |
| **5** | `user/modules/CreateDealModule.jsx` | `resources/js/pages/dashboard/user/CreateDealModule.jsx` | Multi-step deal creation, buyer/seller fee split, coupon calculation, safeguards checklist | `/api/deals` (POST), `/api/categories`, `/api/public/calculate-charge` | Grid layout, responsive input forms, live fee summary card | **MIGRATED & VERIFIED** |
| **6** | `user/modules/ProjectManagementModule.jsx` | `resources/js/pages/dashboard/user/ProjectManagementModule.jsx` | Escrow deals workspace, status filters, search, countdown timers, accept/decline, deliverables submission, release, dispute filing | `/api/deals`, `/api/deals/{id}/accept`, `/api/deals/{id}/decline`, `/api/deals/{id}/release`, `/api/deals/{id}/deliverable`, `/api/deals/{id}/report` | Compound `Dialog.Root`, custom modal positioning, badge styling | **MIGRATED & VERIFIED** |
| **7** | `user/modules/DepositModule.jsx` | `resources/js/pages/dashboard/user/DepositModule.jsx` | Gateway selector (bKash, Nagad, Rocket, Bank), dynamic instructions, proof submission, screenshot upload, deposit history | `/api/deposit-methods`, `/api/deposits` (GET & POST), `/api/deposits/upload-screenshot` | Visual gateway selector grid, responsive form controls, compound card | **MIGRATED & VERIFIED** |
| **8** | `user/modules/WithdrawalModule.jsx` | `resources/js/pages/dashboard/user/WithdrawalModule.jsx` | Payout methods management (Mobile & Bank), 1.5% fee calculation, net payout preview, withdrawal request, log history | `/api/withdrawal-methods` (GET, POST, DELETE), `/api/withdrawals` (GET & POST) | Multi-column payout form, real-time calculation breakdown | **MIGRATED & VERIFIED** |
| **9** | `user/modules/ActivityModule.jsx` | `resources/js/pages/dashboard/user/ActivityModule.jsx` | Full immutable financial ledger, debit/credit color indicators, reference ID lookup, balance history | `/api/transactions` (paginated) | Tabular data display with Chakra 3 borders, hover states, badges | **MIGRATED & VERIFIED** |
| **10** | `user/modules/MessagesModule.jsx` | `resources/js/pages/dashboard/user/MessagesModule.jsx` | Bilateral deal messaging hub, active contract conversation list, in-thread messaging | `/api/deals`, `/api/deals/{id}/message` | Split chat layout (conversation list + message thread), message bubbles | **MIGRATED & VERIFIED** |
| **11** | `user/modules/ProfileModule.jsx` | `resources/js/pages/dashboard/user/ProfileModule.jsx` | User overview, profile editing, National KYC document submission, password change, counterparty reviews | `/api/user/profile`, `/api/user/change-password`, `/api/user/request-verification`, `/api/users/{id}/reviews` | Tabbed container, form state management, security checklist | **MIGRATED & VERIFIED** |
| **12** | `user/modules/SecurityModule.jsx` | `resources/js/pages/dashboard/user/SecurityModule.jsx` | Ironclad Security Hub, 2FA status card, cryptographic vault specifications, session history, password shortcut | `/api/me`, `/api/user/change-password` | Compound cards, security status badges, visual icon headers | **MIGRATED & VERIFIED** |
| **13** | `user/modules/SupportModule.jsx` | `resources/js/pages/dashboard/user/SupportModule.jsx` | Help desk queue, ticket creation with file attachments, message reply thread | `/api/support/tickets` (GET & POST), `/api/support/tickets/{id}/reply` | Split ticket panel, reply drawer, priority badges | **MIGRATED & VERIFIED** |
| **14** | `user/components/ReviewModal.jsx` | `resources/js/components/modals/ReviewModal.jsx` | 4-dimension counterparty review modal (Communication, Conduct, Delivery, Reliability) + feedback notes | `/api/deals/{id}/review`, `/api/deals/{id}/can-review` | Chakra UI 3 `Dialog.Root`, star rating controls, modal positioner | **MIGRATED & VERIFIED** |
| **15** | `user/layout/NotificationDropdown.jsx` | `resources/js/components/navigation/NotificationDropdown.jsx` | Unread notifications drawer, notification count badge, mark-all-read trigger, route navigation | `/api/notifications`, `/api/notifications/read-all`, `/api/notifications/{id}/read` | Absolute positioned dropdown panel, shadow elevation, loading spinners | **MIGRATED & VERIFIED** |
| **16** | `user/context/UserContext.jsx` | `resources/js/hooks/useAuth.jsx` & `resources/js/services/auth.js` | Authoritative session state, authenticated user profile, wallet balance, currency formatting, login/logout | `/api/me`, `/api/logout`, `/sanctum/csrf-cookie` | Modern React 19 hook architecture with memoized auth context | **MIGRATED & VERIFIED** |
| **17** | `user/components/SvgIcon.jsx` | `resources/js/components/common/Icons.jsx` | System icon library (Dashboard, Deals, Wallet, Deposit, Withdrawal, Support, Security, Users, Gateways, Chat, Activity, etc.) | Frontend Presentation | Strict React 19 SVG element props, `IconBase` reusable wrapper | **MIGRATED & VERIFIED** |
| **18** | `user/theme.js` | `resources/js/theme/index.js` | Design system configuration, MigratePay brand colors, typography, component recipes | Chakra UI 3 System | Modern Chakra UI 3 `createSystem` and `defaultConfig` tokens | **MIGRATED & VERIFIED** |

---

## 3. Compatibility & Architectural Decisions

1. **Chakra UI 3 Compound Components:**
   - Chakra UI 2 `<Card>`, `<Modal>`, `<Menu>`, `<Table>` were rewritten to Chakra UI 3 `<Card.Root>`, `<Dialog.Root>`, `<Dialog.Positioner>`, and semantic table layouts.
2. **Notification & Toast System:**
   - Chakra UI 2 `useToast()` was replaced with Chakra UI 3 `toaster.create()` using `components/feedback/toaster.jsx`.
3. **Session-Driven Identity:**
   - No JWT or localStorage tokens. Authentication relies on Laravel 12 session cookies with CSRF header injection (`X-XSRF-TOKEN`).
4. **Authoritative Financial Calculations:**
   - All monetary figures are calculated on the Laravel backend using BCMath (`App\Services\DecimalMath`). Frontend calculations in `CreateDealModule` and `WithdrawalModule` serve only as dynamic client previews and are rigorously validated on the server.

