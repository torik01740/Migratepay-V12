# Route & API Catalogue: MigratePay V10

**Source Project:** MigratePay V10 (`F:\laragon\www\migratepay\routes`)  
**Assessment Date:** 2026-11-02  
**Scope:** Complete Route Registry, Middleware Mapping, Frontend API Callers, and Contract Discrepancies  

---

## 1. Route Registry Overview

In V10, almost all application routes—including RESTful JSON API endpoints—are registered within **`routes/web.php`** (218 lines), while **`routes/api.php`** contains only the default Sanctum skeleton route (`GET /api/user`).

- **Total Web & API Routes:** 62
- **Public Website Routes:** 14
- **Auth Routes & Endpoints:** 8
- **Public API Endpoints:** 6
- **User Dashboard & API Endpoints:** 23
- **Admin Dashboard & API Endpoints:** 23

---

## 2. Master Route Catalogue

### A. Public Website Routes (`web` middleware)

| Method | URI | Name | Controller / Handler | Middleware | Frontend Caller | Notes |
|---|---|---|---|---|---|---|
| `GET` | `/` | — | Closure (`view('welcome')`) | `web` | Direct navigation | Passes `$activeBanner` to welcome page |
| `GET` | `/about` | `about` | Closure (`view('pages.about')`) | `web` | Navigation header / footer | Corporate background and story |
| `GET` | `/contact` | `contact` | Closure (`view('pages.contact')`) | `web` | Navigation header / footer | Contact form & office locations |
| `GET` | `/privacy-policy` | `privacy` | Closure (`view('pages.privacy')`) | `web` | Footer links | Legal privacy statement |
| `GET` | `/terms-and-conditions` | `terms` | Closure (`view('pages.terms')`) | `web` | Footer links | User agreement & escrow terms |
| `GET` | `/cookies` | `cookies` | Closure (`view('pages.cookies')`) | `web` | Cookie consent popup / footer | Cookie policy |
| `GET` | `/faq` | `faq` | Closure (`view('pages.faq')`) | `web` | Header / footer | Platform FAQs |
| `GET` | `/our-teams` | `team` | Closure (`view('pages.team')`) | `web` | Header / footer | Leadership & engineer bios |
| `GET` | `/documents` | `documents` | Closure (`view('pages.documents')`) | `web` | Footer | API and policy documentation |
| `GET` | `/blog` | `blog` | Closure (`view('pages.blog')`) | `web` | Header / footer | Educational articles |
| `GET` | `/gallery` | `gallery` | Closure (`view('pages.gallery')`) | `web` | Footer | Image showcase |
| `GET` | `/charge-calculator` | `calculator` | Closure (`view('pages.calculator')`) | `web` | Header navigation | Interactive fee estimator page |
| `GET` | `/profiles` | `profiles.index` | `PublicProfileController@index` | `web` | Directory navigation | Public verified freelancer/merchant directory |
| `GET` | `/user/{identifier}` | `profiles.show` | `PublicProfileController@show` | `web` | Profile link | Public profile, reviews, and completed deals |

---

### B. Authentication Routes (`web` middleware)

| Method | URI | Name | Controller / Handler | Middleware | Frontend Caller | Parameters / Payload |
|---|---|---|---|---|---|---|
| `GET` | `/auth` | `auth` | Closure (`view('auth')`) | `web` | Login/Signup buttons | Redirects to `/dashboard` or `/admin` if already logged in |
| `POST` | `/api/register` | — | `AuthController@register` | `web` | `auth.blade.php` | `name`, `username`, `email`, `phone`, `password`, `date_of_birth`, `nid_number`, `division`, `district`, `gender` |
| `POST` | `/api/check-availability` | — | `AuthController@checkAvailability` | `web` | `auth.blade.php` | `email`, `username` (real-time availability check) |
| `POST` | `/api/login` | — | `AuthController@login` | `web` | `auth.blade.php` | `email`, `password` (starts session, returns role redirect) |
| `POST` | `/api/logout` | — | `AuthController@logout` | `web` | Header logout buttons | Invalidates session, regenerates CSRF token |
| `POST` | `/api/forgot-password` | — | `AuthController@forgotPassword` | `web` | `auth.blade.php` | `email` (dispatches 6-digit OTP via mail) |
| `POST` | `/api/verify-otp` | — | `AuthController@verifyOtp` | `web` | `auth.blade.php` | `email`, `otp` (validates against `password_reset_tokens`) |
| `POST` | `/api/reset-password` | — | `AuthController@resetPassword` | `web` | `auth.blade.php` | `email`, `otp`, `password`, `password_confirmation` |

