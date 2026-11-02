# MigratePay V10 → V12 Reconstruction & Migration Checklist

> **Purpose:** This is the authoritative working plan for any AI agent
> or developer working on the MigratePay V10-to-V12 reconstruction. Read
> this file before inspecting, editing, copying, installing, migrating,
> or deleting project files.
>
> **Current status:** Planning and initial source assessment completed.
> The actual folder-by-folder migration has not yet been confirmed as
> completed. Keep this document updated as work progresses.

------------------------------------------------------------------------

## 1. Project identity and source of truth

  ----------------------------------------------------------------------------------------
  Item                                Details
  ----------------------------------- ----------------------------------------------------
  Destination project                 `F:\laragon\www\Migratepay-V12`

  Destination framework               Laravel 12

  Destination PHP                     PHP 8.4.27

  Destination React                   React 19.3.1

  Destination Chakra UI               Chakra UI 3.37.0

  Destination Vite                    Vite 7.3.7

  Destination Tailwind                Tailwind CSS 4.x

  Source project                      MigratePay V10 (uploaded archive:
                                      `Migratepay-v10.zip`)

  Source framework                    Laravel 10.10

  Source frontend                     React 19.2.8, Chakra UI 2.10.10, Vite 5

  GitHub destination                  `https://github.com/torik01740/Migratepay-V12.git`

  Primary terminal                    Git Bash on Windows / Laragon
  ----------------------------------------------------------------------------------------

### Authoritative project decisions

1.  **V12 is the destination and technical foundation.** Do not create
    or switch to a V13 project.
2.  **V10 is the source of existing product functionality and design.**
    The objective is to preserve the nearly completed product, not
    rebuild it from scratch.
3.  Do not blindly copy the V10 project or overwrite the V12 framework
    skeleton.
4.  Inspect each source file/folder, understand dependencies and
    behavior, adapt it to Laravel 12 / React 19 / Chakra UI 3, integrate
    it into V12, and test it.
5.  Use React + Chakra UI 3 for interactive application interfaces,
    especially the user and admin dashboards. Keep Blade where it
    remains suitable, such as server-rendered public/SEO content;
    integrate React where it adds value. Do not force React onto every
    static page.
6.  Do not add dependencies, upgrade packages, or introduce a new
    architecture unless there is a clear requirement. Prefer the
    existing V12 stack.
7.  The old application does **not** appear to use Inertia. The current
    V12 setup has Inertia packages installed, but their major versions
    are not aligned (Laravel adapter 2.x and React adapter 3.x at last
    inspection). Do not introduce Inertia into the migration or change
    its versions without inspecting the current integration and
    documenting a reason. A Blade + React + API architecture may be
    retained.
8.  Keep the V12 Git repository and its history. Never copy the V10
    `.git` directory into V12.

------------------------------------------------------------------------

## 2. Rules for every AI agent / developer

Before making changes:

-   [ ] Read this entire checklist and inspect the current Git status.
-   [ ] Confirm the working directory is
    `F:\laragon\www\Migratepay-V12`.
-   [ ] Identify the exact V10 source path available in the current
    environment. Do not assume the uploaded ZIP has already been
    extracted.
-   [ ] Inspect the relevant V10 files and their V12 counterparts before
    editing.
-   [ ] Search for usages, imports, routes, configuration, database
    dependencies and frontend consumers.
-   [ ] Identify Laravel 10 / Chakra UI 2 / Vite 5 assumptions that need
    adaptation.
-   [ ] Explain the planned changes and risks before significant or
    destructive operations.
-   [ ] Make focused changes. Do not replace whole directories when
    selective integration is possible.
-   [ ] Preserve unrelated V12 files and configuration.
-   [ ] Never overwrite `.env`, credentials, keys, or the V12 dependency
    lockfiles with V10 versions.
-   [ ] Never copy `vendor/`, `node_modules/`, `.git/`, generated
    caches, or compiled build output from V10.
