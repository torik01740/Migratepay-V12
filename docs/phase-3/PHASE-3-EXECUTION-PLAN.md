# MigratePay V12: Phase 3 Execution & Security Foundation Plan

> **Document ID:** `DOC-P3-02` (Revised & Authoritative)  
> **Source Database:** `F:\laragon\www\migratepay\database\database.sqlite` (Verified Canonical Source)  
> **Destination Database:** MySQL 8.0.30 (Laragon) — Target Database: `migratepay_v12`  
> **Phase:** 3 — Database & Security Core Foundation  
> **Status:** REVISED & PREPARED FOR OWNER REVIEW — AWAITING EXPLICIT AUTHORIZATION  
> **Execution Boundary:** Implementation remains strictly blocked until explicit owner authorization.

---

## 1. Executive Summary & Objective

This plan defines the step-by-step engineering roadmap to implement the database and security core of MigratePay V12 on MySQL 8.0.30, resolving all audited vulnerabilities (`SEC-01` to `SEC-08`), establishing decimal-safe financial precision, enforcing robust RBAC and private file protection, and preparing a safe, repeatable, dry-run-capable historical migration path.

### Absolute Hard Constraints
1. **Zero Destructive Commands:** Strictly prohibited from executing `migrate:fresh`, `db:wipe`, `DROP DATABASE`, or `TRUNCATE`.
2. **Implementation Gate:** No schema modifications, migrations, or data import operations will be initiated during the audit stage.
3. **Primary Key Invariance:** Source primary keys (`id`: 1, 2, 3, 4, 5, 7, 8) must be preserved exactly. Missing ID 6 is maintained as an authentic sequence gap.
4. **Separate Authorization:** Historical data migration requires independent, explicit written authorization following successful schema and test validation.

---

## 2. Environment Preparation & Database Infrastructure

### 2.1 Destination Database Isolation
Inspection of `F:\laragon\data\mysql-8` confirmed existing staging databases (`migratepay`, `migratepay-v13`). To ensure absolute isolation and prevent table pollution:
- The target database will be cleanly isolated as **`migratepay_v12`**.
- Character Set: `utf8mb4` | Collation: `utf8mb4_unicode_ci`.

### 2.2 Least-Privilege Database Accounts
Two separate database accounts are defined to enforce separation between DDL maintenance and runtime application access:

1. **Schema Maintenance / Migration Account (`migratepay_migrator`):**
   - **Privileges:** `SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, INDEX, DROP, REFERENCES, LOCK TABLES` on `migratepay_v12.*`.
   - **Usage:** Used strictly during deployments, CI/CD, and CLI maintenance (`php artisan migrate`, `php artisan db:*`).
   ```sql
   CREATE USER IF NOT EXISTS 'migratepay_migrator'@'127.0.0.1' IDENTIFIED BY 'MigrationSecureVaultPass2026!';
   GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, INDEX, DROP, REFERENCES, LOCK TABLES ON migratepay_v12.* TO 'migratepay_migrator'@'127.0.0.1';
   FLUSH PRIVILEGES;
   ```

2. **Application Runtime Account (`migratepay_app`):**
   - **Privileges:** `SELECT, INSERT, UPDATE, DELETE` on `migratepay_v12.*` (Strictly **NO DDL** — no `CREATE`, no `ALTER`, no `DROP`).
   - **Usage:** Configured in production `.env` for the web server runtime.
   ```sql
   CREATE USER IF NOT EXISTS 'migratepay_app'@'127.0.0.1' IDENTIFIED BY 'AppRuntimeSecurePass2026!';
   GRANT SELECT, INSERT, UPDATE, DELETE ON migratepay_v12.* TO 'migratepay_app'@'127.0.0.1';
   FLUSH PRIVILEGES;
   ```

### 2.3 Pre-Migration Backup & Restoration Procedures
Before any DDL or migration is executed against MySQL:
1. **Pre-Migration Snapshot:**
   ```bash
   mysqldump -u root -h 127.0.0.1 --single-transaction --routines --triggers --databases migratepay_v12 > F:\laragon\www\Migratepay-V12-backups\pre_migration_baseline.sql
   ```
2. **Rollback Procedure:**
   In the event of an unexpected migration interruption:
   - Put application into maintenance mode: `php artisan down`.
   - Restore database: `mysql -u root -h 127.0.0.1 migratepay_v12 < F:\laragon\www\Migratepay-V12-backups\pre_migration_baseline.sql`.
   - Clear application cache: `php artisan optimize:clear`.
   - Return application online: `php artisan up`.

