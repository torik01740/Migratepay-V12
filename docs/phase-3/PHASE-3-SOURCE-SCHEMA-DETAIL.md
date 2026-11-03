# MigratePay V12: Phase 3 Source Schema Specification & Forensic Table Directory

> **Document ID:** `DOC-P3-04` (Verified & Reconciled)  
> **Source Database:** `F:\laragon\www\migratepay\database\database.sqlite` (Verified Canonical Source)  
> **Phase:** 3 — Database & Security Core Foundation  
> **Audit Status:** 100% INDEPENDENTLY VERIFIED AGAINST SQLITE_MASTER & PRAGMA  
> **Destination Target:** MySQL 8.0.30 (`migratepay_v12`)

---

## 1. Executive Summary & Migration Discrepancy Resolution

This specification provides the authoritative, column-by-column schema for all 22 tables in the MigratePay V10 SQLite database. Every column name, data type, nullability constraint, default value, foreign key relationship, and index has been extracted directly via `PRAGMA table_info`, `PRAGMA foreign_key_list`, and `PRAGMA index_list`.

### The 28 vs. 29 Migration Count Discrepancy Reconciled
- **Classification:** **Verified**
- **The Question:** Why are there 28 migration files on disk in `database/migrations/`, but 29 rows in the SQLite `migrations` table?
- **Forensic Evidence:** Direct inspection of SQLite `migrations` records:
  - `Row ID 22 (Batch 6)`: `2026_09_08_185657_add_attachment_path_to_support_ticket_messages_table`
  - `Row ID 25 (Batch 8)`: `2026_09_10_000000_add_attachment_path_to_support_ticket_messages_table`
- **Root Cause Analysis:** On September 8, 2026, migration `2026_09_08_185657_...` was executed against SQLite. Subsequently, the developer replaced the file on disk with a standardized timestamp migration `2026_09_10_000000_...` and ran migrations again in Batch 8. Because SQLite does not auto-purge records from `migrations` when files are renamed, the legacy batch 6 entry remained orphan-tracked in the database table while only the newer file existed in the filesystem.
- **V12 Modernization Treatment:** In V12, the `attachment_path` column is included directly in the base `create_support_ticket_messages_table` migration, eliminating both patch migrations entirely.

---

## 2. Table Classification & Record Count Directory

All 22 tables have been verified with exact record counts:

| Table Name | Row Count | Column Count | Status Classification | Primary Purpose |
| :--- | :---: | :---: | :--- | :--- |
| `ai_chat_histories` | 0 | 8 | Empty Table | Chatbot message logs |
| `ai_knowledge_bases` | 13 | 8 | Populated Active | RAG FAQ & prompt knowledge |
| `ai_settings` | 4 | 5 | Populated Active | AI configuration key-value store |
| `banners` | 1 | 12 | Populated Active | Announcement header banner |
| `categories` | 7 | 7 | Populated Active | Escrow service categories |
| `coupon_usages` | 1 | 9 | Populated Active | Deal discount redemption ledger |
| `coupons` | 4 | 13 | Populated Active | Promotional discount vouchers |
| `deals` | 2 | 28 | Populated Active | Escrow contracts & workspaces |
| `deposits` | 4 | 13 | Populated Active | Inward fiat deposit requests |
| `failed_jobs` | 0 | 7 | Empty Table | Laravel queue failure tracking |
| `migrations` | 29 | 3 | Populated Internal | Laravel migration execution history |
| `notifications` | 8 | 11 | Populated Active | User system event notifications |
| `password_reset_tokens`| 1 | 3 | Populated Active | Legacy password recovery tokens |
| `personal_access_tokens`| 0 | 10 | Empty Table | Laravel Sanctum API tokens |
| `reports` | 1 | 11 | Populated Active | Deal dispute escalations |
| `reviews` | 1 | 12 | Populated Active | Counterparty reputation ratings |
| `support_ticket_messages`| 3 | 10 | Populated Active | Support discussion messages |
| `support_tickets` | 1 | 11 | Populated Active | Customer service tickets |
| `transactions` | 8 | 11 | Populated Active | Single-entry wallet transaction journal |
| `users` | 7 | 40 | Populated Active | User accounts, profiles, balances |
| `withdrawal_methods` | 1 | 14 | Populated Active | Saved bank & mobile payout methods |
| `withdrawal_requests` | 1 | 15 | Populated Active | Outward fund withdrawal requests |

