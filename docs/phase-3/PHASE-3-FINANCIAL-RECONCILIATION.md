# MigratePay V12: Phase 3 Financial Balance & Ledger Reconciliation

> **Document ID:** `DOC-P3-03`  
> **Source Database:** `F:\laragon\www\migratepay\database\database.sqlite` (Canonical Source)  
> **Destination Environment:** MySQL 8.0.30 (Laragon)  
> **Phase:** 3 — Database & Security Core Foundation  
> **Audit Status:** VERIFIED FORENSIC FINANCIAL RECONCILIATION  
> **Methodology:** Exact decimal-safe arithmetic (zero floating-point drift)  

---

## 1. Executive Summary & Ledger Nature Clarification

### Critical Distinction Regarding Ledger Architecture
The MigratePay V10 source application does **not** employ a balanced double-entry bookkeeping ledger (where every debit has an offsetting credit across asset, liability, and equity accounts). 

Instead, V10 implements a **single-entry transaction journal with balance snapshots** in the `transactions` table (`type`, `amount`, `balance_before`, `balance_after`, `reference_type`, `reference_id`). 

This audit provides an exact, penny-accurate mathematical reconciliation of all 7 users, distinguishes available wallet balances from escrow-held funds, accounts for unjournaled opening balances, and calculates total platform custodial obligations.

---

## 2. Exhaustive Per-User Balance Reconciliation

The source database contains 7 user records. Below is the individual forensic ledger statement for every user:

