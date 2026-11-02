# MigratePay V10 Integrations and Operations Audit

> **Document ID:** `DOC-P1-10`  
> **Source Project:** `F:\laragon\www\migratepay` (Laravel 10.10 / PHP 8.1+)  
> **Destination Project:** `F:\laragon\www\Migratepay-V12` (Laravel 12.70.2 / PHP 8.4.27)  
> **Audit Status:** Complete forensic analysis  

---

## 1. Executive Summary

This document details all external third-party services, application configuration patterns, queue and scheduling infrastructure, file storage mechanisms, and operational dependencies identified in the MigratePay V10 source application.

Key architectural takeaways:
1. **Zero External Payment SDKs:** Although the application presents configuration interfaces for SSLCommerz, bKash, Nagad, Stripe, and Bank Transfer, there are **no external payment SDKs or automated webhook endpoints installed**. All payment processing operates under an unautomated, manual offline verification model.
2. **File-Based Unencrypted Secret Storage:** Payment gateway credentials (merchant keys, API secrets, bank accounts) are stored unencrypted in `storage/app/gateway_settings.json` and served over an unauthenticated public route (`SEC-01`).
3. **Dual-Tier AI Architecture:** AI Support utilizes a hybrid model: dynamic OpenAI-compatible HTTP requests against external endpoints with an automatic fallback to local database keyword scoring (`ai_knowledge_bases`).
4. **Synchronous Execution:** The application does not utilize background job workers or asynchronous queues; all operations (emails, ledger updates, file uploads) execute synchronously on the HTTP thread.
5. **Auto-Seeding Side Effect:** `AppServiceProvider::boot()` automatically invokes `Artisan::call('db:seed')` on every cold boot if `auto_seeded` is absent from cache.

---

## 2. Third-Party Integrations Inventory