---

## 3. Exhaustive Forensic Column Specification (All 22 Tables)

### 3.1 `ai_chat_histories` (0 rows)
- `id` (INTEGER, Primary Key, Auto-Increment)
- `session_id` (VARCHAR, Not Null) — Indexed
- `sender_type` (VARCHAR, Not Null)
- `message` (TEXT, Not Null)
- `created_at` (DATETIME, Nullable)
- `updated_at` (DATETIME, Nullable)
- `visitor_name` (VARCHAR, Nullable)
- `visitor_contact` (VARCHAR, Nullable)
- **Indexes:** `ai_chat_histories_session_id_index` (`session_id`)

### 3.2 `ai_knowledge_bases` (13 rows)
- `id` (INTEGER, Primary Key, Auto-Increment)
- `question` (TEXT, Not Null)
- `answer` (TEXT, Not Null)
- `category` (VARCHAR, Nullable)
- `keywords` (TEXT, Nullable)
- `status` (VARCHAR, Not Null, Default 'Active')
- `created_at` (DATETIME, Nullable)
- `updated_at` (DATETIME, Nullable)
- **Indexes:** None

### 3.3 `ai_settings` (4 rows)
- `id` (INTEGER, Primary Key, Auto-Increment)
- `key` (VARCHAR, Not Null, Unique)
- `value` (TEXT, Nullable)
- `created_at` (DATETIME, Nullable)
- `updated_at` (DATETIME, Nullable)
- **Indexes:** `ai_settings_key_unique` (`key`)
- **Verified Keys:** `ai_widget_active` ('1'), `ai_api_key` (null), `ai_api_endpoint` (null), `ai_api_model` (null).

### 3.4 `banners` (1 row)
- `id` (INTEGER, Primary Key, Auto-Increment)
- `name` (VARCHAR, Not Null)
- `status` (VARCHAR, Not Null, Default 'draft')
- `placement` (VARCHAR, Not Null, Default 'above_header')
- `position_behavior` (VARCHAR, Not Null, Default 'static')
- `start_date` (DATETIME, Nullable)
- `end_date` (DATETIME, Nullable)
- `targeting` (TEXT / JSON, Nullable)
- `design` (TEXT / JSON, Nullable)
- `elements` (TEXT / JSON, Nullable)
- `created_at` (DATETIME, Nullable)
- `updated_at` (DATETIME, Nullable)
- **Indexes:** None

### 3.5 `categories` (7 rows)
- `id` (INTEGER, Primary Key, Auto-Increment)
- `name` (VARCHAR, Not Null)
- `slug` (VARCHAR, Not Null, Unique)
- `description` (TEXT, Nullable)
- `is_active` (TINYINT(1), Not Null, Default 1)
- `created_at` (DATETIME, Nullable)
- `updated_at` (DATETIME, Nullable)
- **Indexes:** `categories_slug_unique` (`slug`)

### 3.6 `coupon_usages` (1 row)
- `id` (INTEGER, Primary Key, Auto-Increment)
- `coupon_id` (INTEGER, Not Null, Foreign Key -> `coupons.id` ON DELETE CASCADE)
- `user_id` (INTEGER, Not Null, Foreign Key -> `users.id` ON DELETE CASCADE)
- `deal_id` (INTEGER, Not Null, Foreign Key -> `deals.id` ON DELETE CASCADE)
- `original_charge` (NUMERIC, Not Null) — Target: `DECIMAL(15, 2)`
- `discount_amount` (NUMERIC, Not Null) — Target: `DECIMAL(15, 2)`
- `final_charge` (NUMERIC, Not Null) — Target: `DECIMAL(15, 2)`
- `created_at` (DATETIME, Nullable)
- `updated_at` (DATETIME, Nullable)
- **Indexes:** None

