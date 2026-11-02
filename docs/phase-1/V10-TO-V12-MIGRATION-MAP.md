# MigratePay V10 to V12 Component Migration Mapping

> **Document ID:** `DOC-P1-14`  
> **Source Project:** `F:\laragon\www\migratepay` (Laravel 10.10, PHP 8.1+, React 19.2.8, Chakra UI 2.10.10)  
> **Destination Project:** `F:\laragon\www\Migratepay-V12` (Laravel 12.70.2, PHP 8.4.27, React 19.3.1, Chakra UI 3.37.0)  
> **Audit Status:** Complete architectural mapping and implementation roadmap  

---

## 1. Executive Summary

This mapping blueprint establishes the transformation path for every component of the MigratePay application from its legacy Laravel 10 / Chakra UI 2 implementation into the modern, secure Laravel 12 / Chakra UI 3 destination stack.

### Classification Categories
1. **PRESERVE AND ADAPT:** Retain business logic; upgrade syntax for Laravel 12 / PHP 8.4 / Chakra 3 compatibility.
2. **RECONSTRUCT:** Rebuild component architecture from scratch to resolve fundamental security or design flaws.
3. **IMPROVE:** Preserve feature intent while fixing edge cases, performance bottlenecks, or user experience gaps.
4. **REPLACE:** Retire legacy mechanism in favor of modern standard Laravel 12 / React 19 capabilities.
5. **RETIRE:** Eliminate obsolete, orphaned, or dangerous legacy code.
6. **INVESTIGATE FURTHER:** Requires additional user consultation prior to implementation.

---

## 2. Layer-by-Layer Component Mapping

