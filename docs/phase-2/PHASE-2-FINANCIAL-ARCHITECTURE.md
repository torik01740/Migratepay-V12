# MigratePay V12: Phase 2 Financial Architecture & Escrow State Machine

> **Document ID:** `DOC-P2-05`  
> **Source Project:** `F:\laragon\www\migratepay`  
> **Destination Project:** `F:\laragon\www\Migratepay-V12`  
> **Phase:** 2 — Architecture Decisions & Blueprint  
> **Status:** APPROVED FINANCIAL ARCHITECTURE  

---

## 1. Executive Summary

MigratePay is fundamentally a digital escrow and financial payment intermediary. The integrity of customer balances, escrow reservations, fee calculations, and payouts must be mathematically guaranteed and protected against race conditions, duplicate executions, and rounding errors.

This specification establishes:
1. **The Invariant-Driven Wallet Ledger:** Converting balance updates into an auditable, double-entry financial ledger.
2. **The Escrow State Machine:** Formally modeling deal lifecycle states with explicit transition guards.
3. **Elimination of Critical Financial Flaws:** Detailed remediation for Duplicate Escrow Payouts (`FIN-01`), Unrefunded Deal Declines (`FIN-02`), and Floating-Point Rounding Drift (`FIN-03`).
4. **Concurrency & Idempotency Controls:** Enforcing pessimistic row locking (`User::lockForUpdate()`) and atomic database transactions.

---

## 2. Core Financial Invariants

The following four invariants are inviolable across the MigratePay V12 financial engine:

```
┌────────────────────────────────────────────────────────────────────────┐
│                   MANDATORY FINANCIAL INVARIANTS                       │
├────────────────────────────────────────────────────────────────────────┤
│ Invariant 1: Non-Negative Wallet Balance                               │
│   ∀ user ∈ Users : user.wallet_balance >= 0.00                         │
│   No balance deduction or withdrawal may result in a negative balance. │
├────────────────────────────────────────────────────────────────────────┤
│ Invariant 2: Mathematical Ledger Parity                                │
│   user.wallet_balance = Σ(credit_transactions) - Σ(debit_transactions) │
│   Every balance mutation must be accompanied by an immutable           │
│   Transaction record where: balance_after = balance_before ± amount.   │
├────────────────────────────────────────────────────────────────────────┤
│ Invariant 3: Escrow Sum Conservation                                   │
│   deal.buyer_pays = deal.seller_gets + deal.charge_total               │
│   Platform service fees and seller payouts must exactly equal the total │
│   funds locked from the buyer upon deal activation.                    │
├────────────────────────────────────────────────────────────────────────┤
│ Invariant 4: Single-Release Idempotency                                │
│   Funds for any deal_id may be released to the seller EXACTLY ONCE.   │
│   Subsequent payout requests must be rejected immediately.             │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 3. Escrow State Machine Specification

### 3.1 State Diagram
```
                     [Initiate Deal]
                            │
                            ▼
                  ┌───────────────────┐
                  │awaiting_acceptance│
                  └─────────┬─────────┘
                            │
            ┌───────────────┴───────────────┐
            ▼                               ▼
   [Seller Accepts]                 [Seller Declines]
   (Lock Escrow from Buyer)         (No Escrow Lock / Refund)
            │                               │
            ▼                               ▼
     ┌──────────────┐                 ┌───────────┐
     │funds_secured │                 │ declined  │
     └──────┬───────┘                 └───────────┘
            │
            ▼
    [Seller Delivers Work]
            │
            ▼
     ┌──────────────┐
     │ buyer_review │
     └──────┬───────┘
            │
      ┌─────┴────────────────────────┐
      ▼                              ▼
 [Buyer Approves]              [Buyer Rejects]
      │                              │
      ▼                              ▼
┌───────────┐                  ┌───────────┐
│ completed │                  │ disputed  │
└───────────┘                  └─────┬─────┘
                                     │
                                     ▼
                          [Admin Resolves Dispute]
                          ┌──────────┴──────────┐
                          ▼                     ▼
                  [Refund to Buyer]     [Payout to Seller]
                          │                     │
                          ▼                     ▼
                  ┌───────────────┐     ┌───────────────┐
                  │   refunded    │     │   completed   │
                  └───────────────┘     └───────────────┘
