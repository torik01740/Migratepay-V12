# DOC-P2-04: Security and Permission Matrix

**Project:** MigratePay V10 → V12 Reconstruction  
**Phase:** 9 of 10 — Security, Financial Integrity & Privacy Hardening  
**Document ID:** DOC-P2-04-SECURITY-AND-PERMISSION-MATRIX  
**Date:** November 4, 2026  

---

## 1. Overview of Identity & Role Hierarchy

MigratePay V12 enforces a strict separation between public customers and internal administrative staff. Roles are stored in `users.role` and normalized for granular server-side enforcement within [AdminMiddleware.php](file:///F:/laragon/www/Migratepay-V12/app/Http/Middleware/AdminMiddleware.php) and individual controllers.

| Role Identifier | Role Category | Scope & Responsibilities |
| :--- | :--- | :--- |
| `admin` | Primary Administrator | Unrestricted system clearance. Full authority over platform configuration, finances, security policies, staff management, and system settings. |
| `co-admin` | Operational Administrator | High-level operational authority over transactions, deals, and content. Barred from tampering with root admin accounts, payment gateway credentials, or global security policies. |
| `editorial` / `editorial-team` | Content & Editorial | Authority limited to CMS pages, blog articles, deal categories, marketing banners, media library, and announcements. Strictly barred from financial data, user profiles, and gateways. |
| `support` / `support-team` | Customer Support | Authority limited to customer support tickets, dispute investigations, AI knowledge base maintenance, and counterparty reviews. Barred from approving payouts or modifying users. |
| `user` | Customer / Counterparty | Standard customer permissions. Limited strictly to authenticated user dashboard, deal participation, wallet deposits, and payout requests. Completely barred from `/admin*` and `/api/admin/*`. |

---

## 2. Server-Side Administrative Route Permission Matrix

The following table details the server-side authorization enforcement applied to every administrative endpoint:

| Endpoint Group | Method & Route Pattern | `admin` | `co-admin` | `editorial` | `support` | `user` |
| :--- | :--- | :---: | :---: | :---: | :---: | :---: |
| **Admin UI Shell** | `GET /admin/{any?}` | Allowed | Allowed | Allowed | Allowed | 403 / Redirect |
| **User Directory** | `GET /api/admin/users` | Allowed | Allowed | Denied (403) | Allowed (Read) | Denied (403) |
| **User Status Update** | `PUT /api/admin/users/{user}/status` | Allowed | Restricted* | Denied (403) | Denied (403) | Denied (403) |
| **User KYC Verification**| `PUT /api/admin/users/{user}/verification`| Allowed | Restricted* | Denied (403) | Denied (403) | Denied (403) |
| **Deposits List** | `GET /api/admin/deposits` | Allowed | Allowed | Denied (403) | Allowed (Read) | Denied (403) |
| **Deposit Approval** | `POST /api/admin/deposits/{id}/approve` | Allowed | Allowed | Denied (403) | Denied (403) | Denied (403) |
| **Deposit Rejection** | `POST /api/admin/deposits/{id}/reject` | Allowed | Allowed | Denied (403) | Denied (403) | Denied (403) |
| **Withdrawals List** | `GET /api/admin/withdrawals` | Allowed | Allowed | Denied (403) | Allowed (Read) | Denied (403) |
| **Withdrawal Approval**| `POST /api/admin/withdrawals/{id}/approve`| Allowed | Allowed | Denied (403) | Denied (403) | Denied (403) |
| **Withdrawal Rejection**| `POST /api/admin/withdrawals/{id}/reject`| Allowed | Allowed | Denied (403) | Denied (403) | Denied (403) |
| **Deals Directory** | `GET /api/admin/deals` | Allowed | Allowed | Denied (403) | Allowed (Read) | Denied (403) |
| **Deal Fund Release** | `POST /api/admin/deals/{id}/approve-funds`| Allowed | Allowed | Denied (403) | Denied (403) | Denied (403) |
| **Dispute Reports** | `GET /api/admin/reports` | Allowed | Allowed | Denied (403) | Allowed | Denied (403) |
| **Dispute Resolution** | `POST /api/admin/reports/{id}/resolve` | Allowed | Allowed | Denied (403) | Allowed | Denied (403) |
| **CMS Pages** | `GET/POST/PUT/DELETE /api/admin/cms*` | Allowed | Allowed | Allowed | Denied (403) | Denied (403) |
| **Blog Management** | `GET/POST/PUT/DELETE /api/admin/blog*` | Allowed | Allowed | Allowed | Denied (403) | Denied (403) |
| **Categories** | `* /api/admin/categories*` | Allowed | Allowed | Allowed | Denied (403) | Denied (403) |
| **Marketing Banners**| `GET/POST/PUT/DELETE /api/admin/banners*`| Allowed | Allowed | Allowed | Denied (403) | Denied (403) |
| **Support Tickets** | `POST /api/admin/support-tickets/{id}/*` | Allowed | Allowed | Denied (403) | Allowed | Denied (403) |
| **AI Knowledge Base** | `GET/POST/PUT/DELETE /api/admin/knowledge-base*` | Allowed | Allowed | Denied (403) | Allowed | Denied (403) |
| **Gateway Settings** | `GET /api/admin/gateway-settings` | Allowed | Allowed (Masked)| Denied (403) | Denied (403) | Denied (403) |
| **Gateway Credentials**| `POST /api/admin/gateway-settings/{slug}`| Allowed | Denied (403) | Denied (403) | Denied (403) | Denied (403) |
| **Security Policies** | `GET /api/admin/security/policies` | Allowed | Allowed | Denied (403) | Denied (403) | Denied (403) |
| **Update Policies** | `POST /api/admin/security/policies` | Allowed | Denied (403) | Denied (403) | Denied (403) | Denied (403) |
| **Financial Ledger** | `GET /api/admin/reports/ledger` | Allowed | Allowed | Allowed (Analytics)| Denied (403)| Denied (403) |
| **Private KYC Stream** | `GET /api/private/kyc/{user}` | Allowed | Allowed (Compliance)| Denied (403) | Denied (403) | Owner Only (Others 403) |

*\*Note on Restricted Operations:* Co-Admins cannot modify any user with role `admin` (privilege escalation protection). Private KYC identity documents are strictly barred from editorial and support staff to enforce least-privilege customer privacy.

---

## 3. Privilege Escalation Prevention Rules

1. **Root Administrator Protection:**
   - [AdminMiddleware.php](file:///F:/laragon/www/Migratepay-V12/app/Http/Middleware/AdminMiddleware.php) and [UserController.php](file:///F:/laragon/www/Migratepay-V12/app/Http/Controllers/UserController.php) block non-admins from modifying root administrator accounts.
   - If a `co-admin` attempts to deactivate, suspend, or alter the verification status of a user where `role === 'admin'`, the server immediately rejects the request with HTTP 403 Forbidden and records an audit warning (`rbac.privilege_escalation_blocked`).
2. **Self-Suspension Guard:**
   - An administrator cannot deactivate or suspend their own account (`$user->id === $authUser->id`), returning HTTP 422 Unprocessable Entity.
3. **Gateway Credential Isolation:**
   - Only `admin` role users may update payment gateway credentials or toggle gateway status. Attempts by other staff roles return HTTP 403 Forbidden.
4. **Security Policy Lock:**
   - Only `admin` role users may update platform security thresholds, failed login limits, or IP whitelists.

---

## 4. Authentication Throttling & Session Governance

| Security Control | Parameter | Enforcement Mechanism |
| :--- | :--- | :--- |
| **Failed Login Threshold** | Dynamic (Default: 5 attempts) | Configured in `SystemSetting: security.max_failed_logins`. Enforced via `RateLimiter::tooManyAttempts()`. |
| **Lockout Window** | 300 Seconds (5 Minutes) | IP/Email rate-key decay window. During lockout, returns HTTP 429 Too Many Requests with retry-after header. |
| **OTP Verification Rate** | 3 Attempts / 300 Seconds | Prevents brute-forcing of password reset OTP codes. Returns HTTP 429 on exhaustion. |
| **Session Invalidation** | On Password Change / Reset | Calls `Auth::logoutOtherDevices()` and rotates `remember_token` via `Str::random(60)`. |
| **Session Idle Timeout** | Dynamic (Default: 30 Minutes) | Server-side enforcement via [EnforceSessionTimeout.php](file:///F:/laragon/www/Migratepay-V12/app/Http/Middleware/EnforceSessionTimeout.php) tracking `last_activity_time`. Inactive sessions are invalidated and redirected to login. |
| **Two-Factor Auth (2FA)** | Policy Setting (Partial) | Managed via `security.enforce_2fa`. Global policy setting stored; full TOTP authenticator app enrollment is scheduled for Phase 10 / dedicated MFA package integration. |
| **IP Whitelisting** | Optional (Configurable) | Set via `security.ip_whitelist`. When populated, [AdminMiddleware.php](file:///F:/laragon/www/Migratepay-V12/app/Http/Middleware/AdminMiddleware.php) blocks non-listed client IPs with HTTP 403. |

---

## 5. Audit Logging Framework & Credential Sanitization

All security, authorization, and financial state changes produce structured JSON log entries written atomically to `storage/logs/audit.log` via [AuditLogService.php](file:///F:/laragon/www/Migratepay-V12/app/Services/AuditLogService.php).

### Security Protections in Audit Logging:
1. **Automated Sensitive Field Redaction:** Recursively sanitizes payloads, replacing values for `password`, `secret_key`, `api_key`, `otp`, `token`, `remember_token`, and `nid_number` with `[REDACTED]`.
2. **Fail-Safe Non-Blocking I/O:** File system operations are wrapped in safe exception handling. Audit writing failures never crash or abort primary financial transactions.
3. **File System Permissions:** The audit log file is initialized with restrictive `0640` POSIX permissions, preventing unauthorized multi-user host reads.

### Audit Event Taxonomy

| Event Key | Trigger Context | Captured Payload |
| :--- | :--- | :--- |
| `auth.login_success` | User authentication succeeds | User ID, role, client IP, timestamp |
| `auth.login_failed` | Bad credentials submitted | Target email, client IP, timestamp |
| `auth.lockout` | Rate limiter threshold exceeded | Target email, client IP, attempts, retry window |
| `auth.password_changed` | User changes password | User ID, client IP, timestamp |
| `auth.password_reset` | User completes OTP reset | User ID, client IP, timestamp |
| `rbac.unauthorized_access` | Non-staff requests `/api/admin/*` | User ID, role, client IP, target endpoint |
| `rbac.access_denied` | Staff requests unauthorized route | User ID, role, target route, method |
| `rbac.privilege_escalation_blocked` | Co-Admin attempts admin edit | Actor ID, target user ID, client IP |
| `kyc.document_viewed` | Staff accesses private KYC doc | Staff ID, target user ID, timestamp |
| `security.ip_blocked` | Non-whitelisted IP requests admin | User ID, client IP, allowed IP array |
| `security.policies_updated` | Security settings modified | Admin ID, modified policy keys |
| `admin.user_status_updated` | Staff modifies customer status | Admin ID, target user ID, new status |
| `admin.user_verification_updated`| Staff updates KYC verification | Admin ID, target user ID, new KYC status |
| `finance.deposit_approved` | Admin approves deposit | Deposit ID, admin ID, user ID, credited amount |
| `finance.deposit_rejected` | Admin rejects deposit | Deposit ID, admin ID, user ID, rejection reason |
| `finance.withdrawal_approved` | Admin approves payout | Withdrawal ID, admin ID, user ID, amount |
| `finance.withdrawal_rejected` | Admin rejects payout (refunds) | Withdrawal ID, admin ID, user ID, refunded amount |

---

## 6. Database Least-Privilege & Host Isolation Matrix

Database credentials and server permissions have been audited against MySQL 8.0.30 to enforce least privilege:

| User Account | Host | Assigned Privileges | Denied Privileges | Operational Role |
| :--- | :--- | :--- | :--- | :--- |
| `migratepay_app` | `127.0.0.1` | `SELECT, INSERT, UPDATE, DELETE` | `DROP, CREATE, ALTER, INDEX, REFERENCES, GRANT OPTION` | Application web server runtime (DML only) |
| `migratepay_migrator` | `127.0.0.1` | `SELECT, INSERT, UPDATE, DELETE, CREATE, REFERENCES, INDEX, ALTER, LOCK TABLES` | `DROP, GRANT OPTION, SUPER, SHUTDOWN` | Migration runner & artisan CLI (No DROP) |

*Validation Proof:* Verified via `SHOW GRANTS` on MySQL 8.0.30. Neither user possesses `DROP` authority, guaranteeing that accidental or malicious operations cannot delete tables or databases.