### 2.1 Database & Data Models
| V10 Component | V12 Target Location | Classification | Required Transformations & Adaptations |
| :--- | :--- | :--- | :--- |
| `users` table & [User.php](file:///F:/laragon/www/migratepay/app/Models/User.php) | `app/Models/User.php` | **IMPROVE** | Change `wallet_balance` from `FLOAT` to `DECIMAL(15, 2)`; adopt Laravel 12 `casts()` method; guard `is_admin`, `is_active`, `is_verified`. |
| `deals` table & [Deal.php](file:///F:/laragon/www/migratepay/app/Models/Deal.php) | `app/Models/Deal.php` | **IMPROVE** | Add `fee_paid_by` column; enforce strict decimal casting; add scopes for buyer/seller deals. |
| `transactions` table & [Transaction.php](file:///F:/laragon/www/migratepay/app/Models/Transaction.php) | `app/Models/Transaction.php` | **PRESERVE AND ADAPT** | Index `user_id`, `reference`, `status`; add immutable ledger constraints. |
| `deposits` table & [Deposit.php](file:///F:/laragon/www/migratepay/app/Models/Deposit.php) | `app/Models/Deposit.php` | **PRESERVE AND ADAPT** | Store receipt paths on private disk; add `admin_notes` column. |
| `withdrawals` table & [Withdrawal.php](file:///F:/laragon/www/migratepay/app/Models/Withdrawal.php) | `app/Models/Withdrawal.php` | **PRESERVE AND ADAPT** | Enforce decimal fee precision; add `rejection_reason` column. |
| `reviews` table & [Review.php](file:///F:/laragon/www/migratepay/app/Models/Review.php) | `app/Models/Review.php` | **PRESERVE AND ADAPT** | Standardize Eloquent relationships with Deal and User models. |
| `disputes` table & [Dispute.php](file:///F:/laragon/www/migratepay/app/Models/Dispute.php) | `app/Models/Dispute.php` | **PRESERVE AND ADAPT** | Link dispute resolution directly to wallet ledger transactions. |
| `support_tickets` & [SupportTicket.php](file:///F:/laragon/www/migratepay/app/Models/SupportTicket.php) | `app/Models/SupportTicket.php` | **IMPROVE** | Allow nullable `user_id` for guest tickets to resolve `SEC-07`. |
| `support_ticket_messages` & [SupportTicketMessage.php](file:///F:/laragon/www/migratepay/app/Models/SupportTicketMessage.php) | `app/Models/SupportTicketMessage.php` | **PRESERVE AND ADAPT** | Secure attachment upload paths; sanitize message markup. |
| `ai_settings`, `ai_knowledge_bases`, `ai_chat_histories` | `app/Models/Ai*.php` | **PRESERVE AND ADAPT** | Encrypt `api_key` attribute; register knowledge base seeder in `DatabaseSeeder.php`. |
| `banners`, `coupons`, `categories` | `app/Models/*.php` | **PRESERVE AND ADAPT** | Modernize Eloquent definitions; validate status enums. |
| *LocalStorage Modules* (Pages, Menus, Settings, Announcements, Charges, Blogs) | `database/migrations/` & `app/Models/*.php` | **RECONSTRUCT** | **Create 6 new database tables and models** to transition admin frontend-only prototypes to live database persistence. |

---

### 2.2 Backend Business Services
| V10 Service | V12 Target Location | Classification | Required Transformations & Adaptations |
| :--- | :--- | :--- | :--- |
| [DealService.php](file:///F:/laragon/www/migratepay/app/Services/DealService.php) | `app/Services/DealService.php` | **RECONSTRUCT** | 1. Fix duplicate payout bug in `releaseFunds()` (`FIN-01`).<br>2. Add automated escrow refund on `declineDeal()` (`FIN-02`).<br>3. Move deliverables to private storage disk (`SEC-04`).<br>4. Wrap all state transitions in atomic `DB::transaction()` with row locks. |
| [DepositService.php](file:///F:/laragon/www/migratepay/app/Services/DepositService.php) | `app/Services/DepositService.php` | **IMPROVE** | 1. Move deposit screenshots to private storage (`SEC-05`).<br>2. Prevent duplicate admin approval with idempotency checks.<br>3. Enforce atomic wallet balance increment. |
| [WithdrawalService.php](file:///F:/laragon/www/migratepay/app/Services/WithdrawalService.php) | `app/Services/WithdrawalService.php` | **IMPROVE** | 1. Implement locked balance hold upon request submission.<br>2. Guarantee atomic balance refund upon admin rejection.<br>3. Validate minimum/maximum tier limits. |
| `gateway_settings.json` handler | `app/Services/PaymentGatewayService.php` | **RECONSTRUCT** | 1. Retire unencrypted JSON file storage (`SEC-01`).<br>2. Implement encrypted database vault (`Crypt::encryptString`).<br>3. Provide pluggable driver interface for automated webhooks. |

---

### 2.3 HTTP Controllers & Routing
| V10 Controller / Route Group | V12 Target Location | Classification | Required Transformations & Adaptations |
| :--- | :--- | :--- | :--- |
| [AuthController.php](file:///F:/laragon/www/migratepay/app/Http/Controllers/AuthController.php) | `app/Http/Controllers/Auth/*.php` | **RECONSTRUCT** | 1. Remove plaintext OTP leak in failure response (`SEC-03`).<br>2. Enforce `['auth', 'admin']` middleware on `/api/users/*` (`SEC-02`).<br>3. Separate customer auth and administrative user management into distinct controllers.<br>4. Implement missing `POST /api/user/change-password` endpoint (`API-03`). |
| [PublicController.php](file:///F:/laragon/www/migratepay/app/Http/Controllers/PublicController.php) | `app/Http/Controllers/PublicController.php` | **PRESERVE AND ADAPT** | Maintain high-performance server-rendered Blade delivery; inject dynamic SEO tags. |
| [AiSupportController.php](file:///F:/laragon/www/migratepay/app/Http/Controllers/AiSupportController.php) | `app/Http/Controllers/AiSupportController.php` | **IMPROVE** | Sanitize guest ticket escalation to avoid admin impersonation (`SEC-07`); add rate limiting to `/api/ai/chat`. |
| [MediaController.php](file:///F:/laragon/www/migratepay/app/Http/Controllers/MediaController.php) | `app/Http/Controllers/Admin/MediaController.php` | **RECONSTRUCT** | Enforce MIME-type verification, maximum file sizes, and filename randomization to prevent RCE (`SEC-06`). |
| [PaymentGatewayController.php](file:///F:/laragon/www/migratepay/app/Http/Controllers/PaymentGatewayController.php) | `app/Http/Controllers/Admin/PaymentGatewayController.php` | **RECONSTRUCT** | Restrict configuration routes strictly to super-admins; sanitize public gateway metadata endpoint. |
| *CMS & Settings Controllers* | `app/Http/Controllers/Admin/*.php` | **RECONSTRUCT** | Create RESTful controllers for Announcements, CMS Pages, Menus, Site Settings, and Blog Posts. |

---

### 2.4 Middleware & Authorization
| V10 Middleware | V12 Target Setup | Classification | Required Transformations & Adaptations |
| :--- | :--- | :--- | :--- |
| `AdminMiddleware.php` | `bootstrap/app.php` alias `admin` | **PRESERVE AND ADAPT** | Register in Laravel 12 `$middleware->alias(['admin' => AdminMiddleware::class])`; verify `$user->is_admin === 1`. |
| `Authenticate.php` | Laravel 12 default auth | **PRESERVE AND ADAPT** | Ensure API unauthenticated requests return JSON 401; web requests redirect to `/login`. |
| `VerifyCsrfToken.php` | `bootstrap/app.php` `$middleware->validateCsrfTokens()` | **PRESERVE AND ADAPT** | Exclude automated payment webhook routes if needed in later phases. |

---

### 2.5 Public Website & Blade Views
| V10 Blade View | V12 Target View | Classification | Required Transformations & Adaptations |
| :--- | :--- | :--- | :--- |
| `layouts/main.blade.php` | `resources/views/layouts/main.blade.php` | **PRESERVE AND ADAPT** | Wire to Vite 7 asset pipeline (`@vite(['resources/css/app.css', 'resources/js/app.js'])`); update navigation links. |
| `home.blade.php` | `resources/views/pages/home.blade.php` | **PRESERVE AND ADAPT** | Preserve visual layout, hero typography, and dynamic fee calculator. |
| `about.blade.php`, `contact.blade.php`, `faq.blade.php` | `resources/views/pages/*.blade.php` | **PRESERVE AND ADAPT** | Maintain design fidelity; wire contact form to verified mail handler. |
| `charge-calculator.blade.php` | `resources/views/pages/charge-calculator.blade.php` | **IMPROVE** | Sync calculator fee tiers dynamically with database settings. |
| `privacy.blade.php`, `terms.blade.php`, `cookie-policy.blade.php` | `resources/views/pages/*.blade.php` | **PRESERVE AND ADAPT** | Preserve complete legal and regulatory compliance content. |

---

### 2.6 User Dashboard (React + Chakra UI)
| V10 User React Module | V12 Target Component | Classification | Required Transformations & Adaptations |
| :--- | :--- | :--- | :--- |
| `UserLayout.jsx` & `Header.jsx` | `resources/js/user/layout/` | **ADAPT TO CHAKRA 3** | Replace Chakra 2 Menu with `<Menu.Root>`, `<Menu.Trigger asChild>`, and `<Menu.Content>`. |
| `CommandCenterModule.jsx` | `resources/js/user/modules/CommandCenter.jsx` | **ADAPT TO CHAKRA 3** | Convert `<Stat>` blocks to `<Stat.Root>`; update metric cards to `colorPalette`. |
| `InitiateNewModule.jsx` | `resources/js/user/modules/InitiateNew.jsx` | **ADAPT TO CHAKRA 3** | Convert form inputs to `<Field.Root>`; validate buyer balance before submission. |
| `AddBalanceModule.jsx` | `resources/js/user/modules/AddBalance.jsx` | **ADAPT TO CHAKRA 3** | Update deposit proof file uploader; fetch sanitized gateway options. |
| `WithdrawFundsModule.jsx` | `resources/js/user/modules/WithdrawFunds.jsx` | **ADAPT TO CHAKRA 3** | Update withdrawal input validation with live fee preview. |
| `ProjectManagementModule.jsx` & Workspace | `resources/js/user/modules/Deals/` | **ADAPT TO CHAKRA 3** | Fix deliverable submission endpoint (`API-01`); adapt deal status badges. |
| `SupportTicketsModule.jsx` | `resources/js/user/modules/SupportTickets.jsx` | **ADAPT TO CHAKRA 3** | Modernize messaging thread UI and ticket status badges. |
| `ProfileSettingsModule.jsx` | `resources/js/user/modules/ProfileSettings.jsx` | **ADAPT TO CHAKRA 3** | Fix profile update route mismatch (`API-02`); wire change password tab (`API-03`). |
| `IroncladSecurityModule.jsx` | `resources/js/user/modules/Security.jsx` | **IMPROVE** | Connect real password change and 2FA authentication state. |

---

### 2.7 Admin Dashboard (React + Chakra UI)
| V10 Admin React Module | V12 Target Component | Classification | Required Transformations & Adaptations |
| :--- | :--- | :--- | :--- |
| `AdminLayout.jsx` & `AdminSidebar.jsx` | `resources/js/admin/layout/` | **ADAPT TO CHAKRA 3** | Modernize sidebar collapsible navigation and active indicator styles. |
| `OverviewModule.jsx` | `resources/js/admin/modules/Overview.jsx` | **ADAPT TO CHAKRA 3** | Convert metric cards and quick action tiles to Chakra 3. |
| `UserManagementModule.jsx` | `resources/js/admin/modules/Users.jsx` | **IMPROVE & ADAPT** | Secure backend routes with admin middleware; adapt data table to `<Table.Root>`. |
| `DealsModule.jsx` | `resources/js/admin/modules/Deals.jsx` | **IMPROVE & ADAPT** | Connect admin arbitration actions to verified, non-duplicating payout service. |
| `DepositsModule.jsx` | `resources/js/admin/modules/Deposits.jsx` | **IMPROVE & ADAPT** | Stream receipts via secure authorized route; prevent double approvals. |
| `WithdrawalsModule.jsx` | `resources/js/admin/modules/Withdrawals.jsx` | **IMPROVE & ADAPT** | Add rejection modal with required reason input. |
| `DisputesModule.jsx` | `resources/js/admin/modules/Disputes.jsx` | **IMPROVE & ADAPT** | Implement 3-way arbitration settlement modal (Buyer / Seller / Split). |
| `AiLiveChatModule.jsx` & `AiSettingsModule.jsx` | `resources/js/admin/modules/Ai/` | **IMPROVE & ADAPT** | Secure AI API key input; display real-time chat history. |
| *LocalStorage Modules (8 Modules)* | `resources/js/admin/modules/*.jsx` | **RECONSTRUCT** | Replace `localStorage` calls with standard RESTful Axios API requests backed by database models. |
| `BackupModule.jsx` | `resources/js/admin/modules/Backup.jsx` | **RECONSTRUCT** | Replace `setTimeout` placeholder (`ARCH-02`) with live artisan backup triggering. |

---

## 3. Recommended Phased Implementation Sequence

```
                                  PHASE 2
                    Architecture Decisions & Blueprint
                                     │
                                     ▼
                                  PHASE 3
                   Database & Security Core Foundation
         (Schema, Migrations, Seeders, Auth Core, Admin Middleware)
                                     │
                                     ▼
                                  PHASE 4
                  Financial Engine & Backend Services
       (Wallet Ledger, Escrow State Machine, Deposits, Withdrawals, Vault)
                                     │
                                     ▼
                                  PHASE 5
                   Public Website & Server-Side Blade
               (Layouts, Marketing Pages, Dynamic Calculator)
                                     │
                                     ▼
                                  PHASE 6
                     User Dashboard Modernization
           (Chakra 3 Setup, User Context, Deal Workspace, Tickets)
                                     │
                                     ▼
                                  PHASE 7
                    Admin Control Tower & CMS Rebuild
             (Chakra 3 Admin, 23 Modules with Live DB Persistence)
                                     │
                                     ▼
                                  PHASE 8
               Quality Assurance, E2E Testing & Verification
            (18 Test Suites, Static Analysis, Security Validation)
```

### Dependency Rules:
1. **Security & Database before Logic:** We cannot implement financial services until decimal types and atomic transactions are established in Phase 3.
2. **Backend APIs before Dashboards:** User and Admin React dashboards must not be assembled until real, verified backend endpoints exist in Phase 4.
3. **Chakra 3 Primitives before Modules:** UI wrapper components (`toaster`, `dialog`, `field`) must be configured before rebuilding dashboard screens.
4. **Automated Tests Alongside Code:** Each phase must implement its corresponding test suite to ensure zero regressions before moving forward.
