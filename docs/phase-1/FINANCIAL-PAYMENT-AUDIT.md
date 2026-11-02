# Financial System & Payment Integration Audit: MigratePay V10

**Source Project:** MigratePay V10 (`F:\laragon\www\migratepay`)  
**Assessment Date:** 2026-11-02  
**Scope:** Wallet Mechanics, Transaction Ledger, Deposits, Withdrawals, Escrow State Machine, Fee Tiers, Gateways, and Financial Risks  

---

## 1. Wallet Mechanics & Balance Architecture

- **Balance Storage:** Stored on the `users` table in column `wallet_balance`.
- **Data Type Issue:** The column is defined as `FLOAT` in `2014_10_12_000000_create_users_table.php`. While PHP code enforces `round(..., 2)`, IEEE 754 floating-point numbers can introduce cumulative binary precision rounding discrepancies.
- **Row Locking:** All critical wallet updates in services use pessimistic locking via `User::lockForUpdate()->findOrFail($userId)` inside `DB::transaction(...)`.
- **Master Ledger (`transactions` table):**
  - Captures an immutable double-entry style audit log for every balance change.
  - Required fields: `user_id`, `type`, `amount`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `description`.
  - Transaction types identified in source:
    - `deposit`: Admin approved deposit credit.
    - `withdrawal`: Balance deduction upon withdrawal submission.
    - `withdrawal_rejected`: Balance refund upon admin withdrawal rejection.
    - `escrow_lock`: Balance deduction when funding a deal (either at deal creation if buyer initiates, or deal acceptance if seller initiates).
    - `escrow_release`: Balance credit to seller when funds are released.

---

## 2. Deposit Lifecycle (Manual Verification Model)

1. **Initiation (`POST /api/deposits`):**
   - User chooses gateway: `bkash`, `nagad`, `rocket`, `bank_transfer`.
   - Reads instruction details (merchant numbers, bank routing) loaded from `gateway_settings.json`.
   - Enters `amount` (BDT `৳`) and `proof_note` (transaction ID / sender account number).
   - Uploads receipt screenshot via `POST /api/deposits/upload-screenshot`.
   - Creates a record in `deposits` with status `pending` and `admin_read = false`.
2. **Admin Notification:**
   - Admin Control Tower poller detects unread pending deposits and triggers an alert banner.
3. **Admin Review & Decision:**
   - **Approval (`POST /api/admin/deposits/{id}/approve`):**
     - Verifies `deposit.status === 'pending'`.
     - Opens `DB::transaction`.
     - Acquires `User::lockForUpdate()`.
     - Credits `user.wallet_balance += deposit.amount`.
     - Creates `Transaction` record (`type = 'deposit'`).
     - Updates deposit status to `approved`, stamps `reviewed_by` and `reviewed_at`.
   - **Rejection (`POST /api/admin/deposits/{id}/reject`):**
     - Updates status to `rejected` with required `admin_note`. No wallet modification.

---

## 3. Withdrawal Lifecycle (Payout Model)

1. **Payout Method Setup (`POST /api/withdrawal-methods`):**
   - User registers payout accounts: Bank Account (Bank, Branch, Routing, Account Name/Number) or Mobile Banking (Provider, Mobile Number).
   - Guard: Methods cannot be deleted if a pending withdrawal is associated with them.
2. **Withdrawal Request (`POST /api/withdrawals`):**
   - Validates that user has sufficient `wallet_balance >= amount`.
   - Calculates cashout fee: `fee = method.type === 'mobile' ? round($amount * 0.015, 2) : 0.0` (1.5% fee on mobile banking, 0% on bank transfers).
   - Calculates `net_amount = amount - fee`.
   - Atomically locks user record and deducts the full `amount` from `wallet_balance`.
   - Creates `Transaction` record (`type = 'withdrawal'`).
   - Generates unique reference ID: `WDR-<6-digit-hex>`.
3. **Admin Payout Processing:**
   - **Approval (`POST /api/admin/withdrawals/{id}/approve`):**
     - Admin performs offline bank transfer or mobile payment.
     - Marks request status as `approved` and stamps `processed_at`.
     - Dispatches notification to user.
   - **Rejection (`POST /api/admin/withdrawals/{id}/reject`):**
     - Atomically locks user record and refunds the full `amount` back to `wallet_balance`.
     - Creates `Transaction` record (`type = 'withdrawal_rejected'`).
     - Marks request status as `rejected` with explanation note.

---

## 4. Escrow State Machine & Fee Calculations

### Fee Tiering Logic (`DealService::getChargePercentage`)
- ৳1,000 – ৳5,000: **5.0%**
- ৳5,001 – ৳10,000: **4.5%**
- ৳10,001 – ৳15,000: **4.0%**
- ৳15,001+: **3.0%**
- Baseline fee: 5.0%

### Fee Split Allocation (`charge_payer`)
- `seller_100`: Seller pays entire platform fee; deducted from deal payout (`seller_gets = amount - total_charge`).
- `buyer_100`: Buyer pays entire platform fee; added to funding amount (`buyer_pays = amount + total_charge`).
- `shared_50`: Split equally 50/50 between buyer and seller.
- `custom`: Split by custom percentage (`seller_pct + buyer_pct === 100`).

### Coupon Engine
- Validates code in `coupons` table: active flag, date validity (`start_date` to `expiry_date`), global limit, user usage limit.
- Supports `percentage` discount off platform fee or `fixed` fee discount.
- Increments `current_usage` and logs audit entry in `coupon_usages`.

