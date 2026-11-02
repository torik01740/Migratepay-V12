# Backend Inventory: MigratePay V10

**Source Project:** MigratePay V10 (`F:\laragon\www\migratepay\app`)  
**Assessment Date:** 2026-11-02  
**Scope:** Models, Controllers, Services, Middleware, Providers, Commands, and Architecture  

---

## 1. Models Inventory (`app/Models/`)

The V10 application contains **18 Eloquent Models**. All models extend `Illuminate\Database\Eloquent\Model` (with `User` extending `Illuminate\Foundation\Auth\User`).

| # | Model Class | File Path | Table Name | Primary Key | Key Fillable Fields | Relationships | Business Rules & Notes |
|---|---|---|---|---|---|---|---|
| 1 | `User` | `app/Models/User.php` | `users` | `id` (int) | `user_id_string`, `name`, `username`, `email`, `phone`, `password`, `date_of_birth`, `nid_number`, `division`, `district`, `gender`, `id_type`, `wallet_balance`, `status`, `role`, `headline`, `location`, `address`, `bio`, `skills`, `languages`, `education`, `certifications`, `professional_links`, `cover_photo`, `profile_photo`, `verification_status`, `verified_at`, `verified_by`, `rejection_reason` | `hasMany(Deal::class, 'initiator_id')`, `hasMany(Deal::class, 'counterparty_id')`, `hasMany(Deposit::class)`, `hasMany(WithdrawalRequest::class)`, `hasMany(Transaction::class)`, `hasMany(WithdrawalMethod::class)`, `hasMany(SupportTicket::class)` | Float balance representation. Default role: `user`. Statuses: `Active`, `Suspended`, `Banned`. Verification: `NOT_VERIFIED`, `PENDING`, `VERIFIED`, `REJECTED`. |
| 2 | `Deal` | `app/Models/Deal.php` | `deals` | `id` (int) | `deal_id`, `title`, `amount`, `buyer_pays`, `seller_gets`, `charge_total`, `counterparty_email`, `initiator_role`, `status`, `deadline`, `workspace`, `category_id`, `coupon_id`, `original_charge`, `coupon_discount`, `admin_approved`, `buyer_accepted`, `custom_service`, `custom_service_charge` | `belongsTo(User::class, 'initiator_id')`, `belongsTo(User::class, 'counterparty_id')`, `belongsTo(Category::class)`, `belongsTo(Coupon::class)`, `hasMany(Report::class)`, `hasMany(Review::class)` | `workspace` is cast to `array`/JSON containing `messages`, `submissions`, `extensions`. Status machine: `awaiting_acceptance`, `funds_secured`, `buyer_review`, `completed`, `released`, `disputed`, `declined`. |
| 3 | `Deposit` | `app/Models/Deposit.php` | `deposits` | `id` (int) | `user_id`, `amount`, `gateway`, `proof_note`, `screenshot_path`, `status`, `admin_note`, `reviewed_by`, `reviewed_at`, `admin_read` | `belongsTo(User::class, 'user_id')`, `belongsTo(User::class, 'reviewed_by')` | Statuses: `pending`, `approved`, `rejected`. `admin_read` tracks unread admin banner notices. Manual payment workflow. |
| 4 | `Transaction` | `app/Models/Transaction.php` | `transactions` | `id` (int) | `user_id`, `type`, `amount`, `balance_before`, `balance_after`, `reference_type`, `reference_id`, `description` | `belongsTo(User::class)` | Master ledger entry. Types: `escrow_lock`, `escrow_release`, `deposit`, `withdrawal`, `withdrawal_rejected`. Stores snapshot of balance before and after. |
| 5 | `WithdrawalRequest` | `app/Models/WithdrawalRequest.php` | `withdrawal_requests` | `id` (int) | `reference_id`, `user_id`, `withdrawal_method_id`, `amount`, `fee`, `net_amount`, `type`, `status`, `admin_note`, `reviewed_by`, `reviewed_at`, `processed_at` | `belongsTo(User::class)`, `belongsTo(WithdrawalMethod::class)`, `belongsTo(User::class, 'reviewed_by')` | Statuses: `pending`, `approved`, `rejected`, `processing`, `completed`. Types: `bank`, `mobile`. |
| 6 | `WithdrawalMethod` | `app/Models/WithdrawalMethod.php` | `withdrawal_methods` | `id` (int) | `user_id`, `type`, `bank_name`, `branch`, `routing_number`, `account_name`, `account_number`, `mobile_provider`, `mobile_number`, `is_default`, `is_verified` | `belongsTo(User::class)`, `hasMany(WithdrawalRequest::class)` | Stores user payout destinations. Cannot be deleted while pending withdrawal exists. |
| 7 | `SupportTicket` | `app/Models/SupportTicket.php` | `support_tickets` | `id` (int) | `user_id`, `ticket_number`, `subject`, `priority`, `status`, `closed_at`, `closed_by`, `source` | `belongsTo(User::class)`, `hasMany(SupportTicketMessage::class)`, `belongsTo(User::class, 'closed_by')` | Statuses: `Open`, `Closed`, `Resolved`, `Replied`. Sources: `Manual`, `AI_ESCALATION`. Priorities: `General Inquiry`, `High`, `Urgent`. |
| 8 | `SupportTicketMessage` | `app/Models/SupportTicketMessage.php` | `support_ticket_messages` | `id` (int) | `support_ticket_id`, `sender_id`, `sender_type`, `message`, `attachment_path`, `is_read_by_user`, `is_read_by_admin` | `belongsTo(SupportTicket::class)`, `belongsTo(User::class, 'sender_id')` | Sender types: `User`, `Admin`. Tracks read status independently. |
| 9 | `AiKnowledgeBase` | `app/Models/AiKnowledgeBase.php` | `ai_knowledge_bases` | `id` (int) | `question`, `answer`, `category`, `keywords`, `status` | None directly (standalone knowledge entity) | Statuses: `Active`, `Draft`, `Inactive`. Used by `AiSupportController` for dynamic prompt generation and keyword scoring. |
| 10 | `AiChatHistory` | `app/Models/AiChatHistory.php` | `ai_chat_histories` | `id` (int) | `session_id`, `visitor_name`, `visitor_contact`, `sender_type`, `message` | None (indexed by `session_id`) | Sender types: `user`, `ai`. Stores unauthenticated visitor conversations for context and admin audit. |
| 11 | `AiSetting` | `app/Models/AiSetting.php` | `ai_settings` | `id` (int) | `key`, `value` | None (key-value store) | Helper method `getVal($key, $default = null)`. Stores OpenAI API key, model, endpoint, system KB toggle. |
| 12 | `Banner` | `app/Models/Banner.php` | `banners` | `id` (int) | `name`, `status`, `placement`, `position_behavior`, `start_date`, `end_date`, `targeting`, `design`, `elements` | None | Scopes: `active()`. Placements: `above_header`. Statuses: `draft`, `active`, `scheduled`, `archived`. Casts JSON attributes. |
| 13 | `Category` | `app/Models/Category.php` | `categories` | `id` (int) | `name`, `slug`, `description`, `is_active` | `hasMany(Deal::class)` | Deal classification. Slug is unique. |
| 14 | `Coupon` | `app/Models/Coupon.php` | `coupons` | `id` (int) | `code`, `discount_type`, `discount_value`, `total_usage_limit`, `usage_per_user`, `current_usage`, `start_date`, `expiry_date`, `is_active` | `hasMany(CouponUsage::class)`, `hasMany(Deal::class)` | Soft deletes enabled. `isValidForUse()` checks date range, active flag, and total usage limits. Types: `percentage`, `fixed`. |
| 15 | `CouponUsage` | `app/Models/CouponUsage.php` | `coupon_usages` | `id` (int) | `coupon_id`, `user_id`, `deal_id`, `original_charge`, `discount_amount`, `final_charge` | `belongsTo(Coupon::class)`, `belongsTo(User::class)`, `belongsTo(Deal::class)` | Audit trail of redeemed coupons per user per deal. |
| 16 | `Notification` | `app/Models/Notification.php` | `notifications` | `id` (int) | `user_id`, `type`, `title`, `message`, `reference_type`, `reference_id`, `is_read` | `belongsTo(User::class)` | Polymorphic references (`reference_type`, `reference_id`). Unread counter queried by dashboard header polling. |
| 17 | `Report` | `app/Models/Report.php` | `reports` | `id` (int) | `deal_id`, `reported_by`, `reason`, `description`, `status`, `resolution_note`, `resolved_by`, `resolved_at` | `belongsTo(Deal::class)`, `belongsTo(User::class, 'reported_by')`, `belongsTo(User::class, 'resolved_by')` | Deal dispute tickets. Statuses: `open`, `resolved`. |
| 18 | `Review` | `app/Models/Review.php` | `reviews` | `id` (int) | `deal_id`, `reviewer_id`, `reviewee_id`, `role`, `communication`, `behavior`, `delivery`, `reliability`, `comment` | `belongsTo(Deal::class)`, `belongsTo(User::class, 'reviewer_id')`, `belongsTo(User::class, 'reviewee_id')` | Rating criteria (1-5 integers). Unique index on `[deal_id, reviewer_id]`. |