-   [ ] Never expose or commit secrets, real personal data, database
    dumps, private uploads, or production credentials.
-   [ ] Do not run destructive database commands (`migrate:fresh`,
    `db:wipe`, truncation, destructive seeders) without explicit
    approval and a verified backup.
-   [ ] Do not claim a feature is complete merely because its UI or
    source file exists. Verify the complete flow.
-   [ ] After each logical unit, run relevant tests/build checks and
    report exact results.
-   [ ] Update this checklist immediately after verified work. Do not
    mark items complete based on intention.

### Required reporting format after each work session

1.  **Scope:** files and features inspected/changed.
2.  **Findings:** what the old implementation does and any issues found.
3.  **Changes:** exact files changed and why.
4.  **Compatibility:** Laravel 12 / React 19 / Chakra UI 3
    considerations.
5.  **Verification:** commands run and their actual results.
6.  **Remaining work:** blockers, risks and next smallest step.
7.  **Checklist updates:** only mark verified items complete.

------------------------------------------------------------------------

## 3. Initial source assessment (from archive inspection)

The initial static assessment of the V10 archive reported the following.
These are inventory leads, not proof that every feature is complete or
operational. Re-verify against the actual source before migration.

-   [x] V10 archive received and initially inspected.
-   [x] Initial architecture and major feature inventory documented.
-   [x] Initial security and integration concerns identified.
-   [ ] Full source inventory exported and checked against actual
    extracted files.
-   [ ] Every feature independently traced from UI → API/route →
    controller/service → model/database.
-   [ ] Runtime behavior verified by launching the old application (if
    safe and feasible).

### Reported V10 inventory

  -----------------------------------------------------------------------
  Area                                Initial finding
  ----------------------------------- -----------------------------------
  Backend                             Laravel 10.10

  Frontend                            React 19.2.8

  UI library                          Chakra UI 2.10.10

  Build tool                          Vite 5

  Models                              Approximately 18

  Controllers                         Approximately 18

  Services                            Approximately 4

  Migrations                          Approximately 28

  Web route definitions               Approximately 113

  Admin React modules                 Approximately 23

  User React modules                  Approximately 10

  Authentication                      Laravel session authentication;
                                      Sanctum package present

  Public site                         Blade pages and shared layouts

  Main product                        Escrow / marketplace workflows,
                                      wallets and administration
  -----------------------------------------------------------------------

### Reported product areas to preserve and verify

**Public website** - \[ \] Landing page and shared public layout - \[ \]
About, Contact, FAQ, Team - \[ \] Privacy, Terms, Cookie Policy - \[ \]
Blog, Gallery, Documents - \[ \] Charge calculator - \[ \] Public
profiles and profile detail pages - \[ \] Banner, footer, menus and
support widget

**User application** - \[ \] Dashboard / command center - \[ \]
Registration, login, logout and password recovery - \[ \] Profile and
account settings - \[ \] KYC / account verification - \[ \] Create and
manage escrow deals - \[ \] Deal workspace, messages and deliverables -
\[ \] Wallet and transaction history - \[ \] Deposit requests - \[ \]
Withdrawals and payout methods - \[ \] Notifications and activity
history - \[ \] Reviews - \[ \] Support tickets and AI support

**Admin application** - \[ \] Dashboard and analytics - \[ \] User and
verification management - \[ \] Deal and dispute management - \[ \]
Deposit and withdrawal review - \[ \] Categories and coupons - \[ \]
Banners, media and website content - \[ \] Menus, pages and site
settings - \[ \] Support tickets and AI support - \[ \] Payment gateway
configuration - \[ \] Charges, announcements and blog - \[ \] Reports
and project submissions - \[ \] Backup functionality (verify whether
real backend functionality exists)

