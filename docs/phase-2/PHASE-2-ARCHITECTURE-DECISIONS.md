# MigratePay V12: Phase 2 Architecture Decisions & System Design

> **Document ID:** `DOC-P2-01`  
> **Source Project:** `F:\laragon\www\migratepay` (Laravel 10.10, PHP 8.1+, React 19.2.8, Chakra UI 2.10.10)  
> **Destination Project:** `F:\laragon\www\Migratepay-V12` (Laravel 12.70.2, PHP 8.4.27, React 19.3.1, Chakra UI 3.37.0, Vite 7.3.7, Tailwind CSS 4.3.3)  
> **Phase:** 2 — Architecture Decisions & Blueprint  
> **Status:** APPROVED ARCHITECTURAL BLUEPRINT  

---

## 1. Executive Summary & Core Architectural Decisions

This document formally records the architectural decisions for the reconstruction of MigratePay on Laravel 12. These decisions are established upon deep forensic analysis of the V10 source application, verification of the destination V12 baseline, and adherence to the mandatory principles of financial correctness, institutional security, design preservation, and zero-debt maintainability.

### Master Architectural Decisions At-A-Glance

| Architectural Area | Legacy V10 Pattern | V12 Architecture Decision | Rationale & Impact |
| :--- | :--- | :--- | :--- |
| **Frontend Composition** | Server Blade (Public) + React 19 SPAs (User & Admin) | **Preserve Dual-Tier Architecture:** Server Blade for Public; React 19 / Chakra UI 3 for Dashboards | Maximum SEO performance, instant marketing loads; rich, interactive fintech dashboards; zero UI regressions. |
| **Inertia.js Integration** | Not used (0 references in V10) | **Retain Blade Host + React 19 Client SPA; Defer Full Inertia Rewrite** | V12 has unaligned Inertia packages (Laravel 2.x / React 3.x). Rewriting 33 modules to Inertia is high risk and unnecessary. |
| **Routing & Transport** | Session-gated `/api/*` inside `routes/web.php` | **Session/CSRF Web-API Architecture** | Seamless cookie authentication without Sanctum stateful token overhead; keeps client state synchronized. |
| **Design System** | Chakra UI 2.10.10 (Emotion CSS-in-JS) | **Chakra UI 3.37.0 (Ark UI Primitives + Static Tokens)** | Eliminates runtime CSS-in-JS overhead; preserves Midnight Navy & Sapphire brand aesthetics with modern primitives. |
| **Database & Schema** | `FLOAT` wallet balances; stale SQL backup; 8 localStorage modules | **Strict Decimal Typings; Unified Schema; Consolidated Settings** | `DECIMAL(15, 2)` eliminates rounding bugs; resolves `reports` and `withdrawal_requests` naming; eliminates localStorage. |
| **Financial Transactions** | Direct model balance mutations; duplicate payout risk | **Atomic Ledger Architecture with Row Locking** | Enforces `User::lockForUpdate()`, immutable transaction ledger, and strict state machine preventing `FIN-01` & `FIN-02`. |
| **Security & Secrets** | Public `gateway_settings.json`; missing admin middleware | **Encrypted Database Vault & Strict Admin Middleware** | Credentials encrypted via `Crypt::encryptString`; `/api/users/*` gated behind `['auth', 'admin']`; private file disks. |

---

## 2. Decision 1: Application Architecture & Frontend Composition

### 2.1 Context & Problem
The V10 codebase features a hybrid approach:
- Public marketing pages (Home, About, FAQ, Calculator, Legal) are server-rendered Blade templates in `resources/views/pages/`.
- The User Portal is a Single Page Application mounted on `#user-root` in `resources/views/layouts/dashboard.blade.php`.
- The Admin Control Tower is a Single Page Application mounted on `#admin-root` in `resources/views/layouts/admin.blade.php`.

The V12 destination skeleton currently has `inertiajs/inertia-laravel: ^2.0` and `@inertiajs/react: ^3.10.0` installed.

### 2.2 Evaluation of Options
1. **Option A: Full Inertia.js Re-architecture**
   - *Pros:* Unifies controllers with Inertia responses; standardizes props passing.
   - *Cons:* Requires a total rewrite of all 33 React modules into Inertia page views; destroys client-side tab switching and state in `UserContext` and `AdminContext`; major package version mismatch exists in V12 (`^2.0` adapter vs `^3.10.0` React adapter); high risk of introducing subtle routing regressions.
2. **Option B: Dual-Tier Architecture (Preserve Blade + React SPA with Session APIs) — [RECOMMENDED & APPROVED]**
   - *Pros:* Preserves 100% of tested user interface workflows; maintains lightning-fast server-rendered SEO marketing pages; allows surgical Chakra UI 2 to 3 migration; uses existing tested Axios client with CSRF protection; zero framework churn.
   - *Cons:* Requires maintaining Blade layouts and React mount points.

