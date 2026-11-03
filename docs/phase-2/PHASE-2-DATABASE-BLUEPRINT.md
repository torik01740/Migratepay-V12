# MigratePay V12: Phase 2 Database Blueprint & Data Migration Strategy

> **Document ID:** `DOC-P2-04`  
> **Source Project:** `F:\laragon\www\migratepay` (SQLite 3 / MySQL Source)  
> **Destination Project:** `F:\laragon\www\Migratepay-V12` (Laravel 12 SQLite Foundation)  
> **Phase:** 2 — Architecture Decisions & Blueprint  
> **Status:** APPROVED DATABASE ARCHITECTURE & MIGRATION SPECIFICATION  

---

## 1. Executive Summary

This blueprint specifies the canonical database architecture for MigratePay V12 and establishes a zero-loss, non-destructive data migration protocol for porting all historical customer records, financial balances, active escrow deals, and support histories from V10 to V12.

### Critical Database Improvements in V12
1. **Fixed-Precision Financial Types:** `users.wallet_balance` is upgraded from `FLOAT(10, 2)` to **`DECIMAL(15, 2)`** to eliminate IEEE-754 floating-point rounding hazards (`FIN-03`).
2. **Schema Drift Reconciliation:** Resolves the discrepancy between `database_backup.sql` and `database.sqlite` (`DB-01`) by adopting the active 22-table SQLite schema as canonical.
3. **True Persistence for CMS & Settings:** Eliminates frontend `localStorage` prototypes (`ARCH-01`) by introducing three lean, indexed tables: **`system_settings`**, **`cms_pages`**, and **`blog_posts`**.
4. **Foreign Key Integrity:** Enforces cascading constraints and foreign key indexes across all relational join pathways.

---

## 2. Table-by-Table Canonical Schema Catalogue

### Table 1: `users`
- **Purpose:** Primary account entity for customers, merchants, and staff.
- **Primary Key:** `id` (bigint unsigned, auto-increment)
- **Important Columns:** `name` (string), `email` (string, unique), `phone` (string, nullable), `password` (string), `wallet_balance` (**`DECIMAL(15, 2)`**, default 0.00), `is_admin` (boolean, default 0), `is_active` (boolean, default 1), `is_verified` (boolean, default 0), `role` (string 30, default 'user'), `avatar_path` (string, nullable), `id_type` (string, nullable), `id_number` (string, nullable), `id_photo_front` (string, nullable), `id_photo_back` (string, nullable).
- **Sensitive Data:** Password hashes, personal identity documents (KYC), phone numbers.
- **Indexes:** `unique(email)`, `index(wallet_balance)`, `index(role)`, `index(is_admin)`.

### Table 2: `deals`
- **Purpose:** Escrow contract entity governing transactions between buyers and sellers.
- **Primary Key:** `id` (bigint unsigned, auto-increment)
- **Important Columns:** `deal_id` (string 30, unique), `title` (string), `amount` (`DECIMAL(15, 2)`), `buyer_pays` (`DECIMAL(15, 2)`), `seller_gets` (`DECIMAL(15, 2)`), `charge_total` (`DECIMAL(15, 2)`), `initiator_id` (foreignId -> `users`), `counterparty_id` (foreignId -> `users`, nullable), `counterparty_email` (string), `initiator_role` (enum: 'buyer', 'seller'), `status` (string 30, default 'awaiting_acceptance'), `deadline` (date, nullable), `workspace` (json, nullable), `admin_approved` (boolean, default 0), `buyer_accepted` (boolean, default 0), `custom_service` (boolean, default 0), `custom_service_charge` (`DECIMAL(15, 2)`, default 0.00).
- **Financial Relevance:** Determines locked escrow funds and payouts.
- **Indexes:** `unique(deal_id)`, `index(initiator_id, status)`, `index(counterparty_id, status)`.

### Table 3: `transactions`
- **Purpose:** Immutable double-entry financial audit ledger.
- **Primary Key:** `id` (bigint unsigned, auto-increment)
- **Important Columns:** `user_id` (foreignId -> `users`), `type` (string 30), `amount` (`DECIMAL(15, 2)`), `balance_before` (`DECIMAL(15, 2)`), `balance_after` (`DECIMAL(15, 2)`), `reference_type` (string, nullable), `reference_id` (bigint unsigned, nullable), `description` (text, nullable).
- **Financial Invariant:** `balance_after = balance_before ± amount` must strictly validate.
- **Indexes:** `index(user_id, created_at)`, `index(reference_type, reference_id)`.

