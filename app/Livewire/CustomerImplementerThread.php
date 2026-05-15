<?php

namespace App\Livewire;

use App\Enums\ImplementerTicketStatus;
use App\Models\ImplementerTicket;
use App\Models\ImplementerTicketReply;
use App\Notifications\ImplementerTicketNotification;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class CustomerImplementerThread extends Component
{
    use WithFileUploads;

    public const STATUS_MAP = [
        'open'           => ['bg' => '#EFF6FF', 'text' => '#1D4ED8', 'dot' => '#3B82F6', 'label' => 'Open'],
        'awaiting_reply' => ['bg' => '#FFFBEB', 'text' => '#B45309', 'dot' => '#F59E0B', 'label' => 'Awaiting Reply'],
        'in_progress'    => ['bg' => '#F5F3FF', 'text' => '#6D28D9', 'dot' => '#8B5CF6', 'label' => 'In Progress'],
        'closed'         => ['bg' => '#ECFDF5', 'text' => '#047857', 'dot' => '#10B981', 'label' => 'Closed'],
    ];

    // View state
    public string $currentView = 'dashboard';
    public ?int $selectedTicketId = null;

    // Dashboard filters
    public string $search = '';
    public array $statusFilter = [];
    public array $categoryFilter = [];
    public array $moduleFilter = [];

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
            }]);

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

        // Category filter (multi-select)
        if (!empty($this->categoryFilter)) {
            $query->whereIn('category', $this->categoryFilter);
        }

        // Module filter (multi-select)
        if (!empty($this->moduleFilter)) {
            $query->whereIn('module', $this->moduleFilter);
        }

        $tickets = $query->get();

        // Status filter (multi-select) is applied on the derived customer-facing status,
        // which crosses SQL boundaries (DB 'open' may map to either 'open' or
        // 'awaiting_reply' depending on last reply sender).
        if (!empty($this->statusFilter)) {
            $tickets = $tickets->filter(
                fn ($t) => in_array($t->customerFacingStatus(), $this->statusFilter, true)
            )->values();
        }

        // Order by latest activity: newest non-internal reply, else ticket updated_at.
        // The `replies` relation is ASC by created_at, so ->last() is the most recent.
        return $tickets->sortByDesc(function ($t) {
            $last = $t->replies->last();
            return $last ? $last->created_at : $t->updated_at;
        })->values();
    }

    public function getStatusCounts(): array
    {
        $customer = $this->getCustomer();
        $counts = ['open' => 0, 'awaiting_reply' => 0, 'in_progress' => 0, 'closed' => 0, 'total' => 0];

        if (!$customer || !$customer->lead_id) {
            return $counts;
        }

        $tickets = ImplementerTicket::where('lead_id', $customer->lead_id)
            ->whereNull('merged_into_ticket_id')
            ->with(['replies' => fn ($q) => $q->where('is_internal_note', false)->orderBy('created_at', 'asc')])
            ->get();

        $counts['total'] = $tickets->count();
        foreach ($tickets as $ticket) {
            $key = $ticket->customerFacingStatus();
            $counts[$key] = ($counts[$key] ?? 0) + 1;
        }
        return $counts;
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
                $q->where('is_internal_note', false)
                  ->with('sender')
                  ->reorder('created_at', 'desc');
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
    public function toggleFilter(string $type, string $value): void
    {
        $prop = match ($type) {
            'status'   => 'statusFilter',
            'category' => 'categoryFilter',
            'module'   => 'moduleFilter',
            default    => null,
        };
        if ($prop === null) {
            return;
        }

        $this->$prop = in_array($value, $this->$prop, true)
            ? array_values(array_diff($this->$prop, [$value]))
            : [...$this->$prop, $value];
    }

    public function filterByStatus($status): void
    {
        $this->toggleFilter('status', $status);
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->statusFilter = [];
        $this->categoryFilter = [];
        $this->moduleFilter = [];
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
            'newCategory' => ['required', 'string', Rule::notIn(['Add on License', 'Add on Module', 'Add on Device'])],
            'newModule' => 'required|string',
            'newPriority' => 'required|in:low,medium,high,urgent',
            'newAttachments' => 'nullable|array',
            'newAttachments.*' => 'file|max:10240|mimes:doc,docx,xls,xlsx,pdf,png,jpg,jpeg',
        ], [
            'newCategory.not_in' => 'For Add-on requests, please contact your dedicated Salesperson.',
            'newAttachments.*.mimes' => 'Only Word, Excel, PDF, and image files are accepted.',
            'newAttachments.*.max'   => 'Each file must be 10MB or smaller.',
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
            'statusMap' => self::STATUS_MAP,
        ]);
    }
}