### 2.3 Formal Decision
**MigratePay V12 will preserve the proven Dual-Tier Architecture:**
- Public marketing and legal pages remain server-rendered **Blade templates** styled with modern Tailwind CSS 4 and Vite 7.
- User Dashboard and Admin Dashboard operate as dedicated **React 19 single-page application roots** powered by **Chakra UI 3**, communicating with session-authenticated JSON endpoints.
- Inertia packages will remain unconfigured in the background rather than forcing an invasive architectural rewrite during initial reconstruction.

---

## 3. Decision 2: API Routing, Authentication & Transport

### 3.1 Context & Problem
In V10, `routes/api.php` is empty. All 62 routes (including `/api/deals`, `/api/deposits`, `/api/users`) are declared in `routes/web.php`.
The frontend `apiClient` (`resources/js/api/client.js`) sends `X-CSRF-TOKEN` and `X-Requested-With: XMLHttpRequest` via standard browser cookies.

### 3.2 Evaluation of Options
1. **Option A: Migrate all `/api/*` routes to `routes/api.php` with Laravel Sanctum**
   - *Pros:* Follows strict Laravel directory conventions.
   - *Cons:* Requires setting up Sanctum stateful domain cookies, configuring CORS, handling bearer tokens, and troubleshooting SPA cookie handshakes.
2. **Option B: Retain Session-Gated Web API Architecture in `routes/web.php` — [RECOMMENDED & APPROVED]**
   - *Pros:* Fully verified in V10; session cookie is automatically shared between Blade and React; CSRF protection is active on every state-changing mutation; zero token-storage vulnerabilities (no tokens in `localStorage`).
   - *Cons:* Route file contains both page views and JSON endpoints (cleanly mitigated by organizing routes into route group files or dedicated controllers).

### 3.3 Formal Decision
**MigratePay V12 will maintain cookie-session authenticated web endpoints:**
- Interactive endpoints for dashboard modules will be grouped under `/api/` prefixes within session-authenticated route groups.
- Stateless external endpoints (e.g., payment provider webhook callbacks) will be isolated in `routes/api.php` without CSRF token verification.
- Controllers will return standardized JSON envelopes: `{ "success": true, "data": ..., "message": ... }`.

---

## 4. Decision 3: Resolution of Phase 1 Discrepancies (DOC-P1-14 Corrections)

Forensic inspection of the actual V10 source code and database migrations revealed four significant assumptions in the initial Phase 1 mapping document that must be corrected:

### 4.1 Disputes vs. Reports
- **Finding:** There is **no `disputes` table** or `Dispute.php` model in V10. All deal issues, disputes, and arbitration requests are stored in the **`reports` table** (`Report.php` model) created in migration `2024_07_23_000002_update_deals_add_workflow_fields.php`.
- **Decision:** **Preserve the `reports` table** in V12. Maintain 1:1 database compatibility with historical report data (`deal_id`, `reported_by`, `reason`, `description`, `status`, `resolution_note`, `resolved_by`, `resolved_at`). Upgrade the admin resolution workflow to support automated financial settlements (buyer refund, seller payout, split refund).

### 4.2 Withdrawals vs. Withdrawal Requests
- **Finding:** There is **no `withdrawals` table** or `Withdrawal.php` model in V10. Payout requests are stored in **`withdrawal_requests`** and user payout destinations in **`withdrawal_methods`** (created in `2024_07_23_000003_create_withdrawal_methods_and_requests_table.php`).
- **Decision:** **Preserve `withdrawal_requests` and `withdrawal_methods` tables** in V12. Do not introduce a redundant `withdrawals` table.

### 4.3 Proposed Six CMS Tables Consolidation
- **Finding:** DOC-P1-14 proposed creating 6 new database tables for admin modules that stored data in `localStorage`. Forensic analysis revealed that:
  - `AnnouncementsModule`: Actually broadcasts to the existing `notifications` table via `NotificationController`.
  - `ChargeManagementModule`: Manages 4 global fee tiers.
  - `SiteSettingsModule`: Manages site name, currency, hold period, and maintenance mode.
  - `UserSettingsModule`: Manages staff accounts, which belong in the `users` table.
- **Decision:** **Do NOT create 6 ad-hoc tables.** Instead:
  1. Consolidate Charge Management and Site Settings into a single, high-performance **`system_settings`** table (`key`, `value`, `group`, `type`).
  2. Enhance `NotificationController` to support global broadcasting to all users in the existing `notifications` table.
  3. Create dedicated, clean tables for **`cms_pages`** and **`blog_posts`** to support rich CMS capabilities.
  4. Attribute staff accounts directly to the `users` table using an indexed `role` column (`admin`, `co_admin`, `support_team`, `editorial_team`).

---