**Backend domains** - \[ \] Authentication and authorization - \[ \]
Users and profiles - \[ \] KYC / verification - \[ \] Deals and escrow
lifecycle - \[ \] Wallet ledger and balance changes - \[ \] Deposits -
\[ \] Withdrawals and refunds - \[ \] Payment gateway integrations and
webhooks - \[ \] Notifications - \[ \] Reviews and disputes - \[ \]
Coupons and categories - \[ \] Support tickets and messages - \[ \] AI
knowledge base, chat history and settings - \[ \] Media / uploads - \[
\] Website content and settings - \[ \] Analytics and reporting

### Known issues to investigate and resolve

These concerns were identified during the initial static archive review.
Confirm each against the source and record the fix and test.

-   [ ] Public gateway-settings endpoint may expose secret-bearing
    configuration. Ensure public responses contain only safe, explicitly
    allowlisted fields.
-   [ ] User-management endpoints may be protected only by
    authentication instead of admin authorization. Enforce
    role/permission checks on every administrative route and object.
-   [ ] Escrow release / admin approval paths may permit duplicate
    seller credits. Make release idempotent and atomic, with database
    transactions, row locks and unique ledger references.
-   [ ] Deposit approval may be vulnerable to concurrent duplicate
    credits. Make approval idempotent and atomic.
-   [ ] Deal deliverable uploads need strict validation, random
    filenames, size limits, safe storage and authorization. Avoid public
    execution/access to private files.
-   [ ] Password reset OTP handling may disclose OTP in a response if
    email delivery fails. Never return secrets or OTPs in API responses
    or logs.
-   [ ] Deposit proof and other private uploads may be publicly
    accessible. Use private storage and authorized download routes.
-   [ ] Frontend API paths may not match backend routes. Build and
    verify an API contract map.
-   [ ] Some admin screens may use `localStorage` rather than persistent
    backend APIs. Identify and connect these to database-backed
    endpoints where required.
-   [ ] Payment gateway settings may exist without complete checkout,
    signature validation, callback and webhook handling. Verify provider
    integrations end-to-end.
-   [ ] AI knowledge-base seeder may not be included in the default
    seeding flow. Verify intended seed behavior.
-   [ ] Old database backup may not contain all current tables. Do not
    treat it as the authoritative schema.
-   [ ] Hardcoded test credentials, debug scripts, logs and private data
    must not enter the V12 repository.

------------------------------------------------------------------------

## 4. Phase 0 --- Protect and baseline V12

**Goal:** Establish a safe, reproducible destination before migration.

-   [ ] Confirm V12 path and Laravel version.
-   [ ] Confirm PHP, Composer, Node and npm versions.
-   [ ] Confirm Git is initialized.
-   [ ] Confirm Git remote points to `torik01740/Migratepay-V12`.
-   [ ] Inspect `.gitignore` and ensure `.env`, `vendor`,
    `node_modules`, `/public/build`, logs, private uploads and backups
    are excluded as appropriate.
-   [ ] Verify no secrets or private data are staged.
-   [ ] Commit the clean V12 baseline.
-   [ ] Push baseline to GitHub.
-   [ ] Create a secure, separate backup of V12.
-   [ ] Record current `composer.json`, `composer.lock`, `package.json`,
    `package-lock.json` and relevant configuration.
-   [ ] Run baseline `composer install` only if needed and safe.
-   [ ] Run baseline `npm install` only if needed and safe.
-   [ ] Run `php artisan about`, `php artisan route:list`,
    `php artisan migrate:status` (if configured) and `npm run build`.
-   [ ] Record baseline errors and warnings; do not conceal existing
    issues.

**Exit criteria:** V12 baseline is committed, pushed, backed up and its
current health is documented.

------------------------------------------------------------------------

## 5. Phase 1 --- Extract and inventory V10 safely

**Goal:** Understand the old project before importing any code.

-   [ ] Extract V10 into a separate source directory. Never extract over
    V12.
-   [ ] Preserve the original ZIP unchanged.
-   [ ] Inspect archive contents and identify nested project roots.
-   [ ] Record the complete V10 folder tree.
-   [ ] Identify all tracked files and Git history location; do not copy
    V10 `.git`.
