# MigratePay V12: Phase 2 Implementation Roadmap (Phases 3 to 8)

> **Document ID:** `DOC-P2-10`  
> **Source Project:** `F:\laragon\www\migratepay`  
> **Destination Project:** `F:\laragon\www\Migratepay-V12`  
> **Phase:** 2 — Architecture Decisions & Blueprint  
> **Status:** APPROVED MULTI-PHASE IMPLEMENTATION ROADMAP  

---

## 1. Executive Summary & Phased Dependency Sequence

The MigratePay V10-to-V12 transformation is organized into six disciplined, sequential phases following Phase 2 blueprint approval:

```
┌────────────────────────────────────────────────────────┐
│                        PHASE 3                         │
│           DATABASE & SECURITY CORE FOUNDATION          │
│   (Schema, Migrations, Seeders, Auth Core, Admin RBAC) │
└───────────────────────────┬────────────────────────────┘
                            │
                            ▼
┌────────────────────────────────────────────────────────┐
│                        PHASE 4                         │
│           FINANCIAL ENGINE & BACKEND SERVICES          │
│  (Wallet Ledger, Escrow Engine, Deposits, Withdrawals) │
└───────────────────────────┬────────────────────────────┘
                            │
                            ▼
┌────────────────────────────────────────────────────────┐
│                        PHASE 5                         │
│           PUBLIC WEBSITE & SERVER-SIDE BLADE           │
│     (Marketing Views, SEO Meta, Dynamic Calculator)    │
└───────────────────────────┬────────────────────────────┘
                            │
                            ▼
┌────────────────────────────────────────────────────────┐
│                        PHASE 6                         │
│              USER DASHBOARD MODERNIZATION              │
│       (Chakra 3, 10 Modules, Deal Workspace, Tickets)  │
└───────────────────────────┬────────────────────────────┘
                            │
                            ▼
┌────────────────────────────────────────────────────────┐
│                        PHASE 7                         │
│           ADMIN CONTROL TOWER & CMS REBUILD            │
│   (Chakra 3, 23 Modules, DB Persistence, Real Backup)  │
└───────────────────────────┬────────────────────────────┘
                            │
                            ▼
┌────────────────────────────────────────────────────────┐
│                        PHASE 8                         │
│          QA, E2E TESTING & RELEASE PREPARATION         │
│  (Full Regression, Concurrency, Migration Dry Run)     │
└────────────────────────────────────────────────────────┘
```

---

## 2. Phase-by-Phase Execution Specifications

### Phase 3: Database & Security Core Foundation
- **Primary Scope:**
  - Execute canonical V12 database migrations (all 22 existing tables with `DECIMAL(15, 2)` wallet precision + 3 lean settings/CMS tables).
  - Register `DatabaseSeeder` including `AiKnowledgeBaseSeeder`.
  - Implement authentication core, registration, login, rate limiting, and secure password reset without OTP disclosure (`SEC-03`).
  - Configure `AdminMiddleware` in `bootstrap/app.php` and secure `/api/users/*` (`SEC-02`).
  - Establish `private` storage disk in `config/filesystems.php` (`SEC-04`, `SEC-05`).
  - Implement encrypted `PaymentGateway` model vault replacing `gateway_settings.json` (`SEC-01`).
- **Dependencies:** Phase 2 Architecture Approval.
- **Key Deliverables:** Migration files, Auth controllers, middleware, private disk configuration, security tests.
- **Acceptance Criteria:**
  - `php artisan migrate:fresh` executes cleanly with 0 errors.
  - Unauthenticated access to admin routes returns 401/403.
  - Password reset failure returns no plaintext OTP.
  - Gateway secrets are stored encrypted in the database.
- **What Must NOT Be Started:** Deal escrow creation or financial payouts must not be started until the ledger schema is verified.

---

### Phase 4: Financial Engine & Backend Services
- **Primary Scope:**
  - Build `WalletService` with pessimistic locking (`User::lockForUpdate()`) and atomic ledger writing.
  - Build `DealService` with complete state machine, fixing duplicate payout (`FIN-01`) and unrefunded declines (`FIN-02`).
  - Build `DepositService` with private receipt uploads (`SEC-05`) and duplicate approval guards.
  - Build `WithdrawalService` with atomic balance holds and tier fee deductions.
  - Implement RESTful controllers for Deals, Deposits, Withdrawals, Reports, and Support Tickets.
