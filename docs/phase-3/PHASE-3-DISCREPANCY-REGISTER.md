# MigratePay V12: Phase 3 Discrepancy & Correction Register

> **Document ID:** `DOC-P3-DISCREPANCY-REGISTER`  
> **Phase:** 3 — Database Verification & Pre-Migration Audit  
> **Source Database:** `F:\laragon\www\migratepay\database\database.sqlite`  
> **Destination Environment:** MySQL 8.0.30 (Laragon)  
> **Purpose:** Formal audit register documenting every variance between prior documentation, assumptions, and independently verified system reality.

---

## 1. Classification Methodology

In compliance with the project owner's mandate, every finding in this register is classified under one of four rigorous standards:
- **[VERIFIED]:** Independently corroborated through direct binary inspection, database engine queries (`sqlite3`, PDO `PRAGMA`), filesystem inspection, or PHP source code analysis.
- **[REQUIRES CORRECTION]:** A discrepancy where earlier project documentation, architecture drafts, or migration proposals conflicted with ground reality, requiring specific, documented rectification.
- **[REQUIRES ADDITIONAL EVIDENCE]:** A finding that requires operational validation once services (such as the MySQL server daemon) are started.
- **[OWNER DECISION REQUIRED]:** A business logic, data governance, or architectural choice that requires explicit sign-off from the project owner before execution.

---

## 2. Register of Discrepancies & Corrections

### Item 01: Table Column Count & Schema Definitions
- **Prior Claim / Documentation:** Prior drafts claimed `deals` had 18 columns, `deposits` had 10 columns, `withdrawal_requests` had 13 columns, `ai_settings` had 6 columns (`provider`, `model`, `api_key`), `banners` had 9 columns (`image_path`, `target_url`), and `categories` had an `icon` column.
- **Independently Verified Reality:**
  - `deals`: Exactly **28 columns**. Missed columns in prior drafts: `admin_approved`, `buyer_accepted`, `custom_service`, `custom_service_charge`, `buyer_reviewed`, `seller_reviewed`, `category_id`, `coupon_id`, `original_charge`, `coupon_discount`.
  - `deposits`: Exactly **13 columns**. Missed column: `admin_read`.
  - `withdrawal_requests`: Exactly **15 columns**. Missed columns: `net_amount`, `fee` defaults, and separate timestamps (`processed_at`, `reviewed_at`).
  - `withdrawal_methods`: Exactly **14 columns**.
  - `ai_settings`: Exactly **5 columns** (`id`, `key`, `value`, `created_at`, `updated_at`). It is a key-value store, NOT a relational configuration table.
  - `banners`: Exactly **12 columns** (`id`, `name`, `status`, `placement`, `position_behavior`, `start_date`, `end_date`, `targeting`, `design`, `elements`, `created_at`, `updated_at`). It uses JSON structures (`targeting`, `design`, `elements`) rather than flat image URLs.
  - `categories`: Exactly **7 columns** (`id`, `name`, `slug`, `description`, `is_active`, `created_at`, `updated_at`). There is NO `icon` column.
  - `notifications`: Exactly **11 columns**. Includes polymorphic reference fields (`reference_type`, `reference_id`).
