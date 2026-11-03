# MigratePay V12: Phase 2 Component Migration Map & Removal Strategy

> **Document ID:** `DOC-P2-03`  
> **Source Project:** `F:\laragon\www\migratepay`  
> **Destination Project:** `F:\laragon\www\Migratepay-V12`  
> **Phase:** 2 — Architecture Decisions & Blueprint  
> **Status:** AUTHORITATIVE COMPONENT MAPPING  

---

## 1. Executive Summary & Classification Taxonomy

This document establishes the verified migration classification for every application component in MigratePay V10. It supersedes and corrects the preliminary assumptions of DOC-P1-14 by using verified database table names (`reports`, `withdrawal_requests`, `withdrawal_methods`) and approved architectural decisions.

### Classification Taxonomy
1. **PRESERVE AND ADAPT:** Retain existing business logic and code structure; update syntax for Laravel 12 (PHP 8.4) and Chakra UI 3 compatibility.
2. **RECONSTRUCT:** Fundamentally rebuild component architecture to eliminate critical security flaws, financial race conditions, or unmaintainable implementations while retaining the intended feature capability.
3. **IMPROVE:** Preserve functional workflows while resolving edge cases, validation gaps, UX bottlenecks, or route mismatches.
4. **REPLACE:** Retire outdated implementation mechanisms in favor of modern, standard framework solutions.
5. **RETIRE:** Permanently remove obsolete, hazardous, or orphaned files without carrying forward technical debt.
6. **INVESTIGATE FURTHER:** Requires business owner confirmation before final action.

---

## 2. Complete Layer-by-Layer Component Mapping

### 2.1 Database Tables & Eloquent Models
| V10 Model & Table | V12 Target Component | Classification | Detailed Rationale & Required Changes |
| :--- | :--- | :--- | :--- |
| `User.php` (`users`) | `app/Models/User.php` | **IMPROVE** | Change `wallet_balance` from `FLOAT` to `DECIMAL(15, 2)`; adopt Laravel 12 `casts()` method; guard role attributes (`is_admin`, `is_active`, `is_verified`); add `role` column for staff access. |
| `Deal.php` (`deals`) | `app/Models/Deal.php` | **IMPROVE** | Add `fee_paid_by` column; enforce decimal casting on all financial columns; add buyer/seller Eloquent query scopes; add status constants. |
| `Transaction.php` (`transactions`) | `app/Models/Transaction.php` | **PRESERVE AND ADAPT** | Index `user_id`, `type`, `created_at`; enforce immutable ledger invariant (`balance_after = balance_before ± amount`). |
| `Deposit.php` (`deposits`) | `app/Models/Deposit.php` | **IMPROVE** | Store receipt files in private storage disk (`storage/app/private/deposits`); add `rejection_reason` column; prevent double approval. |
| `WithdrawalRequest.php` (`withdrawal_requests`) | `app/Models/WithdrawalRequest.php` | **IMPROVE** | Enforce decimal casting (`DECIMAL(15, 2)`); add `rejection_reason` column; retain relationship to `WithdrawalMethod`. |
| `WithdrawalMethod.php` (`withdrawal_methods`) | `app/Models/WithdrawalMethod.php` | **PRESERVE AND ADAPT** | Maintain masked account number helpers; validate routing numbers and mobile operator patterns. |
| `Report.php` (`reports`) | `app/Models/Report.php` | **IMPROVE** | Preserve historical deal reports; upgrade resolver action to link directly to ledger refunds or payouts. |
| `Review.php` (`reviews`) | `app/Models/Review.php` | **PRESERVE AND ADAPT** | Standardize Eloquent relationships with Deal and User models; add rating validation (1–5). |
| `SupportTicket.php` (`support_tickets`) | `app/Models/SupportTicket.php` | **IMPROVE** | Make `user_id` nullable to allow legitimate guest inquiries without spoofing super-admin `user_id = 1` (`SEC-07`). |
| `SupportTicketMessage.php` (`support_ticket_messages`) | `app/Models/SupportTicketMessage.php` | **PRESERVE AND ADAPT** | Store attachments on private disk; sanitize message content against XSS. |
| `Notification.php` (`notifications`) | `app/Models/Notification.php` | **PRESERVE AND ADAPT** | Add support for global broadcast announcements to resolve `AnnouncementsModule` without a new table. |
| `Category.php` (`categories`) | `app/Models/Category.php` | **PRESERVE AND ADAPT** | Standardize slug generation; enforce unique naming. |
| `Coupon.php` & `CouponUsage.php` | `app/Models/Coupon*.php` | **PRESERVE AND ADAPT** | Enforce expiration checks; validate usage limits per user. |
| `Banner.php` (`banners`) | `app/Models/Banner.php` | **PRESERVE AND ADAPT** | Validate image dimensions; sanitize redirect URLs. |
| `AiSetting.php` (`ai_settings`) | `app/Models/AiSetting.php` | **IMPROVE** | Encrypt `api_key` using Laravel's `encrypted` attribute casting (`Crypt::encryptString`). |
| `AiKnowledgeBase.php` | `app/Models/AiKnowledgeBase.php` | **PRESERVE AND ADAPT** | Register seeder in `DatabaseSeeder.php` (`DB-02`); optimize token overlap matching. |
| `AiChatHistory.php` | `app/Models/AiChatHistory.php` | **PRESERVE AND ADAPT** | Maintain visitor session attribution; prune old guest transcripts via scheduled task. |
| *CMS & Settings Tables* | `app/Models/Setting.php`, `Page.php`, `BlogPost.php` | **RECONSTRUCT** | **Create 3 lean tables:** `system_settings` (replaces localStorage charges & settings), `cms_pages` (pages module), `blog_posts` (blog module). |