- **Dependencies:** Phase 3 Database & Security Foundation.
- **Key Deliverables:** Backend services, RESTful controllers, domain event listeners, financial unit & feature tests.
- **Acceptance Criteria:**
  - Deal completion releases funds exactly once.
  - Seller decline automatically refunds buyer wallet.
  - Concurrent withdrawal requests cannot produce negative balances.
  - 100% of financial tests pass.
- **What Must NOT Be Started:** User and Admin frontend dashboards must not be mounted until backend endpoints are verified.

---

### Phase 5: Public Website & Server-Side Blade
- **Primary Scope:**
  - Port 14 server-rendered Blade templates to Vite 7 and Tailwind CSS 4.
  - Maintain exact Midnight Navy (`#071A3B`) and institutional typography (`Inter`).
  - Reconstruct interactive Escrow Fee Calculator in Blade/vanilla JS or client React snippet.
  - Implement SEO metadata tags, OpenGraph previews, and contact submission handler.
- **Dependencies:** Phase 3 Authentication Core & Layout Baseline.
- **Key Deliverables:** Blade templates in `resources/views/`, static assets, responsive navigation, contact form handler.
- **Acceptance Criteria:**
  - All 9 public marketing routes load with HTTP 200.
  - Fee calculator matches backend tier calculations exactly.
  - Mobile responsive layouts render cleanly without horizontal scroll.

---

### Phase 6: User Dashboard Modernization
- **Primary Scope:**
  - Configure Chakra UI 3 theme system (`createSystem`) with brand tokens.
  - Implement Chakra 3 UI primitives (`dialog.jsx`, `field.jsx`, `toaster.jsx`, `table.jsx`).
  - Port 10 User Portal React modules from Chakra 2 to Chakra 3.
  - Wire deal workspace messaging, deliverable uploading, and review submissions to live Phase 4 APIs.
  - Align route endpoints (`dealsApi.uploadDeliverable`, `userApi.updateProfile`, `userApi.changePassword`).
- **Dependencies:** Phase 4 Financial Services & Phase 5 Layout Foundation.
- **Key Deliverables:** `resources/js/user/` React modules, UserContext bootstrap, Chakra 3 component library.
- **Acceptance Criteria:**
  - User can initiate, fund, review, and complete a deal end-to-end in the React UI.
  - Deliverable uploads route to private storage disk.
  - Toasts display via Chakra 3 `toaster` singleton.

---

### Phase 7: Admin Control Tower & CMS Rebuild
- **Primary Scope:**
  - Port 23 Admin Control Tower React modules to Chakra UI 3.
  - Replace `localStorage` persistence in 8 modules with live database RESTful APIs (`SystemSettingsController`, `PagesController`, `BlogPostsController`, `AdminUserController`).
  - Reconstruct `BackupModule.jsx` to trigger real artisan SQLite backup archives.
  - Wire AI Live Chat, AI Settings, and Knowledge Base management to database.
  - Wire Reports & Dispute Arbitration modal with 3-way settlement (Buyer / Seller / Split).
- **Dependencies:** Phase 4 Backend Services & Phase 6 Chakra 3 Primitives.
- **Key Deliverables:** `resources/js/admin/` React modules, AdminContext bootstrap, real backup handler.
- **Acceptance Criteria:**
  - Admin changes to fee tiers, site settings, pages, and blogs persist across browser refreshes and sessions.
  - Admin dispute resolution triggers atomic financial payouts or refunds.
  - Backup module generates timestamped archives in private storage.

---

### Phase 8: QA, E2E Testing & Release Preparation
- **Primary Scope:**
  - Execute full regression test suite (all 18 feature and unit test suites).
  - Run data migration rehearsal on sanitized copy of V10 database.
  - Perform ledger reconciliation assertion across all migrated users.
  - Execute security penetration audit (IDOR, CSRF, XSS, file traversal).
  - Production build verification (`npm run build`, `php artisan config:cache`, `php artisan route:cache`).
- **Dependencies:** Phases 3 through 7 completed.
- **Key Deliverables:** Full passing test report, data migration validation report, production deployment checklist.
- **Acceptance Criteria:**
  - 100% test pass rate with zero regressions.
  - Zero unhandled exceptions or 500 errors in browser consoles.
  - Clean production build artifact ready for deployment.
