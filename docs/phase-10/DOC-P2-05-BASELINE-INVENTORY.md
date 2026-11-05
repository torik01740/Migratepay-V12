# DOC-P2-05-BASELINE-INVENTORY: Phase 10 Application Baseline & Environment Inventory

**Project:** MigratePay V10 → V12 Reconstruction  
**Phase:** 10 of 10 — End-to-End Testing & Release Readiness  
**Document ID:** DOC-P2-05-BASELINE-INVENTORY  
**Date:** November 4, 2026  
**Status:** Baseline Established & Non-Destructive Protection Confirmed

---

## 1. Executive Summary & Working-Tree Baseline

Before executing code modifications or release remediation in Phase 10, an exhaustive technical baseline of the MigratePay V12 application in `F:\laragon\www\Migratepay-V12` was established.

### Git Status & Working Tree Protection:
- **Active Branch:** `master` (synchronized with `origin/master`).
- **Safety Rule Observed:** No destructive Git resets, cleans, checkouts, or branch switches were executed. All uncommitted work from Phases 6–9 is preserved.
- **Repository Integrity:** No code or secrets will be pushed to GitHub during this phase.

---

## 2. Environment & Destination Technology Stack

| Component / Layer | Specification / Version | Verification Command | Status |
| :--- | :--- | :--- | :---: |
| **PHP Runtime** | PHP 8.4.27 (cli) ZTS Visual C++ 2022 x64 | `php -v` | Active |
| **Backend Framework**| Laravel Framework 12.70.2 | `php artisan --version` | Active |
| **Database Engine** | MySQL 8.0.30 Community Server (InnoDB) | `SELECT VERSION()` | Active (Port 3306) |
| **Node.js Runtime** | Node.js v24.18.0 / npm 11.16.0 | `node -v; npm -v` | Active |
| **Frontend Framework**| React 19.3.1 / React-DOM 19.3.1 | `package.json` | Active |
| **UI Component System**| Chakra UI 3.37.0 / Emotion 11.14.0 | `package.json` | Active |
| **Build Bundler** | Vite 7.3.7 / @tailwindcss/vite 4.0.0 | `npm run build` | Active (2,097 modules) |
| **Package Manager** | Composer 2.10.3 | `composer --version` | Active |

---

## 3. Database Connection & Table Schema Inventory

Database connection verified against `migratepay_v12` on `127.0.0.1:3306`:
- **Active Connection:** `mysql` (PDO MySQL driver).
- **Migration Status:** 24 migration files executed cleanly; 31 tables present in `migratepay_v12`.
- **Domain Tables (22 Canonical Tables):**
  - Core Users & Identity: `users`, `password_reset_tokens`, `sessions`
  - Financial & Balances: `transactions`, `deposits`, `withdrawal_methods`, `withdrawal_requests`
  - Escrow & Marketplace: `deals`, `deal_categories`, `deal_disputes`, `coupons`, `reviews`
  - Payment Infrastructure: `payment_gateways`, `payment_webhook_idempotencies`
  - Customer Service: `support_tickets`, `support_ticket_messages`, `ai_conversations`, `knowledge_base_articles`
  - CMS & Platform: `cms_pages`, `blog_posts`, `banners`, `system_settings`
- **Integrity Rule:** Zero drop, truncate, or recreate operations will be run. All existing testing accounts and configuration data are protected.

---

## 4. Application Architecture & Route Inventory

### A. Middleware Pipeline (`bootstrap/app.php`)
1. **Web Pipeline:**
   - `EncryptCookies`, `AddQueuedCookiesToResponse`, `StartSession`, `ShareErrorsFromSession`, `VerifyCsrfToken`
   - `EnforceSessionTimeout` (Server-side idle timeout enforcement)
   - `SubstituteBindings`
2. **Custom Domain Barriers:**
   - `AdminMiddleware`: Server-side RBAC barrier verifying staff membership (`admin`, `co-admin`, `editorial`, `support`), blocking customers with 403, preventing co-admin tampering with root admin accounts, and enforcing IP whitelisting.

### B. Route Endpoints Inventory
- **Public Website Routes (Blade Views):**
  - `/` (Home), `/about`, `/how-it-works`, `/fees`, `/contact`, `/faq`, `/terms`, `/privacy`, `/blog`, `/blog/{slug}`, `/page/{slug}`, `/user/{username}` (Public profile), `/auth` (Customer & Admin Login/Register).
- **User Dashboard API Routes (`/api/*` protected by `auth`):**
  - `/api/user/profile`, `/api/user/change-password`, `/api/user/kyc/submit`
  - `/api/user/deals` (CRUD, deliverable upload, submission review, fund release)
  - `/api/user/wallet`, `/api/user/transactions`
  - `/api/user/deposits`, `/api/user/withdrawals`
  - `/api/user/support-tickets`
- **Administrative API Routes (`/api/admin/*` protected by `auth` + `AdminMiddleware`):**
  - All 28 modules covered across users, deposits, withdrawals, deals, disputes, gateways, charge tiers, CMS, blogs, support, knowledge base, system settings, backup, and security policies.

---

## 5. Baseline Test & Build Metrics

The pre-remediation baseline test suite was executed:

```
PHPUnit 11.5.57: OK (117 tests, 517 assertions, 0 failures)
Vite 7.3.7: Production build clean (2,097 modules transformed in 18.36s)
```

---

## 6. Known Incomplete Items, TODOs & Release Gates

1. **Two-Factor Authentication (MFA / TOTP):**
   - *Status:* Deferred in Phase 9. `security.enforce_2fa` was locked to `false`.
   - *Action in Phase 10 (Workstream B):* Implement full production-ready TOTP enrollment, encrypted secret storage, recovery code hashing, login challenge, and administrative policy enforcement.
2. **Database Backup Automation:**
   - *Status:* The UI simulates backup manifests.
   - *Action in Phase 10 (Workstream H):* Formally document production backup architecture (mysqldump, encrypted storage, offsite retention, RPO/RTO) and execute an isolated non-destructive restore drill.
3. **End-to-End Verification Across All 28 Admin Modules:**
   - *Action in Phase 10 (Workstream E):* Verify role-based API access across all 5 roles (`admin`, `co-admin`, `editorial`, `support`, `user`).
4. **Performance & Mobile Responsiveness Verification:**
   - *Action in Phase 10 (Workstream I):* Capture 14+ browser screenshots across mobile and desktop interfaces.

---

## 7. Non-Destructive Protection Declarations

- `F:\laragon\www\migratepay` (V10) remains strictly **READ-ONLY**.
- Database `migratepay_v12` is protected against any destructive schema or data operations.
- Phase 10 execution begins immediately with Workstream B.
