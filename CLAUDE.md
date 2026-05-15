# TT CRM v2

## Project Overview
Enterprise CRM built on Laravel 10 + Filament 3 + Livewire 3. Two panels: Admin (staff) and Customer Portal.

## Tech Stack
- **Backend:** Laravel 10, PHP 8.1+, Filament 3.2, Livewire 3
- **Frontend:** Vite 4, Tailwind CSS 4, Alpine.js
- **Database:** MySQL
- **Testing:** PHPUnit 10

## Key Commands
```bash
php artisan serve          # Run dev server
npm run dev                # Vite dev server
php artisan migrate        # Run migrations
php artisan test           # Run tests
npm run build              # Production build
php artisan queue:work     # Process queued jobs
php artisan schedule:run   # Run scheduled commands
php artisan tinker         # REPL for debugging
```

## Environment Setup
Required env vars (see `.env.example`):
- `DB_*` — MySQL connection
- `MICROSOFT_TENANT_ID`, `MICROSOFT_CLIENT_ID`, `MICROSOFT_CLIENT_SECRET` — MS OAuth/Graph
- `TWILIO_SID`, `TWILIO_AUTH_TOKEN`, `TWILIO_WHATSAPP_FROM` — Twilio SMS/WhatsApp
- `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET` — S3 + SES
- `CIPHER_METHOD`, `CIPHER_PASSWORD` — Encryption
- Timezone: `Asia/Kuala_Lumpur`

## Architecture
- `app/Filament/` — Admin panel (Resources, Pages, Actions, Filters)
- `app/Filament/Customer/` — Customer portal (separate Filament panel for customer-facing resources/pages)
- `app/Filament/Resources/LeadResource/Tabs/` — 40 tab classes for lead view (e.g., `ProjectPlanTabs.php`, `DataMigrationTabs.php`, `ThreadTabs.php`, `ProspectPICTabs.php`)
- `app/Filament/Resources/LeadResource/Pages/ViewLeadRecord.php` — Lead view entry point that owns `$visibleTabs`, `getDefaultVisibleTabs()`, and the `filterTabs` action
- `app/Filament/Pages/ImplementerTicketingDashboard.php` — Implementer thread/ticketing dashboard (admin)
- `app/Filament/Actions/` — Reusable custom actions (`LeadActions`, `AdminRenewalActions`, `ImplementerActions`)
- `app/Livewire/` — Livewire components: ~130 top-level (mostly customer-portal pages and admin one-offs) plus ~209 nested in dashboard subdirectories (`ImplementerDashboard/`, `AdminFinanceDashboard/`, `AdminRenewalDashboard/`, `LeadownerDashboard/`, `ManagerDashboard/`, `SalespersonDashboard/`, etc.). Treat the dashboard subdirs as scoped feature folders, not a flat namespace.
- `app/Services/` — 22 service classes (PDF generation, external-API integrations, business logic — see "Services Layer" section)
- `app/Support/` — Lightweight, framework-free utilities: `DataFileSections`, `TicketAttachmentNamer`, `SparklinePath` (Catmull-Rom → Bezier path used by Customer Dashboard spark chart)
- `app/Mail/` — 29 Mailable classes (handover notifications, ticket alerts, reseller updates, etc.)
- `app/Notifications/` — 3 Notification classes (`ImplementerTicketNotification`, `TicketNotification`, `DataFileAssignedByImplementerNotification`) — all write to the `notifications` table consumed by `CustomerNotificationBell`
- `resources/views/customer/dashboard.blade.php` — Customer portal dashboard with inline sidebar navigation and tab-switched content
- `resources/views/components/icons/` — Custom Blade SVG icon components (currently 5: `timetec-{profile,attendance,leave,claim,payroll}.blade.php`); use `fill="currentColor"` so they inherit text color. Invoke via `<x-dynamic-component :component="$iconName" width="20" height="20" />` or `<x-icons.timetec-profile />`.
- `app/Models/` — Eloquent models
- `app/Helpers/general_helpers.php` — Three global helpers: `generate_company_id()`, `quotation_reference_no()`, `remove_company_suffix()`. Most utility logic lives in `app/Services/`, not here.
- `resources/views/filament/pages/` — Custom Blade views for Filament pages
- `resources/views/filament/resources/lead-resource/tabs/` — Blade views for lead view tabs
- `resources/views/livewire/` — Blade views for Livewire components
- `app/Console/Commands/` — 38 custom artisan commands (see "Console Commands & Scheduling" section)
- `config/` — Includes custom configs: imap.php, reverb.php, invoices.php, notification-scenarios.php
- `routes/web.php` — Most "API-style" endpoints (PDF generation, file downloads, exports, webhooks) live here, not `api.php`. ~1100 lines, many inline closures.
- `routes/api.php` — Sanctum-authenticated REST endpoints (smaller surface than `web.php`)
- `HR icons/` (untracked, repo root) — Legacy Vue 2 SVG icon components from a prior project. NOT used by current code; superseded by `resources/views/components/icons/`. Do not reference.

