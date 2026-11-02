# Authentication & Security Assessment: MigratePay V10

**Source Project:** MigratePay V10 (`F:\laragon\www\migratepay`)  
**Assessment Date:** 2026-11-02  
**Scope:** Authentication Workflows, Role & Permission Enforcement, Account Lifecycle, Cryptographic Handling, and Confirmed Vulnerabilities  

---

## 1. Authentication Lifecycle

### A. User Registration (`AuthController@register`)
- **Route:** `POST /api/register` (Public).
- **Validation Rules:**
  - `name`: string, max 255.
  - `username`: string, unique in `users`, lowercase trimmed.
  - `email`: email, unique in `users`, lowercase trimmed.
  - `phone`: string, unique in `users`.
  - `password`: string, min 8 characters.
  - `date_of_birth`: date.
  - `nid_number`: string, nullable, max 20.
  - `division`, `district`: string, max 100.
  - `gender`: in `Male`, `Female`.
- **Password Security:** Hashed via `Illuminate\Support\Facades\Hash::make()` (Bcrypt).
- **Identifier:** Auto-generates `user_id_string` formatted as `USR-<timestamp><2-digit-rand>`.
- **Default State:** Role assigned as `user`, status as `Active`, wallet balance initialized to `0.0`.

### B. Session Authentication & Login (`AuthController@login`)
- **Route:** `POST /api/login` (Public).
- **Credential Check:** `Auth::attempt(['email' => $email, 'password' => $password], $remember = true)`.
- **Session Security:** Calls `$request->session()->regenerate()` immediately upon successful login to prevent session fixation attacks.
- **Role Redirection:** Returns JSON specifying destination (`/admin` for role `admin`, `/dashboard` for role `user`).

### C. Session Termination (`AuthController@logout`)
- **Route:** `POST /api/logout` (Public / Authenticated).
- **Lifecycle:** Calls `Auth::logout()`, `$request->session()->invalidate()`, and `$request->session()->regenerateToken()`.

### D. Password Reset & OTP Workflow
- **Flow:**
  1. `POST /api/forgot-password`: Generates a 6-digit cryptographic random integer (`sprintf("%06d", mt_rand(1, 999999))`). Updates `password_reset_tokens` with email, token, and timestamp. Dispatches email via `Mail::raw`.
  2. `POST /api/verify-otp`: Compares submitted OTP against `password_reset_tokens` for the given email; checks that age is `<= 15 minutes`.
  3. `POST /api/reset-password`: Validates OTP, updates password via `Hash::make()`, and purges token from `password_reset_tokens`.

---

## 2. Role & Permission Architecture

The V10 system has two defined user roles: `user` and `admin`.

- **Admin Guarding in Blade:** Both `/admin/{any?}` and `/faitor` inspect `auth()->check() && auth()->user()->role === 'admin'`.
- **Middleware Guarding:** `AdminMiddleware.php` checks `$request->user() && $request->user()->role === 'admin'`.
- **Vulnerability Identified:** As detailed below in finding `SEC-02`, several administrative API routes were inadvertently placed under the general `auth` group rather than `['auth', 'admin']`.

---

## 3. Confirmed Security Vulnerabilities Register

### Finding 1: SEC-01 — Public Gateway Settings Exposing API Keys & Passwords
- **Severity:** **CRITICAL**
- **Affected File:** `routes/web.php` (Lines 84–90)
- **Source Evidence:**
  ```php
  Route::get('/api/public/gateway-settings', function() {
      $path = storage_path('app/gateway_settings.json');
      if (file_exists($path)) {
          return response()->file($path, ['Content-Type' => 'application/json']);
      }
      return response()->json([]);
  });
  ```
- **Root Cause:** A public, unauthenticated closure sends the raw JSON file `storage/app/gateway_settings.json` directly to the client.
- **Observed Content in File:** SSLCommerz `storeId` & `storePassword`, bKash `appKey`, `appSecret`, `apiKey`, Nagad `merchantId`, Stripe `publishableKey`, `secretKey`, `webhookSecret`, and bank account numbers.
- **Impact:** Any anonymous visitor can steal all payment gateway credentials, private keys, and bank routing numbers.
- **Recommended Remediation:**
  1. Deprecate public file serving.
  2. Create a filtered public endpoint that returns **only safe metadata** (`title`, `logoUrl`, `isEnabled`, instructions).
  3. Store all private merchant credentials in encrypted server-side storage or `.env`.

---

### Finding 2: SEC-02 — Missing Admin Authorization on User Management Endpoints
- **Severity:** **CRITICAL**
- **Affected Files:** `routes/web.php` (Lines 99–101), `app/Http/Controllers/AuthController.php` (Lines 145–202)
- **Source Evidence:**
  ```php
  // routes/web.php
  Route::middleware('auth')->group(function () {
      Route::get('/api/users', [AuthController::class, 'listUsers']);
      Route::put('/api/users/{id}/status', [AuthController::class, 'updateUserStatus']);
      Route::put('/api/users/{id}/verification', [AuthController::class, 'updateUserVerification']);
  ```
- **Root Cause:** Endpoints intended for the Admin Control Tower are mapped inside the `auth` middleware group rather than `['auth', 'admin']`, and the controller methods contain no internal role validation checks.
- **Impact:** Any authenticated standard user can query `GET /api/users` to dump personal NID numbers, phone numbers, and wallet balances for all users; or issue a `PUT` request to ban, suspend, or elevate any account.
- **Recommended Remediation:** Move these routes into the `Route::middleware(['auth', 'admin'])` group and enforce `$request->user()->role === 'admin'` inside `AuthController`.