---

### C. Public API Endpoints (`web` middleware)

| Method | URI | Handler | Middleware | Frontend Caller | Purpose / Security Note |
|---|---|---|---|---|---|
| `GET` | `/api/public/categories` | `CategoryController@index` | `web` | Deal creation form | Returns active deal categories |
| `GET` | `/api/public/gateway-settings` | Closure | `web` | `finance.js:getGateways`, `adminApi.js` | **CRITICAL LEAK:** Directly returns `gateway_settings.json` with private secrets |
| `GET` | `/api/public/calculate-charge` | `DealController@calculateChargeAPI` | `web` | `deals.js:calculateCharge`, `calculator.blade.php` | Public fee tier calculation with coupon code support |
| `GET` | `/api/public/banners` | `BannerController@publicIndex` | `web` | `welcome.blade.php` | Returns active header notification banner |
| `POST` | `/api/ai/chat` | `AiSupportController@chat` | `web` | `ai-chat-widget.blade.php` | Live AI support chat widget query |
| `POST` | `/api/ai/escalate` | `AiSupportController@escalate` | `web` | `ai-chat-widget.blade.php` | Converts unhandled chat to Support Ticket |

---

### D. User Dashboard & Protected API Endpoints (`auth` middleware)

| Method | URI | Name | Controller / Handler | Middleware | Frontend Caller | Purpose |
|---|---|---|---|---|---|---|
| `GET` | `/dashboard/{any?}` | `user.dashboard` | Closure (`view('user.dashboard')`) | `web` | Browser navigation | SPA shell, passes `$authUser` to `window.AUTH_USER` |
| `GET` | `/api/me` | — | `AuthController@me` | `auth` | `UserContext.jsx:refreshUser` | Returns latest user record & wallet balance |
| `GET` | `/api/users` | — | `AuthController@listUsers` | `auth` | `adminApi.js:getUsers` | **CRITICAL VULNERABILITY:** Placed under `auth` instead of `admin` |
| `PUT` | `/api/users/{id}/status` | — | `AuthController@updateUserStatus` | `auth` | `adminApi.js:updateUserStatus` | **CRITICAL VULNERABILITY:** Placed under `auth` instead of `admin` |
| `PUT` | `/api/users/{id}/verification` | — | `AuthController@updateUserVerification` | `auth` | `adminApi.js:updateUserVerification` | **CRITICAL VULNERABILITY:** Placed under `auth` instead of `admin` |
| `POST` | `/api/user/request-verification` | — | `AuthController@requestVerification` | `auth` | `ProfileModule.jsx` | Submits user account for verification |
| `PUT` | `/api/profile` | — | `ProfileController@update` | `auth` | `ProfileModule.jsx` | **MISMATCH:** Frontend calls `POST /api/user/profile` |
| `POST` | `/api/deposits/upload-screenshot` | — | `DepositController@uploadScreenshot` | `auth` | `finance.js:uploadScreenshot` | Saves deposit receipt to `public/storage/deposits` |
| `POST` | `/api/deposits` | — | `DepositController@store` | `auth` | `finance.js:submit` | Creates manual deposit request |
| `GET` | `/api/deposits` | — | `DepositController@index` | `auth` | `finance.js:getAll` | User deposit history |
| `POST` | `/api/deals` | — | `DealController@store` | `auth` | `deals.js:create` | Creates deal, locks escrow if initiator is buyer |
| `GET` | `/api/deals` | — | `DealController@index` | `auth` | `deals.js:getAll` | User deals list |
| `GET` | `/api/deals/{id}` | — | `DealController@show` | `auth` | `deals.js:getById` | Single deal detail and workspace state |
| `POST` | `/api/deals/{id}/accept` | — | `DealController@accept` | `auth` | `deals.js:accept` | Accepts deal, locks escrow if counterparty is buyer |
| `POST` | `/api/deals/{id}/decline` | — | `DealController@decline` | `auth` | `deals.js:decline` | Declines deal (missing buyer refund) |
| `POST` | `/api/deals/{id}/release` | — | `DealController@release` | `auth` | `deals.js:releasePayment` | Buyer releases escrow payment to seller |
| `POST` | `/api/deals/{id}/message` | — | `DealController@sendMessage` | `auth` | `deals.js:sendMessage` | Appends message to deal workspace |
| `POST` | `/api/deals/{id}/submit` | — | `DealController@submitWork` | `auth` | `ProjectManagementModule.jsx` | **MISMATCH:** Frontend calls `/api/deals/${id}/deliverable` |
| `POST` | `/api/deals/{id}/review-submission` | — | `DealController@reviewSubmission` | `auth` | `deals.js:reviewSubmission` | Buyer approves or rejects deliverable submission |
| `POST` | `/api/deals/{id}/extension` | — | `DealController@requestExtension` | `auth` | `ProjectManagementModule.jsx` | Requests deadline extension |
| `POST` | `/api/deals/{id}/approve-extension` | — | `DealController@approveExtension` | `auth` | `ProjectManagementModule.jsx` | Approves deadline extension |
| `POST` | `/api/deals/{id}/report` | — | `DealController@reportIssue` | `auth` | `deals.js:reportDispute` | Files dispute report on active deal |
| `GET` | `/api/deals/{id}/reports` | — | `DealController@getReports` | `auth` | `ProjectManagementModule.jsx` | Lists reports for deal |
| `POST` | `/api/deals/{id}/custom-service` | — | `DealController@requestCustomService` | `auth` | `ProjectManagementModule.jsx` | Requests custom escrow service (1% charge) |
| `GET` | `/api/notifications` | — | `NotificationController@index` | `auth` | `userAndSupport.js:getNotifications` | Lists user notifications |
| `POST` | `/api/notifications/{id}/read` | — | `NotificationController@markRead` | `auth` | `NotificationDropdown.jsx` | Marks notification as read |
| `POST` | `/api/notifications/read-all` | — | `NotificationController@markAllRead` | `auth` | `userAndSupport.js:markAllNotificationsRead` | Marks all notifications as read |
| `GET` | `/api/withdrawal-methods` | — | `WithdrawalController@methodsIndex` | `auth` | `finance.js:getMethods` | Lists user payout accounts |
| `POST` | `/api/withdrawal-methods` | — | `WithdrawalController@methodStore` | `auth` | `finance.js:saveMethod` | Adds/updates payout method |
| `DELETE` | `/api/withdrawal-methods/{id}` | — | `WithdrawalController@methodDestroy` | `auth` | `finance.js:deleteMethod` | Deletes payout method |
| `GET` | `/api/withdrawals` | — | `WithdrawalController@index` | `auth` | `finance.js:getAll` | User withdrawal requests |
| `POST` | `/api/withdrawals` | — | `WithdrawalController@store` | `auth` | `finance.js:submit` | Requests withdrawal, deducts balance |
| `POST` | `/api/deals/{id}/review` | — | `ReviewController@store` | `auth` | `deals.js:submitReview` | Submits 4-criteria rating |
| `GET` | `/api/deals/{id}/can-review` | — | `ReviewController@canReview` | `auth` | `ReviewModal.jsx` | Checks if user is eligible to review deal |
| `GET` | `/api/users/{id}/reviews` | — | `ReviewController@userReviews` | `auth` | `userAndSupport.js:getUserReviews` | Reviews received by user |
| `GET` | `/api/support-tickets` | — | `SupportTicketController@index` | `auth` | `userAndSupport.js:getTickets` | Lists user's support tickets |
| `POST` | `/api/support-tickets` | — | `SupportTicketController@store` | `auth` | `userAndSupport.js:createTicket` | Submits new support ticket |
| `POST` | `/api/support-tickets/{id}/reply` | — | `SupportTicketController@reply` | `auth` | `userAndSupport.js:replyTicket` | Appends message to ticket |
| `POST` | `/api/support-tickets/sync` | — | `SupportTicketController@sync` | `auth` | `SupportModule.jsx` | Offline sync synchronization |