## Scale Reference
- 32 Filament Resources, 101 custom Pages, 136 Models, 182 migrations
- 40 Lead view tab classes, ~130 top-level Livewire components (~339 including dashboard subdirectories), 38 artisan commands, 22 Services, 29 Mailables
- Counts drift over time — re-run `find` if making decisions based on these

## Conventions
- Filament custom pages use Livewire properties + Alpine.js for interactivity
- Rich text editors use `contenteditable` + Alpine.js + `document.execCommand()` (not external deps)
- Cursor-aware insertion in `contenteditable`: save `Range` via `saveSelection()` on `@mouseup/@keyup/@blur`, restore before `insertText` — clicking outside (e.g., placeholder buttons) loses cursor position
- `wire:ignore` wraps Alpine-managed DOM to prevent Livewire re-render conflicts
- Livewire events (`$this->dispatch()`) communicate from PHP to Alpine; sync data at submission boundaries only
- CSS is inline `<style>` within Blade views, each feature uses a unique prefix: `imp-` (Implementer Ticketing), `dm-` (Data Migration admin), `dmt-` (Data Migration Templates customer), `thr-` (Thread tab admin), `cit-` (Customer Implementer Thread), `cnb-` (Customer Notification Bell), `emt-` (Email Template), `shp-` (Software Handover Process admin), `cshp-` (Software Handover Process customer), `cdb-` (Customer Dashboard home view)
- Fixed/overlay drawers use `body.imp-drawer-open` class to hide Filament topbar (stacking context workaround)
- Admin sidebar is custom (`resources/views/layouts/custom-sidebar.blade.php`), not Filament's default; pages set `$shouldRegisterNavigation = false` and must be manually registered in `AdminPanelProvider->pages([])` (auto-discovery is disabled)
- Customer portal sidebar is inline in `resources/views/customer/dashboard.blade.php` with collapsible groups (Implementation, Training, Support, Knowledge Base, Commercial, Settings) and JS `switchTab()` for content switching. Non-working groups render a single `<div style="padding: 8px 16px 8px 36px; color: #94a3b8; font-size: 12.25px; font-style: italic;">Coming Soon</div>` placeholder under their header — match this convention rather than inventing a new disabled style. The Implementation group contains 5 tabs in order: `calendar` (Project Session), `softwareHandover` (Project Details), `project` (Project Plan, conditional), `impThread` (Project Thread, with badge count), `dataMigration` (Project File). The JS array `implementationTabs` (around line 969) drives auto-expand of the parent group when a sub-tab is active.
- Custom SVG icons go in `resources/views/components/icons/` as one Blade file per icon. Each file contains a single `<svg>` with `fill="currentColor"` (so the icon inherits text color) and accepts `$attributes->merge(...)` for sizing. Reference them via `<x-dynamic-component :component="$section['icon_component']" width="20" height="20" />` when the icon is data-driven (see `DataFileSections::map()` which emits both `icon_component` and a legacy FontAwesome `icon` for fallback). Don't add raw inline SVGs to feature views when an icon component exists.
- Lead view tabs follow pattern: `class TabName { public static function getSchema(): array }` returning Filament form components
- Tab visibility is session-based (`lead_visible_tabs`), with role-based defaults in `ViewLeadRecord::getDefaultVisibleTabs()` AND `LeadResource::form()` fallback
- When adding new tabs, update three places: `LeadResource::form()` defaults, `ViewLeadRecord::getDefaultVisibleTabs()`, and the `filterTabs` action switch in `ViewLeadRecord`
- Lead view tab Blade views rendered via ViewField are inside Filament's `<form>` — never nest `<form>` tags or Livewire `WithFileUploads` components; use Alpine.js + `fetch()` POST to a dedicated route instead (reference: `data-migration.blade.php`, `software-handover-process.blade.php`)
- Filament's CSS aggressively overrides native `<select>` elements — use custom Alpine.js dropdowns (div-based) instead; follow the `emt-select-*` or `imp-searchable-*` class pattern with `x-data` containing `open`, `select()`, `clear()` methods and `@click.away` to close
- Searchable filter dropdowns use Alpine.js `x-data` with `@entangle('property').live`, `Js::from()` for items, client-side filtering via computed getter
- Livewire 3 `@entangle('property')` defers updates — use `@entangle('property').live` for immediate server-side sync (required for filters, toggles)
- "HR Details" tab in Lead view = `ProspectPICTabs.php`; primary HR contact is `company_details` table (name, email, contact_no, position); secondary HR contacts stored as JSON in `company_details.additional_prospect_pic` (array of {name, position, contact_no, email, status})
- Fixed-position drawers/modals (like merge drawer) should be placed at root level of the Blade template (outside `@if/@else` conditionals) to avoid rendering issues with Livewire morphing
- `foreignId()->constrained()` may fail if DB column types don't match — use `unsignedBigInteger()` without FK constraints as fallback
- MySQL index names have 64-char limit — use short custom names for long composite indexes

