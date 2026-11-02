# Database Schema Inventory: MigratePay V10

**Source Project:** MigratePay V10 (`F:\laragon\www\migratepay\database`)  
**Assessment Date:** 2026-11-02  
**Scope:** Migrations, Database Tables, Columns, Constraints, Indexes, Foreign Keys, Relationships, and Backup Discrepancies  

---

## 1. Migration History (28 Migrations)

| Order | Migration File | Created / Altered Tables | Key Columns & Constraints Added |
|---|---|---|---|
| 01 | `2014_10_12_000000_create_users_table.php` | `users` | `name`, `email` (unique), `password`, `wallet_balance` (default 0), `role` (default 'user'), `status` (default 'Active') |
| 02 | `2014_10_12_100000_create_password_reset_tokens_table.php` | `password_reset_tokens` | `email` (PK), `token`, `created_at` |
| 03 | `2019_08_19_000000_create_failed_jobs_table.php` | `failed_jobs` | `uuid` (unique), `connection`, `queue`, `payload`, `exception`, `failed_at` |
| 04 | `2019_12_14_000001_create_personal_access_tokens_table.php` | `personal_access_tokens` | Sanctum tokens table (`tokenable`, `name`, `token`, `abilities`) |
| 05 | `2024_07_23_000000_create_deposits_deals_transactions_tables.php` | `deposits`, `deals`, `transactions` | Core financial tables: deposit amounts & gateways; deal pricing & party references; ledger audit balances |
| 06 | `2024_07_23_000001_create_notifications_table.php` | `notifications` | Polymorphic notification tracking (`reference_type`, `reference_id`) |
| 07 | `2024_07_23_000002_update_deals_add_workflow_fields.php` | `deals` (alter) | Added `admin_approved`, `buyer_accepted`, `custom_service`, `custom_service_charge`, `buyer_reviewed`, `seller_reviewed` |
| 08 | `2024_07_23_000003_create_withdrawal_methods_and_requests_table.php` | `withdrawal_methods`, `withdrawal_requests` | Bank & mobile payout methods and withdrawal queue |
| 09 | `2024_07_23_000004_create_reviews_table.php` | `reviews` | 4-criterion rating system; unique compound index on `[deal_id, reviewer_id]` |
| 10 | `2024_07_23_000005_create_categories_table.php` | `categories` | Deal category taxonomy (`name`, `slug` unique) |
| 11 | `2026_07_29_184022_add_custom_category_to_deals_table.php` | `deals` (alter) | Added `custom_category` column |
| 12 | `2026_07_29_191146_add_admin_read_to_deposits_table.php` | `deposits` (alter) | Added `admin_read` boolean flag |
| 13 | `2026_09_01_000000_create_coupons_table.php` | `coupons` | Promo codes, discount types (`percentage`, `fixed`), limits, soft deletes |
| 14 | `2026_09_01_000001_create_coupon_usages_table.php` | `coupon_usages` | Coupon redemption audit log |
| 15 | `2026_09_01_000002_add_coupon_fields_to_deals_table.php` | `deals` (alter) | Added `coupon_id`, `original_charge`, `coupon_discount` |
| 16 | `2026_09_01_000003_create_banners_table.php` | `banners` | Notification banner scheduling, targeting, and design JSON |
| 17 | `2026_09_04_153705_add_profile_fields_to_users_table.php` | `users` (alter) | Added profile biography, skills, languages, headline, photos |
| 18 | `2026_09_06_163401_add_cover_position_x_to_users_table.php` | `users` (alter) | Added `cover_position_x` visual positioning |
| 19 | `2026_09_08_100000_add_verification_fields_to_users_table.php` | `users` (alter) | Added `verification_status`, `verified_at`, `verified_by`, `rejection_reason` |
| 20 | `2026_09_09_000000_create_support_tickets_table.php` | `support_tickets` | Support ticket tracking (`ticket_number` unique, priority, status) |
| 21 | `2026_09_09_000001_create_support_ticket_messages_table.php` | `support_ticket_messages` | Threaded ticket messages |
| 22 | `2026_09_10_000000_add_attachment_path_to_support_ticket_messages_table.php` | `support_ticket_messages` (alter) | Added file attachment path |
| 23 | `2026_09_18_000000_create_ai_knowledge_bases_table.php` | `ai_knowledge_bases` | Q&A pairs for automated support |
| 24 | `2026_09_18_000001_add_source_to_support_tickets_table.php` | `support_tickets` (alter) | Added `source` column (`Manual`, `AI_ESCALATION`) |
| 25 | `2026_09_23_235654_create_ai_chat_histories_table.php` | `ai_chat_histories` | Guest and user live chat transcript logs |
| 26 | `2026_09_23_235656_create_ai_settings_table.php` | `ai_settings` | Key-value store for AI configuration |
| 27 | `2026_09_24_161127_add_visitor_info_to_ai_chat_histories.php` | `ai_chat_histories` (alter) | Added `visitor_name`, `visitor_contact` |
| 28 | `2026_09_25_000000_add_gender_and_id_type_to_users_table.php` | `users` (alter) | Added `gender`, `id_type` to users table |