---

### E. Admin Dashboard & Admin API Endpoints (`['auth', 'admin']` middleware)

| Method | URI | Name | Controller / Handler | Middleware | Frontend Caller | Purpose |
|---|---|---|---|---|---|---|
| `GET` | `/admin/{any?}` | `admin.dashboard` | Closure (`view('admin.dashboard')`) | `web` | Browser navigation | Admin SPA container |
| `GET` | `/faitor` | — | Closure (`view('admin.dashboard')`) | `web` | Secret alias | Secret direct admin URL |
| `GET` | `/api/admin/deposits` | — | `DepositController@adminIndex` | `auth`, `admin` | `adminApi.js:getDeposits` | Admin deposit review list |
| `POST` | `/api/admin/gateway-settings` | — | Closure | `auth`, `admin` | `adminApi.js:updateGatewaySettings` | Writes to `storage/app/gateway_settings.json` |
| `POST` | `/api/admin/deposits/{id}/approve` | — | `DepositController@approve` | `auth`, `admin` | `adminApi.js:approveDeposit` | Approves deposit, credits wallet |
| `POST` | `/api/admin/deposits/{id}/reject` | — | `DepositController@reject` | `auth`, `admin` | `adminApi.js:rejectDeposit` | Rejects deposit with note |
| `POST` | `/api/admin/deposits/{id}/read` | — | `DepositController@markAsRead` | `auth`, `admin` | `adminApi.js:markDepositRead` | Dismisses unread banner |
| `GET` | `/api/admin/deals` | — | `DealController@adminIndex` | `auth`, `admin` | `adminApi.js:getDeals` | Admin deals management queue |
| `POST` | `/api/admin/deals/{id}/approve-funds` | — | `DealController@adminApproveFunds` | `auth`, `admin` | `adminApi.js:approveDealFunds` | Admin force-releases funds to seller |
| `GET` | `/api/admin/reports` | — | `DealController@adminReports` | `auth`, `admin` | `adminApi.js:getReports` | Admin deal dispute tickets |
| `POST` | `/api/admin/reports/{id}/resolve` | — | `DealController@adminResolveReport` | `auth`, `admin` | `adminApi.js:resolveReport` | Resolves dispute with resolution note |
| `GET` | `/api/admin/reports/ledger` | — | `AnalyticsController@getLedger` | `auth`, `admin` | `AnalyticsModule.jsx` | Full platform financial transaction ledger |
| `GET` | `/api/admin/analytics/dashboard` | — | `AnalyticsController@getDashboardMetrics` | `auth`, `admin` | `DashboardModule.jsx` | Platform stats (volumes, users, active deals) |
| `GET` | `/api/admin/withdrawals` | — | `WithdrawalController@adminIndex` | `auth`, `admin` | `adminApi.js:getWithdrawals` | Admin withdrawal payout queue |
| `POST` | `/api/admin/withdrawals/{id}/approve` | — | `WithdrawalController@adminApprove` | `auth`, `admin` | `adminApi.js:approveWithdrawal` | Marks withdrawal processed |
| `POST` | `/api/admin/withdrawals/{id}/reject` | — | `WithdrawalController@adminReject` | `auth`, `admin` | `adminApi.js:rejectWithdrawal` | Rejects withdrawal and refunds user balance |
| `GET` | `/api/admin/media` | — | `MediaController@index` | `auth`, `admin` | `AdminContext.jsx`, `MediaModule.jsx` | Lists media in `public/assets/images` |
| `POST` | `/api/admin/media/upload` | — | `MediaController@upload` | `auth`, `admin` | `MediaModule.jsx` | Admin file upload |
| `DELETE` | `/api/admin/media` | — | `MediaController@delete` | `auth`, `admin` | `MediaModule.jsx` | Deletes media file and metadata |
| `PUT` | `/api/admin/media/{id}/metadata` | — | `MediaController@updateMetadata` | `auth`, `admin` | `MediaModule.jsx` | Updates alt/title/caption |
| `GET` | `/api/admin/coupons` | — | `CouponController@index` | `auth`, `admin` | `CouponsModule.jsx` | Lists promo codes |
| `POST` | `/api/admin/coupons` | — | `CouponController@store` | `auth`, `admin` | `CouponsModule.jsx` | Creates promo code |
| `GET` | `/api/admin/coupons/{id}` | — | `CouponController@show` | `auth`, `admin` | `CouponsModule.jsx` | Single coupon detail |
| `PUT` | `/api/admin/coupons/{id}` | — | `CouponController@update` | `auth`, `admin` | `CouponsModule.jsx` | Updates coupon parameters |
| `DELETE` | `/api/admin/coupons/{id}` | — | `CouponController@destroy` | `auth`, `admin` | `CouponsModule.jsx` | Deletes/soft-deletes coupon |
| `GET` | `/api/admin/banners` | — | `BannerController@index` | `auth`, `admin` | `BannersModule.jsx` | Lists marketing banners |
| `POST` | `/api/admin/banners` | — | `BannerController@store` | `auth`, `admin` | `BannersModule.jsx` | Creates banner |
| `GET` | `/api/admin/banners/{banner}` | — | `BannerController@show` | `auth`, `admin` | `BannersModule.jsx` | Banner detail |
| `PUT` | `/api/admin/banners/{banner}` | — | `BannerController@update` | `auth`, `admin` | `BannersModule.jsx` | Updates banner design & dates |
| `DELETE` | `/api/admin/banners/{banner}` | — | `BannerController@destroy` | `auth`, `admin` | `BannersModule.jsx` | Deletes banner |
| `POST` | `/api/admin/support-tickets/{id}/reply` | — | `SupportTicketController@reply` | `auth`, `admin` | `adminApi.js:replyTicket` | Admin reply to ticket |
| `POST` | `/api/admin/support-tickets/{id}/close` | — | `SupportTicketController@close` | `auth`, `admin` | `adminApi.js:closeTicket` | Admin closes ticket |
| `GET` | `/api/admin/knowledge-base` | — | `KnowledgeBaseController@index` | `auth`, `admin` | `AiLiveChatModule.jsx` | Lists AI Q&A knowledge base |
| `POST` | `/api/admin/knowledge-base` | — | `KnowledgeBaseController@store` | `auth`, `admin` | `AiLiveChatModule.jsx` | Adds new AI Q&A pair |
| `PUT` | `/api/admin/knowledge-base/{id}` | — | `KnowledgeBaseController@update` | `auth`, `admin` | `AiLiveChatModule.jsx` | Updates AI Q&A pair |
| `DELETE` | `/api/admin/knowledge-base/{id}` | — | `KnowledgeBaseController@destroy` | `auth`, `admin` | `AiLiveChatModule.jsx` | Deletes AI Q&A pair |
| `GET` | `/api/admin/ai-settings` | — | `AiAdminController@getSettings` | `auth`, `admin` | `AiLiveChatModule.jsx` | Gets AI model & API keys |
| `POST` | `/api/admin/ai-settings` | — | `AiAdminController@saveSettings` | `auth`, `admin` | `AiLiveChatModule.jsx` | Saves AI configuration |
| `GET` | `/api/admin/ai-chat-history` | — | `AiAdminController@getHistory` | `auth`, `admin` | `AiLiveChatModule.jsx` | Audits visitor AI chat transcripts |

---

## 3. Discrepancy & Mismatch Matrix

| Finding ID | Frontend Invocation | Expected / Backend Route | Issue Description | Impact |
|---|---|---|---|---|
| **API-01** | `dealsApi.uploadDeliverable`: `POST /api/deals/${id}/deliverable` | `POST /api/deals/{id}/submit` | Path mismatch between API client and backend route | Calling `uploadDeliverable` returns 404 Route Not Found |
| **API-02** | `userApi.updateProfile`: `POST /api/user/profile` | `PUT /api/profile` | Path and HTTP method mismatch | Profile updates from `userApi` helper fail with 404/405 |
| **API-03** | `userApi.changePassword`: `POST /api/user/change-password` | *Non-existent* | No backend route registered for authenticated password change | Calling `changePassword` returns 404 Route Not Found |
| **SEC-01** | `finance.js:getGateways`: `GET /api/public/gateway-settings` | Public closure | Returns raw JSON file with merchant secrets, API keys, passwords | Severe secret leakage |
| **SEC-02** | `adminApi.js:getUsers`: `GET /api/users` | `AuthController@listUsers` | Placed inside `auth` middleware instead of `['auth', 'admin']` | Regular users can dump the entire user database |
