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
- `app/Filament/Customer/` — Customer portal
- `app/Filament/Resources/LeadResource/Tabs/` — 39 tab classes for lead view (e.g., `ProjectPlanTabs.php`, `DataMigrationTabs.php`, `ThreadTabs.php`)
- `app/Filament/Pages/ImplementerTicketingDashboard.php` — Implementer thread/ticketing dashboard (admin)
- `app/Livewire/` — 142 Livewire components (admin + customer portal)
- `resources/views/customer/dashboard.blade.php` — Customer portal dashboard with inline sidebar navigation and tab-switched content
- `app/Models/` — Eloquent models
- `app/Helpers/general_helpers.php` — Global helper functions
- `resources/views/filament/pages/` — Custom Blade views for Filament pages
- `resources/views/filament/resources/lead-resource/tabs/` — Blade views for lead view tabs
- `resources/views/livewire/` — Blade views for Livewire components
- `app/Console/Commands/` — 35 custom artisan commands (scheduled tasks, syncs, reminders)
- `config/` — Includes custom configs: imap.php, reverb.php, invoices.php, notification-scenarios.php
- `routes/api.php` — API routes (Sanctum-authenticated)

## Scale Reference
- 32 Filament Resources, 100 custom Pages, 134 Models, 171 migrations

## Conventions
- Filament custom pages use Livewire properties + Alpine.js for interactivity
- Rich text editors use `contenteditable` + Alpine.js + `document.execCommand()` (not external deps)
- Cursor-aware insertion in `contenteditable`: save `Range` via `saveSelection()` on `@mouseup/@keyup/@blur`, restore before `insertText` — clicking outside (e.g., placeholder buttons) loses cursor position
- `wire:ignore` wraps Alpine-managed DOM to prevent Livewire re-render conflicts
- Livewire events (`$this->dispatch()`) communicate from PHP to Alpine; sync data at submission boundaries only
- CSS is inline `<style>` within Blade views, each feature uses a unique prefix: `imp-` (Implementer Ticketing), `dm-` (Data Migration admin), `dmt-` (Data Migration Templates customer), `thr-` (Thread tab admin), `cit-` (Customer Implementer Thread), `cnb-` (Customer Notification Bell), `emt-` (Email Template), `shp-` (Software Handover Process admin), `cshp-` (Software Handover Process customer), `cdb-` (Customer Dashboard home view)
- Fixed/overlay drawers use `body.imp-drawer-open` class to hide Filament topbar (stacking context workaround)
- Admin sidebar is custom (`resources/views/layouts/custom-sidebar.blade.php`), not Filament's default; pages set `$shouldRegisterNavigation = false` and must be manually registered in `AdminPanelProvider->pages([])` (auto-discovery is disabled)
- Customer portal sidebar is inline in `resources/views/customer/dashboard.blade.php` with collapsible groups (e.g., "Software Onboarding") and JS `switchTab()` for content switching; non-working groups (Training, Support, Knowledge Base, Commercial, Settings) render a single `<div style="padding: 8px 16px 8px 36px; color: #94a3b8; font-size: 13px; font-style: italic;">Coming Soon</div>` placeholder under their header — match this convention rather than inventing a new disabled style
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

## Data Migration System
- **V1 (nested subsections):** `ImplementerDataFile.php` — 5 sections with sub-items, storage at `templates/data-migration-v1/`
- **V2 (flat sections):** `DataMigrationFile.php` — 5 simple sections, storage at `templates/data-migration/`
- **Customer portal:** `CustomerDataMigrationTemplates.php` — downloads V1 templates, uploads filled files with versioning
- **Implementer review:** `DataMigrationTabs.php` — lead view tab showing customer uploads with slide-over for status/remarks
- **Model:** `CustomerDataMigrationFile` — tracks versions per lead+section+item with customer remark, implementer remark, status
- **API routes:** `/admin/api/data-migration-file/{file}/update` (POST), `/admin/data-migration-file/{file}/download` (GET)

## Implementer Thread System
- **Admin dashboard:** `ImplementerTicketingDashboard.php` — WhatsApp-style thread view with search, ticket split, SLA tracking
- **Admin dashboard navigation:** Supports `?ticket={id}` to open a ticket on load, and `?from={url}` for cross-page back navigation (stored as `$returnUrl`)
- **Admin client profile:** `ImplementerClientProfile.php` — Shows customer details + all their tickets; rows link to dashboard with `?from=` for back navigation; CSS prefix `imp-client-`
- **Admin lead tab:** `ThreadTabs.php` + `thread.blade.php` — Lists all tickets for a lead, clickable to open in dashboard via `?ticket={id}` query param
- **Customer portal:** `CustomerImplementerThread.php` Livewire component — Card-based thread list inside "Software Onboarding" group; detail view uses 2-column viewport-fit layout (left: ticket details, right: WhatsApp-style thread with search)
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
- **Model:** `EmailTemplate` — fields: `name`, `subject`, `content`, `type` (implementer_thread/support_thread), `category`, `created_by`; scopes: `implementerThread()`, `supportThread()`
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