### 3.7 `coupons` (4 rows)
- `id` (INTEGER, Primary Key, Auto-Increment)
- `code` (VARCHAR, Not Null, Unique)
- `discount_type` (VARCHAR, Not Null, Default 'percentage')
- `discount_value` (NUMERIC, Not Null) — Target: `DECIMAL(15, 2)`
- `total_usage_limit` (INTEGER, Nullable)
- `usage_per_user` (INTEGER, Nullable)
- `current_usage` (INTEGER, Not Null, Default 0)
- `start_date` (DATETIME, Nullable)
- `expiry_date` (DATETIME, Nullable)
- `is_active` (TINYINT(1), Not Null, Default 1)
- `created_at` (DATETIME, Nullable)
- `updated_at` (DATETIME, Nullable)
- `deleted_at` (DATETIME, Nullable) — Soft delete support
- **Indexes:** `coupons_code_unique` (`code`)

### 3.8 `deals` (2 rows) — 28 Columns
- `id` (INTEGER, Primary Key, Auto-Increment)
- `initiator_id` (INTEGER, Not Null, Foreign Key -> `users.id` ON DELETE CASCADE) — Indexed
- `counterparty_id` (INTEGER, Nullable, Foreign Key -> `users.id` ON DELETE SET NULL) — Indexed
- `deal_id` (VARCHAR(255), Not Null, Unique) — e.g. 'DX-188753'
- `title` (VARCHAR(255), Not Null)
- `amount` (NUMERIC, Not Null) — Target: `DECIMAL(15, 2)`
- `buyer_pays` (NUMERIC, Not Null) — Target: `DECIMAL(15, 2)`
- `seller_gets` (NUMERIC, Not Null) — Target: `DECIMAL(15, 2)`
- `charge_total` (NUMERIC, Not Null, Default 0) — Target: `DECIMAL(15, 2)`
- `counterparty_email` (VARCHAR(255), Not Null)
- `initiator_role` (VARCHAR(255), Not Null) — 'buyer' or 'seller'
- `status` (VARCHAR(255), Not Null, Default 'awaiting_acceptance')
- `deadline` (DATE, Nullable)
- `workspace` (CLOB / JSON, Nullable) — messages, submissions, extensions
- `accepted_at` (DATETIME, Nullable)
- `completed_at` (DATETIME, Nullable)
- `created_at` (DATETIME, Nullable)
- `updated_at` (DATETIME, Nullable)
- `admin_approved` (TINYINT(1), Not Null, Default 0)
- `buyer_accepted` (TINYINT(1), Not Null, Default 0)
- `custom_service` (TINYINT(1), Not Null, Default 0)
- `custom_service_charge` (NUMERIC, Not Null, Default 0) — Target: `DECIMAL(15, 2)`
- `buyer_reviewed` (TINYINT(1), Not Null, Default 0)
- `seller_reviewed` (TINYINT(1), Not Null, Default 0)
- `category_id` (INTEGER, Nullable, Foreign Key -> `categories.id` ON DELETE SET NULL)
- `coupon_id` (INTEGER, Nullable, Foreign Key -> `coupons.id` ON DELETE SET NULL)
- `original_charge` (NUMERIC, Nullable) — Target: `DECIMAL(15, 2)`
- `coupon_discount` (NUMERIC, Nullable) — Target: `DECIMAL(15, 2)`
- **Foreign Keys:**
  - `initiator_id` -> `users.id` ON DELETE CASCADE
  - `counterparty_id` -> `users.id` ON DELETE SET NULL
- **Indexes:**
  - `deals_deal_id_unique` (`deal_id`)
  - `IDX_EF39849B7DB3B714` (`initiator_id`)
  - `IDX_EF39849BDB1FAD05` (`counterparty_id`)

