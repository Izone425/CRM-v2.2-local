# Implementer Email-Template → Customer Thread Mirror

**Date:** 2026-05-04
**Author:** pang@timeteccloud.com
**Status:** Draft (pending review)

## Problem

When an implementer sends an email-template-driven email from the CRM (Add Follow-up, Send Session Summary, Project Plan, Project Closing, Implementer Appointment), the customer receives the email but has no consolidated portal-side view of the project's communication history.

Today, only the **Send Session Summary** action auto-creates a customer-portal ticket (via `ImplementerActions::createTicketFromSessionSummary`, [ImplementerActions.php:2811](../../../app/Filament/Actions/ImplementerActions.php#L2811)). All other implementer-template emails (e.g., "Follow Up - Data Migration") are invisible in the portal.

## Goal

Every implementer-typed email-template send mirrors into a single per-customer master thread (`SW_XXXXXX_IMP0001`), with each reply badged by a configurable short label (e.g., "Follow Up-Migration") so the customer can scan one chronological thread and distinguish each communication's purpose.

## Non-Goals

- Sales / AR follow-up emails (`ARFollowUpTabs.php`) and renewal emails (`AdminRenewalActions.php`) are explicitly out of scope.
- Backfilling historical email-template sends into the thread.
- Customer-side controls for labels (admin-only configuration).
- Allowing customers to reply to labeled entries differently than they reply today.

## Decisions Locked During Brainstorming

| # | Decision | Rationale |
|---|---|---|
| Q1 | Scope = all implementer-typed email-template senders only (6 sites — see Sender Call-Site Wiring). | Coherent boundary matching "send from CRM by implementer". |
| Q2 | Label source = new `thread_label` column on `email_templates`, hand-tuned per template. | User's example "Follow Up - Data Migration" → "Follow Up-Migration" requires hand-tuned shortenings; no clean derivable rule. |
| Q3 | Pre-trigger emails (sent before Kick-Off Meeting) skip thread silently. | Kick-Off Meeting is the intentional gate — matches the existing "first session unlocks portal" pattern. |
| Q4 | Email = notification-only body + portal CTA. Full content lives only in the thread. | User explicitly chose A2 — "divert user to customer portal". |
| Q5 | "Send Email to Customer?" OFF skips both email AND thread reply. | Preserves the toggle's existing "don't bother the customer" mental model. |
| Arch | Mirror trigger as a static helper on `ImplementerActions`, called from `processFollowUpWithEmail` and from two parallel `Mail::html()` senders. | All 4 in-scope senders that route through `processFollowUpWithEmail` get covered automatically; 2 parallel senders (`ProjectPlanTabs`, `ProjectClosingNew`) call the helper directly without forcing a deeper refactor of their custom send paths. Still "Approach 1" — same class/file as the email pipeline, no new service layer. |

**Master-ticket trigger change:** Auto-creation moves from "Send Session Summary action runs" to "any sender selects template id 17 (Session - Completed Online Kick-Off Meeting)".

## Data Model

Two additive forward-only migrations.

### `email_templates`
- Add `thread_label` (`string(50)`, nullable). Hand-configured per template. NULL = template never mirrors to thread (per-template kill-switch).

### `implementer_ticket_replies`
- Add `email_template_id` (`unsignedBigInteger`, nullable, **no FK constraint** per CLAUDE.md gotcha — long index names + missing-template tolerance).
- Add `thread_label` (`string(50)`, nullable). **Denormalized snapshot** of the template label at send time. If admin later renames the template label, historical replies keep what the customer originally saw.

No backfill of existing rows. NULL labels render unbadged.

## Architecture

### Trigger logic — extracted helper called from each sender

Lives as a new static method on `ImplementerActions` (same file as the email pipeline — no new service layer):

```php
public static function mirrorTemplateEmailToThread(
    EmailTemplate $template,
    ?SoftwareHandover $softwareHandover,
    ?Customer $customer,
    User $implementer,
    string $resolvedSubject,
    string $resolvedContent,
    array $attachments = []
): ?ImplementerTicketReply
```

`$softwareHandover` is nullable so the helper itself can short-circuit and log the warning required by the "Software handover missing" edge case (caller doesn't need a guard).

Returns the new reply (or master ticket's first reply), or NULL if the mirror was skipped.

#### Call sites

1. **`processFollowUpWithEmail()`** ([ImplementerActions.php:1620](../../../app/Filament/Actions/ImplementerActions.php#L1620))
   Inserted **after** the email send block and **gated on** `$data['send_email'] === true` (so the Q5 OFF-toggle skip happens automatically). Covers Add Follow-up, Add Follow-up For Lead, Send Session Summary, Implementer Appointment Relation Manager.

2. **`ProjectPlanTabs`** ([ProjectPlanTabs.php:753](../../../app/Filament/Resources/LeadResource/Tabs/ProjectPlanTabs.php#L753))
   Called after the existing `Mail::html()` send. Existing email send is also replaced with the new notification-only mailable (see Email Body Refactor below).

3. **`ProjectClosingNew`** ([ProjectClosingNew.php:702](../../../app/Livewire/ImplementerDashboard/ProjectClosingNew.php#L702))
   Same pattern — called after the existing `Mail::html()` send, which is also swapped to the notification mailable.

#### Inputs available at each call site

- `processFollowUpWithEmail`: all inputs already resolved locally.
- `ProjectPlanTabs` / `ProjectClosingNew`: same data pieces are present (template id, software handover, customer, resolved subject/content, attachments) — just under different local variable names. Wire them through to the helper.

#### Decision tree (inside the helper)

```
1. if $template->thread_label is NULL → skip mirror, return null.

2. if $softwareHandover is NULL → log warning, skip mirror, return null.
   (Master-ticket numbering depends on it.)

3. if $customer is NULL → log warning, skip mirror, return null.
   (Email still sent at the call site; mirror requires a Customer row.)

4. DB::transaction with lockForUpdate on implementer_tickets where
   software_handover_id = $softwareHandover->id:
     $masterTicket = first ImplementerTicket for this software_handover_id,
                     ordered by id ASC.

5. if $masterTicket is NULL:
     a. if $template->id === EmailTemplate::KICK_OFF_TEMPLATE_ID (= 17):
          Create master ImplementerTicket:
            customer_id          = $customer->id
            implementer_user_id  = $implementer->id
            implementer_name     = $implementer->name
            lead_id              = $softwareHandover->lead_id
            software_handover_id = $softwareHandover->id
            subject              = $resolvedSubject
            description          = $resolvedContent
            category             = 'Kick-Off Meeting'
            module               = 'General'
            status               = 'open'
            priority             = 'medium'
            first_responded_at   = now()
          (ImplementerTicket::booted() observer auto-fills
           ticket_number = 'SW_XXXXXX_IMP0001'.)
          Create the first ImplementerTicketReply with $resolvedContent
            + $attachments + thread_label snapshot + email_template_id.
          $customer->notifyNow(new ImplementerTicketNotification(
            $master, 'replied_by_implementer', $implementer->name
          )).
          Return the reply.
     b. else:
          Return null (silent skip — Q3 pre-trigger guard).

6. if $masterTicket exists:
     Append ImplementerTicketReply:
       implementer_ticket_id = $masterTicket->id
       sender_type           = User::class
       sender_id             = $implementer->id
       message               = $resolvedContent
       attachments           = $attachments  // empty array if none
       email_template_id     = $template->id
       thread_label          = $template->thread_label
       is_internal_note      = false
     $customer->notifyNow(new ImplementerTicketNotification(
       $masterTicket, 'replied_by_implementer', $implementer->name
     )).
     Return the reply.
```

#### Removal

- Delete the `createTicketFromSessionSummary($record, ...)` call at [ImplementerActions.php:2736](../../../app/Filament/Actions/ImplementerActions.php#L2736).
- Delete the `createTicketFromSessionSummary()` method itself ([ImplementerActions.php:2811](../../../app/Filament/Actions/ImplementerActions.php#L2811)) and its helper `buildSessionSummaryPlaceholders()` ([ImplementerActions.php:2917](../../../app/Filament/Actions/ImplementerActions.php#L2917)) — superseded by the new template-based trigger. Search-and-remove any test that referenced them.

#### Constants

- `App\Models\EmailTemplate::KICK_OFF_TEMPLATE_ID = 17` — hard-coded ID acceptable per the existing pattern of `EmailTemplate::find(16)` and `EmailTemplate::find(19)` already in `ProjectPlanTabs` and `ProjectClosingNew`.

## Email Body Refactor

Replace the existing full-content send with a notification-style mailable at three places:
- Inside `processFollowUpWithEmail()` (covers sites 1–4).
- Inside `ProjectPlanTabs` send block (site 5).
- Inside `ProjectClosingNew` send block (site 6).

### New mailable: `App\Mail\ImplementerThreadNotificationMail`

- **To:** `Required Attendees` list (HR contacts + customer email — unchanged from today).
- **Subject:** resolved template subject (preserves inbox preview context).
- **Body:** new blade view at `resources/views/emails/implementer-thread-notification.blade.php`. Inline CSS (no layout component — matches `FollowUpNotification`).
  - Greeting: `Dear Customer,`
  - One-line message: "You have a new update in your customer portal."
  - "View in Customer Portal" button linking to:
    - `{APP_URL}/customer/dashboard?tab=impThread&ticket={masterTicket->id}` if master exists
    - `{APP_URL}/customer/dashboard?tab=impThread` otherwise (degraded, points to thread list)
  - Implementer signature block at the bottom — same fields used today (name, designation, company, phone, email).
- **Attachments:** none. Files live on the thread reply only; recipients click through to download from the portal.

### Scheduled-email path

The `scheduled_emails` table at [ImplementerActions.php:1828](../../../app/Filament/Actions/ImplementerActions.php#L1828) keeps storing the same `$emailData` payload. The mirror happens at **send-time** (when the cron processes the row), so the master-ticket existence check is always fresh.

## UI Changes

### Customer portal — `customer-implementer-thread.blade.php`

In the reply-rendering loop ([resources/views/livewire/customer-implementer-thread.blade.php:475](../../../resources/views/livewire/customer-implementer-thread.blade.php#L475)), when `$reply->thread_label` is set, render a badge on its own line between the sender header and the message body. Reuses the existing `.cit-tag` class (already used for category/module). No new CSS prefix.

Replies with NULL label → no badge, current rendering preserved.

### Admin ticketing dashboard — `ImplementerTicketingDashboard`

Mirror the same badge in the implementer's view of the same thread, using the `imp-` CSS prefix. Reuse an existing tag/badge class if one exists; otherwise add `.imp-thread-label`.

### Settings → Email Templates — `EmailTemplateResource`

The `'implementer'` type templates (the ones used by Add Follow-up, Send Session Summary, Project Plan, Project Closing) are managed by [EmailTemplateResource.php](../../../app/Filament/Resources/EmailTemplateResource.php), a regular Filament Resource — **not** by `ImplementerThreadEmailTemplate.php` (that page manages `'implementer_thread'` type templates used by the dashboard's reply composer).

Edit `EmailTemplateResource::form()`:
- Add a `TextInput::make('thread_label')->maxLength(50)->helperText('Short tag shown on customer thread replies. Leave empty to skip thread mirroring for this template.')`. Visible only when `type === 'implementer'`.

Edit `EmailTemplateResource::table()`:
- Add `TextColumn::make('thread_label')->label('Thread Label')->toggleable(isToggledHiddenByDefault: false)`.

## Edge Cases

- **Master-ticket race condition.** Two implementers send Kick-Off concurrently for the same software handover. Mitigated by `DB::transaction` with `lockForUpdate` and `attempts=3`. InnoDB gap X-locks against non-existent rows are mutually compatible, so both concurrent Kick-Off transactions pass the `!$master` check; one wins the insert, the other deadlocks on the insert-intention lock. Laravel's `DB::transaction(...)` retries the closure on `QueryException` deadlock SQLSTATE — on retry the loser sees the master row created by the winner and correctly falls into the append branch.

- **Customer not found for lead.** Master-ticket creation impossible (`customer_id` required). Log a warning (matches existing `createTicketFromSessionSummary` pattern), skip the mirror, send the notification email anyway.

- **Software handover missing (`software_handover_id` NULL).** `SW_XXXXXX_IMP0001` numbering depends on it. Skip mirror. Log warning. Email still sends.

- **Mirror failure (DB error etc.).** `try/catch` around the entire mirror block. Email send must succeed independently. Same defensive pattern as the current `createTicketFromSessionSummary` call site at [ImplementerActions.php:2737](../../../app/Filament/Actions/ImplementerActions.php#L2737).

- **Template deleted after rows reference it.** No FK constraint on `email_template_id`; orphan ID is fine. Denormalized `thread_label` keeps badge rendering correct.

- **`is_internal_note` interaction.** Mirrored replies always set `is_internal_note = false`. Manual internal notes from the dashboard reply box don't go through `processFollowUpWithEmail` and are unaffected.

- **Master-ticket status when appending.** Appending does NOT change ticket status. Closed master tickets still receive appended replies — no status guard.

- **Pre-Kickoff CTA degradation.** When mirror is skipped (Q3) but email still sends, the CTA points to the thread list (no `ticket` param). Customer sees an empty thread page until Kickoff lands.

## Sender Call-Site Wiring

Six in-scope sites:

| # | Site | Routes through `processFollowUpWithEmail`? | Action |
|---|---|---|---|
| 1 | `ImplementerActions::addImplementerFollowUp()` ([:900](../../../app/Filament/Actions/ImplementerActions.php#L900)) | Yes | None |
| 2 | `ImplementerActions::addImplementerFollowUpForLead()` ([:1275](../../../app/Filament/Actions/ImplementerActions.php#L1275)) | Yes | None |
| 3 | `ImplementerActions::sendSessionSummaryAction()` ([:2719](../../../app/Filament/Actions/ImplementerActions.php#L2719)) | Yes | Delete the `createTicketFromSessionSummary` call at [:2736](../../../app/Filament/Actions/ImplementerActions.php#L2736); delete the method itself |
| 4 | `ImplementerAppointmentRelationManager` ([:1679](../../../app/Filament/Resources/LeadResource/RelationManagers/ImplementerAppointmentRelationManager.php#L1679)) | Yes | None — auto-covered by the trigger inside `processFollowUpWithEmail` |
| 5 | `ProjectPlanTabs` ([:753](../../../app/Filament/Resources/LeadResource/Tabs/ProjectPlanTabs.php#L753)) | No — uses `Mail::html()` directly | Swap `Mail::html()` for the new notification mailable; call `ImplementerActions::mirrorTemplateEmailToThread()` after sending |
| 6 | `ProjectClosingNew` ([:702](../../../app/Livewire/ImplementerDashboard/ProjectClosingNew.php#L702)) | No — uses `Mail::html()` directly | Same — swap mailable; call the mirror helper |

## Tests

### `tests/Feature/ImplementerThreadMirrorTest.php` (new)
- Kick-Off + no master → master created with correct shape (`SW_XXXXXX_IMP0001` format, category='Kick-Off Meeting', `first_responded_at` set, FK fields populated).
- Kick-Off + master exists → appends reply, no duplicate ticket.
- Non-Kickoff + no master → mirror skipped, no ticket/reply created, email still sent.
- Non-Kickoff + master exists → appends reply with `email_template_id` and `thread_label` populated.
- Template with NULL `thread_label` → mirror skipped (kill-switch).
- `send_email = false` → no email AND no mirror.
- Customer missing for lead → mirror skipped, warning logged, email still sent.
- Concurrent Kick-Off sends → exactly one master created (transaction lock).
- Template deleted after reply created → reply renders with snapshot label intact.

### `tests/Feature/ImplementerThreadNotificationMailTest.php` (new)
- Mail subject = template subject after placeholder resolution.
- Mail body contains "View in Customer Portal" CTA pointing to `/customer/dashboard?tab=impThread&ticket={id}` when master exists.
- CTA degrades to thread-list URL (no `ticket` param) when master doesn't exist.
- No attachments on the email.

### Removal
- Search and delete any existing test that referenced `createTicketFromSessionSummary` (deleted method).
- Add a regression test: "sendSessionSummaryAction with Kick-Off template creates master ticket via the new path".

### Manual QA
- Admin sends Kick-Off Meeting template → master appears in customer portal under "Software Onboarding > Support Thread"; bell pings; email arrives with CTA only (no full content).
- Admin sends Follow Up - Data Migration → labeled reply ("Follow Up-Migration") appears under same master ticket.
- Admin sends pre-Kickoff Follow-up → email arrives, no thread entry.
- Admin toggles "Send Email to Customer?" OFF → nothing visible to customer; internal `ImplementerLogs` row still created.

## Open Items for the Plan

- Confirm exact CSS class to reuse for the admin-side label badge in `ImplementerTicketingDashboard`.
- Confirm placement of "Thread Label" field in the `ImplementerThreadEmailTemplate` create/edit modal (next to Category vs. on its own row).
- For sites 5–6 (`ProjectPlanTabs`, `ProjectClosingNew`): identify the local variable names holding the resolved subject/content/attachments at the send site so the helper call can be wired in cleanly.