## Key Integrations
- Microsoft OAuth + Graph SDK
- Laravel IMAP (email management)
- Twilio (SMS/WhatsApp)
- AWS S3 + SES
- Spatie Activity Log
- Maatwebsite Excel
- Zoho CRM SDK (lead sync)
- MyInvois PHP SDK (Malaysian e-Invoicing / LHDN)
- DomPDF (PDF generation)
- Tesseract OCR (document scanning)
- FullCalendar (calendar views)
- Laravel Reverb (WebSockets)
- Laravel Sanctum (API auth)

## Gotchas
- Alpine.js is bundled via Filament, not installed as npm dependency
- Many commands are scheduled (`app/Console/Kernel.php`) — ensure `schedule:run` is in cron
- `.env.example` contains some hardcoded values — always review before copying
- Customer portal uses `customer` guard with `Customer` model (has `lead_id` for linking to leads)
- Customer portal routes are inside `Route::prefix('customer')->name('customer.')` group — don't include `customer.` in `->name()` calls (the group prefix adds it automatically)
- `Storage::disk('public')->url()` may not include the port in dev — use `response()->download()` via named routes instead
- User roles use `role_id` field (not `role`): 1=Lead Owner, 2=Salesperson, 3=Manager, 4=Implementer, 5=Implementer, 9=Technician (roles 4/5/9 are DB-assigned but not referenced in code role checks)
- Livewire components must have a single root element — `<script>` and `<style>` tags must be inside the root `<div>`, not siblings
- Buttons with SVG icon + text + `wire:loading.remove`: SVG and text `<span>` must be separate sibling children of the button (not wrapped in a single `<span>`), otherwise `inline-flex`/`gap` can't separate them
- `public/storage` symlink must exist — run `php artisan storage:link` after fresh clone; without it, file uploads return 404
- `php artisan migrate` may fail on existing DB — use `--path=database/migrations/<filename>.php` to run specific new migrations
- `QUEUE_CONNECTION=database` in dev — `ShouldQueue` notifications/mailables won't process without `php artisan queue:work`; use `notifyNow()` or remove `ShouldQueue` for synchronous local testing
- Laravel 10 Symfony Mailer ignores `stream.ssl` config in `mail.php` — to disable SSL verification (local dev), set `setStreamOptions()` directly on the `SocketStream` via `Mail::mailer()->getSymfonyTransport()->getStream()` before sending
- Config changes require restarting `php artisan serve` — `config:clear` alone doesn't reload configs in the running process
- Legacy `crm_customer` VIEW (consumed by the customer-portal `Customer` model) selects un-aggregated columns from `login_profile`; `config/database.php` mysql connection sets an explicit `modes` array that omits `ONLY_FULL_GROUP_BY` so the VIEW resolves. Don't re-add `ONLY_FULL_GROUP_BY` to that connection without first refactoring the VIEW
- TCPDF `Cell()` default `valign='M'` does NOT visually centre text inside a tall cell — the baseline sits below midpoint. To vertically centre overlays inside a rectangle, shrink the cell to font height (`fontHeight = size × 0.3528 mm`) and anchor at `box.y + (box.h - fontHeight) / 2` (see `OnboardingPdfGenerator::drawOverlays()`)
- Livewire 3 does NOT persist `protected` properties across requests — they reset to their declared default after `mount()`. If a non-public property needs to survive `wire:click` / `wire:submit` round-trips (e.g., a cached relation lookup), re-resolve it inside the `hydrate()` hook. Reference: `CustomerDataMigrationTemplates::$handover` is set in both `mount()` and `hydrate()` so `moduleEnabled()` works on every request, not just the initial render
- FPDI's `useTemplate()` renders only the visual content of an imported master page — link annotations baked into the source PDF are dropped. Re-add them via `$pdf->Link($x, $y, $w, $h, $url)` after `useTemplate()` (see `OnboardingPdfGenerator::buildLinkMap` + `drawLinks`)

