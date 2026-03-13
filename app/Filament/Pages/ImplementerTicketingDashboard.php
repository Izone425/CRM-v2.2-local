<?php

namespace App\Filament\Pages;

use App\Enums\ImplementerTicketStatus;
use App\Models\Customer;
use App\Models\ImplementerTicket;
use App\Models\ImplementerTicketReply;
use App\Models\User;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;
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
    public $activeTab = 'pending_client';
    public $statusFilter = '';
    public $showSlaPolicy = false;

    // Pagination
    public $perPage = 15;
    public $currentPage = 1;

    // Ticket detail drawer
    public $showTicketDetail = false;
    public $selectedTicketId = null;

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

    protected $emailTemplates = [
        'First Response' => [
            'subject' => "Re: Your Support Request - We're On It!",
            'body' => "Dear [Client Name],\n\nThank you for reaching out to our support team. We have received your request and our team is currently reviewing the details.\n\nWe aim to provide you with a comprehensive response within [X hours/days] as per our SLA agreement. If you have any additional information that might help us resolve this faster, please feel free to share it.\n\nBest regards,\n[Implementer Name]\nSupport Team",
        ],
        'Require More Time' => [
            'subject' => 'Update: Additional Time Required for Your Request',
            'body' => "Dear [Client Name],\n\nWe are writing to update you on the status of your support ticket [Ticket ID].\n\nAfter thorough review, our team requires additional time to properly address your request. This will ensure we provide you with the best possible solution. We now estimate completion by [New Date/Time].\n\nWe appreciate your patience and understanding. Should you have any questions, please don't hesitate to reach out.\n\nBest regards,\n[Implementer Name]\nSupport Team",
        ],
        'R&D Escalation' => [
            'subject' => 'Escalation Notice: Your Ticket Requires R&D Investigation',
            'body' => "Dear [Client Name],\n\nThank you for your patience. Your support ticket [Ticket ID] has been escalated to our Research & Development team for further investigation.\n\nThis escalation allows our technical experts to conduct a deeper analysis and develop a comprehensive solution. We will keep you updated on the progress and provide an estimated resolution timeline shortly.\n\nWe appreciate your understanding as we work to resolve this matter.\n\nBest regards,\n[Implementer Name]\nSupport Team",
        ],
    ];

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

    public function updatedNewTicketCategory()
    {
        if ($this->newTicketCategory === 'License Activation') {
            $this->newTicketStatus = 'closed';
        } elseif ($this->newTicketStatus === 'closed') {
            $this->newTicketStatus = 'open';
        }
    }

    public function applyEmailTemplate($template)
    {
        $this->newTicketEmailTemplate = $template;

        if (isset($this->emailTemplates[$template])) {
            $this->newTicketEmailSubject = $this->emailTemplates[$template]['subject'];
            // Convert plain text template to HTML paragraphs for rich text editor
            $body = $this->emailTemplates[$template]['body'];
            $paragraphs = array_filter(explode("\n\n", $body), fn($p) => trim($p) !== '');
            $this->newTicketEmailBody = implode('', array_map(fn($p) => '<p>' . nl2br(e(trim($p))) . '</p>', $paragraphs));
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
            'newTicketCategory' => 'required|string',
            'newTicketModule' => 'required|string',
            'newTicketStatus' => 'required|string',
            'newTicketEmailSubject' => 'required|string|max:255',
            'newTicketEmailBody' => 'required|string',
            'ticketAttachments.*' => 'nullable|file|max:10240',
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

        $ticket = ImplementerTicket::create([
            'customer_id' => $customer->id,
            'implementer_user_id' => $implementerData ? $implementerData['user']->id : auth()->id(),
            'implementer_name' => $implementerData ? $implementerData['name'] : auth()->user()->name,
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

        // Create the first reply from email body
        if ($ticket && $this->newTicketEmailBody) {
            ImplementerTicketReply::create([
                'implementer_ticket_id' => $ticket->id,
                'sender_type' => 'App\\Models\\User',
                'sender_id' => auth()->id(),
                'message' => $this->newTicketEmailBody,
                'is_internal_note' => false,
            ]);

            // Set first responded at
            $ticket->update(['first_responded_at' => now()]);
        }

        $this->showCreateDrawer = false;
        $this->resetCreateForm();

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

        Notification::make()
            ->title('Status updated')
            ->body("Ticket status changed to " . ImplementerTicketStatus::from($status)->label())
            ->success()
            ->send();
    }

    public function submitReply()
    {
        if (empty(trim($this->replyMessage))) {
            Notification::make()
                ->title('Reply cannot be empty')
                ->danger()
                ->send();
            return;
        }

        $ticket = ImplementerTicket::find($this->selectedTicketId);
        if (!$ticket) return;

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
            'is_internal_note' => $this->isInternalNote,
        ]);

        // Update ticket status and first response if not internal note
        $wasInternalNote = $this->isInternalNote;
        if (!$wasInternalNote) {
            $ticket->status = 'pending_client';
            if (!$ticket->first_responded_at) {
                $ticket->first_responded_at = now();
            }
            $ticket->save();
        }

        $this->replyMessage = '';
        $this->replyAttachments = [];
        $this->isInternalNote = false;
        $this->replyCc = '';
        $this->replyBcc = '';
        $this->replyEmailTemplate = '';
        $this->dispatch('replyEditorReset');

        Notification::make()
            ->title($wasInternalNote ? 'Internal note added' : 'Reply sent successfully')
            ->success()
            ->send();
    }

    public function applyReplyTemplate($template)
    {
        $this->replyEmailTemplate = $template;

        if (isset($this->emailTemplates[$template])) {
            $body = $this->emailTemplates[$template]['body'];
            $paragraphs = array_filter(explode("\n\n", $body), fn($p) => trim($p) !== '');
            $this->replyMessage = implode('', array_map(fn($p) => '<p>' . nl2br(e(trim($p))) . '</p>', $paragraphs));
            $this->dispatch('replyTemplateApplied');
        }
    }

    public function getViewData(): array
    {
        $baseQuery = ImplementerTicket::with(['customer', 'implementerUser']);

        // Apply implementer filter
        if ($this->selectedImplementer) {
            $baseQuery->where('implementer_user_id', $this->selectedImplementer);
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
        $tableQuery = ImplementerTicket::with(['customer', 'implementerUser']);

        if ($this->selectedImplementer) {
            $tableQuery->where('implementer_user_id', $this->selectedImplementer);
        }

        if ($this->statusFilter) {
            if ($this->statusFilter === 'overdue') {
                $tableQuery->where('status', '!=', 'closed')
                    ->where('created_at', '<', now()->subHours(ImplementerTicket::SLA_HOURS));
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
            $selectedTicket = ImplementerTicket::with(['customer', 'implementerUser', 'replies' => function ($q) {
                $q->orderBy('created_at', 'desc');
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
            'customers' => $customers,
            'selectedTicket' => $selectedTicket,
        ];
    }
}
