# MigratePay V12: Phase 2 Quality Assurance & Testing Architecture

> **Document ID:** `DOC-P2-09`  
> **Source Project:** `F:\laragon\www\migratepay` (0% custom test coverage, legacy PHPUnit 10 skeleton)  
> **Destination Project:** `F:\laragon\www\Migratepay-V12` (PHPUnit 11.5 / Pest 3, Laravel 12.70.2)  
> **Phase:** 2 — Architecture Decisions & Blueprint  
> **Status:** APPROVED TESTING ARCHITECTURE & QUALITY STRATEGY  

---

## 1. Executive Summary

Forensic inspection of MigratePay V10 verified that the legacy application operated with **zero automated business logic test coverage (0%)**, relying instead on manual browser clicking and hazardous root debugging scripts (`test_login.php`, `reset_password.php`).

For MigratePay V12, an institutional test engineering strategy is mandated:
1. **Zero-Defect Financial Core:** No financial service, balance mutation, or escrow release may be merged without comprehensive unit and concurrency test assertions.
2. **Automated Security Guardrails:** Every fixed vulnerability (`SEC-01` through `SEC-08`) will have a dedicated regression test asserting that unauthorized access, OTP leaks, and secret exposures return immediate HTTP 401/403/404 errors.
3. **Execution Environment:** In-memory SQLite (`:memory:`) executing under `RefreshDatabase` for millisecond-speed local and CI/CD test runs.

---

## 2. Testing Framework Architecture & Tooling

- **Testing Engine:** PHPUnit 11.5.50 (configured in V12 root `phpunit.xml`) with optional Pest 3 syntax.
- **Database Driver:** SQLite in-memory (`<env name="DB_CONNECTION" value="sqlite"/>`, `<env name="DB_DATABASE" value=":memory:"/>`).
- **Mail Driver:** Array / Log driver (`<env name="MAIL_MAILER" value="array"/>`) with `Mail::fake()` assertions.
- **Queue Driver:** Sync / Array driver with `Queue::fake()` assertions.
- **Storage Driver:** Mocked local private disk with `Storage::fake('private')`.

---

## 3. Comprehensive Test Suite Specification

### 3.1 Unit Test Suites (`tests/Unit/`)

#### Suite U1: `WalletCalculationTest.php`
- `test_fee_calculation_matches_exact_platform_tier_rates()`
- `test_wallet_credit_increments_balance_with_two_decimal_precision()`
- `test_wallet_debit_fails_if_balance_insufficient()`
- `test_wallet_balance_cannot_become_negative()`
- `test_floating_point_rounding_drift_is_prevented()`

#### Suite U2: `EscrowStateMachineTest.php`
- `test_valid_deal_state_transitions_succeed()`
- `test_invalid_deal_state_transitions_throw_exception()`
- `test_seller_cannot_submit_deliverable_unless_funds_secured()`
- `test_buyer_cannot_release_funds_unless_deal_is_delivered_or_under_review()`
- `test_funds_cannot_be_released_more_than_once_on_same_deal()`

#### Suite U3: `WithdrawalRulesTest.php`
- `test_mobile_withdrawal_applies_one_point_five_percent_fee()`
- `test_bank_withdrawal_applies_zero_percent_fee()`
- `test_net_amount_matches_amount_minus_fee()`

---

### 3.2 Feature Test Suites (`tests/Feature/`)

#### Suite F1: `AuthenticationLifecycleTest.php`
- `test_guest_can_register_with_valid_credentials()`
- `test_registration_fails_on_duplicate_email()`
- `test_login_succeeds_with_correct_credentials()`
- `test_login_rate_limiting_locks_out_after_five_failed_attempts()`
- `test_password_reset_otp_is_sent_to_user_email()`
- `test_failed_password_reset_does_not_leak_otp_in_json_response()` (`SEC-03` guard)

#### Suite F2: `DealEscrowWorkflowTest.php`
- `test_buyer_creates_deal_and_funds_are_locked_from_wallet()`
- `test_seller_accepts_deal_and_status_changes_to_funds_secured()`
- `test_seller_declines_deal_and_buyer_is_automatically_refunded()` (`FIN-02` guard)
- `test_seller_uploads_deliverable_and_file_is_stored_privately()` (`SEC-04` guard)
- `test_buyer_approves_submission_and_seller_wallet_is_credited()`
- `test_duplicate_fund_release_is_rejected()` (`FIN-01` guard)

#### Suite F3: `DepositPipelineTest.php`
- `test_user_can_submit_deposit_request_with_receipt()`
- `test_deposit_receipt_is_stored_on_private_disk()` (`SEC-05` guard)
- `test_admin_can_approve_deposit_and_user_wallet_is_credited()`
- `test_admin_cannot_approve_already_approved_deposit()`
- `test_admin_can_reject_deposit_with_mandatory_reason()`

#### Suite F4: `WithdrawalPipelineTest.php`
- `test_user_cannot_request_withdrawal_exceeding_wallet_balance()`
- `test_withdrawal_request_locks_user_balance_immediately()`
- `test_admin_approval_marks_withdrawal_processed()`
- `test_admin_rejection_refunds_amount_to_user_wallet_atomically()`

#### Suite F5: `DisputeArbitrationTest.php`
- `test_user_can_report_deal_issue_creating_open_report()`
- `test_admin_can_resolve_report_with_full_refund_to_buyer()`
- `test_admin_can_resolve_report_with_full_payout_to_seller()`
- `test_admin_can_resolve_report_with_split_settlement()`

#### Suite F6: `SupportTicketTest.php`
- `test_user_can_open_support_ticket_with_attachment()`
- `test_guest_ai_escalation_does_not_spoof_admin_id()` (`SEC-07` guard)
- `test_admin_and_user_can_exchange_ticket_replies()`

---

### 3.3 Security & Authorization Test Suites (`tests/Feature/Security/`)

#### Suite S1: `RouteBarrierSecurityTest.php`
- `test_unauthenticated_request_to_admin_endpoints_returns_401_or_redirects()`
- `test_authenticated_regular_customer_cannot_access_api_admin_users()` (`SEC-02` guard)
- `test_authenticated_regular_customer_cannot_modify_user_status_or_verification()`
- `test_public_gateway_endpoint_does_not_expose_passwords_or_secret_keys()` (`SEC-01` guard)

#### Suite S2: `PrivateFileAccessTest.php`
- `test_unauthenticated_download_of_deal_deliverable_is_forbidden()`
- `test_unauthorized_user_cannot_download_deliverable_of_another_deal()`
- `test_unauthorized_user_cannot_view_deposit_receipts()`

#### Suite S3: `FileUploadHardeningTest.php`
- `test_uploading_php_script_to_media_controller_is_rejected()` (`SEC-06` guard)
- `test_uploading_executable_or_html_file_to_deliverables_is_rejected()`

#### Suite S4: `ConcurrencyRaceConditionTest.php`
- `test_concurrent_withdrawal_requests_cannot_produce_negative_balance()`
- `test_concurrent_escrow_release_requests_cannot_produce_duplicate_credits()`

---

## 4. Minimum Quality Targets & CI/CD Gating
- **Test Pass Rate:** 100% passing (0 failures, 0 errors).
- **Code Coverage Target:** >= 90% on financial services (`WalletService`, `DealService`, `DepositService`, `WithdrawalService`).
- **Static Analysis:** PHPStan / Larastan at Level 6 passing with 0 errors.
- **Frontend Build:** `npm run build` must complete in Vite 7 with 0 syntax or JSX compilation errors.