---

### 2.2 Backend Business Services
| V10 Service | V12 Target Component | Classification | Detailed Rationale & Required Changes |
| :--- | :--- | :--- | :--- |
| `DealService.php` | `app/Services/DealService.php` | **RECONSTRUCT** | 1. Fix duplicate payout bug in `releaseFunds()` (`FIN-01`).<br>2. Add automated escrow refund on `declineDeal()` (`FIN-02`).<br>3. Move deliverables to private storage disk (`SEC-04`).<br>4. Wrap all state transitions in atomic `DB::transaction()` with row locks (`User::lockForUpdate()`). |
| `DepositService.php` | `app/Services/DepositService.php` | **IMPROVE** | 1. Move deposit screenshots to private storage (`SEC-05`).<br>2. Prevent duplicate admin approval with idempotency checks.<br>3. Enforce atomic wallet balance increment. |
| `WithdrawalService.php` | `app/Services/WithdrawalService.php` | **IMPROVE** | 1. Retain atomic balance hold upon request submission.<br>2. Guarantee atomic balance refund upon admin rejection.<br>3. Validate minimum/maximum tier limits. |
| `gateway_settings.json` handler | `app/Services/PaymentGatewayService.php` | **RECONSTRUCT** | 1. Retire unencrypted JSON file storage (`SEC-01`).<br>2. Implement encrypted database vault (`Crypt::encryptString`).<br>3. Sanitize public metadata endpoint. |
| *New:* `WalletService.php` | `app/Services/WalletService.php` | **REPLACE / EXTRACT** | Extract common wallet credit/debit/lock logic duplicated across DealService, DepositService, and WithdrawalService into a single authoritative service. |

---

### 2.3 HTTP Controllers & Routing
| V10 Controller | V12 Target Controller | Classification | Detailed Rationale & Required Changes |
| :--- | :--- | :--- | :--- |
| `PublicController.php` | `app/Http/Controllers/PublicController.php` | **PRESERVE AND ADAPT** | Deliver fast server-rendered Blade marketing views; inject dynamic SEO metadata. |
| `AuthController.php` | `app/Http/Controllers/Auth/*.php` | **RECONSTRUCT** | 1. Remove plaintext OTP leak in failure response (`SEC-03`).<br>2. Enforce `['auth', 'admin']` middleware on `/api/users/*` (`SEC-02`).<br>3. Separate customer auth and administrative user management into distinct controllers.<br>4. Implement missing `POST /api/user/change-password` endpoint (`API-03`). |
| `DealController.php` | `app/Http/Controllers/DealController.php` | **IMPROVE** | Standardize deliverable endpoint route to `POST /api/deals/{id}/deliverable` (`API-01`); add FormRequest validation. |
| `DepositController.php` | `app/Http/Controllers/DepositController.php` | **IMPROVE** | Validate image upload MIME types; sanitize notes; enforce admin role on approvals. |
| `WithdrawalController.php` | `app/Http/Controllers/WithdrawalController.php` | **PRESERVE AND ADAPT** | Maintain existing user request and admin approval methods with decimal precision. |
| `AiSupportController.php` | `app/Http/Controllers/AiSupportController.php` | **IMPROVE** | Sanitize guest ticket escalation to avoid admin impersonation (`SEC-07`); add rate limiting to `/api/ai/chat`. |
| `MediaController.php` | `app/Http/Controllers/Admin/MediaController.php` | **RECONSTRUCT** | Enforce MIME-type verification, maximum file sizes, and filename randomization to prevent RCE (`SEC-06`). |
| `PaymentGatewayController.php`| `app/Http/Controllers/Admin/PaymentGatewayController.php`| **RECONSTRUCT** | Restrict configuration routes strictly to super-admins; sanitize public gateway metadata endpoint. |
| *CMS & Settings Controllers* | `app/Http/Controllers/Admin/*.php` | **RECONSTRUCT** | Build clean RESTful controllers for `SystemSettingsController`, `PagesController`, and `BlogPostsController`. |

