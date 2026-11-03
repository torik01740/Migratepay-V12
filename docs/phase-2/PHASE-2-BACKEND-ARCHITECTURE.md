# MigratePay V12: Phase 2 Backend Architecture & Service Layer Design

> **Document ID:** `DOC-P2-07`  
> **Source Project:** `F:\laragon\www\migratepay`  
> **Destination Project:** `F:\laragon\www\Migratepay-V12`  
> **Phase:** 2 — Architecture Decisions & Blueprint  
> **Status:** APPROVED BACKEND ARCHITECTURE & SERVICE DESIGN  

---

## 1. Executive Summary

The MigratePay V12 backend architecture transitions the application from a controller-heavy, synchronous codebase into a clean, layered service-oriented architecture. 

Core architecture principles:
1. **Thin Controllers, Rich Services:** Controllers manage HTTP requests, authorize callers, validate FormRequests, and return standardized JSON envelopes. All business logic, state machines, and transaction boundaries live in dedicated Services.
2. **Atomic Financial Boundaries:** Every balance mutation, escrow hold, and payout is executed within a database transaction with pessimistic row locking (`lockForUpdate`).
3. **Dedicated Service Architecture:** Common financial logic is extracted into a dedicated `WalletService` to eliminate duplicate balance mutations.
4. **Asynchronous Queue Integration:** Transactional notification emails, deliverable virus scanning, and webhook calls are decoupled from incoming HTTP cycles using Laravel's database queue worker.

---

## 2. Layer Responsibilities & Architecture Hierarchy

```
┌────────────────────────────────────────────────────────┐
│                      HTTP REQUEST                      │
└───────────────────────────┬────────────────────────────┘
                            │
                            ▼
┌────────────────────────────────────────────────────────┐
│              ROUTING & MIDDLEWARE BARRIER              │
│  (Session Auth, CSRF Protection, Admin/Role Middleware) │
└───────────────────────────┬────────────────────────────┘
                            │
                            ▼
┌────────────────────────────────────────────────────────┐
│                   FORM REQUEST LAYER                   │
│         (Input Sanitization, Strict Type Rules)        │
└───────────────────────────┬────────────────────────────┘
                            │
                            ▼
┌────────────────────────────────────────────────────────┐
│                    CONTROLLER LAYER                    │
│    (HTTP Coordination, Policy Checks, Response Prep)   │
└───────────────────────────┬────────────────────────────┘
                            │
                            ▼
┌────────────────────────────────────────────────────────┐
│                     SERVICE LAYER                      │
│   (Business Logic, Transaction Isolation, State Engine)│
└───────────────────────────┬────────────────────────────┘
                            │
              ┌─────────────┴─────────────┐
              ▼                           ▼
┌───────────────────────────┐ ┌───────────────────────────┐
│     ELOQUENT / DATABASE   │ │       EVENTS / QUEUES     │
│   (Models, Ledger, Locks) │ │   (Async Mails, Webhooks) │
└───────────────────────────┘ └───────────────────────────┘
```

---

## 3. Dedicated Service Layer Catalogue

### 3.1 `WalletService` (`app/Services/WalletService.php`)
- **Purpose:** Centralized, authoritative engine for all wallet balance mutations. Eliminates duplicate balance modification logic previously spread across `DealService`, `DepositService`, and `WithdrawalService`.
- **Public Methods:**
  - `credit(User $user, float $amount, string $type, Model $reference, string $description): Transaction`
  - `debit(User $user, float $amount, string $type, Model $reference, string $description): Transaction`
  - `lockBalance(User $user, float $amount): void`
  - `getLedger(User $user, int $perPage = 20): LengthAwarePaginator`
  - `verifyLedgerIntegrity(User $user): bool`
- **Internal Mechanisms:** `DB::transaction()`, `User::lockForUpdate()`, Non-negative balance invariant check (`wallet_balance >= 0`).

### 3.2 `DealService` (`app/Services/DealService.php`)
- **Purpose:** Manages the complete escrow contract lifecycle and state transitions.
- **Public Methods:**
  - `createDeal(User $initiator, array $data): Deal`
  - `acceptDeal(Deal $deal, User $user): Deal`
  - `declineDeal(Deal $deal, User $user): Deal` (with automatic refund for `FIN-02`)
  - `submitDeliverable(Deal $deal, User $seller, UploadedFile $file, string $notes): Deal`
  - `reviewSubmission(Deal $deal, User $buyer, string $decision): Deal`
  - `releaseFunds(Deal $deal, User $caller): Deal` (with atomic status guard for `FIN-01`)
  - `reportIssue(Deal $deal, User $user, array $data): Report`
  - `adminResolveDispute(Deal $deal, User $admin, string $resolution, ?float $splitRatio): Deal`

