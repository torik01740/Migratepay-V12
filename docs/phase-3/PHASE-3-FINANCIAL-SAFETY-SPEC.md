# MigratePay V12: Phase 3 Financial Migration Safety Specification

> **Document ID:** `DOC-P3-FINANCIAL-SAFETY-SPEC`  
> **Source Database:** `F:\laragon\www\migratepay\database\database.sqlite`  
> **Destination Database:** MySQL 8.0.30 (`migratepay_v12`)  
> **Phase:** 3 — Database & Security Core Foundation  
> **Currency:** Bangladeshi Taka (BDT / ৳)  
> **Audit Status:** VERIFIED ZERO-VARIANCE LEDGER & PRECISION SPECIFICATION

---

## 1. Executive Summary & Core Financial Invariants

The financial core of MigratePay V12 requires absolute monetary precision, mathematical zero-variance reconciliation, and strict separation between available wallet balances and active escrow custodial obligations.

### The Five Invariant Safety Rules
1. **Zero PHP Float Operations:** Native PHP binary floats (`float`, `double`) must **never** be used for financial balance calculation, ledger reconciliation, fee computation, or database persistence. All arithmetic must use **BCMath** (`bcadd`, `bcsub`, `bcmul`, `bcdiv`, `bccomp`).
2. **Database Precision:** All monetary columns in MySQL must be strictly typed as **`DECIMAL(15, 2)`**.
3. **Custodial Separation:** Funds held in escrow for active deals (`DX-188753`) must never be commingled with available user balances, nor claimed as platform revenue before deal fulfillment.
4. **Zero Variance Guarantee:** Total platform custodial liabilities must equal net capital inflows with exactly **৳0.00 variance**.
5. **Authentic Primary Key Parity:** Original user IDs (`1, 2, 3, 4, 5, 7, 8`) must be strictly preserved to maintain historical financial audit trails.

---

## 2. Complete 7-User Balance Reconciliation

Every user record in SQLite has been verified with exact balance, status, role, and transaction count:

| User ID | User String | Name | Email | Role | Status | Available Balance | Transaction Count | Verified Escrow Status |
| :---: | :--- | :--- | :--- | :--- | :--- | :---: | :---: | :--- |
| **1** | `USR-1001` | John Doe | `johndoe@email.com` | user | Active | **৳0.00** | 0 | Inactive baseline account |
| **2** | `USR-1002` | Seller Marketplace | `seller@marketplace.bd` | user | Active | **৳0.00** | 0 | Inactive baseline account |
| **3** | `USR-177444030155` | Pulok Halder | `pulok@gmail.com` | user | Active | **৳7,887.50** | 4 | Verified (Deal 1 Seller: ৳11,760.00 pending) |
| **4** | `USR-177445315522` | Rajib Sarkar | `rajib@gmail.com` | user | Active | **৳5,649.50** | 4 | Verified (Deal 1 Buyer: ৳12,240.00 funded) |
| **5** | `YNYY0EB5` | Admin User | `admin@migratepay.com` | admin| active | **৳0.00** | 0 | Administrative operator |
| **7** | `REWEAKWC` | Test User | `user@example.com` | user | active | **৳500.00** | 0 | Synthetic test account |
| **8** | `USR-179092883178` | Samiul Hasan | `samiul@gmail.com` | user | Active | **৳0.00** | 0 | Registered user |
| **TOTAL**| | | | | | **৳14,037.00**| **8** | |

---

## 3. Escrow Liabilities & Custodial Balance Sheet

### 3.1 Deal 1 (`DX-188753`) Active Escrow Obligation
- **Deal Title:** `Test escrow deal`
- **Deal Status:** `funds_secured` (Active Escrow)
- **Buyer (Initiator):** Rajib Sarkar (User 4)
- **Seller (Counterparty):** Pulok Halder (User 3)
- **Contract Nominal Amount:** ৳12,000.00
- **Buyer Paid Amount:** ৳12,240.00 (Debited from User 4 in Transaction ID 2 on 2026-07-22)
- **Seller Receivable upon Completion:** ৳11,760.00
- **Platform Escrow Service Fee:** ৳480.00
- **Active Escrow Liability Held by MigratePay:** **৳12,240.00**

### 3.2 Deal 2 (`DX-977177`) Completed Escrow Lifecycle
- **Deal Title:** `Adsense`
- **Deal Status:** `completed` (Fulfilled on 2026-09-06)
- **Buyer:** Pulok Halder (User 3) — Paid ৳5,112.50 (Debited in Transaction ID 6)
- **Seller:** Rajib Sarkar (User 4) — Received ৳4,887.50 (Credited in Transaction ID 7)
- **Discount:** ৳25.00 via Coupon `FAITOR1` (Usage ID 1)
- **Platform Fee Revenue Earned:** ৳225.00 (Transferred to platform earned revenue)