---

## 2. Controllers Inventory (`app/Http/Controllers/`)

The application defines **17 functional controllers** (13 in root, 4 in `Admin/` namespace) plus the base controller.

### Base Controller
- **`Controller.php`**: `App\Http\Controllers\Controller`, uses `AuthorizesRequests`, `ValidatesRequests`.

### Root Controllers

#### 1. `AuthController.php`
- **Responsibilities:** User registration, credential authentication, logout, session state (`/api/me`), password reset OTP dispatch and validation, user management listing and status updates.
- **Methods:**
  - `register(Request $request)`: Validates NID, phone, username, email; hashes password; auto-generates `USR-` string ID.
  - `checkAvailability(Request $request)`: Real-time validation for email/username.
  - `login(Request $request)`: `Auth::attempt` with session regeneration.
  - `logout(Request $request)`: Session invalidation and token regeneration.
  - `me(Request $request)`: Fresh DB lookup for authenticated user and wallet balance.
  - `listUsers(Request $request)`: **CRITICAL SECURITY RISK:** Unchecked admin user listing.
  - `updateWalletBalance(Request $request)`: Direct balance override (unrouted).
  - `updateUserStatus(Request $request, $id)`: **CRITICAL SECURITY RISK:** Unchecked status update (`Active`, `Suspended`, `Banned`).
  - `updateUserVerification(Request $request, $id)`: **CRITICAL SECURITY RISK:** Unchecked verification status modifier.
  - `requestVerification(Request $request)`: Sets user's `verification_status` to `PENDING`.
  - `forgotPassword(Request $request)`: Generates 6-digit OTP, updates `password_reset_tokens`. **HIGH SECURITY RISK:** Leaks OTP in JSON response if mail fails.
  - `verifyOtp(Request $request)`: Validates OTP within 15-minute window.
  - `resetPassword(Request $request)`: Re-hashes password upon verified OTP.