-   [ ] Inspect V10 `composer.json` / `composer.lock`.
-   [ ] Inspect V10 `package.json` / `package-lock.json`.
-   [ ] Inspect `.gitignore`, `.env.example` and configuration files
    without copying secrets.
-   [ ] Inventory routes, middleware, controllers, requests, policies,
    providers, services, models, events, listeners, jobs, notifications,
    mail, commands and observers.
-   [ ] Inventory migrations, seeders, factories and database backup
    schema.
-   [ ] Inventory Blade templates, React entry points, components,
    contexts, hooks, styles and assets.
-   [ ] Inventory tests, scripts, scheduled tasks and external
    integrations.
-   [ ] Identify files generated by Laravel or dependencies versus
    custom application files.
-   [ ] Produce a source-to-destination mapping before migration.

**Deliverables:** V10 folder tree, dependency inventory, feature
inventory, route/API inventory and source-to-V12 mapping.

------------------------------------------------------------------------

## 6. Phase 2 --- Architecture and compatibility decisions

**Goal:** Decide how each V10 feature will be implemented in V12.

-   [ ] Confirm destination architecture: Laravel 12 backend + Blade
    where appropriate + React 19 + Chakra UI 3 for interactive
    interfaces.
-   [ ] Decide whether V12 will use ordinary Laravel API endpoints and
    Blade-mounted React apps or a justified Inertia architecture. Do not
    assume Inertia is required.
-   [ ] Resolve the current Inertia Laravel 2.x / React 3.x
    major-version mismatch if those packages remain in use. First
    inspect actual usage; do not upgrade blindly.
-   [ ] Inspect V12 `vite.config.js`, `resources/js/app.js` (or actual
    entry point), `resources/views`, `bootstrap/app.php` and CSS before
    frontend integration.
-   [ ] Verify React Vite plugin configuration and JSX handling.
-   [ ] Verify Chakra UI 3 provider, theme system, components, hooks and
    toast/notification patterns.
-   [ ] Create a Chakra UI 2 → 3 compatibility list for V10 components.
-   [ ] Review Tailwind 4 and Chakra style coexistence; define
    consistent ownership of global styles.
-   [ ] Map authentication/session/API behavior and CSRF handling.
-   [ ] Map roles, policies and admin authorization.
-   [ ] Map upload storage and public/private file access.
-   [ ] Map payment providers, webhook security and secrets handling.
-   [ ] Identify PHP/Laravel API changes and removed/deprecated APIs.
-   [ ] Decide which old features need bug fixes versus
    behavior-preserving migration.
-   [ ] Record architecture decisions and reasons.

**Exit criteria:** A documented, approved migration design exists before
bulk file movement.

------------------------------------------------------------------------

## 7. Phase 3 --- Database reconstruction

**Goal:** Bring the existing business data model into a clean Laravel 12
migration history.

-   [ ] Inventory all V10 migrations in chronological order.
-   [ ] Compare each migration with V12's existing migrations.
-   [ ] Identify duplicate tables, duplicate columns, incompatible types
    and missing indexes.
-   [ ] Inspect foreign keys, delete behavior, unique constraints and
    nullable fields.
-   [ ] Inspect decimal precision and currency representation for all
    financial fields.
-   [ ] Design a reliable wallet ledger with immutable transaction
    references and auditability.
-   [ ] Ensure deal, deposit and withdrawal state transitions are
    explicit.
-   [ ] Decide which migrations can be ported unchanged and which
    require Laravel 12 or schema changes.
-   [ ] Preserve migration chronology and dependencies; do not copy
    duplicate migration timestamps.
-   [ ] Review all seeders for hardcoded credentials, destructive
    operations and personal data.
-   [ ] Keep demo/test data separate from production data.
-   [ ] Do not import the old SQL backup until its completeness and
    provenance are verified.
