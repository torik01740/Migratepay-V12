# MigratePay V12: Phase 3 Migration Readiness & Security Governance

> **Document ID:** `DOC-P3-07`  
> **Source Database:** `F:\laragon\www\migratepay\database\database.sqlite`  
> **Destination Environment:** MySQL 8.0.30 (Laragon)  
> **Phase:** 3 — Database & Security Core Foundation  
> **Audit Status:** VERIFIED SECURITY GOVERNANCE & MIGRATION READINESS  

---

## 1. Executive Summary & Governance Principles

This document formalizes the security policies, token invalidation procedures, credential vault boundaries, and readiness gating for Phase 3 database execution.

---

## 2. Authentication & Sensitive Records Governance

### 2.1 Password-Reset Tokens Policy
- **Observed V10 State:** The `password_reset_tokens` table in SQLite contains 1 row.
- **Mandatory Policy:** **Do NOT migrate existing password-reset tokens.**
- **Rationale:** Stored tokens may be stale, expired, or compromised by past OTP disclosure flaws (`SEC-03`). Importing old tokens introduces account-takeover risks.
- **V12 Action:** The V12 `password_reset_tokens` table will initialize empty. Any user requesting a password reset will generate a fresh, cryptographically secure OTP with a strict 15-minute expiration window and rate limiting.

### 2.2 Password Hash Preservation & Compatibility
- **Observed V10 State:** Passwords in `users.password` are hashed using standard Bcrypt (`$2y$12$...` / `$2y$10$...`).
- **Compatibility:** Laravel 12's `Hash` facade fully supports `$2y$` Bcrypt hashes.
- **Preservation Action:** Existing customer password hashes will be migrated 1:1 without alteration. Users will be able to log in with their existing passwords immediately post-migration.

---

## 3. Credential Vault Segregation & Selective Encryption

To eliminate secret disclosure risks while maintaining high-performance queries, sensitive fields are strictly segregated and selectively encrypted:

```
┌────────────────────────────────────────────────────────────────────────┐
│                   CREDENTIAL VAULT ARCHITECTURE                        │
├──────────────────────────┬─────────────────────────────────────────────┤
│ Domain Vault             │ Storage & Encryption Protocol               │
├──────────────────────────┼─────────────────────────────────────────────┤
│ AI Support Credentials   │ Stored in `ai_settings` table.              │
│ (OpenAI API Keys)        │ `api_key` attribute encrypted via           │
│                          │ Laravel `encrypted` cast (AES-256-CBC).    │
├──────────────────────────┼─────────────────────────────────────────────┤
│ Payment Gateway Secrets  │ Stored in `payment_gateways` table.         │
│ (Stripe Secret Key,      │ `store_password`, `app_secret`, and         │
│  bKash Secret, etc.)     │ `secret_key` encrypted via `encrypted` cast.│
├──────────────────────────┼─────────────────────────────────────────────┤
│ Public Gateway Metadata  │ `name`, `slug`, `icon`, `instructions`,     │
│ (Unencrypted)            │ `currency`, `fee_percentage` remain raw     │
│                          │ text for indexing and public API delivery.  │
└──────────────────────────┴─────────────────────────────────────────────┘
```

**Zero Disclosure Guarantee:** Neither API keys, store passwords, nor private encryption keys are printed in documentation, commit messages, or public API responses.

---

## 4. Phase 3 Non-Destructive Readiness Checklist

Before any database operations commence, the following checkpoints must be satisfied:

| Checkpoint | Requirement | Verification Method | Status |
| :--- | :--- | :--- | :---: |
| **Source Integrity** | V10 SQLite file uncorrupted | `PRAGMA integrity_check;` returned `ok` | **SATISFIED** |
| **Financial Parity** | All 7 users reconciled with 0 variance | Ledger math proven in `DOC-P3-03` | **SATISFIED** |
| **Referential Integrity**| Zero orphaned foreign keys | Cross-table audit proven in `DOC-P3-05` | **SATISFIED** |
| **Schema Fidelity** | 22 tables mapped with verified column names | Documented in `DOC-P3-04` | **SATISFIED** |
| **MySQL Engine** | MySQL 8.0.30 verified on system | Verified `mysql.exe Ver 8.0.30` | **SATISFIED** |
| **Destination Safety** | V12 baseline has 0 user records | Verified 0 rows in baseline `users` | **SATISFIED** |
| **Prohibition Enforced** | Zero destructive commands run | Confirmed clean Git working tree | **SATISFIED** |
| **Laragon MySQL Status** | Service running on port 3306 | Service currently stopped | **PENDING OWNER START** |
| **Owner Authorization** | Explicit approval to implement | Awaiting owner sign-off | **PENDING OWNER APPROVAL** |