#### 2. `DealController.php`
- **Responsibilities:** Deal orchestration, charge calculation, accept/decline workflows, deliverable submission, milestone review, custom service requests, extensions, dispute reporting, admin review.
- **Methods:**
  - `store(Request $request)`: Calls `DealService::createDeal`.
  - `index(Request $request)`: Calls `DealService::getUserDeals`.
  - `show($id)`: Calls `DealService::getDealForUser`.
  - `accept($id)`: Calls `DealService::acceptDeal`.
  - `decline($id)`: Calls `DealService::declineDeal`.
  - `release($id)`: Calls `DealService::releaseFunds`.
  - `submitWork(Request $request, $id)`: Calls `DealService::submitWorkDeliverable`. **MISMATCH:** Frontend calls `/api/deals/${id}/deliverable`.
  - `reviewSubmission(Request $request, $id)`: Calls `DealService::reviewSubmission`.
  - `sendMessage(Request $request, $id)`: Appends message to deal `workspace['messages']`.
  - `reportIssue(Request $request, $id)`: Submits dispute report.
  - `getReports($id)`: Fetches reports for deal.
  - `requestCustomService(Request $request, $id)`: Adds 1% custom service fee.
  - `requestExtension(Request $request, $id)`: Submits deadline extension request.
  - `approveExtension(Request $request, $id)`: Buyer approves extension.
  - `adminIndex(Request $request)`: Admin listing of all deals.
  - `adminReports(Request $request)`: Admin listing of all disputes.
  - `adminResolveReport(Request $request, $id)`: Admin resolves dispute note.
  - `adminApproveFunds(Request $request, $id)`: Admin releases deal funds.
  - `calculateChargeAPI(Request $request)`: Public calculator endpoint.