### Table 4: `deposits`
- **Purpose:** Inbound manual and automated balance top-ups.
- **Primary Key:** `id` (bigint unsigned, auto-increment)
- **Important Columns:** `user_id` (foreignId -> `users`), `amount` (`DECIMAL(15, 2)`), `gateway` (string 50), `proof_note` (string, nullable), `screenshot_path` (string, nullable), `status` (string 20, default 'pending'), `admin_note` (text, nullable), `reviewed_by` (foreignId -> `users`, nullable), `reviewed_at` (timestamp, nullable).
- **Indexes:** `index(user_id, status)`, `index(status, created_at)`.

### Table 5: `withdrawal_requests`
- **Purpose:** Outbound balance payout requests from customers.
- **Primary Key:** `id` (bigint unsigned, auto-increment)
- **Important Columns:** `reference_id` (string 50, unique), `user_id` (foreignId -> `users`), `withdrawal_method_id` (foreignId -> `withdrawal_methods`, nullable), `amount` (`DECIMAL(15, 2)`), `fee` (`DECIMAL(15, 2)`), `net_amount` (`DECIMAL(15, 2)`), `type` (enum: 'bank', 'mobile'), `status` (enum: 'pending', 'approved', 'rejected', 'processing', 'completed'), `admin_note` (text, nullable), `reviewed_by` (foreignId -> `users`, nullable), `reviewed_at` (timestamp, nullable), `processed_at` (timestamp, nullable).
- **Indexes:** `unique(reference_id)`, `index(user_id, status)`, `index(status, created_at)`.

### Table 6: `withdrawal_methods`
- **Purpose:** Saved bank accounts and mobile money accounts for payouts.
- **Primary Key:** `id` (bigint unsigned, auto-increment)
- **Important Columns:** `user_id` (foreignId -> `users`), `type` (enum: 'bank', 'mobile'), `bank_name` (string, nullable), `branch` (string, nullable), `routing_number` (string, nullable), `account_name` (string, nullable), `account_number` (string, nullable), `mobile_provider` (string, nullable), `mobile_number` (string, nullable), `is_default` (boolean, default 0), `is_verified` (boolean, default 0).
- **Indexes:** `index(user_id, type)`.

### Table 7: `reports`
- **Purpose:** Deal disputes and transaction conflict claims.
- **Primary Key:** `id` (bigint unsigned, auto-increment)
- **Important Columns:** `deal_id` (foreignId -> `deals`), `reported_by` (foreignId -> `users`), `reason` (string 100), `description` (text), `status` (string 20, default 'open'), `resolution_note` (text, nullable), `resolved_by` (foreignId -> `users`, nullable), `resolved_at` (timestamp, nullable).
- **Indexes:** `index(deal_id)`, `index(reported_by)`, `index(status)`.

### Table 8: `reviews`
- **Purpose:** Counterparty ratings and reputation reviews.
- **Primary Key:** `id` (bigint unsigned, auto-increment)
- **Important Columns:** `deal_id` (foreignId -> `deals`), `reviewer_id` (foreignId -> `users`), `reviewee_id` (foreignId -> `users`), `rating` (smallint unsigned), `comment` (text, nullable).
- **Indexes:** `index(deal_id)`, `index(reviewee_id)`.

### Table 9: `support_tickets` & `support_ticket_messages`
- **Purpose:** Customer support threads and communications.
- **Important Columns:** `support_tickets`: `id`, `ticket_id` (unique), `user_id` (foreignId -> `users`, nullable for guests), `subject`, `priority`, `status`, `assigned_to`.
- `support_ticket_messages`: `id`, `support_ticket_id`, `user_id`, `message`, `attachment_path`.

### Table 10: `ai_settings`, `ai_knowledge_bases`, `ai_chat_histories`
- **Purpose:** AI Support Engine hyper-parameters, local knowledge base, and message transcripts.
- **Important Columns:** `ai_settings.api_key` stored encrypted (`Crypt::encryptString`).

### Table 11: `banners`, `coupons`, `coupon_usages`, `categories`, `notifications`
- **Purpose:** Marketing banners, platform discount vouchers, voucher redemption records, deal classifications, and user alert notifications.

