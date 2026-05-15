<?php

namespace App\Filament\Pages;

use App\Enums\ImplementerTicketStatus;
use App\Models\Customer;
use App\Models\EmailTemplate;
use App\Models\ImplementerTicket;
use App\Models\ImplementerTicketReply;
use App\Models\SlaConfiguration;
use App\Models\User;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Mail\ImplementerTicketHrNotification;
use Livewire\WithFileUploads;

class ImplementerTicketingDashboard extends Page
{
    use WithFileUploads;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static string $view = 'filament.pages.implementer-ticketing-dashboard';
    protected static ?string $title = '';
    protected static ?string $navigationLabel = 'Implementer Ticketing Dashboard';
    protected static bool $shouldRegisterNavigation = false;

    public $searchQuery = '';
    public $selectedImplementer = '';
    public $selectedCompany = '';
    public $activeTab = 'pending_client';
    public $statusFilter = '';
    public $showSlaPolicy = false;

    // SLA Configuration mode
    public $slaConfigMode = false;
    public $configFirstReplyCutoff = '17:30';
    public $configFirstReplyEnabled = true;
    public $configFollowupReminderDays = 3;
    public $configFollowupAutoCloseDays = 2;
    public $configFollowupEnabled = true;
    public $configBusinessStart = '08:00';
    public $configBusinessEnd = '18:00';
    public $configResolutionSlaHours = 48;
    public $configFirstResponseSlaHours = 24;

    // Pagination
    public $perPage = 15;
    public $currentPage = 1;

    // Ticket detail drawer
    public $showTicketDetail = false;
    public $selectedTicketId = null;
    public $returnUrl = null;

    // Reply form
    public $replyMessage = '';
    public $replyAttachments = [];
    public $isInternalNote = false;
    public $replyTo = '';
    public $replyCc = '';
    public $replyBcc = '';
    public $replyEmailTemplate = '';

    // Create Ticket Drawer
    public $showCreateDrawer = false;
    public $newTicketCustomerId = '';
    public $newTicketCategory = '';
    public $newTicketModule = '';
    public $newTicketStatus = 'open';
    public $newTicketPriority = 'medium';
    public $newTicketEmailTemplate = '';
    public $newTicketEmailSubject = '';
    public $newTicketEmailBody = '';
    public $ticketAttachments = [];
    public $customerSearch = '';

    // Split Ticket Drawer
    public $showSplitDrawer = false;
    public $splitReplyId = null;
    public $splitSubject = '';
    public $splitCategory = '';
    public $splitModule = '';
    public $splitPriority = 'medium';

    // Merge Ticket Drawer
    public $showMergeDrawer = false;
    public $mergeTargetTicketId = null;
    public $mergeSearch = '';