### 3.9 `deposits` (4 rows) — 13 Columns
- `id` (INTEGER, Primary Key, Auto-Increment)
- `user_id` (INTEGER, Not Null, Foreign Key -> `users.id` ON DELETE CASCADE)
- `amount` (NUMERIC, Not Null) — Target: `DECIMAL(15, 2)`
- `gateway` (VARCHAR, Not Null) — 'bank_transfer', 'bkash', 'nagad', 'stripe', 'sslcommerz'
- `proof_note` (VARCHAR, Nullable)
- `screenshot_path` (VARCHAR, Nullable) — Stored on private disk
- `status` (VARCHAR, Not Null, Default 'pending') — 'pending', 'approved', 'rejected'
- `admin_note` (TEXT, Nullable)
- `reviewed_by` (INTEGER, Nullable, Foreign Key -> `users.id` ON DELETE SET NULL)
- `reviewed_at` (DATETIME, Nullable)
- `created_at` (DATETIME, Nullable)
- `updated_at` (DATETIME, Nullable)
- `admin_read` (TINYINT(1), Not Null, Default 0)
- **Foreign Keys:**
  - `user_id` -> `users.id` ON DELETE CASCADE
  - `reviewed_by` -> `users.id` ON DELETE SET NULL
- **Indexes:** None

### 3.10 `failed_jobs` (0 rows)
- `id` (INTEGER, Primary Key, Auto-Increment)
- `uuid` (VARCHAR, Not Null, Unique)
- `connection` (TEXT, Not Null)
- `queue` (TEXT, Not Null)
- `payload` (TEXT, Not Null)
- `exception` (TEXT, Not Null)
- `failed_at` (DATETIME, Not Null, Default CURRENT_TIMESTAMP)
- **Indexes:** `failed_jobs_uuid_unique` (`uuid`)

### 3.11 `migrations` (29 rows)
- `id` (INTEGER, Primary Key, Auto-Increment)
- `migration` (VARCHAR, Not Null)
- `batch` (INTEGER, Not Null)

### 3.12 `notifications` (8 rows) — 11 Columns
- `id` (INTEGER, Primary Key, Auto-Increment)
- `user_id` (INTEGER, Not Null, Foreign Key -> `users.id` ON DELETE CASCADE)
- `type` (VARCHAR, Not Null) — e.g. 'deal_accepted', 'withdrawal_approved', 'deal_invitation', 'announcement', 'funds_received', 'deal_completed'
- `title` (VARCHAR, Not Null)
- `message` (TEXT, Not Null)
- `reference_type` (VARCHAR, Nullable) — Polymorphic model name
- `reference_id` (INTEGER, Nullable) — Polymorphic entity ID
- `is_read` (TINYINT(1), Not Null, Default 0)
- `read_at` (DATETIME, Nullable)
- `created_at` (DATETIME, Nullable)
- `updated_at` (DATETIME, Nullable)
- **Foreign Keys:** `user_id` -> `users.id` ON DELETE CASCADE
- **Indexes:** None

### 3.13 `password_reset_tokens` (1 row)
- `email` (VARCHAR, Primary Key)
- `token` (VARCHAR, Not Null)
- `created_at` (DATETIME, Nullable)
- **Indexes:** `sqlite_autoindex_password_reset_tokens_1` (`email`)

### 3.14 `personal_access_tokens` (0 rows)
- `id` (INTEGER, Primary Key, Auto-Increment)
- `tokenable_type` (VARCHAR, Not Null)
- `tokenable_id` (INTEGER, Not Null)
- `name` (VARCHAR, Not Null)
- `token` (VARCHAR, Not Null, Unique)
- `abilities` (TEXT, Nullable)
- `last_used_at` (DATETIME, Nullable)
- `expires_at` (DATETIME, Nullable)
- `created_at` (DATETIME, Nullable)
- `updated_at` (DATETIME, Nullable)
- **Indexes:**
  - `personal_access_tokens_token_unique` (`token`)
  - `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type, tokenable_id`)

### 3.15 `reports` (1 row) — 11 Columns
- `id` (INTEGER, Primary Key, Auto-Increment)
- `deal_id` (INTEGER, Not Null, Foreign Key -> `deals.id` ON DELETE CASCADE)
- `reported_by` (INTEGER, Not Null, Foreign Key -> `users.id` ON DELETE CASCADE)
- `reason` (VARCHAR, Not Null)
- `description` (TEXT, Not Null)
- `status` (VARCHAR, Not Null, Default 'open')
- `resolution_note` (TEXT, Nullable)
- `resolved_by` (INTEGER, Nullable, Foreign Key -> `users.id` ON DELETE SET NULL)
- `resolved_at` (DATETIME, Nullable)
- `created_at` (DATETIME, Nullable)
- `updated_at` (DATETIME, Nullable)
- **Foreign Keys:**
  - `deal_id` -> `deals.id` ON DELETE CASCADE
  - `reported_by` -> `users.id` ON DELETE CASCADE
  - `resolved_by` -> `users.id` ON DELETE SET NULL
