# User Dashboard Inventory: MigratePay V10

**Source Project:** MigratePay V10 (`F:\laragon\www\migratepay\resources\js\user`)  
**Assessment Date:** 2026-11-02  
**Scope:** SPA Shell, React Architecture, Context Hydration, Layout Components, Modules, and Backend Connectivity  

---

## 1. User Dashboard Architecture

The User Dashboard in V10 is an interactive Single Page Application built with **React 19** and **Chakra UI 2.10.10**:

- **Mount Container:** `resources/views/user/dashboard.blade.php` renders `<div id="user-root"></div>`.
- **Authentication Hydration:** The server injects authenticated user data via `window.AUTH_USER = @json($authUser);`.
- **Entry Script:** `resources/js/user/index.jsx` mounts the `<UserProvider>` and `<UserLayout>`.
- **Routing & Navigation:** Custom client-side history navigation (`window.history.pushState`) managed by `UserContext.jsx`, responding to paths like `/dashboard/command-center`, `/dashboard/initiate-new`, etc.
- **Total Functional Modules:** 10

---

## 2. Layout & Context Components

### 1. `UserContext.jsx` (140 lines / 4,818 bytes)
- **State Managed:** `authUser`, `activeNav`, `sidebarOpen`, `walletBalance`, `notifications`, `unreadNotifsCount`, `currency`.
- **Background Polling:**
  - Polls `/api/notifications` every 15 seconds to update unread badge counts.
- **Helpers:**
  - `refreshUser()`: Calls `GET /api/me` to refresh wallet balance after financial actions.
  - `markNotificationsRead()`: Calls `POST /api/notifications/read-all`.
  - `getCsrfToken()`: Extracts token from `<meta name="csrf-token">`.
- **Architecture Note:** Reads `currency` and site settings from `localStorage.getItem('migratepay_site_settings')`.

### 2. `UserLayout.jsx` & Navigation
- **`UserHeader.jsx` (10.7 KB):** Displays user name, avatar, real-time wallet balance badge with quick "Add Balance" shortcut, notification bell dropdown, and profile menu with logout.
- **`UserSidebar.jsx` (14.6 KB):** Navigation drawer with responsive collapsible behavior. Navigation items:
  1. Command Center (`UserDashboardHome`)
  2. Initiate New (`CreateDealModule`)
  3. Add Balance (`DepositModule`)
  4. Withdraw Funds (`WithdrawalModule`)
  5. Project Management (`ProjectManagementModule`)
  6. Support Tickets (`SupportModule`)
  7. Messages (`MessagesModule`)
  8. Activity (`ActivityModule`)
  9. Ironclad Security (`SecurityModule`)
  10. Profile Settings (`ProfileModule`)
- **`NotificationDropdown.jsx` (5.9 KB):** Popover listing recent notifications with read/unread markers and direct links to deals.

---

## 3. User Dashboard Modules Catalogue

### 1. `UserDashboardHome.jsx` (Command Center) (30.7 KB)
- **Role:** Central mission control dashboard.
- **Features:**
  - High-level metric cards: Total Escrow Secured, Active Deals, Completed Projects, Current Wallet Balance.
  - Active deals quick-action table with status badges (`awaiting_acceptance`, `funds_secured`, `buyer_review`, etc.).
  - Recent financial ledger activity stream.
  - Quick action buttons (New Deal, Deposit, Withdraw).
- **Backend APIs:** Calls `dealsApi.getAll()`, `finance.depositsApi.getAll()`, `userApi.getMe()`.
- **Completeness:** **IMPLEMENTED AND CONNECTED** to database.

### 2. `CreateDealModule.jsx` (Initiate New Deal) (29.4 KB)
- **Role:** Wizard for creating bilateral escrow agreements.
- **Features:**
  - Role selection: "I am Buying" vs "I am Selling".
  - Deal title, category dropdown (`GET /api/public/categories`), custom category field.
  - Amount input in BDT (`৳`).
  - Charge fee payer selection (`shared_50`, `buyer_100`, `seller_100`, `custom`).
  - Coupon code redemption with live charge recalculation (`GET /api/public/calculate-charge`).
  - Counterparty email lookup and validation.
  - Deadline date picker.
  - Escrow fund check: Warns if buyer's wallet balance is insufficient before submission.
- **Backend APIs:** Calls `dealsApi.create()` (`POST /api/deals`).
- **Completeness:** **IMPLEMENTED AND CONNECTED** to database.

### 3. `ProjectManagementModule.jsx` (Project Workspace) (33.0 KB)
- **Role:** Collaborative deal workspace and milestone delivery pipeline.
- **Features:**
  - Filter deals by status tabs: All, Awaiting Acceptance, Active / Secured, In Review, Completed, Disputed.
  - Deal detail modal / drawer:
    - Deal timeline and milestone tracker.
    - Accept / Decline actions for counterparties.
    - File upload component for deliverable submissions.
    - Buyer milestone review (Approve delivery or Request revision).
    - Release escrow payment button with confirmation prompt.
    - Request deadline extension modal.
    - Dispute filing form with reason and description.
    - Review trigger: Pops `ReviewModal` upon completion.
- **Backend APIs:** Calls `dealsApi.getAll()`, `dealsApi.getById()`, `dealsApi.accept()`, `dealsApi.decline()`, `dealsApi.releasePayment()`, `dealsApi.reviewSubmission()`, `dealsApi.reportDispute()`.
- **Discrepancy:** Calls `dealsApi.uploadDeliverable` which targets `/api/deals/${id}/deliverable` (backend route is `/api/deals/${id}/submit`).
- **Completeness:** **IMPLEMENTED AND CONNECTED** (with deliverable route mismatch to resolve).