#### 3. `DepositController.php`
- **Responsibilities:** Deposit request initiation, payment proof upload, admin deposit approval/rejection.
- **Methods:**
  - `store(Request $request)`: Validates `amount`, `gateway`, `proof_note`, calls `DepositService::createDeposit`.
  - `index()`: User deposit history.
  - `uploadScreenshot(Request $request)`: Saves image to `public` disk (`deposits/`). **SECURITY RISK:** Publicly accessible proof files.
  - `adminIndex(Request $request)`: Admin deposit listing.
  - `approve(Request $request, $id)`: Admin approves and credits wallet via row lock.
  - `reject(Request $request, $id)`: Admin rejects with reason.
  - `markAsRead($id)`: Marks deposit notification as read.

#### 4. `WithdrawalController.php`
- **Responsibilities:** Payout method management, user withdrawal submission, admin review.
- **Methods:**
  - `methodsIndex()`: User payout methods.
  - `methodStore(Request $request)`: Creates/updates bank or mobile payout method.
  - `methodDestroy($id)`: Deletes payout method (blocked if pending withdrawal exists).
  - `index()`: User withdrawal history.
  - `store(Request $request)`: Validates balance, deducts funds atomically, creates request.
  - `adminIndex(Request $request)`: Admin withdrawal queue.
  - `adminApprove(Request $request, $id)`: Marks withdrawal as approved.
  - `adminReject(Request $request, $id)`: Refunds deducted funds back to user wallet.

#### 5. `SupportTicketController.php`
- **Responsibilities:** Ticket creation, messaging, user and admin replies, ticket closure.
- **Methods:**
  - `index(Request $request)`: Fetches tickets for user or admin.
  - `store(Request $request)`: Creates ticket with initial message and optional attachment.
  - `reply(Request $request, $id)`: Adds reply message.
  - `close(Request $request, $id)`: Closes ticket.
  - `sync(Request $request)`: Offline-sync reconciliation endpoint.

#### 6. `AiSupportController.php`
- **Responsibilities:** Public live chat AI widget, local knowledge base search, external LLM proxy, ticket escalation.
- **Methods:**
  - `chat(Request $request)`: Checks `AiSetting`, proxies to external LLM or runs internal keyword-scoring engine; logs conversation to `AiChatHistory`.
  - `escalate(Request $request)`: Converts unresolved chat to `SupportTicket`. **BUG:** Hardcodes fallback `user_id => 1` for guest visitors.

#### 7. `ProfileController.php`
- **Responsibilities:** Profile information, bio, headline, photo updates.
- **Methods:**
  - `update(Request $request)`: Updates profile fields and base64/file photos. **MISMATCH:** Backend route is `PUT /api/profile`, but frontend API helper calls `POST /api/user/profile`.