---

## 3. Sub-Phase Implementation Blueprint

### 3.1 Phase 3A: Database Migrations Implementation
All 22 verified tables plus V12 CMS/vault additions will be created on MySQL with strict types:
- **`users` Table:** Adapted in `database/migrations/0001_01_01_000000_create_users_table.php` to prevent duplicate tables. Enforces all 40 verified columns, `wallet_balance DECIMAL(15, 2) DEFAULT 0.00`, `role VARCHAR(50) DEFAULT 'user'`, `status VARCHAR(50) DEFAULT 'Active'`, and `verification_status VARCHAR(50) DEFAULT 'NOT_VERIFIED'`.
- **`deals` Table (28 Columns):** Enforces `DECIMAL(15, 2)` on all monetary fields (`amount`, `buyer_pays`, `seller_gets`, `charge_total`, `custom_service_charge`, `original_charge`, `coupon_discount`); JSON `workspace`; foreign keys to `users`, `categories`, and `coupons`.
- **`transactions` Table (11 Columns):** Enforces `DECIMAL(15, 2)` on `amount`, `balance_before`, `balance_after`; polymorphic references (`reference_type`, `reference_id`); indexed on `(user_id, created_at)`.
- **`deposits` Table (13 Columns):** Enforces `DECIMAL(15, 2)` amount; `screenshot_path` referencing private disk; foreign key to `users` and `reviewed_by`.
- **`withdrawal_methods` (14 Columns) & `withdrawal_requests` (15 Columns):** Enforces `DECIMAL(15, 2)` on `amount`, `fee`, `net_amount`; unique `reference_id`; foreign keys to `users` and `withdrawal_methods`.
- **`reports` (11 Columns) & `reviews` (12 Columns):** Four granular ratings (`communication`, `behavior`, `delivery`, `reliability`), `role`, and unique index on `(deal_id, reviewer_id)`.
- **`support_tickets` (11 Columns) & `support_ticket_messages` (10 Columns):** `support_tickets` with **nullable `user_id`** and added `guest_email` column (`SEC-07`); `support_ticket_messages` with private attachment path.
- **AI Tables:** `ai_settings` (5-col key-value store), `ai_knowledge_bases` (8-col FAQ store), `ai_chat_histories` (8-col session store).
- **CMS & Vault Tables:**
  - `payment_gateways`: Database credential vault replacing insecure JSON file (`SEC-01`).
  - `system_settings`: Central key-value configuration replacing client `localStorage`.
  - `cms_pages` & `blog_posts`: Content management tables.

### 3.2 Phase 3B: Authentication, RBAC & API Route Protection
1. **Admin Middleware Registration:**
   Register `admin` middleware in `bootstrap/app.php` mapped to `\App\Http\Middleware\AdminMiddleware::class`.
2. **Role Verification:**
   Enforce `$user->role === 'admin'` checking against verified role column.
3. **OTP Security Hardening (`SEC-03`):**
   Remove debug OTP leakage in `AuthController::sendOtp()` / `verifyOtp()`.
4. **API Route Architecture (Preserving Frontend Consumers):**
   - **Public / Customer Endpoints:**
     - `GET /api/users/{userId}/reviews` remains accessible to authenticated users (and public profile visitors), preserving `resources/js/api/userAndSupport.js`.
     - `GET /api/public/categories`, `GET /api/public/calculate-charge`, `GET /api/public/banners` remain public.
   - **Administrative Endpoints (`SEC-02`):**
     - Canonical routes grouped under `Route::middleware(['auth', 'admin'])->prefix('api/admin')->group(...)`.
     - To ensure `resources/js/api/adminApi.js` (`apiClient.get('/api/users')`, `put('/api/users/{id}/status')`, `put('/api/users/{id}/verification')`) continues to function without breaking, route aliases or Policy authorization checks on `UserController` will enforce admin rights on administrative methods.

### 3.3 Phase 3C: Private File Storage Architecture (`SEC-04`, `SEC-05`)
1. **Storage Disk Configuration:**
   Configure `private` disk in `config/filesystems.php`:
   ```php
   'private' => [
       'driver' => 'local',
       'root' => storage_path('app/private'),
       'visibility' => 'private',
       'throw' => false,
   ],
   ```
