# MigratePay V10 Bugs and Technical Debt Register

> **Document ID:** `DOC-P1-13`  
> **Source Project:** `F:\laragon\www\migratepay`  
> **Destination Project:** `F:\laragon\www\Migratepay-V12`  
> **Audit Status:** Complete forensic register with verified code evidence  

---

## 1. Register Overview

This register compiles all confirmed security vulnerabilities, financial integrity flaws, architectural deficiencies, API mismatches, and operational debts discovered during the Phase 1 forensic investigation of MigratePay V10.

### Severity Summary
- **CRITICAL:** 6 findings
- **HIGH:** 6 findings
- **MEDIUM:** 6 findings
- **LOW:** 2 findings
- **INFORMATIONAL:** 1 finding
- **Total Registered Findings:** 21

---

## 2. Exhaustive Findings Log

### [SEC-01] Public Gateway Settings Exposing Secret-Bearing Configuration
- **Severity:** `CRITICAL`
- **Verification Status:** `CONFIRMED`
- **Affected Files:**
  - [routes/web.php](file:///F:/laragon/www/migratepay/routes/web.php#L84-L90) (lines 84–90)
  - `storage/app/gateway_settings.json`
- **Source Code Evidence:**
  ```php
  Route::get('/api/payment-gateways/public', function () {
      $path = storage_path('app/gateway_settings.json');
      if (file_exists($path)) {
          return response()->file($path, ['Content-Type' => 'application/json']);
      }
      return response()->json([]);
  });
  ```
- **Root Cause:** A raw unauthenticated file-download route returns the entire JSON configuration file containing merchant store passwords, Stripe secret keys, bKash app secrets, and bank account numbers to any visitor.
- **Potential Impact:** Complete compromise of payment processor accounts, theft of merchant credentials, unauthorized withdrawals, and potential regulatory liability.
- **Remediation:** Remove public file route. Store gateway definitions in an encrypted database table. Create a sanitized public endpoint (`GET /api/payment-gateways/public`) exposing strictly public attributes (`name`, `slug`, `icon`, `instructions`).
- **Target Implementation Phase:** Phase 3 (Database & Security Foundation)

---

### [SEC-02] Admin User Management Endpoints Lack Admin Role Authorization
- **Severity:** `CRITICAL`
- **Verification Status:** `CONFIRMED`
- **Affected Files:**
  - [routes/web.php](file:///F:/laragon/www/migratepay/routes/web.php#L99-L101) (lines 99–101)
  - [AuthController.php](file:///F:/laragon/www/migratepay/app/Http/Controllers/AuthController.php#L190-L240)
- **Source Code Evidence:**
  ```php
  Route::middleware(['auth'])->group(function () {
      ...
      Route::get('/api/users', [AuthController::class, 'getAllUsers']);
      Route::put('/api/users/{id}/status', [AuthController::class, 'updateUserStatus']);
      Route::put('/api/users/{id}/verification', [AuthController::class, 'updateUserVerification']);
  });
  ```
- **Root Cause:** The routes are wrapped inside `middleware(['auth'])` instead of `middleware(['auth', 'admin'])`. Inside `AuthController`, methods do not check `$request->user()->is_admin`.
- **Potential Impact:** Any authenticated regular customer can list all platform users, suspend competitor accounts, activate banned accounts, or artificially verify KYC credentials.
- **Remediation:** Relocate routes to `middleware(['auth', 'admin'])` and implement Gate/Policy enforcement in the controller.
- **Target Implementation Phase:** Phase 3 (Database & Security Foundation)

---

### [SEC-03] Plaintext OTP Disclosed in JSON Failure Response
- **Severity:** `CRITICAL`
- **Verification Status:** `CONFIRMED`
- **Affected Files:**
  - [AuthController.php](file:///F:/laragon/www/migratepay/app/Http/Controllers/AuthController.php#L290-L297) (lines 290–297)
- **Source Code Evidence:**
  ```php
  } catch (\Exception $e) {
      Log::error('Password reset email failed: ' . $e->getMessage());
      return response()->json([
          'message' => 'Failed to send email. For testing, use OTP: ' . $otp,
          'email' => $request->email,
          'testing_otp' => $otp // debug fallback
      ]);
  }
  ```
- **Root Cause:** A debug fallback branch exposes generated one-time passwords in HTTP responses whenever mail delivery fails or SMTP is unconfigured.
- **Potential Impact:** Complete account takeover. An attacker can request a password reset for any victim email and read the valid OTP directly from the response payload.
- **Remediation:** Remove OTP disclosure from response payloads completely. In local development, inspect `laravel.log`.
- **Target Implementation Phase:** Phase 3 (Database & Security Foundation)

---

### [SEC-04] Deal Deliverables Stored in Publicly Accessible Web Root
- **Severity:** `HIGH`
- **Verification Status:** `CONFIRMED`
- **Affected Files:**
  - [DealService.php](file:///F:/laragon/www/migratepay/app/Services/DealService.php#L508-L520) (lines 508–520)
- **Source Code Evidence:**
  ```php
  $uploadDir = public_path('uploads/deals/' . $deal->id);
  if (!file_exists($uploadDir)) {
      mkdir($uploadDir, 0755, true);
  }
  $file->move($uploadDir, $fileName);
  ```
- **Root Cause:** Deliverable contracts, proprietary source code, and confidential documents are moved directly to `public_path()`.
- **Potential Impact:** Unauthenticated web visitors can guess or scrape files via `https://domain.com/uploads/deals/{id}/{filename}` without logging in.
- **Remediation:** Store deliverables on a private disk (`storage_path('app/private/deals')`) and stream files through an authenticated, policy-governed controller endpoint (`GET /api/deals/{id}/deliverable/download`).
- **Target Implementation Phase:** Phase 4 (Backend Services & File Vault)

---

### [SEC-05] Payment Receipt Screenshots Publicly Exposed
- **Severity:** `HIGH`
- **Verification Status:** `CONFIRMED`
- **Affected Files:**
  - [DepositService.php](file:///F:/laragon/www/migratepay/app/Services/DepositService.php#L136-L144) (lines 136–144)
- **Source Code Evidence:**
  ```php
  $path = $request->file('receipt')->store('deposits', 'public');
  ```
- **Root Cause:** User financial deposit receipts are placed in `storage/app/public/deposits`, accessible via the public symlink `public/storage/deposits/`.
- **Potential Impact:** Sensitive transaction numbers, bank account numbers, customer names, and deposit amounts can be scraped by unauthorized parties.
- **Remediation:** Store receipts in private storage (`storage_path('app/private/deposits')`) accessible solely by administrators via signed authorization.
- **Target Implementation Phase:** Phase 4 (Backend Services & File Vault)

---

### [SEC-06] Unrestricted File Upload Without MIME Validation
- **Severity:** `HIGH`
- **Verification Status:** `CONFIRMED`
- **Affected Files:**
  - [MediaController.php](file:///F:/laragon/www/migratepay/app/Http/Controllers/MediaController.php#L75-L87) (lines 75–87)
- **Source Code Evidence:**
  ```php
  $file = $request->file('file');
  $fileName = time() . '_' . $file->getClientOriginalName();
  $file->move(public_path('assets/images'), $fileName);
  ```
- **Root Cause:** The method accepts incoming files without validating extension, MIME type, or file headers.
- **Potential Impact:** Malicious users with admin access can upload arbitrary PHP scripts or HTML webshells directly into the public directory, leading to Remote Code Execution (RCE).
- **Remediation:** Enforce strict FormRequest validation (`image|mimes:jpeg,png,webp,svg|max:5120`) and randomize stored filenames.
- **Target Implementation Phase:** Phase 3 (Database & Security Foundation)

---

### [SEC-07] AI Chat Human Escalation Hardcodes Super-Admin User Attribution
- **Severity:** `MEDIUM`
- **Verification Status:** `CONFIRMED`
- **Affected Files:**
  - [AiSupportController.php](file:///F:/laragon/www/migratepay/app/Http/Controllers/AiSupportController.php#L221) (line 221)
- **Source Code Evidence:**
  ```php
  $ticket = SupportTicket::create([
      'user_id' => $user ? $user->id : 1,
      'subject' => 'AI Chat Escalation: ' . Str::limit($lastMessage, 50),
      ...
  ]);
  ```
- **Root Cause:** If an anonymous guest initiates AI chat and requests human assistance, the ticket is assigned to `user_id = 1` (the primary super-admin).
- **Potential Impact:** Pollutes the administrator's personal ticket dashboard; falsifies audit history; creates confusion in customer support handling.
- **Remediation:** Make `support_tickets.user_id` nullable for guest inquiries, or capture guest email and create an unauthenticated support ticket record.
- **Target Implementation Phase:** Phase 4 (Backend Services)

---

### [SEC-08] Git-Tracked Debug Scripts Containing Plaintext Passwords
- **Severity:** `CRITICAL`
- **Verification Status:** `CONFIRMED`
- **Affected Files:**
  - [test_login.php](file:///F:/laragon/www/migratepay/test_login.php)
  - [reset_password.php](file:///F:/laragon/www/migratepay/reset_password.php)
- **Source Code Evidence:**
  Files in V10 root execute `Hash::make('Faitor@911')` and `Hash::check('Faitor@911', ...)` directly against `admin@migratepay.com`.
- **Root Cause:** Debugging scripts committed to the repository root bypass all authentication and reset administrative passwords upon execution.
- **Potential Impact:** Immediate administrative account takeover if executed on a public server.
- **Remediation:** Ensure neither script is migrated to V12. Exclude all standalone PHP root test scripts via `.gitignore`.
- **Target Implementation Phase:** Phase 1 (Baseline & Repository Isolation)

---

### [FIN-01] Duplicate Payout on Completed Deals
- **Severity:** `CRITICAL`
- **Verification Status:** `CONFIRMED`
- **Affected Files:**
  - [DealService.php](file:///F:/laragon/www/migratepay/app/Services/DealService.php#L408) (line 408)
- **Source Code Evidence:**
  ```php
  if (!in_array($deal->status, ['delivered', 'completed'])) {
      throw new Exception('Funds can only be released for delivered deals.');
  }
  ```
- **Root Cause:** The condition explicitly permits `releaseFunds` when `status === 'completed'`. If an admin invokes `adminApproveFunds()` (which marks the deal `'completed'`), a buyer can subsequently call `releaseFunds()`, triggering a second credit to the seller's wallet.
- **Potential Impact:** Severe financial loss due to double payout of escrow funds to sellers.
- **Remediation:** Restrict `releaseFunds()` strictly to `status === 'delivered'` and enforce row locking with idempotency keys.
- **Target Implementation Phase:** Phase 4 (Backend Services & Financial Core)

---

### [FIN-02] Escrow Funds Not Refunded on Seller Deal Decline
- **Severity:** `CRITICAL`
- **Verification Status:** `CONFIRMED`
- **Affected Files:**
  - [DealService.php](file:///F:/laragon/www/migratepay/app/Services/DealService.php#L372-L399) (lines 372–399)
- **Source Code Evidence:**
  ```php
  $deal->status = 'declined';
  $deal->save();
  // No refund transaction or wallet credit to buyer
  ```
- **Root Cause:** When a seller declines a deal, the status changes to `'declined'`, but the escrow funds previously deducted from the buyer's balance remain deducted and are never refunded.
- **Potential Impact:** Customer funds permanently locked; customer balance discrepancy.
- **Remediation:** Automatically credit the buyer's wallet with the deal amount and log a `refund` transaction inside a database transaction.
- **Target Implementation Phase:** Phase 4 (Backend Services & Financial Core)

---

### [FIN-03] Financial Wallet Balance Stored as Floating Point
- **Severity:** `HIGH`
- **Verification Status:** `CONFIRMED`
- **Affected Files:**
  - [users migration](file:///F:/laragon/www/migratepay/database/migrations/2014_10_12_000000_create_users_table.php#L22) (line 22)
  - `database/database.sqlite` (`users.wallet_balance FLOAT DEFAULT 0.00`)
- **Source Code Evidence:**
  `$table->float('wallet_balance', 10, 2)->default(0.00);`
- **Root Cause:** Floating point arithmetic is susceptible to IEEE-754 precision inaccuracies during repeated balance mutations.
- **Potential Impact:** Rounding errors, phantom fractional cents, and ledger reconciliation imbalances.
- **Remediation:** Change column to `$table->decimal('wallet_balance', 15, 2)->default(0.00)` in V12 migrations.
- **Target Implementation Phase:** Phase 3 (Database Schema Foundation)

---

### [FIN-04] Simulated / Offline Manual Gateways Without Automated Webhooks
- **Severity:** `MEDIUM`
- **Verification Status:** `CONFIRMED`
- **Affected Files:**
  - [PaymentGatewayController.php](file:///F:/laragon/www/migratepay/app/Http/Controllers/PaymentGatewayController.php)
  - [DepositService.php](file:///F:/laragon/www/migratepay/app/Services/DepositService.php)
- **Source Code Evidence:**
  No webhook routes or SDK packages exist in V10.
- **Root Cause:** Gateway configurations store API credentials, but actual deposits operate strictly on manual proof verification.
- **Potential Impact:** Delayed balance crediting, high operational overhead for admins, vulnerability to forged manual receipts.
- **Remediation:** Provide both automated webhook drivers (Stripe, SSLCommerz, bKash) and a verified manual deposit workflow in V12.
- **Target Implementation Phase:** Phase 4 (Backend Services & Gateways)

---

### [API-01] Deliverable Submission Endpoint Route Mismatch
- **Severity:** `HIGH`
- **Verification Status:** `CONFIRMED`
- **Affected Files:**
  - `resources/js/api/dealsApi.js` (line 72)
  - [routes/web.php](file:///F:/laragon/www/migratepay/routes/web.php#L69) (line 69)
- **Source Code Evidence:**
  Frontend calls: `POST /api/deals/${id}/deliverable`  
  Backend expects: `POST /api/deals/{id}/submit`
- **Root Cause:** Endpoint discrepancy between frontend API helper and backend route definition.
- **Potential Impact:** Sellers cannot upload deliverables through the frontend API helper without triggering 404 Not Found.
- **Remediation:** Standardize route URI to `POST /api/deals/{id}/deliverable` across both frontend and backend in V12.
- **Target Implementation Phase:** Phase 4 (API Routes & Integration)

---

### [API-02] User Profile Update Endpoint Mismatch
- **Severity:** `HIGH`
- **Verification Status:** `CONFIRMED`
- **Affected Files:**
  - `resources/js/api/userApi.js` (line 14)
  - [routes/web.php](file:///F:/laragon/www/migratepay/routes/web.php#L61) (line 61)
- **Source Code Evidence:**
  Frontend calls: `POST /api/user/profile`  
  Backend expects: `PUT /api/profile`
- **Root Cause:** Mismatch in both URL prefix (`/api/user/profile` vs `/api/profile`) and HTTP method (`POST` vs `PUT`).
- **Potential Impact:** Profile settings updates fail via standard API helper.
- **Remediation:** Align route to `PUT /api/user/profile` and `POST /api/user/profile` (handling multipart avatar uploads).
- **Target Implementation Phase:** Phase 4 (API Routes & Integration)

---

### [API-03] Missing Change Password Backend Route
- **Severity:** `MEDIUM`
- **Verification Status:** `CONFIRMED`
- **Affected Files:**
  - `resources/js/api/userApi.js` (line 18)
  - [routes/web.php](file:///F:/laragon/www/migratepay/routes/web.php)
- **Source Code Evidence:**
  Frontend calls `POST /api/user/change-password`, but no matching route or controller method exists in `routes/web.php` or `AuthController.php`.
- **Root Cause:** Frontend utility was written for password changing, but backend implementation was omitted.
- **Potential Impact:** Users cannot update their passwords from the dashboard settings tab.
- **Remediation:** Implement `POST /api/user/change-password` with current-password verification and rate limiting in V12.
- **Target Implementation Phase:** Phase 4 (API Routes & Auth)

---

### [ARCH-01] Admin Dashboard Modules Persist Solely to Browser localStorage
- **Severity:** `HIGH`
- **Verification Status:** `CONFIRMED`
- **Affected Files:**
  - [AdminContext.jsx](file:///F:/laragon/www/migratepay/resources/js/admin/context/AdminContext.jsx)
  - `resources/js/admin/modules/AnnouncementsModule.jsx`
  - `resources/js/admin/modules/ChargesModule.jsx`
  - `resources/js/admin/modules/PagesModule.jsx`
  - `resources/js/admin/modules/MenusModule.jsx`
  - `resources/js/admin/modules/SiteSettingsModule.jsx`
  - `resources/js/admin/modules/UserSettingsModule.jsx`
  - `resources/js/admin/modules/BlogPostsModule.jsx`
- **Source Code Evidence:**
  State in these 8 modules initializes from and writes to `localStorage.getItem('migratepay_admin_*')`.
- **Root Cause:** Frontend modules were built as UI mockups/prototypes without backend models or database migrations.
- **Potential Impact:** Changes made by one administrator are invisible to other administrators, lost upon browser cache clear, and never reflected on the public website.
- **Remediation:** Construct dedicated Eloquent models and database tables (`announcements`, `platform_charges`, `cms_pages`, `menus`, `site_settings`, `blog_posts`) with RESTful API controllers in V12.
- **Target Implementation Phase:** Phase 4 & Phase 7 (CMS & Admin Rebuild)

---

### [ARCH-02] Admin Backup Module is a Simulated Demonstration
- **Severity:** `MEDIUM`
- **Verification Status:** `CONFIRMED`
- **Affected Files:**
  - `resources/js/admin/modules/BackupModule.jsx` (lines 35–55)
- **Source Code Evidence:**
  Module contains `setTimeout(() => { setIsCreating(false); toast(...); }, 3000)` simulating backup creation without invoking any backend artisan backup process.
- **Root Cause:** Prototype placeholder implementation.
- **Potential Impact:** Administrators believe database backups are being generated when in reality no backup files are created.
- **Remediation:** Connect V12 backup module to a real Laravel backup command (or SQLite backup procedure) that stores timestamped archives in private storage.
- **Target Implementation Phase:** Phase 7 (Admin Reconstruction)

---

### [DB-01] SQL Backup Out of Sync with Database Schema
- **Severity:** `MEDIUM`
- **Verification Status:** `CONFIRMED`
- **Affected Files:**
  - `database/database_backup.sql` (dated Sep 4, 2026)
  - `database/database.sqlite` (dated Oct 2, 2026)
- **Source Code Evidence:**
  `database_backup.sql` lacks 5 active tables present in SQLite: `support_tickets`, `support_ticket_messages`, `ai_knowledge_bases`, `ai_chat_histories`, and `ai_settings`.
- **Root Cause:** The SQL file is a stale snapshot taken prior to adding support ticket and AI chat modules.
- **Potential Impact:** Developers importing `database_backup.sql` will experience application crashes due to missing tables.
- **Remediation:** Use `database/migrations/` and SQLite schema as the canonical source of truth for V12 migrations.
- **Target Implementation Phase:** Phase 3 (Database Schema Foundation)

---

### [DB-02] AI Knowledge Base Seeder Missing from DatabaseSeeder
- **Severity:** `LOW`
- **Verification Status:** `CONFIRMED`
- **Affected Files:**
  - [DatabaseSeeder.php](file:///F:/laragon/www/migratepay/database/seeders/DatabaseSeeder.php)
  - [AiKnowledgeBaseSeeder.php](file:///F:/laragon/www/migratepay/database/seeders/AiKnowledgeBaseSeeder.php)
- **Source Code Evidence:**
  `DatabaseSeeder::run()` calls `CategorySeeder`, `CouponSeeder`, `BannerSeeder`, `DefaultUserSeeder`, and `AiSettingSeeder`, but omits `AiKnowledgeBaseSeeder`.
- **Root Cause:** Seeder registration was overlooked when knowledge base functionality was implemented.
- **Potential Impact:** Fresh database installations contain no default knowledge base articles, impairing initial AI support responses.
- **Remediation:** Register `AiKnowledgeBaseSeeder::class` in V12 `DatabaseSeeder.php`.
- **Target Implementation Phase:** Phase 3 (Database Schema Foundation)

---

### [OPS-01] Synchronous Request Processing Lacks Background Queue
- **Severity:** `MEDIUM`
- **Verification Status:** `CONFIRMED`
- **Affected Files:**
  - `config/queue.php`
  - `.env` (`QUEUE_CONNECTION=sync`)
- **Root Cause:** V10 has no queue worker setup or queued notification classes.
- **Potential Impact:** Outgoing emails and external AI API calls block browser responses; mail server downtime results in 504 gateway timeouts for end users.
- **Remediation:** Configure `QUEUE_CONNECTION=database` in V12 and queue all transactional emails and webhook dispatches.
- **Target Implementation Phase:** Phase 4 (Backend Architecture)

---

### [TEST-01] Zero Custom Automated Test Coverage
- **Severity:** `HIGH`
- **Verification Status:** `CONFIRMED`
- **Affected Files:**
  - `tests/Feature/ExampleTest.php`
  - `tests/Unit/ExampleTest.php`
- **Root Cause:** Application was built without test-driven or automated test practices.
- **Potential Impact:** High likelihood of introducing regressions during refactoring, framework upgrades, or bug fixes.
- **Remediation:** Implement the 18 automated test suites defined in `TEST-COVERAGE-AND-QUALITY.md` during V12 reconstruction.
- **Target Implementation Phase:** Across Phases 3 through 7