---

### 2.4 User Dashboard React Modules (10 Modules)
| V10 React Module | V12 Target Location | Classification | Required Transformations & Adaptations |
| :--- | :--- | :--- | :--- |
| `CommandCenterModule.jsx` | `resources/js/user/modules/CommandCenter.jsx` | **ADAPT TO CHAKRA 3** | Convert `<Stat>` blocks to `<Stat.Root>`; update metric cards to `colorPalette`. |
| `InitiateNewModule.jsx` | `resources/js/user/modules/InitiateNew.jsx` | **ADAPT TO CHAKRA 3** | Convert form inputs to `<Field.Root>`; validate buyer balance before submission. |
| `AddBalanceModule.jsx` | `resources/js/user/modules/AddBalance.jsx` | **ADAPT TO CHAKRA 3** | Update deposit proof file uploader; fetch sanitized gateway options. |
| `WithdrawFundsModule.jsx` | `resources/js/user/modules/WithdrawFunds.jsx` | **ADAPT TO CHAKRA 3** | Update withdrawal input validation with live fee preview. |
| `ProjectManagementModule.jsx` | `resources/js/user/modules/Deals/` | **ADAPT TO CHAKRA 3** | Fix deliverable submission endpoint (`API-01`); adapt deal status badges. |
| `SupportTicketsModule.jsx` | `resources/js/user/modules/SupportTickets.jsx` | **ADAPT TO CHAKRA 3** | Modernize messaging thread UI and ticket status badges. |
| `MessagesModule.jsx` | `resources/js/user/modules/Messages.jsx` | **ADAPT TO CHAKRA 3** | Adapt deal workspace messaging thread to Chakra 3 layout primitives. |
| `ActivityModule.jsx` | `resources/js/user/modules/Activity.jsx` | **ADAPT TO CHAKRA 3** | Adapt transaction history table to `<Table.Root>`. |
| `IroncladSecurityModule.jsx` | `resources/js/user/modules/Security.jsx` | **IMPROVE & ADAPT** | Connect real password change endpoint (`API-03`) and 2FA authentication state. |
| `ProfileSettingsModule.jsx` | `resources/js/user/modules/ProfileSettings.jsx`| **IMPROVE & ADAPT** | Fix profile update route mismatch (`API-02`); wire avatar file upload. |

---