### 3.3 `DepositService` (`app/Services/DepositService.php`)
- **Purpose:** Inbound balance top-up verification and proof management.
- **Public Methods:**
  - `createDeposit(User $user, array $data, UploadedFile $receipt): Deposit`
  - `approveDeposit(Deposit $deposit, User $admin, ?string $note): Deposit`
  - `rejectDeposit(Deposit $deposit, User $admin, string $reason): Deposit`
- **Security Action:** Stores receipt screenshots in `storage/app/private/deposits/`.

### 3.4 `WithdrawalService` (`app/Services/WithdrawalService.php`)
- **Purpose:** Outbound customer payout requests and tier fee enforcement.
- **Public Methods:**
  - `createRequest(User $user, array $data): WithdrawalRequest`
  - `approveRequest(WithdrawalRequest $request, User $admin, ?string $note): WithdrawalRequest`
  - `rejectRequest(WithdrawalRequest $request, User $admin, string $reason): WithdrawalRequest` (with atomic refund)
  - `storeMethod(User $user, array $data): WithdrawalMethod`

### 3.5 `PaymentGatewayService` (`app/Services/PaymentGatewayService.php`)
- **Purpose:** Encrypted gateway credential vault and webhook routing.
- **Public Methods:**
  - `getPublicGateways(): Collection` (sanitized output without secrets)
  - `getAllGateways(): Collection` (admin only)
  - `updateGateway(int $id, array $data): PaymentGateway`
  - `processWebhook(string $gateway, Request $request): Response`

### 3.6 `AiSupportService` (`app/Services/AiSupportService.php`)
- **Purpose:** Hybrid OpenAI-compatible API client with local keyword fallback.
- **Public Methods:**
  - `generateResponse(?User $user, string $message, string $sessionId): array`
  - `escalateToHumanTicket(?User $user, string $sessionId, ?string $guestEmail): SupportTicket`
  - `matchLocalKnowledge(string $query): ?AiKnowledgeBase`

### 3.7 `SettingService` (`app/Services/SettingService.php`)
- **Purpose:** High-performance cached system configuration repository.
- **Public Methods:**
  - `get(string $key, mixed $default = null): mixed`
  - `set(string $key, mixed $value, string $group = 'general', string $type = 'string'): void`
  - `getFeeTiers(): array`
  - `getSiteSettings(): array`

---

## 4. Input Validation & FormRequest Strategy

Raw `$request->validate()` in controller methods will be replaced with dedicated FormRequest classes:
- `Auth\LoginRequest`, `Auth\RegisterRequest`, `Auth\PasswordResetRequest`
- `Deals\CreateDealRequest`, `Deals\SubmitDeliverableRequest`, `Deals\ReportIssueRequest`
- `Deposits\SubmitDepositRequest`, `Deposits\ReviewDepositRequest`
- `Withdrawals\CreateWithdrawalRequest`, `Withdrawals\StoreMethodRequest`
- `Settings\UpdateChargesRequest`, `Settings\UpdateSiteSettingsRequest`

### Example: Strict Decimal & File Validation
```php
class SubmitDeliverableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('submitDeliverable', $this->route('deal'));
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:zip,pdf,docx,png,jpeg|max:51200', // 50MB max
            'notes' => 'nullable|string|max:2000',
        ];
    }
}
```

---

## 5. Asynchronous Queues & Event-Driven Architecture

### 5.1 Queue Worker Infrastructure
- **Driver:** `QUEUE_CONNECTION=database` in `.env`.
- **Jobs Directory:** `app/Jobs/` established in V12.
- **Jobs Implemented:**
  - `SendEmailNotificationJob`: Dispatches HTML Markdown emails asynchronously.
  - `ProcessPaymentWebhookJob`: Asynchronously processes incoming gateway webhooks.
  - `PruneGuestAiChatsJob`: Cleans up anonymous chat transcripts older than 30 days.

### 5.2 Domain Events & Listeners
- `DealFundsSecured` → `SendEscrowNotificationListener`
- `DealDeliverableSubmitted` → `NotifyBuyerListener`
- `DealFundsReleased` → `SendPayoutNotificationListener`
- `DepositApproved` → `CreditWalletAndNotifyListener`
- `WithdrawalApproved` → `NotifyWithdrawalProcessedListener`

---

## 6. Standardized API Response Envelopes

Every API route returns a consistent JSON envelope to ensure predictable frontend consumption:

### Success Envelope (HTTP 200 / 201)
```json
{
    "success": true,
    "message": "Operation completed successfully.",
    "data": { ... }
}
```

### Error Envelope (HTTP 400 / 403 / 404 / 422 / 500)
```json
{
    "success": false,
    "message": "Human-readable explanation of error.",
    "errors": {
        "field_name": ["Specific validation error message."]
    }
}
```