2. **Authorized Streaming Controller (`FileAccessController`):**
   - `GET /api/private/deposits/{filename}`: Gated by `DepositPolicy` (accessible ONLY to deposit owner or admin).
   - `GET /api/private/support-attachments/{filename}`: Gated by `SupportTicketPolicy` (accessible ONLY to ticket owner or admin).
   - `GET /api/private/deals/{dealId}/deliverables/{filename}`: Gated by `DealPolicy` (accessible ONLY to deal buyer, seller, or admin).
   - Responses streamed with `Storage::disk('private')->response($path)` and headers `Cache-Control: no-store, private`.

### 3.4 Phase 3D: Payment Gateway Vault Implementation (`SEC-01`)
1. **Database Vault Model:**
   Create `PaymentGateway` model with encrypted casting on sensitive keys:
   ```php
   protected $casts = [
       'api_key' => 'encrypted',
       'secret_key' => 'encrypted',
       'store_password' => 'encrypted',
       'is_active' => 'boolean',
   ];
   ```
2. **Public Gateway Endpoint (`finance.js` Parity):**
   - Route: `GET /api/public/gateway-settings` (and alias `GET /api/payment-gateways/public`).
   - Returns strictly non-sensitive display fields: `id`, `name`, `slug`, `icon`, `instructions`, `currency`, `fee_percentage`, `fee_fixed`.
   - **Zero Secrets Guarantee:** API keys, secret keys, and passwords are never returned in public payloads.
3. **Administrative Gateway Management:**
   - Routes: `GET /api/admin/gateway-settings` and `POST /api/admin/gateway-settings`.
   - Strictly guarded by `['auth', 'admin']` middleware, returning masked credentials.

### 3.5 Phase 3E: Foundational Security Hardening
1. **MIME Validation (`SEC-06`):**
   Implement `UploadMediaRequest` with strict validation: `file|mimes:jpeg,png,jpg,pdf|max:10240`.
2. **Guest Escalation Security (`SEC-07`):**
   Refactor `AiSupportController::escalateChat()` to accept nullable `user_id` and capture `guest_email`, eliminating the fallback to `user_id = 1` (John Doe).
3. **Exclusion of Development Artifacts (`SEC-08`):**
   Confirm exclusion of `test_login.php` and `reset_password.php`.

### 3.6 Phase 3F: Automated Security & Regression Test Suite
Develop comprehensive automated PHPUnit/Pest feature tests in `tests/Feature/Security/`:
1. `RouteBarrierTest.php`: Confirms unauthenticated callers and non-admin users receive 401/403 on all `/api/admin/*` routes.
2. `OtpSecurityTest.php`: Confirms OTP generation does not leak plaintext codes in responses.
3. `PrivateFileAccessTest.php`: Confirms deposit slips and support attachments reject unauthorized access.
4. `GatewayVaultTest.php`: Confirms `GET /api/public/gateway-settings` never exposes API keys or secrets.
5. `DatabaseIntegrityTest.php`: Confirms foreign key cascade actions, unique constraints, and decimal precision.

---

## 4. Historical Data Migration Strategy (Safe, Repeatable, Dry-Run Ready)

### 4.1 Command Specification
An artisan command `php artisan app:migrate-v10-data` will be constructed with the following capabilities:
- `--dry-run`: Reads all source records, validates type transformations, asserts referential integrity, and computes balance reconciliation without performing any database writes.
- `--verify-only`: Compares record counts, IDs, and financial totals between SQLite and MySQL.
- `--batch-size=100`: Processes records in manageable transaction batches.

### 4.2 Precision & Data Transformation Rules
- **Decimal Safety:** All monetary conversions (`wallet_balance`, `amount`, `fee`, `charge_total`) use BCMath string formatting (`number_format((float)$val, 2, '.', '')`). PHP binary floats are strictly forbidden.
- **Primary Key Parity:** Original integer IDs (`1, 2, 3, 4, 5, 7, 8`) are forced into auto-increment tables. Sequence gap for ID 6 is preserved.
- **Password Hashes:** Bcrypt hashes (`$2y$12$`) are imported intact, enabling seamless login for all existing users.
- **Token Invalidation:** Legacy `password_reset_tokens` (pulok@gmail.com) are purged; users will request fresh tokens in V12.

### 4.3 Explicit Authorization Gate
**Authoritative Rule:** Authoring and testing the migration script in `--dry-run` mode is permitted during preparation. Executing the actual data migration into MySQL requires **separate explicit written authorization** from the project owner.