---

## 2. Table Catalogue (22 Tables)

### 1. `users`
- **Columns:**
  - `id` (INTEGER, PK, Autoincrement)
  - `user_id_string` (VARCHAR, Unique, Nullable)
  - `name` (VARCHAR, Not Null)
  - `username` (VARCHAR, Unique, Nullable)
  - `email` (VARCHAR, Unique, Not Null)
  - `phone` (VARCHAR, Unique, Nullable)
  - `email_verified_at` (DATETIME, Nullable)
  - `password` (VARCHAR, Not Null)
  - `date_of_birth` (DATE, Nullable)
  - `nid_number` (VARCHAR, Nullable)
  - `division` (VARCHAR, Nullable)
  - `district` (VARCHAR, Nullable)
  - `gender` (VARCHAR, Nullable)
  - `id_type` (VARCHAR, Default 'National ID (NID)')
  - `wallet_balance` (FLOAT, Not Null, Default 0) — *Issue: Should be DECIMAL(15,2)*
  - `status` (VARCHAR, Not Null, Default 'Active')
  - `role` (VARCHAR, Not Null, Default 'user')
  - `headline` (VARCHAR, Nullable)
  - `location` (VARCHAR, Nullable)
  - `address` (VARCHAR, Nullable)
  - `bio` (TEXT, Nullable)
  - `skills` (TEXT, Nullable)
  - `languages` (TEXT, Nullable)
  - `education` (TEXT, Nullable)
  - `certifications` (VARCHAR, Nullable)
  - `professional_links` (TEXT, Nullable)
  - `cover_photo` (VARCHAR, Nullable)
  - `profile_photo` (VARCHAR, Nullable)
  - `cover_position_x` (VARCHAR, Nullable)
  - `cover_position_y` (VARCHAR, Nullable)
  - `profile_position_x` (VARCHAR, Nullable)
  - `profile_position_y` (VARCHAR, Nullable)
  - `verification_status` (VARCHAR, Not Null, Default 'NOT_VERIFIED')
  - `verified_at` (DATETIME, Nullable)
  - `verified_by` (INTEGER, FK -> users.id, Nullable)
  - `rejection_reason` (TEXT, Nullable)
  - `verification_updated_at` (DATETIME, Nullable)
  - `remember_token` (VARCHAR, Nullable)
  - `created_at`, `updated_at` (DATETIME)
- **Indexes:** Primary (`id`), Unique on `user_id_string`, `email`, `phone`, `username`.