#### 8. `PublicProfileController.php`
- **Responsibilities:** Public user directory and public seller profile page.
- **Methods:**
  - `index(Request $request)`: Paginated public directory of verified users.
  - `show($identifier)`: User profile by username or ID with completed deal reviews and stats.

#### 9. `ReviewController.php`
- **Responsibilities:** 4-point rating system for completed deals.
- **Methods:**
  - `store(Request $request, $id)`: Submits communication, behavior, delivery, reliability scores.
  - `canReview($id)`: Checks review eligibility.
  - `userReviews($id)`: Public reviews listing for user.

#### 10. `CategoryController.php`
- **Responsibilities:** CRUD management for deal categories.
- **Methods:** `index`, `store`, `update`, `destroy`.

#### 11. `CouponController.php`
- **Responsibilities:** CRUD for promo codes and fee discount limits.
- **Methods:** `index`, `store`, `show`, `update`, `destroy`.

#### 12. `MediaController.php`
- **Responsibilities:** File management for admin image uploads via `public_path('assets/images')`.
- **Methods:**
  - `index()`: Lists files and parses `metadata.json`.
  - `upload(Request $request)`: Saves files directly into `public_path`. **SECURITY RISK:** No MIME or extension validation.
  - `delete(Request $request)`: Deletes files.
  - `updateMetadata(Request $request, $id)`: Updates title/alt/caption in `metadata.json`.

#### 13. `NotificationController.php`
- **Responsibilities:** User in-app notifications.
- **Methods:** `index`, `store`, `markRead`, `markAllRead`.

### Admin Sub-Controllers (`app/Http/Controllers/Admin/`)

#### 14. `AnalyticsController.php`
- **Responsibilities:** System metrics and financial ledger reporting.
- **Methods:**
  - `getDashboardMetrics()`: Total volume, active escrows, completed deals, platform earnings.
  - `getLedger(Request $request)`: Detailed master transaction audit ledger.

#### 15. `BannerController.php`
- **Responsibilities:** Header notification banner system.
- **Methods:** `index`, `store`, `show`, `update`, `destroy`, `publicIndex`.

#### 16. `KnowledgeBaseController.php`
- **Responsibilities:** Admin CRUD for AI knowledge base Q&A pairs.
- **Methods:** `index`, `store`, `update`, `destroy`.

#### 17. `AiAdminController.php`
- **Responsibilities:** AI support configuration and chat audit history.
- **Methods:** `getSettings()`, `saveSettings(Request $request)`, `getHistory(Request $request)`.

---

## 3. Services Inventory (`app/Services/`)

The application centralizes its financial, escrow, and support workflows into **4 dedicated service classes**:

### 1. `DealService.php` (909 lines / 31,775 bytes)
- **Primary Domain:** Core escrow state machine, financial fee tiering, deadline calculations, and deliverable workflows.
- **Key Methods:**
  - `getChargePercentage(float $amount)`: 5% (1k-5k), 4.5% (5k-10k), 4% (10k-15k), 3% (>15k).
  - `calculateCharge(...)`: Resolves fee payer splits (`seller_100`, `buyer_100`, `shared_50`, `custom`) and applies coupon discounts.
  - `createDeal(User $user, array $data)`: Checks user verification; locks buyer wallet via `DB::transaction` and `lockForUpdate`; generates transaction ledger entry.
  - `acceptDeal(int $id, User $user)`: Funds escrow if initiator was seller; transitions status to `funds_secured`.
  - `declineDeal(int $id, User $user)`: **CRITICAL FINANCIAL BUG:** Marks deal `declined` without refunding the buyer's locked escrow balance!
  - `releaseFunds(int $id, User $user)`: **CRITICAL FINANCIAL BUG:** Releases funds to seller; allows execution when status is `completed`, permitting duplicate payouts after admin approval.
  - `submitWorkDeliverable(...)`: Saves deliverable files into `public_path('uploads/deals/{id}')`. **SECURITY RISK:** Publicly downloadable deliverables.
  - `reviewSubmission(...)`: Buyer approves or requests revisions.
  - `adminApproveFunds(int $id, User $admin)`: Force-releases escrow funds to seller and marks status `completed`.
  - `resolveReport(...)`: Resolves dispute notes in `reports` table.