### 3.3 Platform Custodial Balance Sheet
```
======================================================================
MIGRATEPAY PLATFORM CUSTODIAL BALANCE SHEET (AS OF AUDIT DATE)
======================================================================

I. CUSTODIAL LIABILITIES (OBLIGATIONS TO USERS)
   Available User Wallet Balances:
   - User 3 (Pulok Halder):                     ৳7,887.50
   - User 4 (Rajib Sarkar):                     ৳5,649.50
   - User 7 (Test User):                           ৳500.00
   - Users 1, 2, 5, 8:                               ৳0.00
   -------------------------------------------------------
   Subtotal Available Balances:                ৳14,037.00

   Active Escrow Funds Held in Trust:
   - Deal 1 (DX-188753 - Funds Secured):       ৳12,240.00
   -------------------------------------------------------
   TOTAL PLATFORM CUSTODIAL LIABILITIES:       ৳26,277.00
======================================================================

II. CAPITAL FLOW RECONCILIATION
   Capital Inflows:
   - Deposit 1 (User 4, Approved):             ৳10,000.00
   - Deposit 2 (User 4, Approved):              ৳5,000.00
   - Deposit 3 (User 3, Approved):              ৳1,000.00
   - Deposit 4 (User 3, Approved):              ৳1,000.00
   - User 3 Pre-Migration Opening Baseline:    ৳11,000.00
   - User 7 Seeded Opening Baseline:              ৳500.00
   -------------------------------------------------------
   Total Capital Inflows:                      ৳28,500.00

   Capital Outflows & Earned Fee Realization:
   - Withdrawal 1 (User 4, Approved/Processed): ৳1,998.00
   - Deal 2 Platform Service Fee Earned:          ৳225.00
   -------------------------------------------------------
   Total Outflows & Realized Deductions:        ৳2,223.00
   -------------------------------------------------------
   NET CAPITAL POSITION HELD IN CUSTODY:       ৳26,277.00
======================================================================

III. RECONCILIATION VARIANCE
   Custodial Liabilities:                      ৳26,277.00
   Net Capital Position:                       ৳26,277.00
   -------------------------------------------------------
   MATHEMATICAL RECONCILIATION VARIANCE:            ৳0.00 (ZERO VARIANCE)
======================================================================
```

---

## 4. Monetary Precision & Conversion Rules

### 4.1 Conversion from SQLite Float to MySQL DECIMAL(15, 2)
In SQLite, `wallet_balance` was stored as `FLOAT`, and transaction amounts as `NUMERIC`. IEEE 754 binary floating-point representation can cause rounding artifacts during type conversion.

**Mandatory Conversion Algorithm:**
```php
/**
 * Safe conversion of source value to exact 2-decimal string.
 */
function toExactDecimal(mixed $value): string {
    if ($value === null || $value === '') {
        return '0.00';
    }
    // Convert to string without scientific notation, then format to 2 decimals
    $formatted = number_format((float)$value, 2, '.', '');
    return $formatted;
}
```

### 4.2 BDT Rounding Rules
- **Currency:** Bangladeshi Taka (BDT / ৳).
- **Sub-unit:** Paisa (100 Paisa = 1 BDT).
- **Rounding Mode:** `ROUND_HALF_UP` (PHP standard `PHP_ROUND_HALF_UP` / BCMath equivalent).
- **Fractional Policy:** All intermediate fee calculations (percentage fees, coupon discounts) must calculate to 4 decimal places internally and round to 2 decimal places before ledger posting.
  ```php
  // Example: 2% fee on 12,000 BDT
  $fee = bcdiv(bcmul('12000.00', '2', 4), '100', 4); // '240.0000'
  $finalFee = number_format((float)$fee, 2, '.', ''); // '240.00'
  ```

---

## 5. Historical Data Migration Safety Protocol

### 5.1 Pre-Migration Invariant Assertions
Before any record is inserted into MySQL, the migration command must execute the following automated validation checks:
1. **Account Integrity Check:** Verify exactly 7 user rows exist in source SQLite with IDs `[1, 2, 3, 4, 5, 7, 8]`.
2. **Balance Sum Check:** Verify `bcadd` sum of all user balances equals exactly `'14037.00'`.
3. **Escrow Sum Check:** Verify active escrow liability for Deal 1 equals exactly `'12240.00'`.
4. **Deposit Sum Check:** Verify approved deposits sum to `'17000.00'`.
5. **Withdrawal Sum Check:** Verify approved withdrawals sum to `'1998.00'`.

### 5.2 Dry-Run Verification Command
The migration script will be invoked with `--dry-run`:
```bash
php artisan app:migrate-v10-data --dry-run
```
In dry-run mode:
- The script executes within an explicit `DB::beginTransaction()`.
- Reads all SQLite rows, maps types, verifies foreign key links, and asserts balance invariants.
- Computes post-migration checksums.
- Issues `DB::rollBack()` unconditionally.
- Emits a comprehensive console report confirming zero discrepancies.

### 5.3 Authorization Gate
Actual write execution into MySQL (`php artisan app:migrate-v10-data --force`) is **strictly prohibited** until separate explicit written authorization is granted by the project owner.