### 2.5 Admin Dashboard React Modules (23 Modules)
| V10 Admin Module | V12 Target Component | Classification | Required Transformations & Adaptations |
| :--- | :--- | :--- | :--- |
| `DashboardModule.jsx` | `resources/js/admin/modules/Dashboard.jsx` | **ADAPT TO CHAKRA 3** | Convert metric cards, stat grids, and action tiles to Chakra 3. |
| `UsersManagementModule.jsx` | `resources/js/admin/modules/Users.jsx` | **IMPROVE & ADAPT** | Secure backend routes with admin middleware; adapt data table to `<Table.Root>`. |
| `DealsManagementModule.jsx` | `resources/js/admin/modules/Deals.jsx` | **IMPROVE & ADAPT** | Connect admin arbitration actions to verified, non-duplicating payout service. |
| `DepositsManagementModule.jsx` | `resources/js/admin/modules/Deposits.jsx` | **IMPROVE & ADAPT** | Stream receipts via secure authorized route; prevent double approvals. |
| `WithdrawalsManagementModule.jsx`| `resources/js/admin/modules/Withdrawals.jsx` | **IMPROVE & ADAPT** | Add rejection modal with required reason input. |
| `ReportsManagementModule.jsx` | `resources/js/admin/modules/Reports.jsx` | **IMPROVE & ADAPT** | Implement 3-way arbitration settlement modal (Buyer / Seller / Split). |
| `AiLiveChatModule.jsx` & `AiSettingsModule.jsx`| `resources/js/admin/modules/Ai/` | **IMPROVE & ADAPT** | Secure AI API key input; display real-time chat history. |
| `AnnouncementsModule.jsx` | `resources/js/admin/modules/Announcements.jsx` | **IMPROVE & ADAPT** | Wire to `NotificationController::store` with support for all-user broadcast. |
| `ChargeManagementModule.jsx` | `resources/js/admin/modules/Charges.jsx` | **RECONSTRUCT** | Replace `localStorage` with `system_settings` API (`GET/PUT /api/admin/settings/charges`). |
| `SiteSettingsModule.jsx` | `resources/js/admin/modules/SiteSettings.jsx` | **RECONSTRUCT** | Replace `localStorage` with `system_settings` API (`GET/PUT /api/admin/settings/site`). |
| `ManagePagesModule.jsx` | `resources/js/admin/modules/Pages.jsx` | **RECONSTRUCT** | Replace `localStorage` with `cms_pages` API (`GET/PUT /api/admin/pages`). |
| `MenusModule.jsx` | `resources/js/admin/modules/Menus.jsx` | **RECONSTRUCT** | Replace `localStorage` with `system_settings` JSON API (`GET/PUT /api/admin/settings/menus`). |
| `BlogPostsModule.jsx` | `resources/js/admin/modules/BlogPosts.jsx` | **RECONSTRUCT** | Replace `localStorage` with `blog_posts` API (`GET/POST/PUT/DELETE /api/admin/blogs`). |
| `UserSettingsModule.jsx` | `resources/js/admin/modules/Staff.jsx` | **RECONSTRUCT** | Replace `localStorage` with `users` staff management API (`GET/POST /api/admin/staff`). |
| `BackupModule.jsx` | `resources/js/admin/modules/Backup.jsx` | **RECONSTRUCT** | Replace `setTimeout` placeholder (`ARCH-02`) with real artisan SQLite backup trigger. |
| `ManageCategoryModule.jsx` | `resources/js/admin/modules/Categories.jsx` | **ADAPT TO CHAKRA 3** | Adapt category table and modal to Chakra 3 `<Dialog.Root>`. |
| `CouponsModule.jsx` | `resources/js/admin/modules/Coupons.jsx` | **ADAPT TO CHAKRA 3** | Adapt coupon table and creation dialog to Chakra 3. |
| `BannersModule.jsx` | `resources/js/admin/modules/Banners.jsx` | **ADAPT TO CHAKRA 3** | Adapt banner image table and placement toggles to Chakra 3. |
| `MediaModule.jsx` | `resources/js/admin/modules/Media.jsx` | **IMPROVE & ADAPT** | Add strict file size and MIME validation to uploader. |
| `ProjectSubmissionModule.jsx` | `resources/js/admin/modules/Submissions.jsx` | **ADAPT TO CHAKRA 3** | Adapt submission moderation view to Chakra 3. |
| `AnalyticsModule.jsx` | `resources/js/admin/modules/Analytics.jsx` | **IMPROVE & ADAPT** | Wire completely to `AnalyticsController` for real transaction volume stats. |

---

## 3. Hazardous & Obsolete File Removal Strategy

The following files identified in V10 will be **permanently retired and excluded from V12**:

| File to Retire | Reason for Retirement & Evidence | Safety & Impact Analysis |
| :--- | :--- | :--- |
| `test_login.php` | Contains plaintext admin test password checks (`Faitor@911`). | **100% Safe to remove.** Test artifact left in root; zero application references. |
| `reset_password.php` | Backdoor script force-updating admin password without authentication. | **100% Safe to remove.** Security vulnerability (`SEC-08`); zero application references. |
| `api_out.txt` | Raw dump of JSON test responses. | **100% Safe to remove.** Scratch file. |
| `composer-setup.php` | Installer executable left in project root. | **100% Safe to remove.** One-off installer. |
| `database_backup.sql` | Outdated MySQL snapshot (missing 5 active SQLite tables). | **100% Safe to remove.** Replaced by canonical migrations and SQLite database. |
| `php.exe` (in root) | Standalone executable binary left in web directory. | **100% Safe to remove.** Binary junk. |
