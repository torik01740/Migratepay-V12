# MigratePay V12: Phase 3 Security Implementation Checklist & Governance

> **Document ID:** `DOC-P3-SECURITY-CHECKLIST`  
> **Phase:** 3 — Database & Security Core Foundation  
> **Scope:** Hardening Specifications, Vulnerability Remediation & Verification Gates  
> **Status:** AUDITED & PREPARED FOR IMPLEMENTATION

---

## 1. Vulnerability Remediation Matrix

| Vuln ID | Description & Risk | V10 Status | V12 Remediation Specification | Verification Criteria | Status |
| :--- | :--- | :--- | :--- | :--- | :---: |
| **`SEC-01`** | **Payment Gateway Credential Exposure**<br>Unauthenticated `GET /api/public/gateway-settings` served raw JSON containing secret keys, store passwords, and API tokens. | High Risk Vulnerability | Replace raw JSON file with `payment_gateways` database vault table. Cast credentials with `'encrypted'`. Route `GET /api/public/gateway-settings` returns strictly public display fields. | Response never contains `secret_key`, `store_password`, or `api_key`. Automated test asserts keys omitted. | **Pending Approval** |
| **`SEC-02`** | **Missing Administrative Route Barrier**<br>`/api/users` and related administrative endpoints lacked explicit role-based access control. | High Risk Vulnerability | Implement `AdminMiddleware` checking `$user->role === 'admin'`. Protect administrative endpoints under `['auth', 'admin']` while maintaining public review endpoint `GET /api/users/{userId}/reviews`. | Authenticated non-admin receives `403 Forbidden`. Unauthenticated receives `401 Unauthorized`. | **Pending Approval** |
| **`SEC-03`** | **Plaintext OTP Disclosure**<br>OTP generation response leaked plaintext OTP code in response payload for debugging. | Critical Auth Flaw | Remove OTP from API response in `AuthController::sendOtp()` and `verifyOtp()`. Enforce 5-minute expiry and 3-attempt brute-force limit. | Response returns only `{ "success": true, "message": "OTP sent" }`. Plaintext code absent from payload. | **Pending Approval** |
| **`SEC-04`** | **Public Deal Deliverable Exposure**<br>Uploaded deliverables were saved to public storage. | Confidentiality Risk | Move deliverables to `private` disk (`storage/app/private/`). Stream via `FileAccessController` gated by `DealPolicy`. | Direct URL returns 404/403. Non-party receives 403. Deal parties stream file successfully. | **Pending Approval** |
| **`SEC-05`** | **Public Deposit Receipt Exposure**<br>Bank deposit receipts with sensitive account data were stored in `storage/app/public/deposits/`. | High Risk Data Leak | Store all deposit screenshots on `private` disk (`storage/app/private/deposits/`). Stream via `FileAccessController` gated by `DepositPolicy`. | Direct URL returns 404. Non-owner receives 403. Deposit owner and admin stream successfully. | **Pending Approval** |
| **`SEC-06`** | **Unrestricted File Upload**<br>File upload endpoints lacked strict MIME-type and magic-byte inspection. | Remote Code Execution | Implement `UploadMediaRequest` validating `file|mimes:jpeg,png,jpg,pdf|max:10240`. Sanitize filenames. | Malicious file types (.php, .exe, .sh) rejected with 422 Unprocessable Entity. | **Pending Approval** |
| **`SEC-07`** | **Guest Chat Escalation Identity Hijack**<br>Guest AI escalation hardcoded fallback to `user_id = 1` (John Doe). | Identity Integrity Flaw | Make `support_tickets.user_id` nullable. Add `guest_email VARCHAR(255)`. Capture guest email without attaching ticket to User 1. | Guest escalation creates ticket with `user_id = null`, `guest_email = ...`. User 1 tickets remain unaffected. | **Pending Approval** |
| **`SEC-08`** | **Exclusion of Stale Testing Scripts**<br>Root directory contained standalone test scripts (`test_login.php`, `reset_password.php`). | Backdoor Risk | Ensure all standalone testing scripts are excluded from V12 repository and deployment packages. | Files do not exist in V12 codebase. | **Verified (Clean)** |

---

## 2. Infrastructure & Access Control Security Checklist

### 2.1 Database Security Checklist
- [ ] **Dedicated Runtime User:** Database account `migratepay_app` granted strictly DML (`SELECT, INSERT, UPDATE, DELETE`) with zero DDL privileges (`NO CREATE, NO ALTER, NO DROP`).
- [ ] **Dedicated Migrator User:** Database account `migratepay_migrator` with DDL privileges used strictly for migration execution and CLI maintenance.
- [ ] **Database Network Isolation:** MySQL listening strictly on `127.0.0.1:3306`, rejecting external public interfaces.
- [ ] **Credential Protection:** Database passwords stored exclusively in `.env`, excluded from git repository (`.gitignore` verified).

### 2.2 Storage & File System Checklist
- [ ] **Private Storage Disk Root:** `storage/app/private` configured with `visibility => 'private'`.
- [ ] **No Symlink to Private Root:** `php artisan storage:link` links strictly `storage/app/public` to `public/storage`. `storage/app/private` has no public symlink.
- [ ] **Private Header Enforcement:** All authorized streamed files include `Cache-Control: private, no-cache, no-store, must-revalidate` and `Pragma: no-cache`.

### 2.3 Authentication & Session Security Checklist
- [ ] **Bcrypt Hash Compatibility:** Standard `$2y$12$` Bcrypt password hashes preserved from V10 without alteration.
- [ ] **Session Security:** `SESSION_ENCRYPT=true`, `SESSION_HTTP_ONLY=true`, `SESSION_SAME_SITE=lax`.
- [ ] **Token Invalidation:** Legacy `password_reset_tokens` purged from migration; zero migration of expired or unhashed reset tokens.
- [ ] **Sanctum API Guard:** API routes enforce session-based stateful authentication or scoped Bearer tokens.

---

## 3. Automated Security Feature Test Specifications

The following automated tests in `tests/Feature/Security/` must pass with 100% green status prior to Phase 3 completion:

```
tests/Feature/Security/
├── RouteBarrierTest.php
│   ├── test_guest_cannot_access_admin_routes() [Assert 401/403]
│   ├── test_customer_cannot_access_admin_routes() [Assert 403]
│   ├── test_admin_can_access_admin_routes() [Assert 200]
│   └── test_customer_can_access_user_reviews() [Assert 200]
│
├── OtpSecurityTest.php
│   ├── test_otp_response_does_not_contain_plaintext_code()
│   ├── test_otp_expires_after_5_minutes()
│   └── test_otp_rate_limiting_enforced()
│
├── PrivateFileAccessTest.php
│   ├── test_unauthenticated_cannot_stream_deposit_receipt() [Assert 401]
│   ├── test_other_user_cannot_stream_deposit_receipt() [Assert 403]
│   ├── test_deposit_owner_can_stream_receipt() [Assert 200]
│   └── test_admin_can_stream_deposit_receipt() [Assert 200]
│
├── GatewayVaultTest.php
│   ├── test_public_gateway_endpoint_returns_only_public_fields()
│   ├── test_public_gateway_endpoint_omits_api_key_and_secret()
│   └── test_admin_can_update_gateway_credentials_with_encryption()
│
└── DatabaseIntegrityTest.php
    ├── test_users_wallet_balance_enforces_decimal_precision()
    ├── test_deals_monetary_columns_enforce_decimal_precision()
    └── test_foreign_key_cascades_and_nullifications()
```