- **Classification:** **[REQUIRES CORRECTION]** (Corrected in [DOC-P3-04](file:///f:/laragon/www/Migratepay-V12/docs/phase-3/PHASE-3-SOURCE-SCHEMA-DETAIL.md)).
- **Resolution:** `DOC-P3-04` has been completely rewritten using exact SQLite PRAGMA extracts, ensuring all 22 tables and their true columns are accurately defined for V12 migrations.

---

### Item 02: Characterization of the Financial Ledger
- **Prior Claim / Documentation:** Prior drafts occasionally referred to the system as a "double-entry ledger".
- **Independently Verified Reality:**
  - The SQLite database contains a single table `transactions` (8 records) recording individual user balance changes (`balance_before`, `balance_after`, `amount`, `type`).
  - There are NO paired debit and credit ledger postings, no chart of accounts, and no balancing journal entries.
- **Classification:** **[REQUIRES CORRECTION]** (Corrected in [DOC-P3-03](file:///f:/laragon/www/Migratepay-V12/docs/phase-3/PHASE-3-FINANCIAL-RECONCILIATION.md)).
- **Resolution:** The system is strictly documented as a **single-entry transaction journal with balance snapshots**. Double-entry claims have been formally retracted.

---

### Item 03: Deal 1 Active Escrow Liability
- **Prior Claim / Documentation:** Initial drafts noted total user balances as ৳14,037.00 but omitted or conflated the escrow-held funds for Deal 1 (`DX-188753`).
- **Independently Verified Reality:**
  - Deal 1 is in status `funds_secured`.
  - Buyer (User 4) had ৳12,240.00 debited from their available balance on 2026-07-22 (Transaction ID 2).
  - This ৳12,240.00 is NOT in any user's available wallet balance; it is held in custodial trust by the platform.
  - Upon deal completion, ৳11,760.00 is payable to Seller (User 3), and ৳480.00 is retained as platform fee revenue.
  - Total custodial liability of the platform is **৳26,277.00** (৳14,037.00 available + ৳12,240.00 escrow trust).
- **Classification:** **[VERIFIED]** (Reconciled in [DOC-P3-03](file:///f:/laragon/www/Migratepay-V12/docs/phase-3/PHASE-3-FINANCIAL-RECONCILIATION.md)).
- **Resolution:** Custodial balance sheet formally incorporates Deal 1's ৳12,240.00 active escrow liability, demonstrating 100.00% capital parity (৳28,500.00 inflows - ৳2,223.00 outflows = ৳26,277.00 liability, zero variance).

---

### Item 04: Synthetic Test Account (User 7) & Baseline Balance (User 3)
- **Prior Claim / Documentation:** Early documentation assumed all user balances derived exclusively from logged transactions in the `transactions` table.
- **Independently Verified Reality:**
  - User 7 (`Test User`, `user@example.com`): Wallet balance is ৳500.00, but User 7 has 0 rows in `transactions`, 0 deposits, and 0 deals. This is a synthetic seed/test account.
  - User 3 (`Pulok Halder`): In Transaction ID 5 (Deposit 3 of ৳1,000.00 on 2026-07-22), `balance_before` is recorded as ৳11,000.00 and `balance_after` as ৳12,000.00, demonstrating an initial unlogged baseline balance of ৳11,000.00 prior to transaction tracking.
- **Classification:** **[VERIFIED]** (Documented in [DOC-P3-03](file:///f:/laragon/www/Migratepay-V12/docs/phase-3/PHASE-3-FINANCIAL-RECONCILIATION.md)).
- **Resolution:** Reconciled as initial capital baseline inflows. User 7 will be migrated to preserve relational parity, but flagged in administrative reports as a seeded test account.

---

### Item 05: Proposed API Route Migration Strategy vs. Frontend Consumers
- **Prior Claim / Documentation:** Section 3.2 of `PHASE-3-EXECUTION-PLAN.md` proposed moving all `/api/users/*` routes behind `Route::middleware(['auth', 'admin'])->prefix('api/admin')->group(...)`.
- **Independently Verified Reality:**
  - Inspection of frontend API client `resources/js/api/userAndSupport.js` (line 18) revealed:
    `getUserReviews: (userId) => apiClient.get('/api/users/' + userId + '/reviews').then(res => res.data)`
    This is an authenticated user/customer call to view counterparty reviews. If all `/api/users/*` routes are forced into `['auth', 'admin']`, regular users will receive `403 Forbidden` when opening counterparty profiles or deal modals!
  - Furthermore, `resources/js/api/adminApi.js` (lines 53, 56, 59) calls `GET /api/users`, `PUT /api/users/{id}/status`, and `PUT /api/users/{id}/verification`. Blindly changing URLs without backward-compatible routing breaks the admin dashboard SPA.
- **Classification:** **[REQUIRES CORRECTION]** (Resolved in revised [DOC-P3-02](file:///f:/laragon/www/Migratepay-V12/docs/phase-3/PHASE-3-EXECUTION-PLAN.md)).
- **Resolution:**
  - Preserve `GET /api/users/{userId}/reviews` under `auth` (or public) middleware.
  - Route administrative operations (`/api/admin/users/*`) with proper `['auth', 'admin']` middleware, while maintaining `/api/users` routes backed by Policy authorization checks (`UserController::index`, `updateStatus`, `updateVerification`) so existing frontend consumers never break.

---

### Item 06: Contradiction Regarding Public Payment Gateway Endpoint
- **Prior Claim / Documentation:** `PHASE-3-EXECUTION-PLAN.md` contained an internal contradiction:
  - Line 103: "Delete the unauthenticated file-download route `GET /api/payment-gateways/public`."
  - Line 107: "`GET /api/payment-gateways/public` returns strictly public fields: `['id', 'name', 'slug', 'icon', ...]`."
- **Independently Verified Reality:**
  - In V10, `routes/web.php` line 84 declared:
    `Route::get('/api/public/gateway-settings', function() { return response()->file(storage_path('app/gateway_settings.json')); });`
  - This served the raw JSON file containing unmasked live secret keys and store credentials (`SEC-01`).
  - In `resources/js/api/finance.js` line 15, the deposit modal queries `apiClient.get('/api/public/gateway-settings')` to render available payment gateways.
- **Classification:** **[REQUIRES CORRECTION]** (Resolved in revised [DOC-P3-02](file:///f:/laragon/www/Migratepay-V12/docs/phase-3/PHASE-3-EXECUTION-PLAN.md)).
- **Resolution:**
  - Eliminate the dangerous raw file response completely.
  - Implement `GET /api/public/gateway-settings` (and alias `GET /api/payment-gateways/public`) backed by the `payment_gateways` database vault, returning ONLY sanitized public display fields (`id`, `name`, `slug`, `icon`, `instructions`, `currency`, `fee_percentage`, `fee_fixed`).
  - Gating credential administration under `GET /api/admin/gateway-settings` and `POST /api/admin/gateway-settings` with `['auth', 'admin']` middleware.

---

### Item 07: Destination MySQL Database State & Existing Tables
- **Prior Claim / Documentation:** Prior drafts assumed the destination database was completely empty or unconfigured based solely on the V12 SQLite baseline (`database.sqlite` containing 9 default Laravel tables).
- **Independently Verified Reality:**
  - Independent forensic inspection of `F:\laragon\data\mysql-8` revealed:
    1. Database `migratepay` exists and contains 22 `.ibd` tables with timestamps matching October 2026 (including `users.ibd` from October 30, 2026).
    2. Database `migratepay-v13` (`migratepay@002dv13`) exists and contains 27 `.ibd` tables (including Laravel 12 default tables `cache`, `sessions`, `jobs` plus migrated V10 tables from November 1, 2026).
    3. Database `migratepay-v12` (`migratepay@002dv12`) exists as an empty database schema directory (0 `.ibd` files).
  - MySQL port 3306 is currently stopped in Laragon (`TcpTestSucceeded: False`).
- **Classification:** **[REQUIRES CORRECTION]** (Documented in [DOC-P3-06](file:///f:/laragon/www/Migratepay-V12/docs/phase-3/PHASE-3-ENVIRONMENT-VERIFICATION.md)).
- **Resolution:** The destination MySQL environment already contains legacy/staging databases (`migratepay`, `migratepay-v13`). Target for Phase 3 implementation must be isolated to a clean `migratepay_v12` database schema, verifying it contains 0 tables before running fresh migrations.

---

### Item 08: Database Accounts & Principle of Least Privilege
- **Prior Claim / Documentation:** Earlier execution plans proposed creating a single database account `migratepay_app` with both DDL (`CREATE`, `ALTER`, `INDEX`) and DML (`SELECT`, `INSERT`, `UPDATE`, `DELETE`) privileges.
- **Independently Verified Reality:**
  - Granting DDL privileges to the application runtime account violates least-privilege security principles and exposes the database to structural alteration if an application injection flaw is exploited.
- **Classification:** **[REQUIRES CORRECTION]** (Resolved in revised [DOC-P3-02](file:///f:/laragon/www/Migratepay-V12/docs/phase-3/PHASE-3-EXECUTION-PLAN.md)).
- **Resolution:**
  - Define two separate accounts:
    1. `migratepay_migrator`: DDL + DML privileges (`CREATE, ALTER, INDEX, DROP, REFERENCES, SELECT, INSERT, UPDATE, DELETE`) reserved strictly for deployment and migration CLI tasks.
    2. `migratepay_app`: DML privileges only (`SELECT, INSERT, UPDATE, DELETE`) for application runtime.

---

### Item 09: Laravel Encryption Key Preservation & Sensitive Records
- **Prior Claim / Documentation:** Unclear whether V10 records were encrypted with `Crypt::` and whether `APP_KEY` change would cause data loss.
- **Independently Verified Reality:**
  - Source V10 `APP_KEY`: `base64:CdryUP5BdwEhaTsszqkEDh0wjSDg20YIJsXPFpyU6dU=` (in `F:\laragon\www\migratepay\.env`).
  - Destination V12 `APP_KEY`: `base64:VIuSRL6oTaKLnZu7RQaO0hp8iqHEEE973UIZDRQCMzo=`.
  - Forensic codebase search confirmed **zero instances** of `Crypt::encrypt()` or `encrypted` Eloquent casts in the V10 application.
  - Passwords are salted Bcrypt hashes (`$2y$12$`), which verify independently of `APP_KEY`.
  - Gateways were stored in plaintext JSON in `storage/app/gateway_settings.json`.
  - AI settings in SQLite had `ai_api_key = null`.
- **Classification:** **[VERIFIED]**.
- **Resolution:** No legacy database records will break due to an `APP_KEY` change. However, for maximum safety, the migration script will support a `LEGACY_APP_KEY` environment fallback, and all sensitive credentials inserted into V12 (`payment_gateways.secret_key`, `ai_settings.value`) will be encrypted with V12's `APP_KEY`.

---

### Item 10: Private File Storage Architecture & Access Control
- **Prior Claim / Documentation:** Deposit screenshots and ticket attachments were treated as public storage files.
- **Independently Verified Reality:**
  - Filesystem inspection revealed deposit screenshots (`storage/app/public/deposits/`) and support ticket attachments (`storage/app/public/support_attachments/`) were physically located on the public disk in V10, allowing unauthenticated direct URL access (`SEC-04`, `SEC-05`).
- **Classification:** **[VERIFIED]** (Corrected in [DOC-P3-02](file:///f:/laragon/www/Migratepay-V12/docs/phase-3/PHASE-3-EXECUTION-PLAN.md)).
- **Resolution:** In V12, sensitive files are stored exclusively on the non-symlinked `private` disk (`storage/app/private/`) and served strictly through authenticated, policy-gated streaming endpoints (`FileAccessController`).