## 5. Decision 4: Financial Integrity & Ledger Architecture

### 5.1 Wallet Precision
- **Finding:** V10 defined `users.wallet_balance` as `FLOAT(10, 2)` (`FIN-03`), which suffers from binary floating-point rounding inaccuracies.
- **Decision:** All currency and balance columns in V12 (`users.wallet_balance`, `deals.amount`, `deposits.amount`, `withdrawal_requests.amount`, `transactions.amount`) will strictly use **`DECIMAL(15, 2)`**. In PHP, amounts will be handled using bcmath or string-formatted decimals to eliminate rounding drift.

### 5.2 Atomic Ledger Invariant
- **Decision:** Direct balance updates without corresponding transaction records are strictly prohibited.
- Every balance change (credit, debit, hold, release, fee) must be wrapped inside a `DB::transaction()` with `User::lockForUpdate()`.
- An immutable `Transaction` record must be inserted inside the exact same transaction, recording `balance_before`, `amount`, and `balance_after`.
- **Mathematical Invariant:** `balance_after = balance_before ± amount` must hold true on every row.

### 5.3 Escrow State Machine Remediation
- **Remediation for `FIN-01` (Duplicate Payout):** Fund release (`releaseFunds`) will be restricted strictly to `status === 'buyer_review'` and guarded by an atomic status update: `UPDATE deals SET status = 'completed' WHERE id = ? AND status = 'buyer_review'`. If zero rows are affected, execution aborts immediately.
- **Remediation for `FIN-02` (Unrefunded Decline):** When a seller declines a deal (`declineDeal`), the system will automatically execute an atomic refund transaction crediting the buyer's locked escrow balance back to their available wallet.

---

## 6. Decision 5: Security Architecture & Credential Vault

### 6.1 Encrypted Payment Gateway Vault (`SEC-01` Remediation)
- **Problem:** V10 exposed `storage/app/gateway_settings.json` publicly via `GET /api/payment-gateways/public`.
- **Decision:** 
  1. Delete the public file route completely.
  2. Implement a `PaymentGateway` model and `payment_gateways` database table.
  3. Store sensitive credentials (API keys, store passwords, secret keys) using Laravel's `encrypted` attribute casting (`Crypt::encryptString`).
  4. Create a public read-only endpoint (`GET /api/payment-gateways/public`) that strictly filters output to public attributes: `['id', 'name', 'slug', 'icon', 'currency', 'instructions', 'is_active']`.

### 6.2 Administrative Route Barriers (`SEC-02` Remediation)
- **Problem:** User management endpoints (`/api/users/*`) were mounted inside `['auth']` without admin role validation.
- **Decision:** 
  1. Register `AdminMiddleware` in Laravel 12's `bootstrap/app.php` middleware pipeline as alias `'admin'`.
  2. Group all administrative API routes under `Route::middleware(['auth', 'admin'])->prefix('api/admin')->group(...)`.
  3. Enforce Laravel Gate authorization checks within controller actions for defense-in-depth.

### 6.3 Private Storage Disk for Sensitive Files (`SEC-04` & `SEC-05`)
- **Problem:** Deal contract deliverables and payment receipts were saved in publicly accessible web directories.
- **Decision:**
  1. Configure a dedicated `private` disk in `config/filesystems.php` rooted at `storage/app/private/`.
  2. Store deal deliverables in `storage/app/private/deals/{deal_id}/` and receipts in `storage/app/private/deposits/`.
  3. Prohibit direct public web symlinks to these folders.
  4. Implement an authenticated streaming controller (`FileAccessController`) that checks user ownership or admin authority before serving files with appropriate `Content-Disposition` headers.

---

## 7. Decision 6: Chakra UI 2 to Chakra UI 3 Design Token Bridge

### 7.1 Visual Preservation
The institutional look and feel of MigratePay—deep Midnight Navy (`#071A3B`), Sapphire Blue (`#1769FF`), Emerald Green (`#006A4E`), crisp cards with 16px radius, and clean typography (`Inter`, `JetBrains Mono`)—must be preserved with zero visual degradation.

### 7.2 Implementation Architecture
- Migrate from Chakra UI 2's runtime `extendTheme` to Chakra UI 3's **`createSystem(defaultConfig, customConfig)`**.
- Replace Chakra UI 2 composite wrappers with Chakra UI 3 Ark UI primitives:
  - `<Modal>` → `<Dialog.Root>`
  - `<FormControl>` → `<Field.Root>`
  - `<Table>` → `<Table.Root>`
  - `<Menu>` → `<Menu.Root>`
- Deploy official Chakra 3 snippet helpers in `resources/js/components/ui/` (`toaster.jsx`, `dialog.jsx`, `field.jsx`).
- Global property migration: replace `colorScheme="blue"` with `colorPalette="blue"`, and `spacing={4}` with `gap={4}`.