### 2. `DepositService.php` (146 lines / 4,300 bytes)
- **Primary Domain:** Deposit request creation, screenshot storage, atomic wallet crediting.
- **Key Methods:**
  - `createDeposit(User $user, array $data)`: Creates pending deposit.
  - `approveDeposit(int $id, User $admin, ?string $adminNote)`: `DB::transaction` with `User::lockForUpdate`; increments `wallet_balance`; creates `deposit` ledger entry.
  - `rejectDeposit(int $id, User $admin, string $adminNote)`: Marks status `rejected`.
  - `storeScreenshot($file)`: Stores proof in `public` disk (`deposits/`).

### 3. `WithdrawalService.php` (251 lines / 9,263 bytes)
- **Primary Domain:** Payout method validation, atomic withdrawal locking, admin processing, rejection refunds.
- **Key Methods:**
  - `storeMethod(...)`, `deleteMethod(...)`: Payout destination management.
  - `requestWithdrawal(User $user, array $data)`: Atomically checks balance; deducts funds; calculates mobile cashout fee (1.5%); logs `withdrawal` transaction.
  - `approveWithdrawal(int $id, User $admin, ?string $adminNote)`: Marks request approved.
  - `rejectWithdrawal(int $id, User $admin, string $adminNote)`: Atomically refunds deducted amount back to user wallet; logs `withdrawal_rejected` transaction.

### 4. `SupportTicketService.php` (6,857 bytes)
- **Primary Domain:** Ticket generation, message threading, unread counter updates, and offline sync.

---

## 4. Middleware Inventory (`app/Http/Middleware/`)

| Middleware Class | Purpose | Route Group | Notes / Risk |
|---|---|---|---|
| `AdminMiddleware.php` | Restricts access to users where `role === 'admin'` | Custom alias `admin` | Functional, but several critical admin routes in `routes/web.php` were placed outside this middleware. |
| `Authenticate.php` | Standard Laravel authentication check | `auth` | Redirects unauthenticated visitors to `/auth`. |
| `VerifyCsrfToken.php` | CSRF token verification | `web` | Enforces CSRF token check on state-changing requests. |
| `EncryptCookies.php` | Standard cookie encryption | `web` | Laravel default. |
| `RedirectIfAuthenticated.php` | Redirects logged-in users away from guest pages | `guest` | Redirects to `/dashboard` or `/admin`. |
| `TrimStrings.php` | Automatic input string trimming | Global | Laravel default. |
| `TrustHosts.php` | Allowed host patterns | Global | Laravel default. |
| `TrustProxies.php` | Reverse proxy header handling | Global | Laravel default. |
| `ValidateSignature.php` | Signed URL validation | `signed` | Laravel default. |
| `PreventRequestsDuringMaintenance.php` | Maintenance mode filter | Global | Laravel default. |

---

## 5. Console Commands & Providers

### Console Commands
- **`app/Console/Commands/MigrateToSqlite.php`**: Custom utility command developed during V10 to migrate MySQL database contents into SQLite.
- **`app/Console/Kernel.php`**: Registers commands. No automated recurring cron jobs are scheduled.

### Service Providers (`app/Providers/`)
- `AppServiceProvider.php`: Standard bootstrap.
- `AuthServiceProvider.php`: Standard model policies (empty).
- `BroadcastServiceProvider.php`: Default broadcasting channel setup.
- `EventServiceProvider.php`: Default event-listener mapping (empty).
- `RouteServiceProvider.php`: Maps `routes/web.php` and `routes/api.php` with `web` and `api` middleware.