-   [ ] Create a fresh disposable test database.
-   [ ] Run migrations against that test database.
-   [ ] Run seeders selectively and verify expected tables/data.
-   [ ] Test rollback only in a disposable database.
-   [ ] Document the final schema and migration order.

**Exit criteria:** Fresh V12 test database builds successfully and
schema integrity checks pass.

------------------------------------------------------------------------

## 8. Phase 4 --- Backend domain migration

**Goal:** Migrate and modernize backend logic in dependency order.

For every domain below: inspect V10 code → map dependencies → adapt to
Laravel 12 → integrate → test → update this checklist.

### 4A. Core application

-   [ ] Models and relationships
-   [ ] Enums / constants / state definitions
-   [ ] Services and business rules
-   [ ] Form requests and validation
-   [ ] Policies, gates and role permissions
-   [ ] Custom middleware and registration
-   [ ] Service providers and bindings
-   [ ] Events, listeners, jobs and queues
-   [ ] Notifications and mail
-   [ ] Console commands and scheduled tasks
-   [ ] API resources / response formats
-   [ ] Exception handling and logging

### 4B. Authentication and account

-   [ ] Registration
-   [ ] Login / logout
-   [ ] Password reset and OTP
-   [ ] Email verification
-   [ ] Session and CSRF behavior
-   [ ] Profile and password changes
-   [ ] KYC submission and admin review
-   [ ] Account status / suspension
-   [ ] Rate limiting and abuse prevention

### 4C. Escrow and financial workflows

-   [ ] Deal creation and participant authorization
-   [ ] Deal state machine and permitted transitions
-   [ ] Funding / wallet debit
-   [ ] Seller delivery and private attachments
-   [ ] Buyer acceptance
-   [ ] Admin dispute handling
-   [ ] Release / refund / cancellation
-   [ ] Fees, commissions and rounding
-   [ ] Wallet ledger and balance reconciliation
-   [ ] Deposit submission and approval/rejection
-   [ ] Withdrawal request and approval/rejection
-   [ ] Payout methods
-   [ ] Duplicate-request protection and idempotency
-   [ ] Database transactions and row locking
-   [ ] Audit trail for every financial action

### 4D. Supporting domains

-   [ ] Notifications
-   [ ] Reviews
-   [ ] Categories
-   [ ] Coupons
-   [ ] Support tickets and messages
-   [ ] AI support and knowledge base
-   [ ] Banner and media management
-   [ ] Blog and content management
-   [ ] Menus and site settings
-   [ ] Analytics and reports
-   [ ] Payment gateways and webhooks

### Backend verification

-   [ ] Every route has correct authentication and authorization.
-   [ ] Every user-owned resource checks ownership.
-   [ ] Every admin action checks admin permissions server-side.
-   [ ] Validation is server-side and explicit.
-   [ ] API responses never expose secrets or unnecessary personal data.
-   [ ] Financial actions are atomic, idempotent and auditable.
-   [ ] Errors do not leak stack traces, secrets or OTPs.
-   [ ] Tests cover success, failure, unauthorized and concurrent cases.

**Exit criteria:** Backend domains work through tested Laravel 12
routes/services against the new schema.

------------------------------------------------------------------------

## 9. Phase 5 --- Public website reconstruction

**Goal:** Preserve the old site's design and content while adapting
templates to V12.

-   [ ] Inspect all V10 Blade templates and shared layouts.
-   [ ] Map each public URL to its V12 route and view.
-   [ ] Port header, navigation, footer and shared components.
-   [ ] Port landing page and banner behavior.
-   [ ] Port About, Contact, FAQ and Team pages.
-   [ ] Port Privacy, Terms and Cookie Policy pages.
-   [ ] Port Blog and Gallery pages.
-   [ ] Port Documents and Charge Calculator.
-   [ ] Port public profile and profile detail pages.
-   [ ] Port AI/support widget if retained.
-   [ ] Copy only required images, fonts, icons and static assets.
-   [ ] Verify responsive layouts and mobile navigation.
-   [ ] Verify forms, validation, success/error states and spam
    controls.