## Data Migration System
- **V1 (nested subsections):** `ImplementerDataFile.php` — admin page configured to read templates from `storage/app/templates/data-migration-v1/` (path is wired in code; populate the directory before relying on it)
- **V2 (flat sections):** `DataMigrationFile.php` — admin page configured to read templates from `storage/app/templates/data-migration/` (same caveat — code-configured destination, populate before relying)
- **Section definitions:** `app/Support/DataFileSections.php::map()` — hardcoded array of 5 section keys (`profile`, `attendance`, `leave`, `claim`, `payroll`) with labels, FontAwesome `icon` (legacy), `icon_component` (Blade SVG component name from `resources/views/components/icons/`), color, and item lists; consumed by both customer and admin views
- **Customer portal:** `CustomerDataMigrationTemplates.php` — downloads templates, uploads filled files with versioning ("Project File" page; tab key `dataMigration`)
- **Implementer review:** `DataMigrationTabs.php` — lead view tab showing customer uploads with slide-over for status/remarks
- **Model:** `CustomerDataMigrationFile` — tracks versions per lead+section+item with customer remark, implementer remark, status
- **API routes:** `/admin/api/data-migration-file/{file}/update` (POST), `/admin/data-migration-file/{file}/download` (GET)
- **Subscription gating (customer portal):** `CustomerDataMigrationTemplates::SECTION_FLAG` maps each section key to a `SoftwareHandover` boolean column (`attendance→ta`, `leave→tl`, `claim→tc`, `payroll→tp`); `profile` is foundational and always enabled. `moduleEnabled($sectionKey)` powers both view-side gating (cards get `dmt-card--disabled` + "Not subscribed" pill + "Not included in your subscription" body note) AND server-side enforcement in `downloadTemplate()`, `startUpload()`, `submitUpload()`. No-handover customers see only Profile. Handover resolved once per request via the same `Customer.sw_id` → `Customer.lead_id` fallback used by `OnboardingPdfGenerator::findHandover()`; cached on a protected `$handover` property and re-resolved in `hydrate()` (Livewire 3 doesn't persist protected props across requests).
- **Per-item filling guides:** `CustomerDataMigrationTemplates::GUIDE_MAP` (4 entries seeded: `profile.import-user`, plus 3 payroll items) maps `{section}.{item}` keys to PDF filenames stored in `base_path('Project File Guide/')` (NOT `storage/app/`). Customer route `GET /customer/project-file-guide/{key}` (`routes/web.php` ~lines 543–575) streams the PDF inline, gated by the same module flags. To add a new guide: drop the PDF into `Project File Guide/`, add the key→filename pair to `GUIDE_MAP`. UI: `.dmt-guide-btn` (FontAwesome `fa-book-open`) renders next to `.dmt-download-btn` only when the item key has a guide.

## Implementer Thread System
- **Admin dashboard:** `ImplementerTicketingDashboard.php` — WhatsApp-style thread view with search, ticket split, SLA tracking
- **Admin dashboard navigation:** Supports `?ticket={id}` to open a ticket on load, and `?from={url}` for cross-page back navigation (stored as `$returnUrl`)
- **Admin client profile:** `ImplementerClientProfile.php` — Shows customer details + all their tickets; rows link to dashboard with `?from=` for back navigation; CSS prefix `imp-client-`
- **Admin lead tab:** `ThreadTabs.php` + `thread.blade.php` — Lists all tickets for a lead, clickable to open in dashboard via `?ticket={id}` query param
- **Customer portal:** `CustomerImplementerThread.php` Livewire component — Card-based thread list inside "Software Onboarding" group; detail view uses 2-column viewport-fit layout (left: ticket details, right: WhatsApp-style thread with search)
- **Customer-facing ticket status:** `ImplementerTicket::customerFacingStatus()` is the canonical mapper from DB statuses to the 4 statuses the customer sees: `closed` → `'closed'`; `pending_support` / `pending_rnd` → `'in_progress'`; `pending_client` → `'open'`; `open` + last non-internal reply by User → `'open'`; `open` + last reply by Customer (or no reply) → `'awaiting_reply'`. Anything user-facing (stat cards, sidebar badge, filters) MUST derive from this — don't filter on raw DB status directly
- **Customer sidebar badge (Project Thread):** `$impThreadBadgeCount` in `resources/views/customer/dashboard.blade.php` (~line 685) feeds BOTH the Implementation group-header pill AND the Project Thread sub-item pill, and must equal the "Open Tickets" stat card on the thread page. Use `ImplementerTicket::customerOpenCountForLead($leadId)` — single source of truth that excludes merged tickets and applies `customerFacingStatus() === 'open'`. Do NOT re-derive the count inline from raw statuses (history: an inline `whereIn(['open','pending_rnd','pending_client'])` query drifted from the card and was replaced)
- **Customer ticketing:** `ImplementerTicketResource` (Filament Customer panel) — Full ticket CRUD for customers, linked from "Support Thread" nav
- **Models:** `ImplementerTicket` (has `lead_id`, `customer_id`, SLA methods, auto-generated ticket number `IMP-{YY}{IDPADDED}`), `ImplementerTicketReply` (polymorphic `sender()` — User or Customer)
- **SLA Configuration:** `SlaConfiguration` model (single-row, cached) — configurable via gear button in SLA Policy modal; `sla:check-first-reply` (every 5min) and `sla:process-followups` (daily) scheduled commands; uses `PublicHoliday` + `CustomPublicHoliday` for working-day calculations
- **Merge Ticket:** `merged_into_ticket_id`, `merged_at`, `merged_by` fields on `ImplementerTicket`; `mergedInto()` / `mergedFrom()` relationships; drawer to select target (same customer only); source ticket closes as "Merged to IMP-XXXX"; clickable ticket IDs in messages via regex `/(IMP-\d+)/`
- **Customer notification bell:** `CustomerNotificationBell` Livewire component in portal header; reads existing `notifications` table (polymorphic); 30s polling; CSS prefix `cnb-`
- **Dashboard filters:** Searchable "All Implementers" and "All Companies" Alpine.js dropdowns with `@entangle().live`
- **Email templates:** Database-driven via `EmailTemplate` model (replaces hardcoded `$emailTemplates` array); `getEmailTemplatesProperty()` computed property; `applyEmailTemplate($templateId)` and `applyReplyTemplate($templateId)` load by ID
- **HR email notifications:** `ImplementerTicketHrNotification` Mailable — sends to all HR contacts (primary `company_details.email` + secondary `additional_prospect_pic` JSON with status=Available) on ticket create, reply, status change, merge; dispatched from `sendHrEmailNotification()` in dashboard; email has truncated description preview + "View in Customer Portal" button linking to `/customer/dashboard?tab=impThread&ticket={id}`
- **Customer in-app notifications from admin:** `createTicket()`, `submitReply()`, and `submitMergeTicket()` call `$customer->notifyNow(new ImplementerTicketNotification(...))` to populate the notification bell; actions: `replied_by_implementer`, `status_changed`, `closed`, `merged`
- **Pending-action panel (admin):** `app/Livewire/ImplementerDashboard/ImplementerThreadPendingAction.php` + `resources/views/livewire/implementer_dashboard/implementer-thread-pending-action.blade.php` — surfaces tickets awaiting an implementer reply; rendered inside `resources/views/filament/pages/implementer.blade.php`
- **Attachment filename helper:** `app/Support/TicketAttachmentNamer.php` — centralized generator for ticket attachment filenames (covered by `tests/Unit/TicketAttachmentNamerTest.php`). Use this for any new code that writes ticket attachments instead of building names ad hoc
- **CSS prefixes:** `imp-` (admin dashboard), `thr-` (admin lead tab), `cit-` (customer portal)

## Email Template System
- **Model:** `EmailTemplate` — fields: `name`, `subject`, `content`, `type` (implementer_thread/support_thread/general/...), `category`, `thread_label` (short label shown next to template name in thread UIs, added 2026-05), `created_by`; scopes: `implementerThread()`, `supportThread()`; categories enumerated by `availableCategories()` (First Response / Follow-up / Escalation / General)
- **Pinned template:** `EmailTemplate::KICK_OFF_TEMPLATE_ID = 17` — the kick-off email template referenced from kick-off booking flows. Don't hardcode `17` elsewhere; reference the constant.
- **Settings pages:** `ImplementerThreadEmailTemplate.php` (full CRUD with modal overlay) + `SupportThreadEmailTemplate.php` (placeholder); under Settings > Email Template in sidebar
- **Dashboard integration:** `ImplementerTicketingDashboard` uses `getEmailTemplatesProperty()` computed property to load templates from DB; template dropdowns in create ticket drawer and reply section use template `id` as value
- **Placeholder replacement:** `EmailTemplate::replacePlaceholders($content, $data)` — dual strategy: simple `str_replace` first, then regex fallback for HTML-artifact-tolerant matching; values are XSS-escaped via `e()`
- **Replacement timing:** Templates are replaced at preview time (template selection) AND at submit time (after entity creation, so `[Ticket ID]` gets the real value)
- **Placeholders:** `[Client Name]`, `[Ticket ID]`, `[Implementer Name]`, `[Company Name]`, `[Category]`, `[Module]` — defined in `EmailTemplate::availablePlaceholders()`
- **Duplicate template:** `duplicateTemplate($id)` opens create modal with "(Copy)" suffix, same subject/body/category
- **Access:** Roles 1 (Lead Owner) and 3 (Manager) can manage templates via `canAccess()` check
- **CSS prefix:** `emt-`

## Customer Dashboard (Home View)
- **Livewire component:** `app/Livewire/CustomerDashboard.php` — renders the customer-portal home (default `?tab=home`)
- **View:** `resources/views/livewire/customer-dashboard.blade.php` — single-viewport layout (`height: calc(100vh - 112px); overflow-y: auto; overflow-x: hidden`); the `112px` accounts for `.main-wrapper` (56+24=80) + `.tab-content` (16+16=32) padding, do not change without re-measuring
- **Sections:** Greeting strip, Quick Actions card (soft-card style, two-line row descriptions), Implementation Snapshot card (4 tinted tile bars + status pill + spark chart), 6-stage ALL-CAPS Implementation Journey
- **Theme:** Navy `#0050B5` via CSS vars `--cdb-coral`, `--cdb-coral-bg`, `--cdb-coral-soft` — variable names retained from the prior coral palette; if rebranding again, also update any direct hex (`#f43f5e`) and `rgba(251, 113, 133, …)` references used by pulse animations
- **Spark chart:** `getSparkPathsProperty()` builds a smooth path via Catmull-Rom → cubic Bezier (tension factor 6: `B1 = P_i + (P_{i+1} - P_{i-1}) / 6`, `B2 = P_{i+1} - (P_{i+2} - P_i) / 6`). The SVG uses `preserveAspectRatio="none"` which stretches `<circle>` into ellipses — dots are rendered as HTML `<span>` overlays positioned by percentage to stay round
- **Status pill:** `getSnapshotStatusProperty()` — stage weights `[5, 20, 35, 60, 80, 100]`; on-plan within ±5%, behind ≤ −5%, ahead ≥ +5%
- **Demo data:** `DEMO_PROJECT_CODES` constant (default `['SW_260005']`); `applyDemoOverrides()` populates `progressSummary`, `migrationCounts`, `journeyStage`, `dtgl`, `ticketsTotal`, and journey dates for demo customers without writing to the DB
- **Journey state helper:** `Customer::hasBookedKickOff()` checks `ImplementerAppointment` for `type='KICK OFF MEETING SESSION'` with status in `['New','Done','Completed']` — drives whether the "First Review" stage shows as active
- **CSS prefix:** `cdb-`

## Software Handover Process
- **Admin tab:** `SoftwareHandoverProcessTabs.php` → `software-handover-process.blade.php` — Alpine.js + fetch POST upload, no nested Livewire
- **Upload route:** POST `/admin/software-handover-process-file/upload` — validates file (pdf,doc,docx,xls,xlsx), stores versioned files at `software-handover-process/{leadId}/v{version}.{ext}`
- **Download routes:** GET `/admin/software-handover-process-file/{file}/download` (admin), GET `/customer/software-handover-process-file/{file}/download` (customer, ownership-checked)
- **Customer portal:** `CustomerSoftwareHandoverProcess.php` Livewire component — read-only file list with direct `<a href>` download links (no `wire:click` redirect)
- **Model:** `SoftwareHandoverProcessFile` — fields: lead_id, uploaded_by, version, file_name, file_path, remark; helpers: `nextVersion($leadId)`, `latestForLead($leadId)`
- **Customer sidebar:** Under Implementation group, after "Meeting Schedule" (`softwareHandover` tab key)
- **Customer-portal "Software Onboarding Process" view** (`resources/views/livewire/customer-software-handover-process.blade.php`): compact header (no project-code/license-date subtitle — that data is baked into PDF page 4 instead). Three icon buttons on the right in order: **Download** (primary `cshp-icon-btn--primary`), **Full Screen** (ghost button — calls `requestFullscreen()` on the `.cshp-viewer` iframe; complemented by `.cshp-viewer:fullscreen { background:#000; }`), **Open in new tab** (ghost). All three share `aria-disabled + pointer-events:none + opacity:0.5` when `$templateMissing`. The embedded iframe loads `/onboarding-pdf/view` with `#toolbar=0&navpanes=0&view=FitH` and renders the generator's output (see Onboarding PDF Generator section).

## Onboarding PDF Generator
- **Service:** `app/Services/OnboardingPdfGenerator.php` — assembles a personalized 24-page onboarding deck per customer from a master template at `storage/app/templates/software-handover/onboarding-process.pdf` using FPDI + TCPDF
- **Controller:** `app/Http/Controllers/CustomerOnboardingPdfController.php` — routes `/onboarding-pdf/view` (inline iframe) and `/onboarding-pdf/download`; auth via `customer` guard
- **Page selection:** `selectPages($handover)` merges `DEFAULT_PAGES = [1, 2, 3, 4, 5, 8, 9, 10, 11, 24]` with module-gated `MODULE_PAGES` keyed by `SoftwareHandover` boolean flags: `tp→[6, 7, 19, 20, 21, 22, 23]` Payroll, `ta→[12, 13, 14]` Attendance, `tl→[15, 16]` Leave, `tc→[17, 18]` Claim. Result is sorted + deduped. `null` handover yields just `DEFAULT_PAGES`
- **Handover resolver:** `findHandover($customer)` — canonical lookup: try `Customer.sw_id` (exact match) first, fall back to latest `SoftwareHandover` by `Customer.lead_id`. Reused as the gating reference in `CustomerDataMigrationTemplates`
- **Field overlays:** `FIELD_MAP[4]` stamps 4 personalized values onto master page 4 in blue Helvetica 18pt — **License Activation Date**, **URL** (v1=`www.timeteccloud.com` or v2=`www.hr2.timeteccloud.com` via `resolveLoginUrl()`), **Temporary Admin Email**, **Temporary Admin Password**. Coordinates: x=125, y=72/100/128/156, w=130, h=16, padX=10. `drawOverlays()` vertically centres text by computing `fontHeight = size × 0.3528 mm` and anchoring the cell at `box.y + (box.h - fontHeight) / 2` — TCPDF's default `valign='M'` does NOT visually centre text inside a tall cell on its own
- **Click annotations:** `buildLinkMap()` adds PDF Link annotations: master page 5 has **five whole-card hit-boxes** (Topics 2-6, generously sized to cover entire cards) all deep-linking to `route('customer.dashboard') . '?tab=dataMigration'` (Project File page, where subscription gating applies); master page 10 has 6 webinar-button boxes pointing to `?tab=webinar`
- **Calibration:** `php artisan onboarding-pdf:calibrate {customerId} --out=storage/app/onboarding-calibration.pdf` (command at `app/Console/Commands/CalibrateOnboardingPdf.php`) renders the deck with red guide rectangles over each `FIELD_MAP` overlay — use after replacing the master template
- **Tests:** `tests/Unit/OnboardingPdfPageSelectorTest.php` covers `selectPages()` page rules and `resolveLoginUrl()` v1/v2 logic — 13 assertions
- **Filename:** `filenameFor()` returns `Software_Onboarding_{projectCode}.pdf` (falls back to `Software_Onboarding_handover.pdf` when no project code)
- **Required fields helper:** `hasCompleteData($customer)` flips true only when license date, implementer, temp email, and temp password are all populated — drives the "Some details will be filled in once your implementer completes setup" banner in the customer view
- **Re-calibration trigger:** any time the master template PDF is replaced, run `onboarding-pdf:calibrate` and visually compare; the May-2026 master required moving the license-date overlay from page 5 to page 4 and reclassifying master page 18 from Leave to Claim (page 5 in the new template carries the Topic-card grid)

## Console Commands & Scheduling
- **Scheduler entry point:** `app/Console/Kernel.php::schedule()` — 27 active scheduled tasks. `php artisan schedule:run` must be in cron (every minute) for any of this to fire.
- **High-frequency syncs (≤ 5 min cadence):**
  - `notifications:sync-ticketing` (every minute) — drains the cross-system ticketing notification queue
  - `sla:check-first-reply`, `hrdf:sync-emails --days=1`, `hrdf:process-claim-payments` (every 5 min)
  - `zoho:fetch-leads` (every 4 min via cron expression — pulls leads from Zoho CRM into local DB)
- **Half-hourly:** `userleave:update`, `handovers:sync`, `renewal:auto-mapping`, `sales-order:update-status`, `teams:fetch-recordings`
- **Daily housekeeping (00:01–00:15):** `leads:update-status`, `repair-appointments:update-status`, `implementer-appointments:update-status`, `repair:check-pending-status`, `handovers:check-delays`, `renewals:reset-completed` (00:07), `reseller:inactivate-expired` (00:10), `sla:process-followups` (00:15)
- **Twice daily:** `training:fetch-recordings` (14:00 + 19:00)
- **Weekly:** `follow-up:auto` (Tue 10:00), `overtime:send-reminders` (Thu+Fri 16:00), `reseller:send-renewal-notification` (Mon 08:00), `reseller:send-pending-payment-reminder` (Mon 09:00)
- **Conventions:** Commands that hit external APIs (Zoho, Microsoft Graph for Teams, Twilio, IRBM, HRDF email) are stateless and idempotent — re-running them is safe. Several historical commands are commented out in `Kernel.php` (e.g., `tickets:check-updates`, `handovers:check-pending-confirmation`); leave them commented unless the upstream feature is being revived.
- **Local dev:** Don't run `schedule:run` in development unless you intentionally want syncs to fire — invoke individual commands manually instead.

## Mail & Notification System
- **Mailables:** `app/Mail/` — 29 classes covering lead/handover lifecycle, ticket alerts, reseller workflows, training. Notable:
  - `EntityNotificationMail` — generic mailable driven by `config/notification-scenarios.php` (subject/greeting/CTA per scenario key), preferred for new transactional emails over bespoke Mailable classes
  - `ImplementerThreadNotificationMail`, `ImplementerTicketHrNotification` — implementer-thread email surface
  - `HandoverNotification`, `HandoverChangeImplementer`, `ProjectClosingNotification` — software-handover lifecycle
  - `ResellerHandoverStatusUpdate{,Fd,Fe}`, `ResellerInquiryStatusUpdate`, `ResellerDatabaseCreationStatusUpdate`, `ResellerPendingPaymentReminder`, `ResellerRenewalExpiryNotification`, `InstallationPaymentNotification` — reseller workflow surface
  - `HrdfTrainingNotification`, `WebinarTrainingNotification`, `TrainingCompletionNotification`, `OvertimeReminder` — training/HRDF surface
  - `CustomerActivationMail`, `CustomerResetPassword` — customer-portal auth
  - `FollowUpNotification`, `NewLeadNotification`, `LeadOwnerChangedNotification`, `ChangeLeadOwnerNotification`, `BDReferralClosure`, `SalespersonNotification`, `DemoNotification`, `CancelDemoNotification` — sales-pipeline events
- **Notifications (in-app):** `app/Notifications/` — 3 classes (`ImplementerTicketNotification`, `TicketNotification`, `DataFileAssignedByImplementerNotification`). All persist to the polymorphic `notifications` table, which is what `CustomerNotificationBell` (and any future bell) reads from.
- **Scenario config:** `config/notification-scenarios.php` defines per-scenario subject lines, greeting text, CTA labels, and recipient roles — `EntityNotificationMail` looks them up by scenario key. Add new scenarios here rather than subclassing Mailable.
- **Dispatch helper:** `TicketNotificationService` (in `app/Services/`) wraps the common pattern of "create a ticket event, send the in-app notification AND the HR email" — use it from new code instead of dispatching mail + notification separately.
- **Sync vs queue:** `QUEUE_CONNECTION=database` in dev; if a Mailable/Notification implements `ShouldQueue`, it won't actually send without `php artisan queue:work` running. For local debugging use `notifyNow()` (already a documented gotcha).

## Services Layer
`app/Services/` (22 classes) — preferred home for non-trivial logic that doesn't fit a model. Grouped by purpose:
- **PDF generation:** `OnboardingPdfGenerator` (see Onboarding PDF Generator section)
- **External APIs:**
  - `MicrosoftGraphService`, `MicrosoftTeamsServiceV2` — MS Graph SDK calls (mail, Teams recordings)
  - `WhatsAppService` — Twilio WhatsApp send + webhook handling
  - `HRV2LicenseSeatApiService` — HR2 license-seat provisioning (used by handover sync)
  - `LeaveAPIService` — leave system bridge
  - `IrbmService` — Malaysian e-Invoicing (LHDN / MyInvois)
  - `MetaConversionsApiService` — Meta Conversions API server-side tracking
  - `SalesOrderApiService` — sales-order sync
- **Business / domain logic:** `QuotationService`, `ProductService`, `CategoryService`, `CountryService`, `IndustryService`, `ProjectProgressService`, `TicketNotificationService`, `TemplateSelector`
- **Invoice / OCR:** `AutoCountInvoiceService`, `HrdfAutoCountInvoiceService`, `InvoiceOcrService` (Tesseract-driven)
- **Data import:** `ImportZohoLeads`, `ImportSoftwareHandovers`
Conventions: services are plain PHP classes (no base class), instantiated via `app(...)` or constructor injection. Avoid mixing Filament/Livewire concerns inside services — keep them framework-agnostic so they can be reused from commands, controllers, and components.

## Filament Resources & Pages — Major Surface Areas
The admin panel (`app/Filament/Resources/` + `app/Filament/Pages/`) is large. Use this as a map when locating features.
- **Sales / pipeline resources:** `LeadResource` (largest — owns the 40 lead-view tabs), `QuotationResource`, `SalesPricingResource`, `ProductResource`, `PolicyResource`
- **Implementation resources:** `ImplementerTicketResource` (also exists in customer panel), `ProjectTaskResource`
- **Hardware/inventory resources:** `HardwareAttachmentResource`, `HardwarePendingStockResource`, `SparePartResource`, `AdminRepairResource`
- **People resources:** `UserResource`
- **Custom pages by area:** `app/Filament/Pages/` (101 files) is grouped loosely by feature prefix:
  - **Implementer:** `ImplementerTicketingDashboard`, `ImplementerClientProfile`, `ImplementerCalendar`, `ImplementerAuditList`, `ImplementerRequestList`, `ImplementerRequestCount`, `ImplementationSession`, `KickOffMeetingSession`, `ImplementerDataFile`
  - **Sales:** `SalesAdminAnalysisV1`–`V4`, `SalesDebtor`, `SalesPersonSurveyRequest`, `SalesForecast*`, `SalesPricingManagement`, `SearchLead`, `SearchLicense`
  - **Finance / renewal:** `AdminRenewalDashboard*`, `AdminRenewalProcessData{Myr,Usd}`, `DebtorAging*`, `FinanceHandoverList`, `InvoicesTable`, `InvoiceSummary`, `ProformaInvoices`
  - **Hardware:** `HardwareDashboardAll`, `HardwareDashboardPendingStock`, `DeviceStockInformation`, `DevicePurchaseInformation`, `OnsiteRepairList`
  - **HRDF / training:** `HrdfClaimTracker`, `HrdfHandoverList`, `HrdfInvoiceList*`, `HeadcountHandoverList`, `TrainerFileUpload`, `TrainerFileView`, `TrainerHandover`, `TrainingRequest*`, `TrainingSettingTrainer*`
  - **Project tracking:** `ProjectAnalysis`, `ProjectCategoryOpen/Closed/Delay/Inactive`, `ProjectPlanSummary`, `ProjectPriority`
  - **Analytics / raw data:** `LeadAnalysis`, `DemoAnalysis*`, `CallLogAnalysis`, `RevenueAnalysis`, `RevenueTable`, `TicketAnalysis`, `TicketDashboard`, `MarketingAnalysis`, `ApolloLeadTracker`, `ManageCustomerStages`
  - **Calendars / utilities:** `MonthlyCalendar`, `TaskCalendar`, `SupportCalendar`, `Whatsapp`
  - **Settings:** `ImplementerThreadEmailTemplate`, `SupportThreadEmailTemplate` (under Settings > Email Template)
- **Auto-discovery is OFF.** New custom pages must be added to `AdminPanelProvider->pages([])` and set `$shouldRegisterNavigation = false` (the custom sidebar handles nav).