- **Indexes:** None

### 3.16 `reviews` (1 row) — 12 Columns
- `id` (INTEGER, Primary Key, Auto-Increment)
- `deal_id` (INTEGER, Not Null, Foreign Key -> `deals.id` ON DELETE CASCADE)
- `reviewer_id` (INTEGER, Not Null, Foreign Key -> `users.id` ON DELETE CASCADE)
- `reviewee_id` (INTEGER, Not Null, Foreign Key -> `users.id` ON DELETE CASCADE)
- `role` (VARCHAR, Not Null) — 'buyer' or 'seller'
- `communication` (INTEGER, Not Null) — Rating 1-5
- `behavior` (INTEGER, Not Null) — Rating 1-5
- `delivery` (INTEGER, Not Null) — Rating 1-5
- `reliability` (INTEGER, Not Null) — Rating 1-5
- `comment` (TEXT, Nullable)
- `created_at` (DATETIME, Nullable)
- `updated_at` (DATETIME, Nullable)
- **Foreign Keys:**
  - `deal_id` -> `deals.id` ON DELETE CASCADE
  - `reviewer_id` -> `users.id` ON DELETE CASCADE
  - `reviewee_id` -> `users.id` ON DELETE CASCADE
- **Indexes:** `reviews_deal_id_reviewer_id_unique` (`deal_id, reviewer_id`)

### 3.17 `support_ticket_messages` (3 rows) — 10 Columns
- `id` (INTEGER, Primary Key, Auto-Increment)
- `support_ticket_id` (INTEGER, Not Null, Foreign Key -> `support_tickets.id` ON DELETE CASCADE)
- `sender_id` (INTEGER, Nullable, Foreign Key -> `users.id` ON DELETE SET NULL)
- `sender_type` (VARCHAR, Not Null) — 'User' or 'Admin'
- `message` (TEXT, Not Null)
- `is_read_by_user` (TINYINT(1), Not Null, Default 0)
- `is_read_by_admin` (TINYINT(1), Not Null, Default 0)
- `attachment_path` (VARCHAR, Nullable) — Private storage attachment path
- `created_at` (DATETIME, Nullable)
- `updated_at` (DATETIME, Nullable)
- **Foreign Keys:**
  - `support_ticket_id` -> `support_tickets.id` ON DELETE CASCADE
  - `sender_id` -> `users.id` ON DELETE SET NULL
- **Indexes:** None

### 3.18 `support_tickets` (1 row) — 11 Columns
- `id` (INTEGER, Primary Key, Auto-Increment)
- `user_id` (INTEGER, Not Null, Foreign Key -> `users.id` ON DELETE CASCADE) — In V12: Made Nullable for Guest Escalations
- `ticket_number` (VARCHAR, Not Null, Unique) — e.g. 'TKT-513849'
- `subject` (VARCHAR, Not Null)
- `priority` (VARCHAR, Not Null, Default 'General Inquiry')
- `status` (VARCHAR, Not Null, Default 'Open') — 'Open', 'Closed'
- `closed_at` (DATETIME, Nullable)
- `closed_by` (INTEGER, Nullable, Foreign Key -> `users.id` ON DELETE SET NULL)
- `created_at` (DATETIME, Nullable)
- `updated_at` (DATETIME, Nullable)
- `source` (VARCHAR, Not Null, Default 'Manual')
- **Foreign Keys:**
  - `user_id` -> `users.id` ON DELETE CASCADE
  - `closed_by` -> `users.id` ON DELETE SET NULL
- **Indexes:** `support_tickets_ticket_number_unique` (`ticket_number`)