---

### Finding 3: SEC-03 — Plaintext OTP Disclosure on Mail Failure
- **Severity:** **HIGH**
- **Affected File:** `app/Http/Controllers/AuthController.php` (Lines 290–297)
- **Source Evidence:**
  ```php
  } catch (\Exception $e) {
      \Illuminate\Support\Facades\Log::error('Mail failed: ' . $e->getMessage());
      return response()->json([
          'success' => true, 
          'message' => 'OTP generated successfully. (Simulated check console or use OTP: ' . $otp . ')'
      ]);
  }
  ```
- **Root Cause:** Development debugging shortcut returns the valid 6-digit OTP in the JSON response whenever email delivery fails.
- **Impact:** An attacker who triggers password reset on a victim's account can intercept the OTP directly from the API response when SMTP is down or misconfigured, enabling full account takeover.
- **Recommended Remediation:** Never return OTPs in HTTP responses under any circumstance. Always log mail errors to secure server logs and return a generic delivery failure message.

---

### Finding 4: SEC-04 — Public Deliverable File Downloads in Webroot Without Authentication
- **Severity:** **HIGH**
- **Affected File:** `app/Services/DealService.php` (Lines 508–520)
- **Source Evidence:**
  ```php
  $destPath = public_path('uploads/deals/' . $deal->id);
  ...
  $file->move($destPath, $filename);
  $url = asset('uploads/deals/' . $deal->id . '/' . $filename);
  ```
- **Root Cause:** Deal deliverable files are moved directly into the public web document root (`public/uploads/deals/...`).
- **Impact:** Confidential contract deliverables, proprietary source code, and design files are publicly accessible via HTTP GET to anyone who knows or discovers the URL, bypassing authentication and deal membership checks.
- **Recommended Remediation:** Store deliverables on the `private` filesystem disk (`storage/app/private/deals/...`) and serve files exclusively through an authenticated, membership-checked streaming controller route (`GET /api/deals/{id}/deliverable/{file}`).

---

### Finding 5: SEC-05 — Public Deposit Payment Proof Receipts Stored in Public Storage
- **Severity:** **HIGH**
- **Affected File:** `app/Services/DepositService.php` (Lines 136–144)
- **Source Evidence:**
  ```php
  $path = $file->store('deposits', 'public');
  return ['path' => $path, 'url' => Storage::url($path)];
  ```
- **Root Cause:** Deposit payment receipts and bank transfer slips are saved to the public storage symlink (`public/storage/deposits`).
- **Impact:** Financial transaction slips containing sender bank accounts, phone numbers, and transaction IDs can be harvested by unauthenticated visitors.
- **Recommended Remediation:** Store payment screenshots on private storage (`storage/app/private/deposits`) and restrict access to administrators via signed URLs or authenticated controllers.

---

### Finding 6: SEC-06 — Unrestricted File Upload in MediaController
- **Severity:** **HIGH**
- **Affected File:** `app/Http/Controllers/MediaController.php` (Lines 77–87)
- **Source Evidence:**
  ```php
  $request->validate(['file' => 'required|file']);
  $filename = time() . '_' . preg_replace('/[^A-Za-z0-9.\-_]/', '', $originalName);
  $file->move($this->mediaPath, $filename); // mediaPath is public_path('assets/images')
  ```
- **Root Cause:** Validation only checks `file`, with zero MIME-type whitelist, extension verification, or file size cap. Files are moved directly into `public/assets/images`.
- **Impact:** Potential remote code execution if an administrator account is compromised or authentication is bypassed to upload executable script files (`.php`, `.html`, `.svg` with XSS payloads).
- **Recommended Remediation:** Enforce strict validation: `'file' => 'required|file|mimes:jpeg,png,webp,gif|max:5120'`.

---

### Finding 7: SEC-07 — Guest AI Escalation Hardcodes User ID 1
- **Severity:** **MEDIUM**
- **Affected File:** `app/Http/Controllers/AiSupportController.php` (Line 221)
- **Source Evidence:**
  ```php
  $ticket = SupportTicket::create([
      'user_id' => $userId ?? 1, // Fallback to 1 if guests can't make tickets
  ```
- **Root Cause:** Hardcoded fallback assumes User ID 1 exists and associates all anonymous guest inquiries with that user's account.
- **Impact:** Violates data integrity, pollutes User 1's ticket inbox, and causes foreign key crashes if User 1 does not exist.
- **Recommended Remediation:** Make `user_id` nullable on `support_tickets` and store guest contact information directly on the ticket.

---

### Finding 8: SEC-08 — Git-Tracked Sensitive SQL Backups and Testing Scripts
- **Severity:** **MEDIUM**
- **Affected Files:** `database_backup.sql`, `test_login.php`, `reset_password.php`, `test_banner.php`, `test_profile.php`
- **Root Cause:** Production/development database dump and manual testing scripts were committed to the V10 repository.
- **Impact:** Potential credential and user data leakage through repository cloning.
- **Recommended Remediation:** Ensure V12 `.gitignore` permanently excludes `*.sql`, `*.sqlite`, and test scripts.