### 4. `DepositModule.jsx` (Add Balance) (22.2 KB)
- **Role:** Manual deposit request and wallet funding workflow.
- **Features:**
  - Gateway selector: bKash, Nagad, Rocket, Bank Transfer.
  - Gateway instructions display: Merchant/agent number, bank account details, routing numbers loaded from `GET /api/public/gateway-settings`.
  - Amount input.
  - Transaction reference / proof note input.
  - Payment receipt screenshot drag-and-drop uploader (`POST /api/deposits/upload-screenshot`).
  - Deposit history table with status badges (`Pending`, `Approved`, `Rejected`) and admin feedback notes.
- **Backend APIs:** Calls `finance.depositsApi.getGateways()`, `finance.depositsApi.uploadScreenshot()`, `finance.depositsApi.submit()`, `finance.depositsApi.getAll()`.
- **Completeness:** **IMPLEMENTED AND CONNECTED** to database.

### 5. `WithdrawalModule.jsx` (Withdraw Funds) (20.7 KB)
- **Role:** Payout account management and withdrawal requests.
- **Features:**
  - Payout methods manager: Add Bank Account (Bank name, branch, routing, account name, account number) or Mobile Banking (bKash/Nagad/Rocket number).
  - Payout method deletion with pending constraint check.
  - Withdrawal form: Amount input, method selection, fee breakdown calculation (1.5% for mobile cashout, 0% for bank), net payout preview.
  - Withdrawal history log with status badges and reference IDs.
- **Backend APIs:** Calls `finance.withdrawalsApi.getMethods()`, `finance.withdrawalsApi.saveMethod()`, `finance.withdrawalsApi.deleteMethod()`, `finance.withdrawalsApi.submit()`, `finance.withdrawalsApi.getAll()`.
- **Completeness:** **IMPLEMENTED AND CONNECTED** to database.

### 6. `ActivityModule.jsx` (Financial Ledger) (12.5 KB)
- **Role:** Immutable financial audit history for the user.
- **Features:**
  - Table of all transactions (`escrow_lock`, `escrow_release`, `deposit`, `withdrawal`, `withdrawal_rejected`).
  - Columns: Date/Time, Transaction Type, Description, Amount (`+` or `-`), Balance Before, Balance After.
  - Filter by transaction type and date range.
- **Backend APIs:** Fetches transaction records from `GET /api/me` or `/api/transactions`.
- **Completeness:** **IMPLEMENTED AND CONNECTED** to database.

### 7. `MessagesModule.jsx` (Deal Messages Hub) (17.9 KB)
- **Role:** Consolidated communication center for deal negotiations.
- **Features:**
  - Left panel: Active deals list with last message snippet.
  - Right panel: Message conversation thread.
  - Send message input form with timestamping.
- **Backend APIs:** Calls `dealsApi.sendMessage()` (`POST /api/deals/{id}/message`).
- **Completeness:** **IMPLEMENTED AND CONNECTED** (embedded within deal workspace JSON).

### 8. `ProfileModule.jsx` (Profile & Verification) (28.1 KB)
- **Role:** User profile management and KYC identity verification.
- **Features:**
  - Personal Information: Name, phone, email, date of birth, gender, division, district.
  - Professional Profile: Username, headline, bio, skills tags, languages, education, social links.
  - Photo Uploads: Avatar and cover banner positioning.
  - Verification Center:
    - Displays current status (`NOT_VERIFIED`, `PENDING`, `VERIFIED`, `REJECTED`).
    - NID / Passport / Driving License upload fields.
    - Submit verification button (`POST /api/user/request-verification`).
- **Backend APIs:** Calls `userApi.getMe()`, `userApi.updateProfile()`, `POST /api/user/request-verification`.
- **Discrepancy:** Calls `userApi.updateProfile` which issues `POST /api/user/profile`, while backend route is `PUT /api/profile`.
- **Completeness:** **IMPLEMENTED AND CONNECTED** (with route mismatch to resolve).

### 9. `SupportModule.jsx` (Support Tickets) (20.3 KB)
- **Role:** Customer support ticketing desk.
- **Features:**
  - Ticket list with status filter (`Open`, `Closed`, `Resolved`, `Replied`).
  - Create ticket modal: Subject, Priority (`General Inquiry`, `High`, `Urgent`), Message, optional file attachment.
  - Ticket conversation thread: Displays user and admin messages chronologically with read receipts.
  - Reply form to existing tickets.
- **Backend APIs:** Calls `supportApi.getTickets()`, `supportApi.createTicket()`, `supportApi.replyTicket()`.
- **Completeness:** **IMPLEMENTED AND CONNECTED** to database.

### 10. `SecurityModule.jsx` (Ironclad Security Hub) (5.2 KB)
- **Role:** 2FA and encryption security settings.
- **Features:**
  - Two-Factor Authentication card: Displays static "Status: Protected / Authenticator linked via TOTP" and a "Disable 2FA" button that shows an informational toast.
  - Enterprise Vault Encryption card: Static marketing text describing 256-bit encryption and ISO compliance.
- **Backend APIs:** None.
- **Completeness:** **PLACEHOLDER / DEMONSTRATION** (no real TOTP generation or 2FA backend).

---

## 4. Supporting Components

### `ReviewModal.jsx` (5.3 KB)
- Interactive rating modal triggered upon deal completion.
- Collects 1-5 star ratings across 4 distinct dimensions:
  1. Communication
  2. Professional Behavior
  3. Delivery Speed
  4. Reliability & Accuracy
- Optional text feedback review comment.
- Calls `POST /api/deals/{id}/review`. Verified functional and connected to `reviews` table.
