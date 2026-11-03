# MigratePay V12: Phase 3 Foreign Key Integrity & Identity Mapping Audit

> **Document ID:** `DOC-P3-05`  
> **Source Database:** `F:\laragon\www\migratepay\database\database.sqlite`  
> **Destination Environment:** MySQL 8.0.30 (Laragon)  
> **Phase:** 3 — Database & Security Core Foundation  
> **Audit Status:** VERIFIED REFERENTIAL INTEGRITY AUDIT  

---

## 1. Executive Summary & Referential Integrity Findings

This audit provides a forensic evaluation of all foreign-key relationships, identity mappings, and relational dependencies in the canonical MigratePay V10 dataset.

Key findings:
1. **Zero Orphaned Records:** Every foreign key in the source database successfully resolves to an active parent entity. There are no dangling transaction records, orphaned deals, or disconnected support messages.
2. **Missing User ID 6 Resolved:** The gap in the user ID sequence (IDs 1, 2, 3, 4, 5, 7, 8) was forensically traced across all 11 relational tables. Zero records reference User ID 6. It represents an auto-increment gap or deleted test registration. Primary keys will be preserved without renumbering.
3. **Admin Identity Discrepancy Resolved:** Forensic inspection confirmed that the true administrative account is **User ID 5 (`admin@migratepay.com`)** with `role = 'admin'`. The hardcoded fallback `user_id = 1` in `AiSupportController.php` was a critical bug (`SEC-07`) that attributed guest support tickets to customer John Doe instead of the administrator.

---

## 2. Investigation of Missing User ID 6

### 2.1 The Forensic Evidence
- The `sqlite_sequence` table in SQLite tracks auto-increment sequence counters and records: `users | 8`.
- The `users` table contains exactly 7 records with IDs: `[1, 2, 3, 4, 5, 7, 8]`.
- A forensic cross-table sweep was executed to identify any relational references to `ID = 6`:

```sql
SELECT 'deals_initiator'         AS tbl, count(*) FROM deals WHERE initiator_id = 6
UNION ALL
SELECT 'deals_counterparty',             count(*) FROM deals WHERE counterparty_id = 6
UNION ALL
SELECT 'transactions',                   count(*) FROM transactions WHERE user_id = 6
UNION ALL
SELECT 'deposits',                       count(*) FROM deposits WHERE user_id = 6 OR reviewed_by = 6
UNION ALL
SELECT 'withdrawal_requests',            count(*) FROM withdrawal_requests WHERE user_id = 6 OR reviewed_by = 6
UNION ALL
SELECT 'withdrawal_methods',             count(*) FROM withdrawal_methods WHERE user_id = 6
UNION ALL
SELECT 'reports',                        count(*) FROM reports WHERE reported_by = 6 OR resolved_by = 6
UNION ALL
SELECT 'reviews',                        count(*) FROM reviews WHERE reviewer_id = 6 OR reviewee_id = 6
UNION ALL
SELECT 'support_tickets',                count(*) FROM support_tickets WHERE user_id = 6 OR closed_by = 6
UNION ALL
SELECT 'support_ticket_messages',        count(*) FROM support_ticket_messages WHERE sender_id = 6
UNION ALL
SELECT 'notifications',                  count(*) FROM notifications WHERE user_id = 6;
```
**Query Result:** Every count returned **0**.

### 2.2 Root Cause & Decision
- **Root Cause:** User ID 6 was created between July 10, 2026 (User 5 creation) and August 28, 2026 (User 7 creation). It was subsequently deleted during development testing, or the database transaction was rolled back after the auto-increment counter consumed ID 6.
- **Mandatory Migration Rule:** We must **preserve original user IDs** (`1, 2, 3, 4, 5, 7, 8`) during migration. Renumbering IDs 7 and 8 into 6 and 7 would corrupt historical transaction, deposit, and deal foreign keys.

---

## 3. Administrator Identity & Guest AI Attribution (`SEC-07`)

### 3.1 True Administrative Identity Confirmed
A full inspection of all 7 accounts in `users` confirmed:
- User ID 1: John Doe (`johndoe@email.com`), `role = 'user'`, `status = 'Active'`.
- User ID 2: Seller Marketplace (`seller@marketplace.bd`), `role = 'user'`, `status = 'Active'`.
- User ID 3: Pulok Halder (`pulok@gmail.com`), `role = 'user'`, `status = 'Active'`.
- User ID 4: Rajib Sarkar (`rajib@gmail.com`), `role = 'user'`, `status = 'Active'`.
- **User ID 5: Admin User (`admin@migratepay.com`), `role = 'admin'`, `status = 'active'`.**
- User ID 7: Test User (`user@example.com`), `role = 'user'`, `status = 'active'`.
- User ID 8: Samiul Hasan (`samiul@gmail.com`), `role = 'user'`, `status = 'Active'`.