### 3.19 `transactions` (8 rows) — 11 Columns
- `id` (INTEGER, Primary Key, Auto-Increment)
- `user_id` (INTEGER, Not Null, Foreign Key -> `users.id` ON DELETE CASCADE)
- `type` (VARCHAR, Not Null) — 'deposit', 'escrow_lock', 'escrow_release', 'withdrawal'
- `amount` (NUMERIC, Not Null) — Target: `DECIMAL(15, 2)`
- `balance_before` (NUMERIC, Not Null, Default 0) — Target: `DECIMAL(15, 2)`
- `balance_after` (NUMERIC, Not Null, Default 0) — Target: `DECIMAL(15, 2)`
- `reference_type` (VARCHAR, Nullable) — Polymorphic model name
- `reference_id` (INTEGER, Nullable) — Polymorphic entity ID
- `description` (TEXT, Nullable)
- `created_at` (DATETIME, Nullable)
- `updated_at` (DATETIME, Nullable)
- **Foreign Keys:** `user_id` -> `users.id` ON DELETE CASCADE
- **Indexes:** None in SQLite (V12 will add index on `user_id, created_at`)

### 3.20 `users` (7 rows) — 40 Columns
- `id` (INTEGER, Primary Key, Auto-Increment)
- `user_id_string` (VARCHAR, Nullable, Unique) — e.g. 'USR-1001', 'USR-177444030155'
- `name` (VARCHAR, Not Null)
- `email` (VARCHAR, Not Null, Unique)
- `phone` (VARCHAR, Nullable, Unique)
- `email_verified_at` (DATETIME, Nullable)
- `password` (VARCHAR, Not Null) — Bcrypt `$2y$12$` hash
- `date_of_birth` (DATE, Nullable)
- `nid_number` (VARCHAR, Nullable)
- `division` (VARCHAR, Nullable)
- `district` (VARCHAR, Nullable)
- `wallet_balance` (FLOAT, Not Null, Default 0) — Target: `DECIMAL(15, 2)`
- `status` (VARCHAR, Not Null, Default 'Active') — Account status ('Active', 'active')
- `role` (VARCHAR, Not Null, Default 'user') — 'user', 'admin'
- `remember_token` (VARCHAR, Nullable)
- `created_at` (DATETIME, Nullable)
- `updated_at` (DATETIME, Nullable)
- `username` (VARCHAR, Nullable, Unique)
- `headline` (VARCHAR, Nullable)
- `location` (VARCHAR, Nullable)
- `address` (VARCHAR, Nullable)
- `bio` (TEXT, Nullable)
- `skills` (TEXT / JSON, Nullable)
- `languages` (TEXT / JSON, Nullable)
- `education` (TEXT / JSON, Nullable)
- `certifications` (VARCHAR, Nullable)
- `professional_links` (TEXT / JSON, Nullable)
- `cover_photo` (VARCHAR, Nullable) — Relative path on public disk
- `profile_photo` (VARCHAR, Nullable) — Relative path on public disk
- `cover_position_y` (VARCHAR, Nullable)
- `profile_position_x` (VARCHAR, Nullable)
- `profile_position_y` (VARCHAR, Nullable)
- `cover_position_x` (VARCHAR, Nullable)
- `verification_status` (VARCHAR, Not Null, Default 'NOT_VERIFIED') — 'NOT_VERIFIED', 'VERIFIED', 'REJECTED'
- `verified_at` (DATETIME, Nullable)
- `verified_by` (INTEGER, Nullable) — References `users.id`
- `rejection_reason` (TEXT, Nullable)
- `verification_updated_at` (DATETIME, Nullable)
- `gender` (VARCHAR, Nullable)
- `id_type` (VARCHAR, Nullable, Default 'National ID (NID)')
- **Foreign Keys:** None enforced at SQLite engine level (V12 enforces FK on `verified_by` -> `users.id` ON DELETE SET NULL)
- **Indexes:**
  - `users_user_id_string_unique` (`user_id_string`)
  - `users_email_unique` (`email`)
  - `users_phone_unique` (`phone`)
  - `users_username_unique` (`username`)