### 2. `deals`
- **Columns:**
  - `id` (INTEGER, PK, Autoincrement)
  - `deal_id` (VARCHAR(255), Unique, Not Null)
  - `title` (VARCHAR(255), Not Null)
  - `amount` (NUMERIC(10,0), Not Null)
  - `buyer_pays` (NUMERIC(10,0), Not Null)
  - `seller_gets` (NUMERIC(10,0), Not Null)
  - `charge_total` (NUMERIC(10,0), Default 0, Not Null)
  - `initiator_id` (INTEGER, FK -> users.id on delete cascade, Not Null)
  - `counterparty_id` (INTEGER, FK -> users.id on delete set null, Nullable)
  - `counterparty_email` (VARCHAR(255), Not Null)
  - `initiator_role` (VARCHAR(255), Not Null) — 'buyer' or 'seller'
  - `status` (VARCHAR(255), Default 'awaiting_acceptance', Not Null)
  - `deadline` (DATE, Nullable)
  - `workspace` (CLOB / JSON Text, Nullable) — stores messages, submissions, extensions
  - `category_id` (INTEGER, FK -> categories.id on delete set null, Nullable)
  - `coupon_id` (INTEGER, FK -> coupons.id on delete set null, Nullable)
  - `original_charge` (NUMERIC, Nullable)
  - `coupon_discount` (NUMERIC, Nullable)
  - `admin_approved` (TINYINT(1), Default 0)
  - `buyer_accepted` (TINYINT(1), Default 0)
  - `custom_service` (TINYINT(1), Default 0)
  - `custom_service_charge` (NUMERIC, Default 0)
  - `buyer_reviewed` (TINYINT(1), Default 0)
  - `seller_reviewed` (TINYINT(1), Default 0)
  - `accepted_at`, `completed_at`, `created_at`, `updated_at` (DATETIME)
- **Indexes:** Unique on `deal_id`, Indexes on `initiator_id`, `counterparty_id`.

### 3. `deposits`
- **Columns:** `id` (PK), `user_id` (FK -> users.id cascade), `amount` (NUMERIC), `gateway` (VARCHAR), `proof_note` (VARCHAR, Nullable), `screenshot_path` (VARCHAR, Nullable), `status` (VARCHAR, Default 'pending'), `admin_note` (TEXT, Nullable), `reviewed_by` (FK -> users.id set null), `reviewed_at` (DATETIME), `admin_read` (TINYINT(1), Default 0), `created_at`, `updated_at`.

### 4. `transactions`
- **Columns:** `id` (PK), `user_id` (FK -> users.id cascade), `type` (VARCHAR), `amount` (NUMERIC), `balance_before` (NUMERIC, Default 0), `balance_after` (NUMERIC, Default 0), `reference_type` (VARCHAR, Nullable), `reference_id` (INTEGER, Nullable), `description` (TEXT, Nullable), `created_at`, `updated_at`.

### 5. `withdrawal_methods`
- **Columns:** `id` (PK), `user_id` (FK -> users.id cascade), `type` (VARCHAR in 'bank', 'mobile'), `bank_name`, `branch`, `routing_number`, `account_name`, `account_number`, `mobile_provider`, `mobile_number`, `is_default` (TINYINT(1), Default 0), `is_verified` (TINYINT(1), Default 0), `created_at`, `updated_at`.
- **Indexes:** Compound index on `[user_id, type]`.

### 6. `withdrawal_requests`
- **Columns:** `id` (PK), `reference_id` (VARCHAR, Unique), `user_id` (FK -> users.id cascade), `withdrawal_method_id` (FK -> withdrawal_methods.id set null), `amount` (NUMERIC), `fee` (NUMERIC, Default 0), `net_amount` (NUMERIC), `type` (VARCHAR in 'bank', 'mobile'), `status` (VARCHAR in 'pending', 'approved', 'rejected', 'processing', 'completed', Default 'pending'), `admin_note` (TEXT), `reviewed_by` (FK -> users.id set null), `reviewed_at`, `processed_at`, `created_at`, `updated_at`.
- **Indexes:** Compound index on `[user_id, status]`, index on `[status, created_at]`.