-   [ ] Verify SEO metadata, canonical URLs and page titles.
-   [ ] Verify accessibility basics and keyboard navigation.
-   [ ] Compare screenshots against V10 reference pages.
-   [ ] Confirm public pages do not expose private data.

**Exit criteria:** Public pages render correctly in V12 and all linked
interactions work.

------------------------------------------------------------------------

## 10. Phase 6 --- React + Chakra UI 3 foundation

**Goal:** Establish a stable shared frontend foundation before migrating
dashboard modules.

-   [ ] Inspect V12 entry point(s) and current Vite configuration.
-   [ ] Configure the official React Vite plugin if required.
-   [ ] Verify JSX/TSX file handling and page/component resolution.
-   [ ] Configure React mounting points for Blade pages or the selected
    architecture.
-   [ ] Configure Chakra UI 3 provider and default/custom system.
-   [ ] Establish shared theme tokens: colors, typography, spacing,
    radii, shadows and breakpoints.
-   [ ] Establish reusable layout, sidebar, header, navigation, buttons,
    forms, dialogs, tables and feedback components.
-   [ ] Establish API client, CSRF handling, auth state and error
    handling.
-   [ ] Establish reusable loading, empty, error and success states.
-   [ ] Establish accessible responsive behavior.
-   [ ] Migrate Chakra UI 2 components and hooks to Chakra UI 3 APIs.
-   [ ] Remove obsolete imports and unused packages only after checking
    usage.
-   [ ] Verify production build and browser console.

**Exit criteria:** A small real React + Chakra UI 3 page mounts inside
V12 and passes production build.

------------------------------------------------------------------------

## 11. Phase 7 --- User dashboard reconstruction

Migrate in small modules. Do not port all dashboard files in one
operation.

-   [ ] User dashboard shell, sidebar and header
-   [ ] Authentication-aware navigation
-   [ ] Command Center / overview
-   [ ] Profile and account settings
-   [ ] KYC / verification
-   [ ] Create escrow deal
-   [ ] Deal list and deal details
-   [ ] Deal workspace, messaging and deliverables
-   [ ] Wallet overview and ledger
-   [ ] Add balance / deposit request
-   [ ] Withdrawal request and history
-   [ ] Notifications
-   [ ] Activity history
-   [ ] Reviews
-   [ ] Support tickets
-   [ ] AI support
-   [ ] Responsive/mobile layout
-   [ ] Permission and ownership checks for each screen/API
-   [ ] End-to-end tests for important user journeys

**Exit criteria:** A real user can complete the intended workflows
through the UI and backend, with authorization enforced server-side.

------------------------------------------------------------------------

## 12. Phase 8 --- Admin dashboard reconstruction

-   [ ] Admin dashboard shell, sidebar and header
-   [ ] Admin authentication and role enforcement
-   [ ] Analytics dashboard
-   [ ] User management and verification
-   [ ] Deal management
-   [ ] Disputes and reports
-   [ ] Deposit review
-   [ ] Withdrawal review
-   [ ] Categories
-   [ ] Coupons
-   [ ] Banner management
-   [ ] Media library
-   [ ] Support tickets
-   [ ] AI live chat and settings
-   [ ] AI knowledge base
-   [ ] Payment gateway settings with secret-safe handling
-   [ ] Charge management
-   [ ] Announcements
-   [ ] Blog/content management
-   [ ] Page management
-   [ ] Menu management
-   [ ] Site settings
-   [ ] Project submissions
-   [ ] Backup interface only if a safe, real backend implementation
    exists
-   [ ] Replace required `localStorage`-only persistence with authorized
    backend APIs
-   [ ] Test every admin action and unauthorized access attempt

**Exit criteria:** Admin modules persist correctly and every privileged
action is protected and auditable.

------------------------------------------------------------------------

## 13. Phase 9 --- Security, financial integrity and privacy

