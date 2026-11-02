# MigratePay V10 Test Coverage Audit & V12 Quality Assurance Plan

> **Document ID:** `DOC-P1-12`  
> **Source Project:** `F:\laragon\www\migratepay` (PHPUnit 10.1, 0% custom test coverage)  
> **Destination Project:** `F:\laragon\www\Migratepay-V12` (PHPUnit 11.5 / Pest 3 ready, Laravel 12.70.2)  
> **Audit Status:** Complete forensic analysis & test engineering blueprint  

---

## 1. Executive Summary

A comprehensive inspection of `F:\laragon\www\migratepay\tests\` reveals that the MigratePay V10 application possesses **virtually zero automated test coverage (0%)**:
- The test suite consists solely of two unmodified default skeleton files created by Laravel's installer:
  1. `tests/Feature/ExampleTest.php` (checks `GET /` returns status code 200).
  2. `tests/Unit/ExampleTest.php` (evaluates `$this->assertTrue(true)`).
- **Core Financial Workflows Are Uncovered:** Zero automated assertions exist for wallet balance mutations, escrow locks, fee calculations, deposit approvals, or withdrawal requests.
- **Security Boundaries Are Uncovered:** Zero automated assertions protect against role elevation, unauthorized user management access, or unauthenticated file downloads.
- **Ad-Hoc Manual Scripts Present:** Root directory contains raw PHP utility scripts ([test_login.php](file:///F:/laragon/www/migratepay/test_login.php) and [reset_password.php](file:///F:/laragon/www/migratepay/reset_password.php)) containing plaintext test passwords (`Faitor@911`), which were used in lieu of formal test automation (`SEC-08`).

Rebuilding MigratePay in Laravel 12 requires establishing a robust, automated test harness to eliminate financial regressions and prevent security vulnerabilities.

---

## 2. Forensic Inventory of V10 Tests

| Test File | Test Type | Lines of Code | Description & Assertions | Status |
| :--- | :--- | :--- | :--- | :--- |
| `tests/Feature/ExampleTest.php` | Feature (HTTP) | 19 | Executes `$response = $this->get('/'); $response->assertStatus(200);` | Unmodified skeleton |
| `tests/Unit/ExampleTest.php` | Unit | 16 | Executes `$this->assertTrue(true);` | Unmodified skeleton |
| `test_login.php` (Root) | Ad-Hoc Script | 2 | Bootstraps Laravel kernel, queries `admin@migratepay.com`, tests `Hash::check('Faitor@911', ...)` | **Security Debt / Insecure** |
| `reset_password.php` (Root) | Ad-Hoc Script | 2 | Force-updates `admin@migratepay.com` password to `Faitor@911` | **Security Debt / Insecure** |

**Summary Metric:** Total custom business logic tests: **0**. Total custom assertions: **0**.

---

## 3. Critical Business Workflows Lacking Automated Coverage

The absence of tests creates immense regression risk across the following domains:

```
┌────────────────────────────────────────────────────────────────────────┐
│                   UNTESTED CRITICAL BUSINESS DOMAINS                   │
├──────────────────────────┬─────────────────────────────────────────────┤
│ Financial Operations     │ • Escrow balance locks & unlock races        │
│                          │ • Duplicate payout on approved deals         │
│                          │ • Negative wallet balance prevention        │
│                          │ • Multi-currency rounding & fee precision    │
├──────────────────────────┼─────────────────────────────────────────────┤
│ Security & Access        │ • User management endpoints role barrier    │
│                          │ • Insecure direct object reference on deals  │
│                          │ • Gateway credential endpoint protection    │
│                          │ • Unauthenticated deliverable downloads     │
├──────────────────────────┼─────────────────────────────────────────────┤
│ State Machines           │ • Deal state transitions (draft -> released) │
│                          │ • Deposit state transitions (pending -> ok)  │
│                          │ • Dispute arbitration & balance distribution│
├──────────────────────────┼─────────────────────────────────────────────┤
│ Data Integrity           │ • Database transaction rollback on failures  │
│                          │ • Ledger statement reconciliation            │
└──────────────────────────┴─────────────────────────────────────────────┘
```

---

## 4. Recommended V12 Automated Test Plan

For Laravel 12 reconstruction, a strict testing strategy must be implemented using **PHPUnit 11 / Pest 3** executing against an in-memory SQLite database (`:memory:`) with `RefreshDatabase`.

The test suite must cover 18 mandatory specifications:

### Suite 1: Authentication & Account Lifecycle (`tests/Feature/AuthTest.php`)
- `test_user_can_register_with_valid_credentials()`
- `test_registration_fails_on_duplicate_email()`
- `test_user_cannot_login_with_incorrect_password()`
- `test_user_login_rate_limiting_triggers_after_5_failures()`
- `test_password_reset_otp_is_not_disclosed_in_json_response()`

### Suite 2: Role Authorization & Route Barriers (`tests/Feature/AuthorizationTest.php`)
- `test_unauthenticated_request_to_admin_route_redirects_or_returns_401()`
- `test_authenticated_regular_user_cannot_access_admin_api_users_endpoint()`
- `test_admin_user_can_update_user_status_and_verification()`
- `test_regular_user_cannot_update_other_user_profiles()`

### Suite 3: Wallet & Financial Ledger (`tests/Feature/WalletLedgerTest.php`)
- `test_new_user_initializes_with_zero_or_default_balance()`
- `test_wallet_credit_increments_balance_and_creates_transaction_record()`
- `test_wallet_debit_fails_when_balance_insufficient()`
- `test_wallet_balance_cannot_become_negative()`
- `test_financial_operations_execute_within_database_transactions()`

### Suite 4: Manual Deposit Pipeline (`tests/Feature/DepositWorkflowTest.php`)
- `test_user_can_initiate_manual_deposit_request()`
- `test_deposit_requires_valid_receipt_image_upload()`
- `test_admin_can_approve_deposit_and_funds_credit_to_user_wallet()`
- `test_admin_cannot_approve_already_approved_deposit()`
- `test_admin_can_reject_deposit_with_rejection_reason()`

### Suite 5: Withdrawal Pipeline (`tests/Feature/WithdrawalWorkflowTest.php`)
- `test_user_cannot_withdraw_more_than_available_balance()`
- `test_withdrawal_request_locks_user_balance_or_deducts_pending_amount()`
- `test_withdrawal_applies_configured_fee_percentages()`
- `test_admin_approval_marks_withdrawal_paid()`
- `test_admin_rejection_refunds_funds_back_to_user_wallet()`

### Suite 6: Deal Lifecycle & Escrow State Machine (`tests/Feature/DealEscrowTest.php`)
- `test_buyer_creates_deal_and_escrow_amount_is_locked_from_wallet()`
- `test_buyer_cannot_create_deal_if_wallet_balance_insufficient()`
- `test_seller_can_accept_deal_transitioning_status_to_active()`
- `test_seller_decline_deal_refunds_locked_escrow_to_buyer()`
- `test_seller_can_upload_deliverable_file_transitioning_to_delivered()`
- `test_deliverable_file_is_stored_privately_and_cannot_be_accessed_publicly()`
- `test_buyer_approval_releases_escrow_to_seller_minus_service_fee()`
- `test_funds_cannot_be_released_more_than_once_on_completed_deal()`

### Suite 7: Deal Dispute Arbitration (`tests/Feature/DealDisputeTest.php`)
- `test_deal_participant_can_raise_dispute_transitioning_to_disputed()`
- `test_admin_can_resolve_dispute_with_full_refund_to_buyer()`
- `test_admin_can_resolve_dispute_with_full_payout_to_seller()`
- `test_admin_can_resolve_dispute_with_split_settlement()`

### Suite 8: Support Tickets & Messaging (`tests/Feature/SupportTicketTest.php`)
- `test_user_can_create_support_ticket_with_attachment()`
- `test_user_and_admin_can_exchange_ticket_messages()`
- `test_user_cannot_view_or_reply_to_another_users_ticket()`

### Suite 9: AI Support & Knowledge Base (`tests/Feature/AiSupportTest.php`)
- `test_ai_chat_returns_matched_knowledge_base_article()`
- `test_ai_chat_falls_back_gracefully_when_external_api_unavailable()`
- `test_guest_ai_escalation_does_not_spoof_super_admin_user_id()`

### Suite 10: File Storage & Download Authorization (`tests/Feature/FileSecurityTest.php`)
- `test_unauthenticated_download_of_deal_deliverable_is_forbidden()`
- `test_user_cannot_download_deliverable_from_deal_they_do_not_own()`
- `test_deposit_receipt_is_stored_in_private_disk()`

---

## 5. Quality Metrics & CI/CD Target
For Phase 2 and subsequent phases:
- Minimum Unit/Feature Test Coverage: **>= 85%** on financial and auth services.
- Static Analysis: PHPStan / Larastan at Level 6 or higher.
- Frontend Linting & Typecheck: ESLint + TypeScript / React 19 JSX validation passing with 0 errors.
- Pre-commit Hooks: Git hook executing test suite prior to staging commits.