### 7. `reviews`
- **Columns:** `id` (PK), `deal_id` (FK -> deals.id cascade), `reviewer_id` (FK -> users.id cascade), `reviewee_id` (FK -> users.id cascade), `role` (VARCHAR in 'buyer', 'seller'), `communication` (INT), `behavior` (INT), `delivery` (INT), `reliability` (INT), `comment` (TEXT, Nullable), `created_at`, `updated_at`.
- **Indexes:** Unique compound index on `[deal_id, reviewer_id]`.

### 8. `reports`
- **Columns:** `id` (PK), `deal_id` (FK -> deals.id cascade), `reported_by` (FK -> users.id cascade), `reason` (VARCHAR), `description` (TEXT), `status` (VARCHAR, Default 'open'), `resolution_note` (TEXT, Nullable), `resolved_by` (FK -> users.id set null), `resolved_at`, `created_at`, `updated_at`.

### 9. `categories`
- **Columns:** `id` (PK), `name` (VARCHAR), `slug` (VARCHAR, Unique), `description` (TEXT, Nullable), `is_active` (TINYINT(1), Default 1), `created_at`, `updated_at`.

### 10. `coupons`
- **Columns:** `id` (PK), `code` (VARCHAR, Unique), `discount_type` (VARCHAR in 'percentage', 'fixed', Default 'percentage'), `discount_value` (NUMERIC), `total_usage_limit` (INT, Nullable), `usage_per_user` (INT, Nullable), `current_usage` (INT, Default 0), `start_date` (DATETIME, Nullable), `expiry_date` (DATETIME, Nullable), `is_active` (TINYINT(1), Default 1), `created_at`, `updated_at`, `deleted_at`.

### 11. `coupon_usages`
- **Columns:** `id` (PK), `coupon_id` (FK -> coupons.id cascade), `user_id` (FK -> users.id cascade), `deal_id` (FK -> deals.id cascade), `original_charge` (NUMERIC), `discount_amount` (NUMERIC), `final_charge` (NUMERIC), `created_at`, `updated_at`.

### 12. `banners`
- **Columns:** `id` (PK), `name` (VARCHAR), `status` (VARCHAR, Default 'draft'), `placement` (VARCHAR, Default 'above_header'), `position_behavior` (VARCHAR, Default 'static'), `start_date`, `end_date` (DATETIME, Nullable), `targeting` (TEXT), `design` (TEXT), `elements` (TEXT), `created_at`, `updated_at`.

### 13. `notifications`
- **Columns:** `id` (PK), `user_id` (FK -> users.id cascade), `type` (VARCHAR), `title` (VARCHAR), `message` (TEXT), `reference_type` (VARCHAR, Nullable), `reference_id` (INT, Nullable), `is_read` (TINYINT(1), Default 0), `created_at`, `updated_at`.

### 14. `support_tickets`
- **Columns:** `id` (PK), `user_id` (FK -> users.id cascade), `ticket_number` (VARCHAR, Unique), `subject` (VARCHAR), `priority` (VARCHAR, Default 'General Inquiry'), `status` (VARCHAR in 'Open', 'Closed', 'Resolved', 'Replied', Default 'Open'), `source` (VARCHAR, Default 'Manual'), `closed_at`, `closed_by` (FK -> users.id set null), `created_at`, `updated_at`.

### 15. `support_ticket_messages`
- **Columns:** `id` (PK), `support_ticket_id` (FK -> support_tickets.id cascade), `sender_id` (FK -> users.id set null), `sender_type` (VARCHAR in 'User', 'Admin'), `message` (TEXT), `attachment_path` (VARCHAR, Nullable), `is_read_by_user` (TINYINT(1), Default 0), `is_read_by_admin` (TINYINT(1), Default 0), `created_at`, `updated_at`.

