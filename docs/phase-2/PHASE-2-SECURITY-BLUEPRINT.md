# MigratePay V12: Phase 2 Security Architecture & Hardening Blueprint

> **Document ID:** `DOC-P2-06`  
> **Source Project:** `F:\laragon\www\migratepay`  
> **Destination Project:** `F:\laragon\www\Migratepay-V12`  
> **Phase:** 2 — Architecture Decisions & Blueprint  
> **Status:** APPROVED SECURITY ARCHITECTURE & HARDENING BLUEPRINT  

---

## 1. Executive Summary

MigratePay V10 contains 8 verified security vulnerabilities ranging from high-risk public credential exposures to unauthenticated backdoors and file disclosure flaws. 

This document defines the zero-trust security architecture for MigratePay V12, detailing exact root causes, exploitation vectors, remediation implementations, role barriers, and private storage vault policies.

---

## 2. Verification & Remediation of Confirmed Security Findings

### 2.1 [SEC-01] Public Gateway Settings Exposing Secret-Bearing Configuration
- **Verified Root Cause:** `routes/web.php` (lines 84–90) served raw `gateway_settings.json` containing live bKash app secrets, Stripe secret keys, and bank account numbers to unauthenticated visitors.
- **Exploitation Impact:** Critical. Credential theft, unauthorized merchant API manipulation, fraudulent fund transfers.
- **V12 Architectural Remediation:**
  1. Terminate the raw file route entirely.
  2. Implement an encrypted database vault (`payment_gateways` table) where secret attributes are encrypted using Laravel's `encrypted` Eloquent cast (`Crypt::encryptString`).
  3. Expose strictly sanitized public metadata via `GET /api/payment-gateways/public`:
     ```php
     public function publicGateways()
     {
         return PaymentGateway::where('is_active', true)
             ->select(['id', 'name', 'slug', 'icon', 'currency', 'instructions', 'fee_percentage'])
             ->get();
     }
     ```

---

### 2.2 [SEC-02] Admin User Management Endpoints Lack Admin Middleware
- **Verified Root Cause:** In `routes/web.php` (lines 99–101), `/api/users`, `/api/users/{id}/status`, and `/api/users/{id}/verification` were placed inside the generic `['auth']` middleware group. `AuthController` contained no internal role checks.
- **Exploitation Impact:** Critical. Any authenticated regular customer could ban competitor accounts, unban suspended accounts, or artificially grant KYC verification.
- **V12 Architectural Remediation:**
  1. Register `AdminMiddleware` as alias `'admin'` in `bootstrap/app.php`.
  2. Relocate all administrative routes into:
     ```php
     Route::middleware(['auth', 'admin'])->prefix('api/admin')->group(function () {
         Route::get('/users', [AdminUserController::class, 'index']);
         Route::put('/users/{id}/status', [AdminUserController::class, 'updateStatus']);
         Route::put('/users/{id}/verification', [AdminUserController::class, 'updateVerification']);
     });
     ```
  3. Enforce Laravel Gate authorization in the controller: `Gate::authorize('manage-users');`.

---

### 2.3 [SEC-03] Plaintext OTP Disclosed in JSON Failure Response
- **Verified Root Cause:** In `AuthController.php` (lines 290–297), a debug fallback returned `'testing_otp' => $otp` and `'message' => '... use OTP: ' . $otp` when mail sending failed.
- **Exploitation Impact:** Critical. Total account takeover. An attacker could request password reset for any target email and read the valid OTP directly from the HTTP response.
- **V12 Architectural Remediation:**
  1. Purge all debug fallback disclosures from controller responses.
  2. Standardize error message: `"Unable to send verification code. Please try again later."`
  3. In development environments (`APP_ENV=local`), log OTP to `storage/logs/laravel.log` exclusively.

---

### 2.4 [SEC-04] Deal Deliverables Stored in Publicly Accessible Web Root
- **Verified Root Cause:** `DealService.php` (lines 508–520) stored contract deliverables in `public_path('uploads/deals/{id}')`.
- **Exploitation Impact:** High. Proprietary contract deliverables, confidential source code, and deliverables were publicly downloadable without authentication.
- **V12 Architectural Remediation:**
  1. Configure `private` disk in `config/filesystems.php` (`storage_path('app/private')`).
  2. Store files in `storage/app/private/deals/{deal_id}/{uuid}.ext`.
  3. Serve files exclusively through a streaming controller (`DealDeliverableController::download`) checking:
     - User is authenticated.
     - User is buyer, seller, or platform administrator.

---