```
─────────────────────────────────────────────────────────────────────────────
USER ID 1: John Doe (johndoe@email.com) — Role: user
─────────────────────────────────────────────────────────────────────────────
• Source Stored Balance (users.wallet_balance): 0.00
• Opening Balance:                               0.00
• Deposits:                                      0.00
• Withdrawals:                                   0.00
• Escrow Locks:                                  0.00
• Escrow Releases / Refunds:                     0.00
• Calculated Journal Balance:                    0.00
• Available Wallet Balance:                      0.00
• Escrow Funds Held (Buyer):                     0.00
• Escrow Receivable (Seller):                    0.00
• Mathematical Discrepancy:                     0.00 (EXACT PARITY)

─────────────────────────────────────────────────────────────────────────────
USER ID 2: Seller Marketplace (seller@marketplace.bd) — Role: user
─────────────────────────────────────────────────────────────────────────────
• Source Stored Balance (users.wallet_balance): 0.00
• Opening Balance:                               0.00
• Deposits:                                      0.00
• Withdrawals:                                   0.00
• Escrow Locks:                                  0.00
• Escrow Releases / Refunds:                     0.00
• Calculated Journal Balance:                    0.00
• Available Wallet Balance:                      0.00
• Escrow Funds Held (Buyer):                     0.00
• Escrow Receivable (Seller):                    0.00
• Mathematical Discrepancy:                     0.00 (EXACT PARITY)

─────────────────────────────────────────────────────────────────────────────
USER ID 3: Pulok Halder (pulok@gmail.com) — Role: user (Verified)
─────────────────────────────────────────────────────────────────────────────
• Source Stored Balance (users.wallet_balance): 7,887.50
• Unjournaled Baseline (pre-Tx 2 balance):      11,000.00 (Seeded / Initial)
• Movements in Transactions Table:
  1. [Tx 2] 2026-07-22 18:47:59 | Deposit (Bank)      | +1,000.00 | Bal: 11,000.00 -> 12,000.00
  2. [Tx 6] 2026-09-06 17:55:24 | Escrow Lock (Deal 2)| -5,112.50 | Bal: 12,000.00 ->  6,887.50
  3. [Tx 8] 2026-09-24 16:53:27 | Deposit (Bank)      | +1,000.00 | Bal:  6,887.50 ->  7,887.50
• Total Transaction Inflows:                   +2,000.00
• Total Transaction Outflows:                  -5,112.50
• Net Movement:                                -3,112.50
• Calculated Journal Balance:                   7,887.50 (11,000.00 - 3,112.50)
• Available Wallet Balance:                     7,887.50
• Escrow Funds Held (Buyer):                         0.00 (Deal 2 is completed)
• Escrow Receivable (Seller on Deal 1):        11,760.00 (Held in platform custody)
• Mathematical Discrepancy:                     0.00 (EXACT PARITY)

─────────────────────────────────────────────────────────────────────────────
USER ID 4: Rajib Sarkar (rajib@gmail.com) — Role: user
─────────────────────────────────────────────────────────────────────────────
• Source Stored Balance (users.wallet_balance): 5,649.50
• Opening Balance:                                   0.00
• Movements in Transactions Table:
  1. [Tx 1] 2026-07-22 18:30:51 | Deposit (Bank)      | +10,000.00 | Bal:      0.00 -> 10,000.00
  2. [Tx 3] 2026-07-22 18:48:09 | Deposit (Bank)      |  +5,000.00 | Bal: 10,000.00 -> 15,000.00
  3. [Tx 4] 2026-07-22 18:55:28 | Escrow Lock (Deal 1)| -12,240.00 | Bal: 15,000.00 ->  2,760.00
  4. [Tx 5] 2026-07-23 09:38:03 | Withdrawal (Bank)   |  -1,998.00 | Bal:  2,760.00 ->    762.00
  5. [Tx 7] 2026-09-07 15:24:08 | Escrow Release (D2) |  +4,887.50 | Bal:    762.00 ->  5,649.50
• Total Transaction Inflows:                  +19,887.50
• Total Transaction Outflows:                 -14,238.00
• Net Movement:                                +5,649.50
• Calculated Journal Balance:                   5,649.50 (0.00 + 5,649.50)
• Available Wallet Balance:                     5,649.50
• Escrow Funds Held (Buyer on Deal 1):         12,240.00 (Active Escrow)
• Escrow Receivable (Seller):                        0.00 (Deal 2 released)
• Mathematical Discrepancy:                     0.00 (EXACT PARITY)

─────────────────────────────────────────────────────────────────────────────
USER ID 5: Admin User (admin@migratepay.com) — Role: admin
─────────────────────────────────────────────────────────────────────────────
• Source Stored Balance (users.wallet_balance): 0.00
• Opening Balance:                               0.00
• Deposits:                                      0.00
• Withdrawals:                                   0.00
• Escrow Locks:                                  0.00
• Escrow Releases / Refunds:                     0.00
• Calculated Journal Balance:                    0.00
• Available Wallet Balance:                      0.00
• Escrow Funds Held:                             0.00
• Mathematical Discrepancy:                     0.00 (EXACT PARITY)

─────────────────────────────────────────────────────────────────────────────
USER ID 7: Test User (user@example.com) — Role: user
─────────────────────────────────────────────────────────────────────────────
• Source Stored Balance (users.wallet_balance): 500.00
• Opening Seed Balance:                         500.00 (Created via UserSeeder.php)
• Transaction Records:                           0 records (Unjournaled seed)
• Calculated Journal Balance:                   500.00
• Available Wallet Balance:                     500.00
• Escrow Funds Held:                             0.00
• Mathematical Discrepancy:                     0.00 (EXACT PARITY)

─────────────────────────────────────────────────────────────────────────────
USER ID 8: Samiul Hasan (samiul@gmail.com) — Role: user
─────────────────────────────────────────────────────────────────────────────
• Source Stored Balance (users.wallet_balance): 0.00
• Opening Balance:                               0.00
• Deposits:                                      0.00
• Withdrawals:                                   0.00
• Escrow Locks:                                  0.00
• Escrow Releases / Refunds:                     0.00
• Calculated Journal Balance:                    0.00
• Available Wallet Balance:                      0.00
• Escrow Funds Held:                             0.00
• Mathematical Discrepancy:                     0.00 (EXACT PARITY)
─────────────────────────────────────────────────────────────────────────────
```

---

## 3. Escrow Liability & Deal Reconciliation

The database contains 2 deal records. Below is the exact status of escrow commitments:

