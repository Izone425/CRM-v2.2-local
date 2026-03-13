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
- `app/Filament/Resources/LeadResource/Tabs/` — 40 tab classes for lead view (e.g., `ProjectPlanTabs.php`, `DataMigrationTabs.php`, `ThreadTabs.php`)
- `app/Filament/Pages/ImplementerTicketingDashboard.php` — Implementer thread/ticketing dashboard (admin)
- `app/Livewire/` — 126 Livewire components (admin + customer portal)
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
- 32 Filament Resources, 100+ custom Pages, 167+ Models, 169 migrations

## Conventions
- Filament custom pages use Livewire properties + Alpine.js for interactivity
- Rich text editors use `contenteditable` + Alpine.js + `document.execCommand()` (not external deps)
- `wire:ignore` wraps Alpine-managed DOM to prevent Livewire re-render conflicts
- Livewire events (`$this->dispatch()`) communicate from PHP to Alpine; sync data at submission boundaries only
- CSS is inline `<style>` within Blade views, each feature uses a unique prefix: `imp-` (Implementer Ticketing), `dm-` (Data Migration admin), `dmt-` (Data Migration Templates customer), `thr-` (Thread tab admin), `cit-` (Customer Implementer Thread)
- Fixed/overlay drawers use `body.imp-drawer-open` class to hide Filament topbar (stacking context workaround)
- Admin sidebar is custom (`resources/views/layouts/custom-sidebar.blade.php`), not Filament's default; pages set `$shouldRegisterNavigation = false`
- Customer portal sidebar is inline in `resources/views/customer/dashboard.blade.php` with collapsible groups (e.g., "Software Onboarding") and JS `switchTab()` for content switching
- Lead view tabs follow pattern: `class TabName { public static function getSchema(): array }` returning Filament form components
- Tab visibility is session-based (`lead_visible_tabs`), with role-based defaults in `ViewLeadRecord::getDefaultVisibleTabs()` AND `LeadResource::form()` fallback
- When adding new tabs, update three places: `LeadResource::form()` defaults, `ViewLeadRecord::getDefaultVisibleTabs()`, and the `filterTabs` action switch in `ViewLeadRecord`
- Filament's CSS aggressively overrides native `<select>` elements — use custom Alpine.js dropdowns (div-based) instead
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
- `Storage::disk('public')->url()` may not include the port in dev — use `response()->download()` via named routes instead
- User roles: 1=Lead Owner, 2=Salesperson, 3=Manager, 4=Implementer, 5=Implementer, 9=Technician
- Livewire components must have a single root element — `<script>` and `<style>` tags must be inside the root `<div>`, not siblings

## Data Migration System
- **V1 (nested subsections):** `ImplementerDataFile.php` — 5 sections with sub-items, storage at `templates/data-migration-v1/`
- **V2 (flat sections):** `DataMigrationFile.php` — 5 simple sections, storage at `templates/data-migration/`
- **Customer portal:** `CustomerDataMigrationTemplates.php` — downloads V1 templates, uploads filled files with versioning
- **Implementer review:** `DataMigrationTabs.php` — lead view tab showing customer uploads with slide-over for status/remarks
- **Model:** `CustomerDataMigrationFile` — tracks versions per lead+section+item with customer remark, implementer remark, status
- **API routes:** `/admin/api/data-migration-file/{file}/update` (POST), `/admin/data-migration-file/{file}/download` (GET)

## Implementer Thread System
- **Admin dashboard:** `ImplementerTicketingDashboard.php` — WhatsApp-style thread view with search, ticket split, SLA tracking
- **Admin lead tab:** `ThreadTabs.php` + `thread.blade.php` — Lists all tickets for a lead, clickable to open in dashboard via `?ticket={id}` query param
- **Customer portal:** `CustomerImplementerThread.php` Livewire component — Card-based thread list inside "Software Onboarding" group
- **Customer ticketing:** `ImplementerTicketResource` (Filament Customer panel) — Full ticket CRUD for customers, linked from "Support Thread" nav
- **Models:** `ImplementerTicket` (has `lead_id`, `customer_id`, SLA methods, auto-generated ticket number `IMP-{YY}{IDPADDED}`), `ImplementerTicketReply` (polymorphic `sender()` — User or Customer)
- **CSS prefixes:** `imp-` (admin dashboard), `thr-` (admin lead tab), `cit-` (customer portal)