This phase is mandatory before production or real-money use.

-   [ ] Remove/rotate all exposed or hardcoded credentials.
-   [ ] Verify `.env` and secrets are never committed or publicly
    served.
-   [ ] Ensure public gateway configuration exposes only safe fields.
-   [ ] Enforce admin authorization on every admin route and action.
-   [ ] Enforce ownership checks for user resources.
-   [ ] Protect against IDOR and mass assignment.
-   [ ] Validate all input server-side.
-   [ ] Validate uploads by type, size and content; use safe filenames.
-   [ ] Store private documents and payment proof outside public web
    root.
-   [ ] Protect downloads with authorization.
-   [ ] Implement CSRF, rate limiting and appropriate session
    protections.
-   [ ] Secure payment callbacks/webhooks with provider signature
    verification and replay protection.
-   [ ] Make deposit, withdrawal and escrow transitions atomic and
    idempotent.
-   [ ] Use database transactions and appropriate row locks for balance
    changes.
-   [ ] Never trust client-supplied wallet balances, fees or transaction
    status.
-   [ ] Add immutable ledger references and audit records.
-   [ ] Reconcile wallet balances against ledger entries.
-   [ ] Add tests for duplicate, concurrent and replayed financial
    requests.
-   [ ] Review logs and error responses for secret/PII leakage.
-   [ ] Review backup access, retention and restore procedure.
-   [ ] Run dependency/security audit and resolve meaningful findings.
-   [ ] Complete a manual security review before production.

**Exit criteria:** Critical/high security findings are fixed and
verified; financial invariants pass automated tests.

------------------------------------------------------------------------

## 14. Phase 10 --- End-to-end testing and release readiness

-   [ ] Fresh clone/install instructions work.
-   [ ] Environment setup documented without sharing secrets.
-   [ ] Fresh database migrations succeed.
-   [ ] Seeders run safely and predictably.
-   [ ] Production frontend build succeeds.
-   [ ] Laravel automated tests pass.
-   [ ] Frontend lint/type checks run if configured.
-   [ ] Public website verified on desktop and mobile.
-   [ ] Registration/login/password reset verified.
-   [ ] KYC workflow verified.
-   [ ] Deal creation/funding/delivery/acceptance verified.
-   [ ] Dispute/release/refund verified.
-   [ ] Deposit approval and duplicate protection verified.
-   [ ] Withdrawal approval and duplicate protection verified.
-   [ ] Wallet ledger and balance reconciliation verified.
-   [ ] Admin permissions verified.
-   [ ] Payment gateway sandbox flows and webhooks verified, if
    implemented.
-   [ ] Upload privacy and download authorization verified.
-   [ ] Notifications/email/queue behavior verified.
-   [ ] Backup and restore tested.
-   [ ] Error logs reviewed.
-   [ ] Final Git commit and push completed.
-   [ ] Release notes and known limitations documented.

**Exit criteria:** A reproducible, tested V12 application is ready for a
controlled staging deployment. Production launch requires separate
operational approval.

------------------------------------------------------------------------

## 15. Git and change-management rules

-   [ ] Keep the V12 repository as the only destination Git repository.
-   [ ] Use a dedicated branch for each substantial migration phase or
    feature.
-   [ ] Commit after each verified logical milestone.
-   [ ] Use descriptive commit messages.
-   [ ] Review `git diff` before every commit.
-   [ ] Review `git status` and staged files before every commit.
-   [ ] Never commit `.env`, credentials, private data, SQL dumps or
    user uploads.
-   [ ] Push verified commits to `origin`.
-   [ ] Do not rewrite shared history or force-push without explicit
    approval.
-   [ ] Record commit hashes for completed phases.

Suggested commit format:

`migration(phase-N): short description`

------------------------------------------------------------------------

## 16. Current progress tracker