    public function getEmailTemplatesProperty()
    {
        return EmailTemplate::implementerThread()->latest()->get();
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user || !($user instanceof User)) {
            return false;
        }
        return $user->hasRouteAccess('filament.admin.pages.implementer-ticketing-dashboard');
    }

    public function mount(): void
    {
        $ticketId = request()->query('ticket');
        $from = request()->query('from');

        if ($from) {
            $this->returnUrl = $from;
        }

        if ($ticketId) {
            $ticket = ImplementerTicket::find($ticketId);
            if ($ticket) {
                $this->openTicketDetail($ticket->id);
            }
        }
    }

    public function updatedSearchQuery()
    {
        $this->currentPage = 1;
    }

    public function updatedSelectedImplementer()
    {
        $this->currentPage = 1;
    }

    public function updatedSelectedCompany()
    {
        $this->currentPage = 1;
    }

    public function filterByStatus($status)
    {
        if ($this->statusFilter === $status) {
            $this->statusFilter = '';
        } else {
            $this->statusFilter = $status;
        }
        $this->activeTab = 'all';
        $this->currentPage = 1;
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->statusFilter = '';
        $this->currentPage = 1;
    }

    public function goToPage($page)
    {
        $this->currentPage = $page;
    }

    public function nextPage()
    {
        $this->currentPage++;
    }

    public function previousPage()
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
        }
    }

    // --- Create Ticket Drawer Methods ---

    public function openCreateDrawer()
    {
        $this->resetCreateForm();
        $this->showCreateDrawer = true;
    }

    public function closeCreateDrawer()
    {
        $this->showCreateDrawer = false;
        $this->resetCreateForm();
    }

    protected function resetCreateForm()
    {
        $this->newTicketCustomerId = '';
        $this->newTicketCategory = '';
        $this->newTicketModule = '';
        $this->newTicketStatus = 'open';
        $this->newTicketPriority = 'medium';
        $this->newTicketEmailTemplate = '';
        $this->newTicketEmailSubject = '';
        $this->newTicketEmailBody = '';
        $this->ticketAttachments = [];
        $this->customerSearch = '';
        $this->dispatch('drawerReset');
    }

    public function applyEmailTemplate($templateId)
    {
        $this->newTicketEmailTemplate = $templateId;

        $template = EmailTemplate::find($templateId);
        if ($template) {
            $customer = \App\Models\Customer::find($this->newTicketCustomerId);
            $placeholderData = [
                'client_name' => $customer?->name,
                'company_name' => $customer?->company_name,
                'category' => $this->newTicketCategory,
                'module' => $this->newTicketModule,
                'implementer_name' => auth()->user()->name,
            ];
            $this->newTicketEmailSubject = EmailTemplate::replacePlaceholders($template->subject, $placeholderData);
            $this->newTicketEmailBody = EmailTemplate::replacePlaceholders($template->content, $placeholderData);
            $this->dispatch('templateApplied');
        }
    }

    public function removeAttachment($index)
    {
        $attachments = $this->ticketAttachments;
        unset($attachments[$index]);
        $this->ticketAttachments = array_values($attachments);
    }

    public function updateInternalNote($replyId, $message)
    {
        $reply = ImplementerTicketReply::find($replyId);
        if (!$reply || !$reply->is_internal_note) return;
        if ($reply->sender_type !== 'App\\Models\\User' || $reply->sender_id !== auth()->id()) return;

        $reply->message = $message;
        $reply->save();

        Notification::make()
            ->title('Internal note updated')
            ->success()
            ->send();
    }

    public function deleteInternalNote($replyId)
    {
        $reply = ImplementerTicketReply::find($replyId);
        if (!$reply || !$reply->is_internal_note) return;
        if ($reply->sender_type !== 'App\\Models\\User' || $reply->sender_id !== auth()->id()) return;

        $reply->delete();

        Notification::make()
            ->title('Internal note deleted')
            ->success()
            ->send();
    }

    public function removeReplyAttachment($index)
    {
        $attachments = $this->replyAttachments;
        unset($attachments[$index]);
        $this->replyAttachments = array_values($attachments);
    }

    public function createTicket()
    {
        $this->validate([
            'newTicketCustomerId' => 'required|exists:customers,id',
            'newTicketCategory' => ['required', 'string', Rule::notIn(['Add on License', 'Add on Module', 'Add on Device'])],
            'newTicketModule' => 'required|string',
            'newTicketStatus' => 'required|string',
            'newTicketEmailSubject' => 'required|string|max:255',
            'newTicketEmailBody' => 'required|string',
            'ticketAttachments' => 'nullable|array',
            'ticketAttachments.*' => 'file|max:10240|mimes:doc,docx,xls,xlsx,pdf,png,jpg,jpeg',
        ], [
            'newTicketCategory.not_in' => 'Add-on requests should be handled by the Sales team, not via support tickets.',
            'ticketAttachments.*.mimes' => 'Only Word, Excel, PDF, and image files are accepted.',
            'ticketAttachments.*.max'   => 'Each file must be 10MB or smaller.',
        ]);

        $customer = Customer::find($this->newTicketCustomerId);
        if (!$customer) {
            return;
        }

        // Resolve implementer
        $implementerData = ImplementerTicket::resolveImplementerForCustomer($customer);

        // Handle file uploads
        $attachmentPaths = [];
        if (!empty($this->ticketAttachments)) {
            foreach ($this->ticketAttachments as $file) {
                $attachmentPaths[] = $file->store('implementer-tickets', 'public');
            }
        }

        // Determine closed fields
        $closedAt = null;
        $closedBy = null;
        $closedByType = null;
        if ($this->newTicketStatus === 'closed') {
            $closedAt = now();
            $closedBy = auth()->id();
            $closedByType = 'user';
        }

        $implementerName = $implementerData ? $implementerData['name'] : auth()->user()->name;

        $ticket = ImplementerTicket::create([
            'customer_id' => $customer->id,
            'implementer_user_id' => $implementerData ? $implementerData['user']->id : auth()->id(),
            'implementer_name' => $implementerName,
            'lead_id' => $implementerData['lead_id'] ?? null,
            'software_handover_id' => $implementerData['software_handover_id'] ?? null,
            'subject' => $this->newTicketEmailSubject,
            'description' => $this->newTicketEmailBody,
            'status' => $this->newTicketStatus,
            'priority' => $this->newTicketPriority,
            'category' => $this->newTicketCategory,
            'module' => $this->newTicketModule,
            'attachments' => !empty($attachmentPaths) ? $attachmentPaths : null,
            'closed_at' => $closedAt,
            'closed_by' => $closedBy,
            'closed_by_type' => $closedByType,
        ]);

        // Final placeholder replacement pass (now that ticket exists with ID)
        $placeholderData = [
            'client_name' => $customer->name,
            'ticket_id' => $ticket->formatted_ticket_number,
            'implementer_name' => $implementerName,
            'company_name' => $customer->company_name,
            'category' => $this->newTicketCategory,
            'module' => $this->newTicketModule,
        ];
        $finalBody = EmailTemplate::replacePlaceholders($this->newTicketEmailBody, $placeholderData);
        $finalSubject = EmailTemplate::replacePlaceholders($this->newTicketEmailSubject, $placeholderData);

        // Update ticket description and subject with replaced placeholders
        $ticket->update([
            'description' => $finalBody,
            'subject' => $finalSubject,
        ]);

        // Create the first reply from email body
        if ($ticket && $finalBody) {
            ImplementerTicketReply::create([
                'implementer_ticket_id' => $ticket->id,
                'sender_type' => 'App\\Models\\User',
                'sender_id' => auth()->id(),
                'message' => $finalBody,
                'is_internal_note' => false,
            ]);

            // Set first responded at
            $ticket->update(['first_responded_at' => now()]);
        }

        $this->showCreateDrawer = false;
        $this->resetCreateForm();

        // Send HR email notification
        $this->sendHrEmailNotification($ticket, 'created');

        // Notify customer in-app
        if ($ticket->customer) {
            $ticket->customer->notifyNow(
                new \App\Notifications\ImplementerTicketNotification($ticket, 'replied_by_implementer', auth()->user()->name)
            );
        }

        Notification::make()
            ->title('Ticket created successfully')
            ->body('Ticket ' . $ticket->formatted_ticket_number . ' has been created.')
            ->success()
            ->send();
    }

    // --- Ticket Split Methods ---

    public function openSplitDrawer($replyId)
    {
        $reply = ImplementerTicketReply::find($replyId);
        if (!$reply) return;

        $originalTicket = ImplementerTicket::find($this->selectedTicketId);

        $this->splitReplyId = $replyId;
        $this->splitSubject = mb_substr(strip_tags($reply->message), 0, 255);
        $this->splitCategory = $originalTicket->category ?? '';
        $this->splitModule = $originalTicket->module ?? '';
        $this->splitPriority = 'medium';
        $this->showSplitDrawer = true;
    }

    public function closeSplitDrawer()
    {
        $this->showSplitDrawer = false;
        $this->splitReplyId = null;
        $this->splitSubject = '';
        $this->splitCategory = '';
        $this->splitModule = '';
        $this->splitPriority = 'medium';
    }

    public function submitSplitTicket()
    {
        $this->validate([
            'splitSubject' => 'required|string|max:255',
            'splitCategory' => 'required|string',
            'splitModule' => 'required|string',
        ]);

        $originalReply = ImplementerTicketReply::find($this->splitReplyId);
        $originalTicket = ImplementerTicket::find($this->selectedTicketId);

        if (!$originalReply || !$originalTicket) {
            Notification::make()->title('Error')->body('Original message not found.')->danger()->send();
            return;
        }

        $newTicket = ImplementerTicket::create([
            'customer_id' => $originalTicket->customer_id,
            'implementer_user_id' => $originalTicket->implementer_user_id,
            'implementer_name' => $originalTicket->implementer_name,
            'lead_id' => $originalTicket->lead_id,
            'software_handover_id' => $originalTicket->software_handover_id,
            'subject' => $this->splitSubject,
            'description' => $originalReply->message,
            'status' => 'open',
            'priority' => $this->splitPriority,
            'category' => $this->splitCategory,
            'module' => $this->splitModule,
        ]);

        // Copy the client message as first reply in the new ticket
        ImplementerTicketReply::create([
            'implementer_ticket_id' => $newTicket->id,
            'sender_type' => $originalReply->sender_type,
            'sender_id' => $originalReply->sender_id,
            'message' => $originalReply->message,
            'attachments' => $originalReply->attachments,
            'is_internal_note' => false,
        ]);

        $this->closeSplitDrawer();

        Notification::make()
            ->title('Ticket split successfully')
            ->body('New ticket ' . $newTicket->formatted_ticket_number . ' has been created.')
            ->success()
            ->send();
    }

    // --- Merge Ticket Methods ---

    public function openMergeDrawer()
    {
        $this->mergeTargetTicketId = null;
        $this->mergeSearch = '';
        $this->showMergeDrawer = true;
    }

    public function closeMergeDrawer()
    {
        $this->showMergeDrawer = false;
        $this->mergeTargetTicketId = null;
        $this->mergeSearch = '';
    }

    public function getMergeableTicketsProperty()
    {
        if (!$this->showMergeDrawer) return collect();

        $ticket = ImplementerTicket::find($this->selectedTicketId);
        if (!$ticket) return collect();

        $query = ImplementerTicket::where('customer_id', $ticket->customer_id)
            ->where('id', '!=', $ticket->id)
            ->whereNull('merged_into_ticket_id')
            ->where('status', '!=', 'closed')
            ->with('implementerUser');

        if ($this->mergeSearch) {
            $search = $this->mergeSearch;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->limit(20)->get();
    }

    public function selectMergeTarget($ticketId)
    {
        $this->mergeTargetTicketId = $ticketId;
    }

    public function submitMergeTicket()
    {
        if (!$this->mergeTargetTicketId) {
            Notification::make()->title('Please select a target ticket')->danger()->send();
            return;
        }

        $sourceTicket = ImplementerTicket::find($this->selectedTicketId);
        $targetTicket = ImplementerTicket::find($this->mergeTargetTicketId);

        if (!$sourceTicket || !$targetTicket) {
            Notification::make()->title('Ticket not found')->danger()->send();
            return;
        }

        if ($sourceTicket->customer_id !== $targetTicket->customer_id) {
            Notification::make()->title('Tickets must belong to the same customer')->danger()->send();
            return;
        }

        if ($sourceTicket->isMerged()) {
            Notification::make()->title('This ticket has already been merged')->danger()->send();
            return;
        }

        if ($targetTicket->status === ImplementerTicketStatus::CLOSED) {
            Notification::make()->title('Cannot merge into a closed ticket')->danger()->send();
            return;
        }

        // Close source ticket as merged
        $sourceTicket->update([
            'merged_into_ticket_id' => $targetTicket->id,
            'merged_at' => now(),
            'merged_by' => auth()->id(),
            'status' => 'closed',
            'closed_at' => now(),
            'closed_by' => auth()->id(),
            'closed_by_type' => 'user',
        ]);

        // Add system note to target ticket
        ImplementerTicketReply::create([
            'implementer_ticket_id' => $targetTicket->id,
            'sender_type' => 'App\\Models\\User',
            'sender_id' => auth()->id(),
            'message' => '<p><em>Ticket ' . $sourceTicket->formatted_ticket_number . ' (' . e($sourceTicket->subject) . ') has been merged into this ticket by ' . auth()->user()->name . '.</em></p>',
            'is_internal_note' => false,
        ]);

        // Notify customer
        if ($targetTicket->customer) {
            $targetTicket->customer->notifyNow(
                new \App\Notifications\ImplementerTicketNotification(
                    $targetTicket,
                    'merged',
                    auth()->user()->name,
                    ['merged_ticket_number' => $sourceTicket->formatted_ticket_number, 'merged_ticket_id' => $sourceTicket->id]
                )
            );
        }

        // Send HR email notification for merge
        $this->sendHrEmailNotification($targetTicket, 'merged');

        $this->closeMergeDrawer();

        // Navigate to target ticket
        $this->openTicketDetail($targetTicket->id);

        Notification::make()
            ->title('Ticket merged successfully')
            ->body($sourceTicket->formatted_ticket_number . ' has been merged into ' . $targetTicket->formatted_ticket_number)
            ->success()
            ->send();
    }

    #[\Livewire\Attributes\On('openTicketByNumber')]
    public function openTicketByNumber($number)
    {
        $ticket = ImplementerTicket::where('ticket_number', $number)->first();
        if ($ticket) {
            $this->openTicketDetail($ticket->id);
        }
    }

    // --- Ticket Detail Drawer Methods ---

    public function openTicketDetail($ticketId)
    {
        $this->selectedTicketId = $ticketId;
        $this->showTicketDetail = true;
        $this->replyMessage = '';
        $this->replyAttachments = [];
        $this->isInternalNote = false;
        $this->replyCc = '';
        $this->replyBcc = '';
        $this->replyEmailTemplate = '';

        // Pre-fill TO with customer email
        $ticket = ImplementerTicket::find($ticketId);
        $this->replyTo = $ticket?->customer?->email ?? '';
    }

    public function closeTicketDetail()
    {
        if ($this->returnUrl) {
            $this->redirect($this->returnUrl);
            return;
        }

        $this->showTicketDetail = false;
        $this->selectedTicketId = null;
        $this->replyMessage = '';
        $this->replyAttachments = [];
        $this->isInternalNote = false;
        $this->replyTo = '';
        $this->replyCc = '';
        $this->replyBcc = '';
        $this->replyEmailTemplate = '';
    }

    public function changeTicketStatus($status)
    {
        $ticket = ImplementerTicket::find($this->selectedTicketId);
        if (!$ticket) return;

        $oldStatus = $ticket->status->value;
        $ticket->status = $status;

        // Track pending_client_since for SLA follow-up automation
        if ($status === 'pending_client') {
            if (!$ticket->pending_client_since) {
                $ticket->pending_client_since = now();
            }
        } else {
            $ticket->pending_client_since = null;
            $ticket->followup_sent_at = null;
        }

        if ($status === 'closed' && !$ticket->closed_at) {
            $ticket->closed_at = now();
            $ticket->closed_by = auth()->id();
            $ticket->closed_by_type = 'user';
        } elseif ($status !== 'closed') {
            $ticket->closed_at = null;
            $ticket->closed_by = null;
            $ticket->closed_by_type = null;
        }

        $ticket->save();

        // Log status change
        activity('implementer_ticket')
            ->performedOn($ticket)
            ->causedBy(auth()->user())
            ->withProperties(['old_status' => $oldStatus, 'new_status' => $status, 'trigger' => 'manual'])
            ->log('Status changed from ' . ucwords(str_replace('_', ' ', $oldStatus)) . ' to ' . ucwords(str_replace('_', ' ', $status)));

        Notification::make()
            ->title('Status updated')
            ->body("Ticket status changed to " . ImplementerTicketStatus::from($status)->label())
            ->success()
            ->send();
    }

    public function submitReply()
    {
        $ticket = ImplementerTicket::with(['customer', 'implementerUser'])->find($this->selectedTicketId);
        if (!$ticket) return;

        // Final placeholder replacement pass before saving
        $this->replyMessage = EmailTemplate::replacePlaceholders($this->replyMessage, [
            'client_name' => $ticket->customer?->name,
            'ticket_id' => $ticket->formatted_ticket_number,
            'implementer_name' => $ticket->implementerUser?->name ?? auth()->user()->name,
            'company_name' => $ticket->customer?->company_name,
            'category' => $ticket->category,
            'module' => $ticket->module,
        ]);

        $hasMessage = !empty(trim($this->replyMessage));
        $wasInternalNote = $this->isInternalNote;

        // Internal notes require a message
        if ($wasInternalNote && !$hasMessage) {
            Notification::make()
                ->title('Internal note cannot be empty')
                ->danger()
                ->send();
            return;
        }

        // If there's a message, create the reply
        if ($hasMessage) {
            // Handle file attachments
            $attachmentPaths = [];
            if (!empty($this->replyAttachments)) {
                foreach ($this->replyAttachments as $file) {
                    $attachmentPaths[] = $file->store('implementer-ticket-replies', 'public');
                }
            }

            ImplementerTicketReply::create([
                'implementer_ticket_id' => $ticket->id,
                'sender_type' => 'App\\Models\\User',
                'sender_id' => auth()->id(),
                'message' => $this->replyMessage,
                'attachments' => !empty($attachmentPaths) ? $attachmentPaths : null,
                'is_internal_note' => $wasInternalNote,
            ]);
        }

        // Update first response tracking and SLA fields if not internal note
        // Status is NOT overridden here — the user controls status via the sidebar dropdown
        if (!$wasInternalNote) {
            // Re-read ticket to get the latest status (user may have changed it via sidebar)
            $ticket->refresh();

            // Track pending_client_since if current status is pending_client
            if ($ticket->status === ImplementerTicketStatus::PENDING_CLIENT) {
                if (!$ticket->pending_client_since) {
                    $ticket->pending_client_since = now();
                }
            } else {
                $ticket->pending_client_since = null;
                $ticket->followup_sent_at = null;
            }

            if ($hasMessage && !$ticket->first_responded_at) {
                $ticket->first_responded_at = now();
                $ticket->is_overdue = false;
            }
            $ticket->save();
        }

        // Send HR email + customer in-app notification (skip internal notes)
        if (!$wasInternalNote) {
            if ($hasMessage) {
                $action = $ticket->status === ImplementerTicketStatus::CLOSED ? 'closed' : 'replied_by_implementer';
                $this->sendHrEmailNotification($ticket, $action);
                // Notify customer in-app
                if ($ticket->customer) {
                    $ticket->customer->notifyNow(
                        new \App\Notifications\ImplementerTicketNotification($ticket, $action, auth()->user()->name)
                    );
                }
            } else {
                $action = $ticket->status === ImplementerTicketStatus::CLOSED ? 'closed' : 'status_changed';
                $this->sendHrEmailNotification($ticket, $action);
                // Notify customer in-app
                if ($ticket->customer) {
                    $ticket->customer->notifyNow(
                        new \App\Notifications\ImplementerTicketNotification($ticket, $action, auth()->user()->name)
                    );
                }
            }
        }

        $this->replyMessage = '';
        $this->replyAttachments = [];
        $this->isInternalNote = false;
        $this->replyCc = '';
        $this->replyBcc = '';
        $this->replyEmailTemplate = '';
        $this->dispatch('replyEditorReset');

        // Show appropriate notification
        if ($wasInternalNote) {
            $notifTitle = 'Internal note added';
        } elseif ($hasMessage) {
            $notifTitle = 'Reply sent successfully';
        } else {
            $notifTitle = 'Status updated successfully';
        }

        Notification::make()
            ->title($notifTitle)
            ->success()
            ->send();
    }

    public function applyReplyTemplate($templateId)
    {
        $this->replyEmailTemplate = $templateId;

        $template = EmailTemplate::find($templateId);
        if ($template) {
            $ticket = \App\Models\ImplementerTicket::with(['customer', 'implementerUser'])->find($this->selectedTicketId);
            $this->replyMessage = EmailTemplate::replacePlaceholders($template->content, [
                'client_name' => $ticket?->customer?->name,
                'ticket_id' => $ticket?->formatted_ticket_number,
                'implementer_name' => $ticket?->implementerUser?->name ?? auth()->user()->name,
                'company_name' => $ticket?->customer?->company_name,
                'category' => $ticket?->category,
                'module' => $ticket?->module,
            ]);
            $this->dispatch('replyTemplateApplied');
        }
    }

    // --- SLA Configuration Methods ---

    public function toggleSlaConfigMode()
    {
        if (!$this->slaConfigMode) {
            // Entering edit mode — load current config
            $config = SlaConfiguration::current();
            $this->configFirstReplyCutoff = $config->first_reply_cutoff_time;
            $this->configFirstReplyEnabled = $config->first_reply_enabled;
            $this->configFollowupReminderDays = $config->followup_reminder_days;
            $this->configFollowupAutoCloseDays = $config->followup_auto_close_days;
            $this->configFollowupEnabled = $config->followup_enabled;
            $this->configBusinessStart = $config->business_start_time;
            $this->configBusinessEnd = $config->business_end_time;
            $this->configResolutionSlaHours = $config->resolution_sla_hours;
            $this->configFirstResponseSlaHours = $config->first_response_sla_hours;
        }
        $this->slaConfigMode = !$this->slaConfigMode;
    }

    public function saveSlaConfig()
    {
        $this->validate([
            'configFirstReplyCutoff' => 'required|date_format:H:i',
            'configFollowupReminderDays' => 'required|integer|min:1|max:30',
            'configFollowupAutoCloseDays' => 'required|integer|min:1|max:30',
            'configResolutionSlaHours' => 'required|integer|min:1|max:720',
            'configFirstResponseSlaHours' => 'required|integer|min:1|max:720',
            'configBusinessStart' => 'required|date_format:H:i',
            'configBusinessEnd' => 'required|date_format:H:i',
        ]);

        $config = SlaConfiguration::first();
        if (!$config) {
            $config = new SlaConfiguration();
        }

        $config->fill([
            'first_reply_cutoff_time' => $this->configFirstReplyCutoff,
            'first_reply_enabled' => $this->configFirstReplyEnabled,
            'followup_reminder_days' => $this->configFollowupReminderDays,
            'followup_auto_close_days' => $this->configFollowupAutoCloseDays,
            'followup_enabled' => $this->configFollowupEnabled,
            'business_start_time' => $this->configBusinessStart,
            'business_end_time' => $this->configBusinessEnd,
            'resolution_sla_hours' => $this->configResolutionSlaHours,
            'first_response_sla_hours' => $this->configFirstResponseSlaHours,
            'updated_by' => auth()->id(),
        ]);
        $config->save();

        SlaConfiguration::clearCache();
        $this->slaConfigMode = false;

        Notification::make()
            ->title('SLA Configuration saved successfully')
            ->success()
            ->send();
    }

    public function getViewData(): array
    {
        $baseQuery = ImplementerTicket::with(['customer', 'implementerUser']);

        // Apply implementer filter
        if ($this->selectedImplementer) {
            $baseQuery->where('implementer_user_id', $this->selectedImplementer);
        }

        // Apply company filter
        if ($this->selectedCompany) {
            $baseQuery->where('customer_id', $this->selectedCompany);
        }

        $allTickets = $baseQuery->get();

        // Stat card counts
        $openCount = $allTickets->where('status', ImplementerTicketStatus::OPEN)->count();
        $pendingSupportCount = $allTickets->where('status', ImplementerTicketStatus::PENDING_SUPPORT)->count();
        $pendingRndCount = $allTickets->where('status', ImplementerTicketStatus::PENDING_RND)->count();
        $overdueCount = $allTickets->filter(fn ($t) => $t->isOverdue())->count();

        // SLA Performance
        $closedTickets = $allTickets->where('status', ImplementerTicketStatus::CLOSED);
        $totalClosed = $closedTickets->count();
        $resolvedWithinSla = $closedTickets->filter(fn ($t) => $t->wasResolvedWithinSla())->count();
        $complianceRate = $totalClosed > 0 ? round(($resolvedWithinSla / $totalClosed) * 100, 1) : 0;

        $avgResolutionHours = 0;
        if ($totalClosed > 0) {
            $totalHours = $closedTickets->sum(fn ($t) => $t->created_at->diffInMinutes($t->closed_at) / 60);
            $avgResolutionHours = round($totalHours / $totalClosed, 1);
        }

        $ticketsWithResponse = $allTickets->whereNotNull('first_responded_at')->count();
        $respondedWithinSla = $allTickets->filter(fn ($t) => $t->wasFirstResponseWithinSla())->count();
        $firstResponseRate = $ticketsWithResponse > 0 ? round(($respondedWithinSla / $ticketsWithResponse) * 100, 1) : 0;

        // Tab counts
        $pendingClientCount = $allTickets->where('status', ImplementerTicketStatus::PENDING_CLIENT)->count();
        $allTicketsCount = $allTickets->count();

        // Build filtered query for table
        $tableQuery = ImplementerTicket::with(['customer', 'implementerUser', 'mergedInto']);

        if ($this->selectedImplementer) {
            $tableQuery->where('implementer_user_id', $this->selectedImplementer);
        }

        if ($this->selectedCompany) {
            $tableQuery->where('customer_id', $this->selectedCompany);
        }

        if ($this->statusFilter) {
            if ($this->statusFilter === 'overdue') {
                $slaHours = SlaConfiguration::current()->resolution_sla_hours;
                $tableQuery->where('status', '!=', 'closed')
                    ->where(function ($q) use ($slaHours) {
                        $q->where('is_overdue', true)
                          ->orWhere('created_at', '<', now()->subHours($slaHours));
                    });
            } else {
                $tableQuery->where('status', $this->statusFilter);
            }
        } elseif ($this->activeTab === 'pending_client') {
            $tableQuery->where('status', 'pending_client');
        }

        if ($this->searchQuery) {
            $search = $this->searchQuery;
            $tableQuery->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('company_name', 'like', "%{$search}%")
                         ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        $totalFiltered = $tableQuery->count();
        $totalPages = max(1, ceil($totalFiltered / $this->perPage));
        $this->currentPage = min($this->currentPage, $totalPages);

        $tickets = $tableQuery
            ->orderBy('created_at', 'desc')
            ->skip(($this->currentPage - 1) * $this->perPage)
            ->take($this->perPage)
            ->get();

        // Implementer list for filter
        $implementers = User::whereHas('implementerTickets')
            ->orderBy('name')
            ->pluck('name', 'id');

        // Company list for filter
        $companies = Customer::whereHas('implementerTickets')
            ->whereNotNull('company_name')
            ->where('company_name', '!=', '')
            ->orderBy('company_name')
            ->pluck('company_name', 'id');

        // Customers for create drawer
        $customerQuery = Customer::whereNotNull('company_name')
            ->where('company_name', '!=', '');
        if ($this->customerSearch) {
            $customerQuery->where('company_name', 'like', '%' . $this->customerSearch . '%');
        }
        $customers = $customerQuery->orderBy('company_name')->limit(50)->get(['id', 'company_name', 'name']);

        // Load selected ticket for detail drawer
        $selectedTicket = null;
        if ($this->selectedTicketId) {
            $selectedTicket = ImplementerTicket::with(['customer', 'implementerUser', 'mergedInto', 'mergedFrom.replies.sender', 'replies' => function ($q) {
                $q->reorder('created_at', 'desc');
            }, 'replies.sender'])->find($this->selectedTicketId);
        }

        return [
            'openCount' => $openCount,
            'pendingSupportCount' => $pendingSupportCount,
            'pendingRndCount' => $pendingRndCount,
            'overdueCount' => $overdueCount,
            'complianceRate' => $complianceRate,
            'avgResolutionHours' => $avgResolutionHours,
            'firstResponseRate' => $firstResponseRate,
            'pendingClientCount' => $pendingClientCount,
            'allTicketsCount' => $allTicketsCount,
            'tickets' => $tickets,
            'totalFiltered' => $totalFiltered,
            'totalPages' => $totalPages,
            'currentPage' => $this->currentPage,
            'perPage' => $this->perPage,
            'implementers' => $implementers,
            'companies' => $companies,
            'customers' => $customers,
            'selectedTicket' => $selectedTicket,
            'slaConfig' => SlaConfiguration::current(),
        ];
    }

    private function sendHrEmailNotification(ImplementerTicket $ticket, string $action, string $actionByName = null)
    {
        $lead = $ticket->lead;
        if (!$lead || !$lead->companyDetail) {
            return;
        }

        $companyDetail = $lead->companyDetail;
        $emails = [];

        // Primary HR email
        if (!empty($companyDetail->email)) {
            $emails[] = $companyDetail->email;
        }

        // Secondary HR contacts (Available status only)
        $additionalPics = $companyDetail->additional_prospect_pic;
        if (is_string($additionalPics)) {
            $additionalPics = json_decode($additionalPics, true) ?? [];
        }
        foreach ($additionalPics ?? [] as $pic) {
            if (($pic['status'] ?? '') === 'Available' && !empty($pic['email'])) {
                $emails[] = $pic['email'];
            }
        }

        // Deduplicate and filter
        $emails = array_unique(array_filter($emails));
        if (empty($emails)) {
            return;
        }

        try {
            // Disable SSL peer verification for local dev SMTP
            $mailer = Mail::mailer();
            $transport = $mailer->getSymfonyTransport();
            if ($transport instanceof \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport) {
                $stream = $transport->getStream();
                if ($stream instanceof \Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream) {
                    $stream->setStreamOptions([
                        'ssl' => [
                            'allow_self_signed' => true,
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                        ],
                    ]);
                }
            }

            Mail::to($emails)->send(new ImplementerTicketHrNotification(
                $ticket->load(['lead.companyDetail', 'customer', 'implementerUser']),
                $action,
                $actionByName ?? auth()->user()->name
            ));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send HR email notification: ' . $e->getMessage(), [
                'ticket_id' => $ticket->id,
                'action' => $action,
                'emails' => $emails,
            ]);
        }
    }
}
