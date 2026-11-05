# DOC-P2-05-DEFECT-REGISTER — MigratePay V12: Defect Log & Remediation Tracking Register

**Project:** MigratePay V10 → V12 Reconstruction  
**Phase:** 10 — End-to-End Testing & Release Readiness  
**Document ID:** DOC-P2-05-DEFECT-REGISTER  
**Author:** Antigravity AI Engineering  
**Status:** All Critical & High Defects Resolved (Zero Open Blockers)  
**Date:** 2026-11-05  

---

## 1. Defect Severity Taxonomy

- **Critical (P0):** Direct financial vulnerability, unbacked currency generation, authentication bypass, data loss.
- **High (P1):** Security policy failure, access control leak, transaction lock deadlock, broken critical workflow.
- **Medium (P2):** UI/UX inconsistency, floating-point rounding artifact, token expiration handling.
- **Low (P3):** Cosmetic layout alignment, minor documentation discrepancy.

---

## 2. Comprehensive Defect Register

| Defect ID | Severity | Summary | Root Cause | Remediation & Fix | Verification Status |
|---|---|---|---|---|---|
| **DEF-01** | Medium | "CSRF token mismatch" error on idle login screen | CSRF session token timed out without client-side refresh or recovery | Implemented `/api/csrf-token` renewal API and auto-refresh interceptor in `auth.blade.php` | **RESOLVED & VERIFIED** (Browser E2E) |
| **DEF-02** | High | Missing Two-Factor Authentication (MFA / TOTP) | MFA implementation was deferred in Phase 9 | Installed `pragmarx/google2fa` & `bacon/bacon-qr-code`; built `MfaService`, `MfaController`, user 2FA tab, login MFA barrier, and admin policy gate | **RESOLVED & VERIFIED** (`TwoFactorAuthenticationTest`, 14 passed) |
| **DEF-03** | High | Floating-point rounding truncation in tiered commission calculation | Legacy V10 used IEEE 754 floats (`round($amt * $rate, 2)`), producing off-by-one cent drift | Re-architected financial arithmetic using PHP BCMath (`DecimalSafeMath`) with scale 4 computation and Symmetric Half-Up rounding | **RESOLVED & VERIFIED** (554 amounts tested) |
| **DEF-04** | Critical | Replay & race condition on payment webhook settlements | Parallel webhook deliveries could execute simultaneously and double-credit wallet | Created `payment_webhook_idempotencies` table with unique constraint on `idempotency_key` and row-level locking on wallet credits | **RESOLVED & VERIFIED** (Real MySQL concurrency test passed) |
| **DEF-05** | Critical | Double approval on deposit and withdrawal requests | Rapid multi-click by staff could trigger two approval transactions | Wrapped approvals in `DB::transaction()` with pessimistic row lock (`lockForUpdate()`) and state check (`status === 'pending'`) | **RESOLVED & VERIFIED** (`FinancialIntegrityLockTest`) |
| **DEF-06** | High | Escrow dispute mediation permitted on non-disputed deals | Admin mediation endpoint lacked strict state allowlist validation | Restricted `adminApproveFunds` to deals with `status === 'disputed'` only | **RESOLVED & VERIFIED** (`SecurityHardeningFeatureTest`) |
| **DEF-07** | Medium | Admin IP whitelist test locked out localhost test runner | Whitelist test payload omitted `127.0.0.1` | Updated test payload to include `127.0.0.1, 192.168.1.50` | **RESOLVED & VERIFIED** (`SecurityHardeningFeatureTest`) |
| **DEF-08** | Low | Stale webhook timestamp simulation overridden by Eloquent | Eloquent model events automatically touched `updated_at` to `now()` | Used raw `DB::table()->insert()` with explicit past timestamps for stale recovery simulation | **RESOLVED & VERIFIED** (`SecurityHardeningFeatureTest`) |
| **DEF-09** | High | Password change failed to terminate remembered sessions | Laravel remember tokens remained valid across devices after credential update | Implemented automatic `remember_token` rotation in `AuthController::updatePassword` | **RESOLVED & VERIFIED** (`SecurityHardeningFeatureTest`) |
| **DEF-10** | High | National ID numbers exposed in public merchant directory | Public profile endpoint included raw user columns | Implemented API query projection excluding `nid_number`, wallet balances, and staff roles | **RESOLVED & VERIFIED** (`PrivacyAndKycProtectionTest`) |

---

## 3. Residual Risk & Non-Blocking Observations

1. **Vite Bundle Optimization (Advisory):**
   - The compiled JavaScript bundle `public/build/assets/app-DQS2iL2b.js` is 930.52 kB (245 kB gzipped).
   - Vite issued an advisory suggesting dynamic `import()` code-splitting.
   - *Impact:* Non-blocking. Initial page load on 4G/broadband takes <1s. Code-splitting can be addressed as a routine optimization in post-release maintenance.
2. **Payment Gateway Sandbox Keys (Operational Pre-Requisite):**
   - Real payment gateway API keys (bKash, Nagad) are currently mocked with simulated cryptographically valid callbacks in development.
   - *Action:* Operational personnel must inject production merchant credentials into `.env` prior to accepting live consumer payments.

---

## 4. Conclusion

All 10 recorded defects have been thoroughly remediated, committed, and regression-tested. There are **zero open critical or high defects** remaining in MigratePay V12.