```

### 3.2 State Transition Matrix & Financial Actions

| Initial State | Event / Trigger | Caller | Guard / Preconditions | Final State | Financial Action |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **`none`** | `createDeal` | Buyer/Seller | Input fields valid | `awaiting_acceptance` | None (deal agreement drafted). |
| **`awaiting_acceptance`** | `acceptDeal` | Counterparty | Buyer `wallet_balance >= buyer_pays` | `funds_secured` | **Lock Escrow:** Debit `buyer_pays` from buyer wallet; create `escrow_lock` transaction. |
| **`awaiting_acceptance`** | `declineDeal` | Counterparty | Status is `awaiting_acceptance` | `declined` | **`FIN-02` Fix:** If buyer was charged in advance, credit refund immediately. |
| **`funds_secured`** | `submitDeliverable` | Seller | Status is `funds_secured` | `buyer_review` | Store deliverable file in private storage disk. |
| **`buyer_review`** | `approveSubmission` | Buyer | Status is `buyer_review` | `completed` | **`FIN-01` Fix:** Atomic check `status === 'buyer_review'`; credit `seller_gets` to seller; create `escrow_release` transaction. |
| **`buyer_review`** | `rejectSubmission` | Buyer | Status is `buyer_review` | `disputed` | Retain escrow in platform hold; create open `Report` dispute record. |
| **`disputed`** | `adminResolve (Refund)` | Admin | Role is admin | `refunded` | Credit `buyer_pays` back to buyer; create `escrow_refund` transaction. |
| **`disputed`** | `adminResolve (Payout)` | Admin | Role is admin | `completed` | Credit `seller_gets` to seller; create `escrow_release` transaction. |
| **`disputed`** | `adminResolve (Split)` | Admin | Role is admin | `resolved_split` | Credit agreed portions to buyer and seller atomically. |

---

## 4. Verification and Remediation of Critical Financial Findings

### 4.1 Remediation of `FIN-01`: Duplicate Escrow Payout
- **Root Cause in V10:**
  In `DealService.php` line 408:
  ```php
  if (!in_array($deal->status, ['delivered', 'completed'])) {
      throw new Exception('Funds can only be released for delivered deals.');
  }
  ```
  Permitting `status === 'completed'` allows a second payout if an admin approves funds first, followed by the buyer clicking release.
- **V12 Solution:**
  1. Remove `'completed'` from allowed release states.
  2. Implement an atomic SQL conditional update:
     ```php
     $updated = Deal::where('id', $deal->id)
         ->where('status', 'buyer_review')
         ->where('admin_approved', false)
         ->update([
             'status' => 'completed',
             'admin_approved' => true,
             'completed_at' => now(),
         ]);

     if ($updated === 0) {
         throw new Exception('Funds have already been released or deal is not in review.');
     }
     ```

### 4.2 Remediation of `FIN-02`: Unrefunded Escrow on Seller Decline
- **Root Cause in V10:**
  In `DealService.php` line 380, `declineDeal()` simply sets `$deal->status = 'declined'; $deal->save();` without checking if the buyer had funds deducted.
- **V12 Solution:**
  Wrap decline in an atomic transaction:
  ```php
  DB::transaction(function () use ($deal) {
      $deal->status = 'declined';
      $deal->save();

      if ($deal->initiator_role === 'buyer' && $deal->funds_deducted) {
          $buyer = User::lockForUpdate()->findOrFail($deal->initiator_id);
          $balanceBefore = $buyer->wallet_balance;
          $buyer->wallet_balance += $deal->buyer_pays;
          $buyer->save();

          Transaction::create([
              'user_id' => $buyer->id,
              'type' => 'escrow_refund',
              'amount' => $deal->buyer_pays,
              'balance_before' => $balanceBefore,
              'balance_after' => $buyer->wallet_balance,
              'reference_type' => Deal::class,
              'reference_id' => $deal->id,
              'description' => "Escrow refunded - deal declined: {$deal->title}",
          ]);
      }
  });
  ```

---

## 5. Wallet Concurrency & Ledger Double-Entry Rules

### 5.1 Pessimistic Locking Implementation
All operations that read or modify balances must follow this strict template:
```php
public function executeMutation(int $userId, float $amount, string $type, callable $operation): Transaction
{
    return DB::transaction(function () use ($userId, $amount, $type, $operation) {
        // Enforce SELECT ... FOR UPDATE on user record
        $user = User::lockForUpdate()->findOrFail($userId);
        $balanceBefore = (string) $user->wallet_balance;

        // Execute domain operation (credit, debit, hold)
        $newBalance = $operation($user, $balanceBefore);

        // Enforce Non-Negative Balance Invariant
        if (bccomp($newBalance, '0.00', 2) === -1) {
            throw new InsufficientFundsException("Wallet balance cannot become negative.");
        }

        $user->wallet_balance = $newBalance;
        $user->save();

        // Write immutable ledger entry
        return Transaction::create([
            'user_id' => $user->id,
            'type' => $type,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $newBalance,
            'reference_type' => $this->referenceType,
            'reference_id' => $this->referenceId,
            'description' => $this->description,
        ]);
    });
}
```

### 5.2 Transaction Types Catalogue
- **`deposit`**: Account top-up approved by admin or gateway webhook (+ balance).
- **`withdrawal`**: Outbound payout request submitted by user (- balance).
- **`withdrawal_rejected`**: Admin rejects payout request; funds refunded (+ balance).
- **`escrow_lock`**: Buyer initiates or accepts deal; funds held for contract (- balance).
- **`escrow_release`**: Contract approved; payout released to seller (+ balance).
- **`escrow_refund`**: Deal declined, cancelled, or disputed in buyer's favor (+ balance).
- **`custom_service_fee`**: Platform fee deducted for dedicated VIP mediation (- balance).
