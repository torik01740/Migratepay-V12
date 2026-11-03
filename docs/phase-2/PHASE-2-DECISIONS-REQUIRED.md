# MigratePay V12: Phase 2 Decisions Required & Business Trade-Offs

> **Document ID:** `DOC-P2-11`  
> **Source Project:** `F:\laragon\www\migratepay`  
> **Destination Project:** `F:\laragon\www\Migratepay-V12`  
> **Phase:** 2 — Architecture Decisions & Blueprint  
> **Status:** PENDING OWNER ALIGNMENT & ARCHITECTURAL SIGN-OFF  

---

## 1. Executive Summary

While Phase 2 has resolved all technical ambiguities and forensic discrepancies in the V10 codebase, four strategic architectural and business trade-offs require formal business-owner sign-off before commencing Phase 3 implementation.

Each decision is presented below with full context, evaluated options, risks, and the technical team's explicit recommendation.

---

## 2. Decision Log

### Decision 1: Inertia.js Role vs. Dual-Tier Blade + React SPA Architecture
- **Context:**
  The destination V12 skeleton contains `inertiajs/inertia-laravel: ^2.0` and `@inertiajs/react: ^3.10.0`. However, forensic inspection of V10 confirmed **zero Inertia code or dependencies**. V10 was built as server-rendered Blade marketing pages combined with rich React 19 Single Page Applications mounted inside Blade host views (`dashboard.blade.php` and `admin.blade.php`), fetching JSON via an Axios client with CSRF protection.
- **Options:**
  - **Option A (Full Inertia Rewrite):** Rewrite all 33 React modules into Inertia page views using `useForm()` and `Inertia::render()`.  
    *Risk:* High. Unaligned package versions; destroys client-side tab state in `UserContext` and `AdminContext`; introduces significant delay and regression risk.
  - **Option B (Preserve Blade + React 19 SPA Architecture) — [RECOMMENDED]:** Keep server-rendered Blade for marketing pages; keep React 19 / Chakra UI 3 SPAs for User and Admin dashboards, communicating with session-authenticated JSON endpoints.  
    *Benefit:* Preserves 100% of tested user workflows, instant marketing page loads, zero framework churn.
- **Owner Action:** Confirm adoption of **Option B**.

---

### Decision 2: Extent of CMS Database Schema
- **Context:**
  V10 contained 8 admin modules that operated solely on browser `localStorage` (`Announcements`, `Charge Management`, `Manage Pages`, `Menus`, `Site Settings`, `User Settings Staff`, `Blog Posts`, `Analytics`).
- **Options:**
  - **Option A (Six Ad-Hoc Tables as proposed in DOC-P1-14):** Create separate tables for `announcements`, `platform_charges`, `cms_pages`, `menus`, `site_settings`, `blog_posts`.  
    *Risk:* Database schema bloat; unnecessary fragmentation.
  - **Option B (Lean Consolidation) — [RECOMMENDED]:**
    1. Consolidate Charge Tiers and Site Meta into a single, high-performance **`system_settings`** key-value table.
    2. Route announcements through the existing **`notifications`** table with broadcast capability.
    3. Attribute staff members directly to the **`users`** table via a `role` column.
    4. Create dedicated, clean tables for **`cms_pages`** and **`blog_posts`**.  
    *Benefit:* Elegant, maintainable schema that provides 100% real backend persistence without database bloat.
- **Owner Action:** Confirm adoption of **Option B**.

---

### Decision 3: Payment Gateway Architecture (Manual vs Automated Webhooks)
- **Context:**
  V10 presented admin configuration interfaces for 5 gateways (SSLCommerz, bKash, Nagad, Stripe, Bank Transfer), but **actual deposits operated under a 100% manual review model** (user enters transaction ID, uploads slip screenshot, admin approves). No automated webhook handlers or external payment SDKs were active.
- **Options:**
  - **Option A (Full Automated Gateway Mandate in Phase 4):** Halt manual deposit flow and mandate real automated SDK checkout and webhooks for Stripe, SSLCommerz, and bKash.  
    *Risk:* Requires live merchant credentials, sandbox sandbox testing, and external API contract dependencies before Phase 4 can complete.
  - **Option B (Hardened Manual Pipeline + Pluggable Webhook Architecture) — [RECOMMENDED]:** Preserve the verified manual deposit review pipeline (moving slips to the private storage disk), while designing an extensible gateway driver interface (`PaymentGatewayDriverInterface`) ready for automated webhooks in Phase 4/8.  
    *Benefit:* Guarantees immediate functional escrow operation without third-party credential blockers.
- **Owner Action:** Confirm adoption of **Option B**.

---

### Decision 4: Currency Model & Precision
- **Context:**
  V10 defaulted to Bangladeshi Taka (BDT / ৳) hardcoded in multiple templates, while `SiteSettingsModule` offered a monetary sign input.
- **Options:**
  - **Option A (Fixed Single Currency BDT):** Hardcode `৳` everywhere.
  - **Option B (Configurable Primary Currency) — [RECOMMENDED]:** Store currency code (`BDT`, `USD`) and symbol (`৳`, `$`) in `system_settings`, formatted through a global backend and frontend currency helper: `formatCurrency(amount)`.
- **Owner Action:** Confirm adoption of **Option B**.

---

### Decision 5: Guest AI Escalation & Anonymous Support
- **Context:**
  In V10, when an anonymous guest chatting with the AI requested human escalation, the ticket creation hardcoded `user_id = 1` (`SEC-07`), attributing guest tickets to the super-admin.
- **Options:**
  - **Option A (Require Login for AI Chat):** Prohibit unauthenticated visitors from using AI chat.  
    *Trade-off:* Reduces friction for prospective users evaluating the platform.
  - **Option B (Nullable Ticket User with Guest Identity Capture) — [RECOMMENDED]:** Allow guest AI chat; make `support_tickets.user_id` nullable; prompt the guest for their email and name upon human escalation.
- **Owner Action:** Confirm adoption of **Option B**.