### Deal 1: `DX-188753` — "Test escrow deal" (ACTIVE ESCROW)
- **Status:** `funds_secured`
- **Buyer (Initiator):** User 4 (Rajib Sarkar)
- **Seller (Counterparty):** User 3 (Pulok Halder)
- **Contract Deal Amount:** ৳12,000.00
- **Buyer Total Paid (`buyer_pays`):** ৳12,240.00 (Debited from User 4 in Tx 4)
- **Seller Expected Payout (`seller_gets`):** ৳11,760.00
- **Platform Charge Total (`charge_total`):** ৳480.00 (৳240 buyer surcharge + ৳240 seller commission)
- **Current Escrow Status:**
  - ৳12,240.00 was debited from User 4's available wallet.
  - The seller has not received funds because the deal is awaiting work submission/approval.
  - **Outstanding Escrow Liability:** The platform currently holds **৳12,240.00 in custodial escrow liability** (৳11,760.00 payable to User 3 upon approval, ৳480.00 platform revenue upon completion).

### Deal 2: `DX-977177` — "Adsense" (COMPLETED ESCROW)
- **Status:** `completed`
- **Buyer (Initiator):** User 3 (Pulok Halder)
- **Seller (Counterparty):** User 4 (Rajib Sarkar)
- **Contract Deal Amount:** ৳5,000.00
- **Buyer Paid (`buyer_pays`):** ৳5,112.50 (Debited from User 3 in Tx 6)
- **Seller Credited (`seller_gets`):** ৳4,887.50 (Credited to User 4 in Tx 7)
- **Platform Fee Collected:** ৳225.00 (৳112.50 + ৳112.50 retained as revenue)
- **Current Escrow Status:** Fully settled. Zero remaining liability.

---

## 4. Platform Custodial Balance Sheet & Proof of Parity

Using decimal-safe arithmetic, the platform financial position reconciles as follows:

```
A. Total User Available Balances:
   User 1 (John Doe):                  ৳0.00
   User 2 (Seller Marketplace):        ৳0.00
   User 3 (Pulok Halder):          ৳7,887.50
   User 4 (Rajib Sarkar):          ৳5,649.50
   User 5 (Admin User):                ৳0.00
   User 7 (Test User):               ৳500.00
   User 8 (Samiul Hasan):              ৳0.00
   ─────────────────────────────────────────
   Total Available Wallet Balances:           ৳14,037.00

B. Active Escrow Liability:
   Deal 1 (DX-188753) Held in Custody:        ৳12,240.00
   ─────────────────────────────────────────
   Total Platform Custodial Obligation (A+B): ৳26,277.00

C. Source Financial Inflows & Outflows:
   + Approved Deposits (Deposits 1, 2, 3, 4): ৳17,000.00
   + User 3 Unjournaled Opening Baseline:     ৳11,000.00
   + User 7 Unjournaled Seed Balance:            ৳500.00
   ─────────────────────────────────────────
   Total Capital Inflows:                     ৳28,500.00

   - Processed Withdrawals (Withdrawal 1):    -৳1,998.00
   - Platform Revenue Retained (Deal 2 Fee):    -৳225.00
   ─────────────────────────────────────────
   Net Reconciled Platform Position:          ৳26,277.00

D. Proof of Mathematical Parity:
   Custodial Obligation (৳26,277.00) = Net Inflow Capital (৳26,277.00)
   VARIANCE: ৳0.00 (EXACT ZERO VARIANCE)
```

---

## 5. Storage Types & Decimal Conversion Strategy

### 5.1 Storage Verification in SQLite Source
In `F:\laragon\www\migratepay\database\database.sqlite`:
- `users.wallet_balance`: Stored as SQLite `FLOAT` with values `0.0`, `7887.5`, `5649.5`, `500.0`.
- `deals.amount`, `buyer_pays`, `seller_gets`: Stored as `DECIMAL(15, 2)` (represented as strings or reals).
- `transactions.amount`, `balance_before`, `balance_after`: Stored as `DECIMAL(15, 2)`.
- `deposits.amount`: Stored as `DECIMAL(15, 2)`.
- `withdrawal_requests.amount`, `fee`, `net_amount`: Stored as `DECIMAL(12, 2)`.

### 5.2 Conversion to MySQL `DECIMAL(15, 2)`
- Direct binary casting in PHP via `(float)` is strictly prohibited during migration to prevent IEEE-754 mantissa drift (e.g., `7887.5` becoming `7887.49999999999`).
- Conversion strategy: Read values as raw text strings from SQLite, format using `number_format($rawString, 2, '.', '')`, and bind directly as strings to MySQL PDO parameters.
- Rounding rule: Standard BDT half-up rounding (`PHP_ROUND_HALF_UP`) at 2 decimal places.