Update this table only when work is actually verified.

  -----------------------------------------------------------------------
  Phase                   Status                  Notes
  ----------------------- ----------------------- -----------------------
  Planning and initial    In progress / partially Archive inspected;
  V10 assessment          complete                initial feature and
                                                  risk inventory reported

  Phase 0: V12 baseline   Not verified            V12 stack exists; Git
  and Git                                         connection/push must be
                                                  confirmed

  Phase 1: Full V10       Not started             Initial archive review
  inventory                                       is not a complete
                                                  file-by-file inventory

  Phase 2: Architecture   Not started             Preserve Blade + React
  decisions                                       where appropriate;
                                                  inspect Inertia
                                                  mismatch

  Phase 3: Database       Not started             Migration/schema audit
  reconstruction                                  required

  Phase 4: Backend        Not started             No folder-by-folder
  migration                                       migration verified yet

  Phase 5: Public website Not started             No V10 page migration
                                                  verified yet

  Phase 6: React + Chakra Partially prepared      Packages installed;
  3 foundation                                    integration/build
                                                  verification required

  Phase 7: User dashboard Not started             No V10 dashboard
                                                  migration verified yet

  Phase 8: Admin          Not started             No V10 dashboard
  dashboard                                       migration verified yet

  Phase 9: Security and   Not started             Findings identified;
  financial integrity                             fixes/tests not
                                                  verified

  Phase 10: End-to-end    Not started             Requires completed
  and release                                     migration
  -----------------------------------------------------------------------

### Current V12 environment facts (last reported)

-   PHP 8.4.27
-   Laravel Framework 12.70.2
-   Composer 2.10.3
-   Node.js 24.18.0
-   npm 11.16.0
-   React 19.3.1
-   React DOM 19.3.1
-   `@inertiajs/react` 3.10.0
-   `inertiajs/inertia-laravel` 2.0.28
-   Chakra UI 3.37.0
-   Emotion 11.14.0
-   Vite 7.3.7
-   Laravel Vite plugin 2.1.0
-   Tailwind CSS 4.x
-   esbuild 0.28.2 (rebuild verified)

**Important:** These are the last reported versions, not a guarantee of
current machine state. Re-check before making dependency decisions.

------------------------------------------------------------------------

## 17. Next action --- do this before migrating files

1.  Open Git Bash.
2.  Go to `F:\laragon\www\Migratepay-V12`.
3.  Run `git status`, `git remote -v`, `php artisan about`,
    `npm list --depth=0`.
4.  Confirm V12 is committed and pushed, or establish a baseline commit
    first.
5.  Identify the extracted V10 source directory.
6.  Generate the complete V10 folder/file inventory.
7.  Start with the database schema and backend dependency map. Do not
    copy files yet.

### First migration work unit

**Inspect V10 database migrations, models and relationships against
V12.** Produce a table for every migration/model with: - Source path -
Purpose - Tables/columns/relationships - Dependencies - Existing V12
equivalent (if any) - Laravel 12 compatibility changes - Security/data
concerns - Proposed destination - Verification/test plan - Status

Only after this review should database files be integrated.

------------------------------------------------------------------------

## 18. Status vocabulary

Use these statuses consistently:

-   `Not started` --- no inspection or work yet.
-   `Inspecting` --- source and destination are being examined.
-   `Planned` --- implementation approach documented, not changed.
-   `In progress` --- changes are being made.
-   `Blocked` --- cannot proceed; blocker documented.
-   `Implemented` --- code integrated, but not fully verified.
-   `Verified` --- relevant tests/build/manual checks passed and results
    recorded.
-   `Deferred` --- intentionally postponed with a reason.
-   `Not applicable` --- feature is intentionally not part of the
    product, with reason.

**Never mark a phase `Verified` just because files were copied, code was
generated, or a page looks correct. Verify its backend behavior,
permissions, data persistence and relevant failure cases.**

------------------------------------------------------------------------

**End of working plan.** This document should evolve with the project.
Preserve its history in Git and keep the progress tracker accurate after
every migration session.