### 2.5 [SEC-05] Payment Receipt Screenshots Publicly Exposed
- **Verified Root Cause:** `DepositService.php` (lines 136–144) stored user payment slips in `storage/app/public/deposits/`, accessible via the public storage symlink.
- **Exploitation Impact:** High. Scrapers can harvest customer payment receipts, transaction IDs, bank account names, and phone numbers.
- **V12 Architectural Remediation:**
  1. Store all deposit proof screenshots on the `private` disk in `storage/app/private/deposits/`.
  2. Restrict image viewing to authorized administrators and the depositing user via temporary signed URLs (`URL::temporarySignedRoute(...)`).

---

### 2.6 [SEC-06] Unrestricted File Upload Without MIME Validation
- **Verified Root Cause:** `MediaController.php` (lines 75–87) accepted uploaded files without checking extensions or MIME types, moving them directly to `public_path('assets/images')`.
- **Exploitation Impact:** High. Potential Remote Code Execution (RCE) via arbitrary PHP script or webshell upload.
- **V12 Architectural Remediation:**
  1. Enforce strict FormRequest validation:
     ```php
     'file' => 'required|file|mimes:jpeg,png,webp,svg|max:5120'
     ```
  2. Sanitize and randomize stored filenames: `Str::uuid() . '.' . $file->extension()`.
  3. Prohibit direct execution of scripts in upload directories via web server configuration.

---

### 2.7 [SEC-07] AI Chat Human Escalation Spoofs Super-Admin User ID
- **Verified Root Cause:** In `AiSupportController.php` (line 221), when an unauthenticated guest requested a human agent, the ticket creation hardcoded `'user_id' => $user ? $user->id : 1`.
- **Exploitation Impact:** Medium. Guest support requests appeared as if submitted by the primary administrator account (`ID: 1`), corrupting admin inbox history and audit logs.
- **V12 Architectural Remediation:**
  1. Alter `support_tickets.user_id` to `nullable()`.
  2. For guest escalations, set `user_id = null` and record `guest_email` and `guest_name` in dedicated columns.

---

### 2.8 [SEC-08] Git-Tracked Debug Scripts Containing Plaintext Credentials
- **Verified Root Cause:** `test_login.php` and `reset_password.php` committed to the repository root contained plaintext administrative passwords (`Faitor@911`) and force-reset administrative passwords without authentication.
- **Exploitation Impact:** Critical. Direct administrative compromise if accessed over web server.
- **V12 Architectural Remediation:**
  1. Permanently delete and exclude these scripts from V12.
  2. Add root PHP script pattern exclusions to `.gitignore`.

---

## 3. Role-Based Access Control (RBAC) Architecture

MigratePay V12 enforces a 5-tier role hierarchy:

```
┌────────────────────────────────────────────────────────────────────────┐
│                        ROLE HIERARCHY MATRIX                           │
├─────────────────┬──────────────────────────────────────────────────────┤
│ Super Admin     │ Full system configuration, gateway vault, staff CRUD,│
│ (Role: admin)   │ manual database backup, all dispute arbitration.     │
├─────────────────┼──────────────────────────────────────────────────────┤
│ Co-Admin        │ Complete operations except deleting root admin.      │
├─────────────────┼──────────────────────────────────────────────────────┤
│ Support Team    │ Manage tickets, arbitrate reports, view deal status. │
├─────────────────┼──────────────────────────────────────────────────────┤
│ Editorial Team  │ Manage blog posts, CMS pages, categories, banners.   │
├─────────────────┼──────────────────────────────────────────────────────┤
│ Customer / User │ Standard buyer/seller deals, wallet, deposits, w/draw│
└─────────────────┴──────────────────────────────────────────────────────┘
```

### Laravel 12 Middleware Configuration in `bootstrap/app.php`
```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'role'  => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'api/webhooks/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Standardized JSON exception formatting
    })->create();
```

---

## 4. Private Storage & Controlled Document Access

### 4.1 Storage Disk Configuration (`config/filesystems.php`)
```php
'disks' => [
    'local' => [
        'driver' => 'local',
        'root' => storage_path('app'),
    ],
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
    ],
    'private' => [
        'driver' => 'local',
        'root' => storage_path('app/private'),
        'visibility' => 'private',
    ],
],
```

### 4.2 Authorized Streaming Controller Implementation
```php
class PrivateDocumentController extends Controller
{
    public function downloadDeliverable(int $dealId, string $filename)
    {
        $deal = Deal::findOrFail($dealId);
        $user = auth()->user();

        if (!$user->is_admin && $deal->initiator_id !== $user->id && $deal->counterparty_id !== $user->id) {
            abort(403, 'Unauthorized access to confidential deal deliverable.');
        }

        $path = "deals/{$dealId}/{$filename}";
        if (!Storage::disk('private')->exists($path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('private')->download($path);
    }
}
```