### Table 12: New Tables for CMS & System Configuration
- **`system_settings`**: `id`, `key` (string, unique), `value` (longText), `group` (string 50), `type` (string 20), `timestamps`. (Consolidates charges, site meta, fees, and menus).
- **`cms_pages`**: `id`, `slug` (string, unique), `title` (string), `content` (longText), `status` (string 20), `author_id` (foreignId -> `users`, nullable), `timestamps`.
- **`blog_posts`**: `id`, `slug` (string, unique), `title` (string), `excerpt` (text), `content` (longText), `category` (string 50), `author_id` (foreignId -> `users`), `status` (string 20), `timestamps`.

---

## 3. Data Integrity & Financial Field Audit

| Table | Column | V10 Definition | V12 Target Definition | Integrity Justification |
| :--- | :--- | :--- | :--- | :--- |
| `users` | `wallet_balance` | `FLOAT(10, 2)` | **`DECIMAL(15, 2)`** | Eliminates binary floating-point precision drift. |
| `deals` | `amount` | `DECIMAL(15, 2)` | `DECIMAL(15, 2)` | Verified intact. |
| `deals` | `buyer_pays` | `DECIMAL(15, 2)` | `DECIMAL(15, 2)` | Verified intact. |
| `deals` | `seller_gets` | `DECIMAL(15, 2)` | `DECIMAL(15, 2)` | Verified intact. |
| `transactions` | `amount` | `DECIMAL(15, 2)` | `DECIMAL(15, 2)` | Verified intact. |
| `transactions` | `balance_before` | `DECIMAL(15, 2)` | `DECIMAL(15, 2)` | Verified intact. |
| `transactions` | `balance_after` | `DECIMAL(15, 2)` | `DECIMAL(15, 2)` | Verified intact. |
| `deposits` | `amount` | `DECIMAL(15, 2)` | `DECIMAL(15, 2)` | Verified intact. |
| `withdrawal_requests`| `amount` | `DECIMAL(12, 2)` | **`DECIMAL(15, 2)`** | Standardized to 15 digits precision across all financial tables. |
| `withdrawal_requests`| `fee` | `DECIMAL(12, 2)` | **`DECIMAL(15, 2)`** | Standardized to 15 digits precision. |
| `withdrawal_requests`| `net_amount` | `DECIMAL(12, 2)` | **`DECIMAL(15, 2)`** | Standardized to 15 digits precision. |

---

## 4. 10-Step Safe Data Migration Strategy

To guarantee that historical records, transactions, user wallets, and active deals are migrated without data loss:

```
Step 1: Backup & Integrity Check
  -> Execute `sqlite3 database.sqlite "PRAGMA integrity_check;"`
  -> Create timestamped snapshot: `database.sqlite.v10-canonical.bak`

Step 2: Schema Preparation in V12
  -> Run V12 migrations on target SQLite database.
  -> Verify all 25 tables (22 canonical + 3 CMS/Settings) exist.

Step 3: Export Source Data to Structured Dumps
  -> Extract source records into individual JSON table archives.

Step 4: Data Type Transformations & Normalization
  -> Convert `FLOAT` wallet balances to exact string representations (`number_format($val, 2, '.', '')`).
  -> Sanitize datetime timestamps to standard ISO-8601 (`YYYY-MM-DD HH:MM:SS`).

Step 5: Disable Foreign Key Checks in Target SQLite
  -> Execute `PRAGMA foreign_keys = OFF;`

Step 6: Chunked Insertion in Topological Dependency Order
  1. `users`
  2. `categories`
  3. `withdrawal_methods`
  4. `deals`
  5. `deposits`
  6. `withdrawal_requests`
  7. `transactions`
  8. `reports`
  9. `reviews`
  10. `support_tickets` & `support_ticket_messages`
  11. `ai_settings`, `ai_knowledge_bases`, `ai_chat_histories`
  12. `banners`, `coupons`, `coupon_usages`, `notifications`

Step 7: Re-enable Foreign Key Constraints
  -> Execute `PRAGMA foreign_keys = ON;`
  -> Validate foreign key integrity: `PRAGMA foreign_key_check;`

Step 8: Ledger Reconciliation Assertion
  -> For every migrated user, verify:
     `ABS(users.wallet_balance - (initial + credits - debits)) < 0.001`
  -> Any discrepancy is flagged and halted before production sign-off.

Step 9: Automated Dry-Run Verification
  -> Execute PHPUnit database integrity test suite against migrated database.

Step 10: Rollback & Recovery Readiness
  -> If verification fails, immediately restore V12 baseline snapshot from Phase 0 backup.
```
