<?php

namespace App\Livewire;

use App\Enums\ImplementerTicketStatus;
use App\Models\ImplementerTicket;
use App\Models\ImplementerTicketReply;
use App\Notifications\ImplementerTicketNotification;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class CustomerImplementerThread extends Component
{
    use WithFileUploads;

    // View state
    public string $currentView = 'dashboard';
    public ?int $selectedTicketId = null;

    // Dashboard filters
    public string $search = '';
    public string $statusFilter = '';
    public string $categoryFilter = '';
    public string $moduleFilter = '';

    // Create ticket form
    public bool $showCreateModal = false;
    public string $newSubject = '';
    public string $newDescription = '';
    public string $newCategory = '';
    public string $newModule = '';
    public string $newPriority = 'medium';
    public $newAttachments = [];

    // Reply form
    public string $replyMessage = '';
    public $replyAttachments = [];

    public function getCustomer()
    {
        return auth('customer')->user();
    }

    public function getCanCreateTicketProperty(): bool
    {
        $customer = $this->getCustomer();

        if (!$customer || !$customer->lead_id) {
            return false;
        }

        return ImplementerTicket::where('lead_id', $customer->lead_id)->exists();
    }

    public function getTickets()
    {
        $customer = $this->getCustomer();

        if (!$customer || !$customer->lead_id) {
            return collect();
        }

        $query = ImplementerTicket::where('lead_id', $customer->lead_id)
            ->with(['customer', 'implementerUser', 'mergedInto', 'replies' => function ($q) {
                $q->where('is_internal_note', false);
            }])
            ->orderBy('created_at', 'desc');

        // Search filter
        if ($this->search) {
            $search = strtolower($this->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(ticket_number) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(subject) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(category) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(module) LIKE ?', ["%{$search}%"]);
            });
        }

        // Status filter
        if ($this->statusFilter) {
            if ($this->statusFilter === 'open') {
                $query->whereIn('status', ['open', 'pending_rnd']);
            } else {
                $query->where('status', $this->statusFilter);
            }
        }

        // Category filter
        if ($this->categoryFilter) {
            $query->whereRaw('LOWER(category) = ?', [strtolower($this->categoryFilter)]);
        }

        // Module filter
        if ($this->moduleFilter) {
            $query->whereRaw('LOWER(module) = ?', [strtolower($this->moduleFilter)]);
        }

        return $query->get();
    }

    public function getStatusCounts()
    {
        $customer = $this->getCustomer();

        if (!$customer || !$customer->lead_id) {
            return ['open' => 0, 'pending_support' => 0, 'pending_client' => 0, 'closed' => 0, 'total' => 0];
        }

        $tickets = ImplementerTicket::where('lead_id', $customer->lead_id)->get();

        return [
            'open' => $tickets->whereIn('status', [ImplementerTicketStatus::OPEN, ImplementerTicketStatus::PENDING_RND])->count(),
            'pending_support' => $tickets->where('status', ImplementerTicketStatus::PENDING_SUPPORT)->count(),
            'pending_client' => $tickets->where('status', ImplementerTicketStatus::PENDING_CLIENT)->count(),
            'closed' => $tickets->where('status', ImplementerTicketStatus::CLOSED)->count(),
            'total' => $tickets->count(),
        ];
    }

    public function getSelectedTicket()
    {
        if (!$this->selectedTicketId) {
            return null;
        }

        return ImplementerTicket::with([
            'customer',
            'implementerUser',
            'mergedInto',
            'replies' => function ($q) {
                $q->where('is_internal_note', false)->with('sender');
            },
        ])->find($this->selectedTicketId);
    }

    // View switching
    public function openTicketDetail($id)
    {
        $this->selectedTicketId = $id;
        $this->currentView = 'detail';
        $this->replyMessage = '';
        $this->replyAttachments = [];
    }

    #[On('openTicketFromNotification')]
    public function openTicketFromNotification($ticketId)
    {
        $this->openTicketDetail($ticketId);
    }

    public function backToDashboard()
    {
        $this->currentView = 'dashboard';
        $this->selectedTicketId = null;
    }

    // Filters
    public function filterByStatus($status)
    {
        $this->statusFilter = $this->statusFilter === $status ? '' : $status;
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->categoryFilter = '';
        $this->moduleFilter = '';
    }

    // Create ticket
    public function openCreateModal()
    {
        if (!$this->canCreateTicket) {
            return;
        }

        $this->showCreateModal = true;
        $this->resetCreateForm();
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetCreateForm();
    }

    private function resetCreateForm()
    {
        $this->newSubject = '';
        $this->newDescription = '';
        $this->newCategory = '';
        $this->newModule = '';
        $this->newPriority = 'medium';
        $this->newAttachments = [];
    }

    public function createTicket()
    {
        if (!$this->canCreateTicket) {
            $this->dispatch('notify', message: 'Your implementer hasn\'t sent the first session summary yet.');
            return;
        }

        $this->validate([
            'newSubject' => 'required|string|max:255',
            'newDescription' => 'required|string',
            'newCategory' => 'required|string',
            'newModule' => 'required|string',
            'newPriority' => 'required|in:low,medium,high,urgent',
        ]);

        $customer = $this->getCustomer();

        if (!$customer) {
            return;
        }

        // Handle attachments
        $attachmentPaths = [];
        if ($this->newAttachments) {
            foreach ($this->newAttachments as $file) {
                $attachmentPaths[] = $file->storeAs(
                    'implementer-tickets',
                    \App\Support\TicketAttachmentNamer::build($file),
                    'public'
                );
            }
        }

        // Resolve implementer
        $resolved = ImplementerTicket::resolveImplementerForCustomer($customer);

        $ticket = ImplementerTicket::create([
            'customer_id' => $customer->id,
            'implementer_user_id' => $resolved['user']->id ?? null,
            'implementer_name' => $resolved['name'] ?? null,
            'lead_id' => $resolved['lead_id'] ?? $customer->lead_id,
            'software_handover_id' => $resolved['software_handover_id'] ?? null,
            'subject' => $this->newSubject,
            'description' => strip_tags($this->newDescription, '<p><br><b><strong><i><em><u><a><ul><ol><li>'),
            'status' => 'open',
            'priority' => $this->newPriority,
            'category' => $this->newCategory,
            'module' => $this->newModule,
            'attachments' => !empty($attachmentPaths) ? $attachmentPaths : null,
        ]);

        // Create initial reply from description
        ImplementerTicketReply::create([
            'implementer_ticket_id' => $ticket->id,
            'sender_type' => 'App\Models\Customer',
            'sender_id' => $customer->id,
            'message' => $ticket->description,
            'attachments' => !empty($attachmentPaths) ? $attachmentPaths : null,
            'is_internal_note' => false,
        ]);

        // Notify implementer
        if ($ticket->implementerUser) {
            $ticket->implementerUser->notify(
                new ImplementerTicketNotification($ticket, 'created', $customer->name ?? 'A customer')
            );
        }

        $this->showCreateModal = false;
        $this->resetCreateForm();
        $this->openTicketDetail($ticket->id);
    }

    // Reply
    public function submitReply()
    {
        if (empty(trim(strip_tags($this->replyMessage)))) {
            return;
        }

        $customer = $this->getCustomer();
        $ticket = ImplementerTicket::find($this->selectedTicketId);

        if (!$customer || !$ticket) {
            return;
        }

        // Handle attachments
        $attachmentPaths = [];
        if ($this->replyAttachments) {
            foreach ($this->replyAttachments as $file) {
                $attachmentPaths[] = $file->storeAs(
                    'implementer-ticket-replies',
                    \App\Support\TicketAttachmentNamer::build($file),
                    'public'
                );
            }
        }

        ImplementerTicketReply::create([
            'implementer_ticket_id' => $ticket->id,
            'sender_type' => 'App\Models\Customer',
            'sender_id' => $customer->id,
            'message' => strip_tags($this->replyMessage, '<p><br><b><strong><i><em><u><a><ul><ol><li>'),
            'attachments' => !empty($attachmentPaths) ? $attachmentPaths : null,
            'is_internal_note' => false,
        ]);

        // Update status to open (revert on any customer reply, including closed tickets)
        $oldStatus = $ticket->status->value;
        $ticket->update(['status' => ImplementerTicketStatus::OPEN->value]);

        // Log status change
        activity('implementer_ticket')
            ->performedOn($ticket)
            ->causedBy(auth('customer')->user())
            ->withProperties(['old_status' => $oldStatus, 'new_status' => 'open', 'trigger' => 'customer_reply'])
            ->log('Status changed from ' . ucwords(str_replace('_', ' ', $oldStatus)) . ' to Open');

        // Notify implementer
        if ($ticket->implementerUser) {
            $ticket->implementerUser->notify(
                new ImplementerTicketNotification($ticket, 'replied_by_customer', $customer->name)
            );
        }

        $this->replyMessage = '';
        $this->replyAttachments = [];
        $this->js("document.querySelector('.cit-reply-editor')?.innerHTML = ''");
        $this->dispatch('reply-sent');
    }

    public function reopenTicket()
    {
        $customer = auth('customer')->user();
        if (!$customer) return;

        $ticket = ImplementerTicket::find($this->selectedTicketId);
        if (!$ticket || $ticket->customer_id !== $customer->id) return;

        $oldStatus = $ticket->status->value;
        $ticket->update(['status' => ImplementerTicketStatus::OPEN->value]);

        // Log status change
        activity('implementer_ticket')
            ->performedOn($ticket)
            ->causedBy($customer)
            ->withProperties(['old_status' => $oldStatus, 'new_status' => 'open', 'trigger' => 'customer_reopen'])
            ->log('Status changed from ' . ucwords(str_replace('_', ' ', $oldStatus)) . ' to Open');

        // Notify implementer
        if ($ticket->implementerUser) {
            $ticket->implementerUser->notify(
                new ImplementerTicketNotification($ticket, 'status_changed', $customer->name)
            );
        }
    }

    public function removeReplyAttachment($index)
    {
        $attachments = collect($this->replyAttachments)->values()->toArray();
        unset($attachments[$index]);
        $this->replyAttachments = array_values($attachments);
    }

    public function removeNewAttachment($index)
    {
        $attachments = collect($this->newAttachments)->values()->toArray();
        unset($attachments[$index]);
        $this->newAttachments = array_values($attachments);
    }

    public function getFollowupCount(): int
    {
        if (!$this->selectedTicketId) {
            return 0;
        }

        return ImplementerTicketReply::query()
            ->where('implementer_ticket_id', $this->selectedTicketId)
            ->where('is_internal_note', false)
            ->whereRaw('LOWER(thread_label) LIKE ?', ['follow%'])
            ->count();
    }

    public function render()
    {
        return view('livewire.customer-implementer-thread', [
            'tickets' => $this->getTickets(),
            'statusCounts' => $this->getStatusCounts(),
            'selectedTicket' => $this->getSelectedTicket(),
            'followupCount' => $this->getFollowupCount(),
        ]);
    }
}
