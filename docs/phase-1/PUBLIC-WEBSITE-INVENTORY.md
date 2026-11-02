# Public Website Inventory: MigratePay V10

**Source Project:** MigratePay V10 (`F:\laragon\www\migratepay\resources\views`)  
**Assessment Date:** 2026-11-02  
**Scope:** Marketing Pages, Layout Architecture, Blade Components, Interactive Widgets, and Design System  

---

## 1. Public Architecture Overview

The public marketing website in V10 is built primarily using **server-rendered Blade templates** extending a shared layout (`resources/views/layouts/frontend.blade.php`), styled with custom CSS and Tailwind utility classes, and enhanced with client-side interactive JavaScript (e.g. calculator widgets, FAQ accordions, and the floating AI live chat widget).

- **Total Public Pages:** 14
- **Shared Layouts:** 1 (`layouts/frontend.blade.php`)
- **Shared Partials:** 2 (`partials/footer.blade.php`, `partials/ai-chat-widget.blade.php`)
- **Interactive Widgets:** 3 (Fee Calculator, AI Live Chat Widget, Cookie Consent Popup)

---

## 2. Public Pages Catalogue

| Page Name | Route / URI | Blade Template | Primary Content & Sections | Dynamic Data / Controllers | Interactive Features |
|---|---|---|---|---|---|
| **Home / Landing** | `GET /` | `welcome.blade.php` (160 KB) | Hero section with animated trust badges, live escrow counter, interactive fee calculator card, process steps (1-2-3), feature highlights, verified merchant spotlight, testimonials, CTA footer. | `\App\Models\Banner::active()->first()`, `PublicProfileController` | Fee calculator form, active banner display, modal triggers, AI widget mount. |
| **About Us** | `GET /about` | `pages/about.blade.php` (33.7 KB) | Company origin, mission, core values, regulatory compliance statement, security philosophy, team introduction. | Static Blade view | Interactive tab navigation between company pillars. |
| **Contact Us** | `GET /contact` | `pages/contact.blade.php` (29.8 KB) | Direct contact form, physical office location in Dhaka, official support email, phone numbers, Google Map embed placeholder. | Static Blade view | Client-side form validation, submit handler. |
| **Charge Calculator** | `GET /charge-calculator` | `pages/calculator.blade.php` (29.3 KB) | Dedicated transaction fee estimator, fee tier breakdown, discount simulator, payment payer split radio options (`seller_100`, `buyer_100`, `shared_50`, `custom`). | Connects to `GET /api/public/calculate-charge` | Live calculation on amount input, coupon code verification. |
| **FAQ** | `GET /faq` | `pages/faq.blade.php` (52.2 KB) | Grouped FAQs (General, Escrow Process, Fees, Account Verification, Dispute Resolution, Payouts). | Static Blade view | Collapsible accordion items, keyword search filter. |
| **Terms & Conditions** | `GET /terms-and-conditions` | `pages/terms.blade.php` (53.5 KB) | Legal agreement, escrow rights, prohibited transactions, liability limitations, dispute resolution rules. | Static Blade view | Sticky table of contents with smooth scroll navigation. |
| **Privacy Policy** | `GET /privacy-policy` | `pages/privacy.blade.php` (57.0 KB) | Data collection disclosures, NID/KYC handling, cookie retention, third-party disclosure policies. | Static Blade view | Sticky sidebar navigation. |
| **Cookie Policy** | `GET /cookies` | `pages/cookies.blade.php` (39.7 KB) | Explanation of essential, analytical, and marketing cookies. | Static Blade view | Cookie preference settings toggles. |
| **Our Team** | `GET /our-teams` | `pages/team.blade.php` (16.5 KB) | Leadership team, advisory board, core engineers, photos and LinkedIn/social links. | Static Blade view | Hover profile card effects. |
| **Blog & Knowledge** | `GET /blog` | `pages/blog.blade.php` (62.6 KB) | Industry guides, cybersecurity awareness, escrow protection tips, f-commerce security guides. | Static Blade view (seeded articles) | Category filter, reading time badges, search bar. |
| **Gallery / Media** | `GET /gallery` | `pages/gallery.blade.php` (2.5 KB) | Brand assets, event photos, platform screenshots. | Static Blade view | Lightbox preview. |
| **Documents** | `GET /documents` | `pages/documents.blade.php` (2.5 KB) | Public documentation repository, whitepapers, downloadable guides. | Static Blade view | Direct download links. |
| **Public Profiles Directory** | `GET /profiles` | `pages/profiles.blade.php` (23.7 KB) | Directory of verified freelancers and merchants, filters by category/district/rating. | `PublicProfileController@index` | Search input, category filter, pagination. |
| **Public User Profile** | `GET /user/{identifier}` | `pages/profile-show.blade.php` (30.0 KB) | Individual public profile: cover photo, avatar, verified badge, bio, headline, skills, completed deal count, 4-criterion reviews. | `PublicProfileController@show` | Review breakdown charts, contact button. |