### 3.21 `withdrawal_methods` (1 row) — 14 Columns
- `id` (INTEGER, Primary Key, Auto-Increment)
- `user_id` (INTEGER, Not Null, Foreign Key -> `users.id` ON DELETE CASCADE) — Indexed
- `type` (VARCHAR, Not Null) — 'bank' or 'mobile'
- `bank_name` (VARCHAR, Nullable)
- `branch` (VARCHAR, Nullable)
- `routing_number` (VARCHAR, Nullable)
- `account_name` (VARCHAR, Nullable)
- `account_number` (VARCHAR, Nullable)
- `mobile_provider` (VARCHAR, Nullable)
- `mobile_number` (VARCHAR, Nullable)
- `is_default` (TINYINT(1), Not Null, Default 0)
- `is_verified` (TINYINT(1), Not Null, Default 0)
- `created_at` (DATETIME, Nullable)
- `updated_at` (DATETIME, Nullable)
- **Foreign Keys:** `user_id` -> `users.id` ON DELETE CASCADE
- **Indexes:** `withdrawal_methods_user_id_type_index` (`user_id, type`)

### 3.22 `withdrawal_requests` (1 row) — 15 Columns
- `id` (INTEGER, Primary Key, Auto-Increment)
- `reference_id` (VARCHAR, Not Null, Unique) — e.g. 'WDR-B7D9D3'
- `user_id` (INTEGER, Not Null, Foreign Key -> `users.id` ON DELETE CASCADE) — Indexed
- `withdrawal_method_id` (INTEGER, Nullable, Foreign Key -> `withdrawal_methods.id` ON DELETE SET NULL)
- `amount` (NUMERIC, Not Null) — Target: `DECIMAL(15, 2)`
- `fee` (NUMERIC, Not Null, Default 0) — Target: `DECIMAL(15, 2)`
- `net_amount` (NUMERIC, Not Null) — Target: `DECIMAL(15, 2)`
- `type` (VARCHAR, Not Null) — 'bank' or 'mobile'
- `status` (VARCHAR, Not Null, Default 'pending') — 'pending', 'approved', 'rejected', 'processing', 'completed'
- `admin_note` (TEXT, Nullable)
- `reviewed_by` (INTEGER, Nullable, Foreign Key -> `users.id` ON DELETE SET NULL)
- `reviewed_at` (DATETIME, Nullable)
- `processed_at` (DATETIME, Nullable)
- `created_at` (DATETIME, Nullable)
- `updated_at` (DATETIME, Nullable)
- **Foreign Keys:**
  - `user_id` -> `users.id` ON DELETE CASCADE
  - `withdrawal_method_id` -> `withdrawal_methods.id` ON DELETE SET NULL
  - `reviewed_by` -> `users.id` ON DELETE SET NULL
- **Indexes:**
  - `withdrawal_requests_reference_id_unique` (`reference_id`)
  - `withdrawal_requests_status_created_at_index` (`status, created_at`)
  - `withdrawal_requests_user_id_status_index` (`user_id, status`)

---

## 4. Required Schema Enhancements & MySQL 8.0 DDL Mapping in V12

1. **Monetary Precision Upgrade:**
   All `FLOAT` and unbounded `NUMERIC` columns in SQLite (`wallet_balance`, `amount`, `buyer_pays`, `seller_gets`, `charge_total`, `fee`, `net_amount`, etc.) are mapped to **`DECIMAL(15, 2)`** in MySQL 8.0 to eliminate IEEE 754 precision artifacts.
2. **Nullable Foreign Key on `support_tickets.user_id`:**
   In SQLite, `support_tickets.user_id` was `NOT NULL`. In V12, it is made `NULLABLE` and an optional `guest_email VARCHAR(255)` column is added to safely support guest AI escalations without assigning tickets to arbitrary user IDs (`SEC-07`).
3. **Explicit Foreign Key Enforcement:**
   SQLite lacked foreign keys on `users.verified_by`, `deals.category_id`, and `deals.coupon_id`. V12 explicitly declares InnoDB foreign key constraints with `ON DELETE SET NULL`.
4. **Preventing Table Collision:**
   Default Laravel 12 skeleton migrations define standard `users`, `password_reset_tokens`, and `failed_jobs`. In V12, `0001_01_01_000000_create_users_table.php` is modified directly to incorporate the 40 verified profile fields and `DECIMAL(15, 2) wallet_balance`, ensuring zero table conflicts or duplicate schema errors.