### Escrow State Machine Flow
```
[ Deal Created ] ──────────────────────────────────────────┐
       │                                                   │
(initiator == buyer)                               (initiator == seller)
  funds locked immediately                           buyer invited, funds not yet locked
       │                                                   │
       ▼                                                   ▼
[ awaiting_acceptance ]                             [ awaiting_acceptance ]
       │                                                   │
 (counterparty accepts)                              (counterparty accepts & funds locked)
       │                                                   │
       └─────────────────────────┬─────────────────────────┘
                                 ▼
                         [ funds_secured ] ◄─── (Work & Deliverables Submitted)
                                 │
           ┌─────────────────────┼─────────────────────┐
           ▼                     ▼                     ▼
   [ Buyer Releases ]   [ Extension Request ]   [ Dispute Filed ]
           │                     │                     │
           ▼                     ▼                     ▼
      [ released ]       [ New Deadline ]         [ disputed ]
           │                                           │
           ▼                                           ▼
      [ completed ]                             [ Admin Resolves ]
```

---

## 5. Confirmed Financial Vulnerabilities Register

### Finding 1: FIN-01 — Duplicate Escrow Payout Vulnerability
- **Severity:** **CRITICAL**
- **Affected File:** `app/Services/DealService.php` (Lines 408–448 & Lines 614–640)
- **Source Evidence:**
  In `releaseFunds` (buyer manual release):
  ```php
  // Line 408
  if (!in_array($deal->status, ['funds_secured', 'completed'])) {
      throw new Exception('Funds cannot be released at this stage.');
  }
  ...
  $lockedSeller->wallet_balance = $newBalance; // Credits seller
  $lockedSeller->save();
  ```
  In `adminApproveFunds` (admin forced release):
  ```php
  // Lines 636-639
  $deal->update([
      'admin_approved' => true,
      'status'         => 'completed',
  ]);
  ```
- **Root Cause:** Line 408 of `DealService.php` explicitly allows `releaseFunds` to execute when the deal status is `'completed'`.
- **Attack Scenario:**
  1. An admin approves and releases deal funds via `adminApproveFunds`. The seller is credited `$sellerGets`, and the deal status is set to `'completed'`.
  2. The buyer subsequently calls `releaseFunds` (`POST /api/deals/{id}/release`). Because `$deal->status === 'completed'` satisfies the condition on line 408, the function runs again, lock-updates the seller, and **credits the seller a second time**.
- **Impact:** Direct financial loss and balance duplication from platform funds.
- **Recommended Remediation:**
  1. Remove `'completed'` from allowed statuses in `releaseFunds`:
     ```php
     if ($deal->status !== 'funds_secured') {
         throw new Exception('Funds cannot be released at this stage.');
     }
     ```
  2. Add an atomic database lock or boolean flag `is_released` to guarantee single-execution idempotency.

---

### Finding 2: FIN-02 — Missing Buyer Refund on Deal Decline
- **Severity:** **HIGH**
- **Affected File:** `app/Services/DealService.php` (Lines 372–399)
- **Source Evidence:**
  ```php
  public function declineDeal(int $id, User $user): void
  {
      $deal = Deal::findOrFail($id);
      ...
      $deal->update(['status' => 'declined']);
      // Notice: No refund transaction or wallet crediting!
  }
  ```
- **Root Cause:** When a deal is initiated by a buyer (`initiator_role === 'buyer'`), the buyer's funds are immediately locked and deducted from `wallet_balance` in `createDeal`. If the counterparty declines the invitation via `declineDeal`, the status transitions to `declined`, but the buyer is **never refunded**.
- **Impact:** Buyer's money is permanently trapped in escrow lock without any transaction record or balance restoration.
- **Recommended Remediation:** Add an atomic refund in `declineDeal`:
  ```php
  if ($deal->initiator_role === 'buyer') {
      $buyer = User::lockForUpdate()->findOrFail($deal->initiator_id);
      $buyer->wallet_balance += $deal->buyer_pays;
      $buyer->save();
      Transaction::create([
          'user_id' => $buyer->id,
          'type' => 'escrow_refund',
          'amount' => $deal->buyer_pays,
          ...
      ]);
  }
  ```

---

### Finding 3: FIN-03 — Floating-Point Precision for Balance Representation
- **Severity:** **MEDIUM**
- **Affected Files:** `database/migrations/2014_10_12_000000_create_users_table.php`, `app/Models/User.php`
- **Root Cause:** Column `wallet_balance` is declared as `float`.
- **Impact:** IEEE floating-point arithmetic can introduce fractional cent discrepancies over many transactions.
- **Recommended Remediation:** In V12 migration, define `wallet_balance` as `decimal(15, 2)`.

---

## 6. Payment Gateway Reality: Manual vs Automated

| Gateway Identifier | Gateway Name | Admin Configuration UI | Automated API SDK | Webhook Handler | Actual Operation Model |
|---|---|---|---|---|---|
| `sslcommerz` | SSLCommerz | Present in `PaymentGatewaysModule` | **None** | **None** | Manual Deposit (User makes external payment, uploads screenshot) |
| `bank_transfer` | Bank Transfer | Present in `PaymentGatewaysModule` | **None** | **None** | Manual Deposit (User transfers to admin bank account, admin verifies) |
| `bkash` | bKash | Present in `PaymentGatewaysModule` | **None** | **None** | Manual Deposit (User sends money to merchant/agent number, submits TrxID) |
| `nagad` | Nagad | Present in `PaymentGatewaysModule` | **None** | **None** | Manual Deposit (User sends money via Nagad app, submits TrxID) |
| `card_stripe` | Stripe | Present in `PaymentGatewaysModule` | **None** | **None** | Manual Deposit (Simulation only; no Stripe Checkout or Webhook) |

> **Conclusion:** MigratePay V10 operates an entirely manual escrow and deposit workflow. There are zero external webhook callbacks or automated gateway APIs currently running in the backend.
