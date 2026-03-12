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
- `app/Models/` — Eloquent models
- `app/Helpers/general_helpers.php` — Global helper functions
- `resources/views/filament/pages/` — Custom Blade views for Filament pages
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
- CSS is inline `<style>` within Blade views, using `imp-` prefix for Implementer Ticketing components
- Fixed/overlay drawers use `body.imp-drawer-open` class to hide Filament topbar (stacking context workaround)

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