| Integration Category | Provider / Technology | V10 Implementation Location | Configuration Source | Status | V12 Target Architecture |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Payment Gateways** | SSLCommerz, bKash, Nagad, Stripe, Bank Transfer | [PaymentGatewayController.php](file:///F:/laragon/www/migratepay/app/Http/Controllers/PaymentGatewayController.php), [DepositService.php](file:///F:/laragon/www/migratepay/app/Services/DepositService.php) | `storage/app/gateway_settings.json` | **Manual / Offline Proof Only** | Database-backed encrypted gateway vault; real automated webhooks & SDKs |
| **AI Support Engine** | OpenAI-compatible REST API | [AiSupportController.php](file:///F:/laragon/www/migratepay/app/Http/Controllers/AiSupportController.php) | `ai_settings` table (API Key, Base URL, Model, Temp) | **Operational with Local Fallback** | Encrypted API keys, prompt templating, streaming responses, audit logging |
| **Email Delivery** | Laravel `Mail::raw()` (SMTP / Log) | [AuthController.php](file:///F:/laragon/www/migratepay/app/Http/Controllers/AuthController.php) | `config/mail.php`, `.env` | **Synchronous / Unformatted** | Laravel Mailable classes, Markdown Blade templates, queued dispatch |
| **SMS / 2FA** | None | None | None | **Not Implemented** | Integrate Twilio or Infobip for SMS OTP; TOTP for 2FA |
| **File Storage** | Local Filesystem Disks | [MediaController.php](file:///F:/laragon/www/migratepay/app/Http/Controllers/MediaController.php), [DepositService.php](file:///F:/laragon/www/migratepay/app/Services/DepositService.php), [DealService.php](file:///F:/laragon/www/migratepay/app/Services/DealService.php) | `config/filesystems.php` | **Public Storage (Unrestricted)** | Private disk storage with signed URLs and role-based streaming |
| **Analytics / Tracking** | None | None | None | **Not Implemented** | Privacy-compliant analytics / optional telemetry |
| **CAPTCHA / Bot Protection**| None | None | None | **Not Implemented** | Turnstile / reCAPTCHA v3 on auth & public forms |

---

## 3. Deep-Dive: External Payment Integrations

### 3.1 Supported Gateway Profiles
The V10 frontend displays tabs for 5 payment methods:
1. **SSLCommerz:**
   - Fields: Store ID, Store Password, Sandbox/Live toggle.
   - Reality: No SSLCommerz PHP SDK or redirect checkout handler exists. Only displays manual payment instructions to users.
2. **bKash:**
   - Fields: App Key, App Secret, Username, Password, Merchant Number.
   - Reality: No tokenized bKash checkout API calls or callback URLs are implemented. Users copy the merchant number and submit a transaction ID and screenshot.
3. **Nagad:**
   - Fields: Merchant ID, Public Key, Private Key, Account Number.
   - Reality: No cryptographic signature generation or Nagad payment engine exists. Operates identically to bKash manual deposit.
4. **Stripe:**
   - Fields: Publishable Key, Secret Key, Webhook Secret.
   - Reality: The `stripe/stripe-php` package is **not installed** in `composer.json`. Frontend displays Stripe instructions, but backend processing is strictly manual proof upload.
5. **Bank Transfer:**
   - Fields: Bank Name, Account Name, Account Number, Routing/Swift, Branch.
   - Reality: Traditional wire transfer with manual slip attachment.

### 3.2 Credential Security Finding (`SEC-01`)
- **Evidence:** `routes/web.php` lines 84–90:
  ```php
  Route::get('/api/payment-gateways/public', function () {
      $path = storage_path('app/gateway_settings.json');
      if (file_exists($path)) {
          return response()->file($path, ['Content-Type' => 'application/json']);
      }
      return response()->json([]);
  });
  ```
- **Risk:** Complete disclosure of secret keys, passwords, and private banking details to unauthenticated public visitors.
- **V12 Correction:** Split configuration into two tiers:
  1. `public_gateways`: Only exposes name, slug, icon, fee, and public merchant instructions.
  2. `admin_gateways`: Stored in encrypted database records (`encrypted` cast in Eloquent), accessible strictly by authorized super-administrators.

---

## 4. Deep-Dive: AI Support Integration

### 4.1 Architecture
The AI engine is implemented in [AiSupportController.php](file:///F:/laragon/www/migratepay/app/Http/Controllers/AiSupportController.php) and backed by three database tables:
- `ai_settings`: Stores provider credentials and hyperparameters.
- `ai_knowledge_bases`: Stores Q&A pairs, articles, and match keywords.
- `ai_chat_histories`: Stores message logs, tokens consumed, and resolution states.

### 4.2 Query Resolution Flow
```
User Prompt (POST /api/ai/chat)
       │
       ▼
Check active configuration in `ai_settings`
       │
   ┌───┴────────────────────────┐
   ▼                            ▼
External API configured?      No external API configured
(api_key present)             (or external API call throws error)
   │                            │
   ▼                            ▼
Http::timeout(15)             Local Keyword Matching Fallback:
   -> POST base_url           Query `ai_knowledge_bases`
   -> Headers: Bearer key     Calculate keyword overlap score
   -> JSON prompt payload       │
   │                            ▼
   ├── Success? ──────────────> Best match score >= 1?
   │                               ├── Yes: Return answer & source article
   ▼                               └── No: Prompt user to escalate to human agent
Return AI response
Log in `ai_chat_histories`
```

### 4.3 Human Escalation & Fallback Flaw (`SEC-07`)
- When a user requests a human agent or the AI fails to answer, `escalateChat()` creates a record in `support_tickets`.
- If the chat was initiated by an unauthenticated guest, `AiSupportController.php` line 221 hardcodes `user_id = 1`:
  ```php
  $ticket = SupportTicket::create([
      'user_id' => $user ? $user->id : 1,
      'subject' => 'AI Chat Escalation: ' . Str::limit($lastMessage, 50),
      ...
  ]);
  ```
- **Consequence:** Anonymous guest support requests are falsely attributed to the super-admin account (`ID: 1`), polluting the administrator's private ticket inbox.

---

## 5. Operations, Infrastructure and Environment

### 5.1 Runtime and Packages
- **PHP Version:** PHP 8.1+ (V10 requirement) -> Upgraded to **PHP 8.4** in V12.
- **Laravel Framework:** Laravel 10.10 -> Upgraded to **Laravel 12.70.2** in V12.
- **Node Environment:** Vite 5 / Node 18+ -> Upgraded to **Vite 7.3.7 / Node 20+** in V12.

### 5.2 Service Providers Analysis
| Provider | File Path | Responsibilities & Observed Behaviors |
| :--- | :--- | :--- |
| `AppServiceProvider` | [AppServiceProvider.php](file:///F:/laragon/www/migratepay/app/Providers/AppServiceProvider.php) | Hooks `boot()` to auto-run `db:seed --force` if `auto_seeded` is not cached. |
| `AuthServiceProvider`| [AuthServiceProvider.php](file:///F:/laragon/www/migratepay/app/Providers/AuthServiceProvider.php) | Standard policy mapping. No custom Gates defined. |
| `EventServiceProvider`| [EventServiceProvider.php](file:///F:/laragon/www/migratepay/app/Providers/EventServiceProvider.php)| Binds `Registered` event to `SendEmailVerificationNotification`. No custom events. |
| `RouteServiceProvider`| [RouteServiceProvider.php](file:///F:/laragon/www/migratepay/app/Providers/RouteServiceProvider.php)| Defines `api` (prefix `/api`) and `web` route groups with rate limiters. |

### 5.3 Queue and Background Processing
- **Queue Driver:** Set to `sync` in `.env`.
- **Jobs Directory:** Does not exist in V10 (`app/Jobs/` is absent).
- **Impact:** All notification emails, document processing, and ledger transactions execute directly inside the incoming HTTP request cycle. An unresponsive mail server blocks the user's browser until timeout.
- **V12 Requirement:** Implement asynchronous worker using `database` or `redis` driver for mail sending, file processing, and webhook callbacks.

### 5.4 Scheduling and Cron Tasks
- **Kernel Schedule:** [Kernel.php](file:///F:/laragon/www/migratepay/app/Console/Kernel.php) defines an empty schedule (`schedule()` method contains only commented boilerplate).
- **Custom Commands:**
  - `app:migrate-to-sqlite` ([MigrateToSqlite.php](file:///F:/laragon/www/migratepay/app/Console/Commands/MigrateToSqlite.php)): Developer utility script to copy MySQL records into SQLite.
- **V12 Requirement:** Implement scheduled background commands for:
  - Expiring stale deals in escrow.
  - Periodic wallet balance reconciliation.
  - Automated database backup rotations.
  - AI conversation history pruning.

### 5.5 Filesystem Configuration
- **Disks Configured:** `local`, `public`, `s3`.
- **Symlink:** `public/storage` linked to `storage/app/public`.
- **Observed File Paths:**
  - `storage/app/gateway_settings.json` (unencrypted credentials).
  - `storage/app/public/deposits/` (payment proofs).
  - `public/uploads/deals/{id}/` (unauthenticated contract deliverables).
  - `public/assets/images/` (media library uploads).

---

## 6. V12 Operations and Integrations Strategy

1. **Vault Storage for Integrations:** Replace `gateway_settings.json` with an Eloquent `PaymentGateway` model using encrypted field casts and strict `['auth', 'admin']` policy barriers.
2. **True Gateway Integrations:** Prepare pluggable driver architecture (e.g. `App\Services\Gateways\Contracts\PaymentGatewayDriverInterface`) supporting webhooks and hosted checkout sessions alongside manual proof verification.
3. **Resilient AI Pipeline:** Isolate external AI requests into an asynchronous or queueable handler with strict circuit breakers, rate limits, and sanitized guest identity handling.
4. **Queue Worker Infrastructure:** Configure database queue driver in V12 with a dedicated worker daemon for reliable, non-blocking notification delivery.
5. **Private Document Storage:** Transition all financial proofs, KYC documents, and deal deliverables to an encrypted private storage disk served via temporary signed URLs.