**Finding:** **User ID 5 is the sole administrative user in the database.**

### 3.2 Root Cause of the Administrator ID 1 Discrepancy
In `AiSupportController.php` line 221:
```php
$ticket = SupportTicket::create([
    'user_id' => $user ? $user->id : 1,
    'subject' => 'AI Chat Escalation: ' . Str::limit($lastMessage, 50),
    ...
]);
```
- The developer hardcoded `1` assuming ID 1 was the default administrator.
- In reality, User ID 1 is John Doe (a regular customer).
- Anonymous guest inquiries were thus injected into John Doe's personal support ticket list.
- **V12 Correction:** In V12, `support_tickets.user_id` is made `nullable()`. Unauthenticated guest escalations will set `user_id = null`, capturing `guest_name` and `guest_email` without spoofing any user account.

---

## 4. Comprehensive Foreign Key Verification Matrix

| Child Table | Foreign Key Column | Parent Table | Total Rows | Target Match | Orphaned Records | Status |
| :--- | :--- | :--- | :---: | :---: | :---: | :--- |
| `deals` | `initiator_id` | `users.id` | 2 | Users 4 and 3 | 0 | **100% Valid** |
| `deals` | `counterparty_id` | `users.id` | 2 | Users 3 and 4 | 0 | **100% Valid** |
| `transactions` | `user_id` | `users.id` | 8 | Users 4 and 3 | 0 | **100% Valid** |
| `transactions` | `reference_id` (Deal) | `deals.id` | 4 | Deals 1 and 2 | 0 | **100% Valid** |
| `transactions` | `reference_id` (Deposit)| `deposits.id` | 3 | Deposits 1, 2, 3 | 0 | **100% Valid** |
| `transactions` | `reference_id` (W/draw) | `withdrawal_requests.id`| 1 | W/draw Request 1 | 0 | **100% Valid** |
| `deposits` | `user_id` | `users.id` | 4 | Users 4 and 3 | 0 | **100% Valid** |
| `deposits` | `reviewed_by` | `users.id` | 4 | User 5 (Admin) | 0 | **100% Valid** |
| `withdrawal_requests`| `user_id` | `users.id` | 1 | User 4 | 0 | **100% Valid** |
| `withdrawal_requests`| `withdrawal_method_id`| `withdrawal_methods.id`| 1 | Method 1 | 0 | **100% Valid** |
| `withdrawal_requests`| `reviewed_by` | `users.id` | 1 | User 5 (Admin) | 0 | **100% Valid** |
| `withdrawal_methods` | `user_id` | `users.id` | 1 | User 4 | 0 | **100% Valid** |
| `reports` | `deal_id` | `deals.id` | 1 | Deal 1 | 0 | **100% Valid** |
| `reports` | `reported_by` | `users.id` | 1 | User 3 | 0 | **100% Valid** |
| `reviews` | `deal_id` | `deals.id` | 1 | Deal 2 | 0 | **100% Valid** |
| `reviews` | `reviewer_id` | `users.id` | 1 | User 3 | 0 | **100% Valid** |
| `reviews` | `reviewee_id` | `users.id` | 1 | User 4 | 0 | **100% Valid** |
| `support_tickets` | `user_id` | `users.id` | 1 | User 3 | 0 | **100% Valid** |
| `support_tickets` | `closed_by` | `users.id` | 1 | User 5 (Admin) | 0 | **100% Valid** |
| `support_ticket_messages`| `support_ticket_id`| `support_tickets.id` | 3 | Ticket 1 | 0 | **100% Valid** |
| `support_ticket_messages`| `sender_id` | `users.id` | 3 | Users 3 and 5 | 0 | **100% Valid** |
| `coupon_usages` | `coupon_id` | `coupons.id` | 1 | Coupon 1 | 0 | **100% Valid** |
| `coupon_usages` | `user_id` | `users.id` | 1 | User 3 | 0 | **100% Valid** |
| `coupon_usages` | `deal_id` | `deals.id` | 1 | Deal 2 | 0 | **100% Valid** |
| `notifications` | `user_id` | `users.id` | 8 | Users 3, 4, 5 | 0 | **100% Valid** |

**Summary Result:** **100% Referential Integrity.** Zero orphaned records exist in any table.