---

## 3. Shared Layout & Partials

### 1. `resources/views/layouts/frontend.blade.php` (37.6 KB)
- Serves as the master HTML shell for all public marketing pages.
- Standardizes `<head>` metadata, OpenGraph tags, Google Fonts (`Inter`, `JetBrains Mono`), and CSRF token.
- Embeds top navigation header with dynamic login/dashboard buttons based on `auth()->check()`.
- Renders active top banner if available.
- Includes `@include('partials.footer')` and `@include('partials.ai-chat-widget')`.

### 2. `resources/views/partials/ai-chat-widget.blade.php` (28.8 KB)
- Floating live chat drawer anchored at bottom-right corner.
- Generates or restores a persistent `session_id` in `localStorage`.
- Connects directly to `POST /api/ai/chat` for conversational Q&A.
- Detects escalation trigger and presents the escalation form (`POST /api/ai/escalate`).
- Handles Bengali, Banglish, and English queries.

### 3. `resources/views/partials/footer.blade.php` (12.5 KB)
- Multi-column footer containing platform links, legal links, supported payment icons (bKash, Nagad, Visa, Mastercard, Bank Transfer), trust badges, and copyright statement.

---

## 4. Visual Design System Reference

| Element | Specification / Token | Usage |
|---|---|---|
| **Primary Brand Color** | `#1769FF` (Sapphire Blue) | CTAs, primary buttons, active links, brand accents |
| **Secondary Brand Color** | `#002147` / `#071A3B` (Midnight Navy) | Dark headers, hero backgrounds, primary text headings |
| **Background Neutral** | `#F5F8FF` / `#F8FAFC` (Soft Ice Blue / Slate) | Main page background, subtle card containers |
| **Success / Trust Color** | `#10B981` / `#006A4E` (Emerald Green) | Secured escrow badges, verification checkmarks, positive balances |
| **Warning / Alert Color** | `#F59E0B` (Amber) | Pending statuses, attention banners |
| **Danger Color** | `#DC4C64` / `#E11D48` (Crimson) | Dispute alerts, rejected statuses, error states |
| **Primary Typography** | `'Inter', sans-serif` | All headings, body copy, form inputs, buttons |
| **Monospace Typography** | `'JetBrains Mono', monospace` | Deal IDs (`DX-123456`), transaction hashes, monetary amounts |
| **Corner Radii** | `borderRadius: '2xl'` (16px) to `'3xl'` (24px) | Cards, modals, containers, floating widgets |
| **Shadows** | `0 1px 3px 0 rgba(0, 0, 0, 0.05)`, `0 10px 25px -5px rgba(23, 105, 255, 0.1)` | Cards, elevated floating buttons |

---

## 5. V12 Recommendation for Public Pages

1. **Preserve Blade Architecture:** Public pages are already SEO-optimized, fast-loading, and cleanly structured in Blade. Reconstructing them in Blade within Laravel 12 preserves performance and search indexing.
2. **Componentize Shared Snippets:** Convert inline repetitive HTML into clean Laravel 12 Blade components (`<x-layout.frontend>`, `<x-banner>`, `<x-calculator-widget>`).
3. **Upgrade AI Chat Widget:** Migrate the vanilla JavaScript AI chat widget into an isolated React component or modernized Blade component that connects cleanly to the V12 AI backend.