### 16. `ai_knowledge_bases`
- **Columns:** `id` (PK), `question` (TEXT), `answer` (TEXT), `category` (VARCHAR, Nullable), `keywords` (TEXT, Nullable), `status` (VARCHAR in 'Active', 'Draft', 'Inactive', Default 'Active'), `created_at`, `updated_at`.

### 17. `ai_chat_histories`
- **Columns:** `id` (PK), `session_id` (VARCHAR), `visitor_name` (VARCHAR, Nullable), `visitor_contact` (VARCHAR, Nullable), `sender_type` (VARCHAR in 'user', 'ai'), `message` (TEXT), `created_at`, `updated_at`.
- **Indexes:** Index on `session_id`.

### 18. `ai_settings`
- **Columns:** `id` (PK), `key` (VARCHAR, Unique), `value` (TEXT, Nullable), `created_at`, `updated_at`.

### 19. `password_reset_tokens`
- **Columns:** `email` (VARCHAR, PK), `token` (VARCHAR), `created_at` (DATETIME).

### 20. `personal_access_tokens`
- **Columns:** `id` (PK), `tokenable_type`, `tokenable_id`, `name`, `token` (VARCHAR(64), Unique), `abilities` (TEXT), `last_used_at`, `expires_at`, `created_at`, `updated_at`.

### 21. `failed_jobs`
- **Columns:** `id` (PK), `uuid` (VARCHAR, Unique), `connection`, `queue`, `payload`, `exception`, `failed_at`.

### 22. `migrations`
- **Columns:** `id` (PK), `migration` (VARCHAR), `batch` (INT).

---

## 3. Database Comparison: `database.sqlite` vs `database_backup.sql`

| Feature / Table | In `database.sqlite` (Current) | In `database_backup.sql` (Dumped 2026-09-04) | Status / Discrepancy Reason |
|---|---|---|---|
| `users`, `deals`, `deposits`, `transactions` | Present | Present | Identical core tables |
| `withdrawal_methods`, `withdrawal_requests` | Present | Present | Identical |
| `reviews`, `reports`, `categories` | Present | Present | Identical |
| `coupons`, `coupon_usages`, `banners` | Present | Present | Identical |
| `support_tickets` | **Present (Created 2026-09-09)** | **MISSING** | Backup was created before ticket system migration |
| `support_ticket_messages` | **Present (Created 2026-09-09)** | **MISSING** | Backup was created before ticket system migration |
| `ai_knowledge_bases` | **Present (Created 2026-09-18)** | **MISSING** | Backup was created before AI KB migration |
| `ai_chat_histories` | **Present (Created 2026-09-23)** | **MISSING** | Backup was created before AI live chat migration |
| `ai_settings` | **Present (Created 2026-09-23)** | **MISSING** | Backup was created before AI settings migration |
| `users.gender`, `users.id_type` | **Present (Added 2026-09-25)** | **MISSING** | Added in late September 2026 |

> **Crucial Finding:** The root SQL dump (`database_backup.sql`) is stale and represents the application state as of September 4, 2026. `database.sqlite` (dated October 2, 2026) is the true authoritative database source for V10.

---

## 4. Financial Schema & Ledger Assessment

1. **Balance Field Type Risk:** In the `users` table, `wallet_balance` is typed as `FLOAT` instead of `DECIMAL(15, 2)`. While PHP `round($balance, 2)` mitigates IEEE floating-point errors, true financial applications in V12 must migrate `wallet_balance` to `DECIMAL(15, 2)`.
2. **Transaction Ledger Integrity:** The `transactions` table correctly captures atomic `balance_before` and `balance_after` snapshots alongside reference IDs for every deposit, withdrawal, and escrow event.
3. **Missing Foreign Key Nullability:** On `support_tickets`, `user_id` is defined as non-nullable (`on delete cascade`). This causes foreign key violations when guest visitors escalate AI live chats to tickets.
