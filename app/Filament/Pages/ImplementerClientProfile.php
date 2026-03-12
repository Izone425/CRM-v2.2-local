<?php

namespace App\Filament\Pages;

use App\Enums\ImplementerTicketStatus;
use App\Models\Customer;
use App\Models\ImplementerTicket;
use App\Models\ImplementerTicketReply;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class ImplementerClientProfile extends Page
{
    use WithFileUploads;

    protected static string $view = 'filament.pages.implementer-client-profile';
    protected static ?string $title = '';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'implementer-client-profile/{customerId}';

    public $customerId;

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

    protected $emailTemplates = [
        'First Response' => [
            'subject' => "Re: Your Support Request - We're On It!",
            'body' => "Dear [Client Name],\n\nThank you for reaching out to us. We have received your support request and our team is currently reviewing it.\n\nWe want to assure you that your issue is important to us and we are working diligently to resolve it as quickly as possible.\n\nBest regards,\nSupport Team",
        ],
        'Require More Time' => [
            'subject' => 'Update: Additional Time Required for Your Request',
            'body' => "Dear [Client Name],\n\nWe are writing to update you on the status of your support ticket [Ticket ID].\n\nAfter thorough review, our team requires additional time to properly address your request. This will ensure we provide you with the best possible solution. We now expect to have a resolution within [timeframe].\n\nThank you for your patience and understanding.\n\nBest regards,\nSupport Team",
        ],
        'R&D Escalation' => [
            'subject' => 'Escalation Notice: Your Ticket Requires R&D Investigation',
            'body' => "Dear [Client Name],\n\nWe are writing to inform you that your support ticket [Ticket ID] has been escalated to our Research & Development team for further investigation.\n\nThis escalation ensures that our most specialized engineers will be reviewing your case. While this may extend the resolution timeline, it is necessary to provide you with the most thorough and effective solution.\n\nBest regards,\nSupport Team",
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

    public function mount($customerId)
    {
        $this->customerId = $customerId;

        $customer = Customer::find($customerId);
        if (!$customer) {
            abort(404);
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

    public function removeReplyAttachment($index)
    {
        $attachments = $this->replyAttachments;
        unset($attachments[$index]);
        $this->replyAttachments = array_values($attachments);
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
        $customer = Customer::find($this->customerId);

        $tickets = ImplementerTicket::with(['customer', 'implementerUser'])
            ->where('customer_id', $this->customerId)
            ->orderBy('created_at', 'desc')
            ->get();

        $selectedTicket = null;
        if ($this->selectedTicketId) {
            $selectedTicket = ImplementerTicket::with([
                'customer',
                'implementerUser',
                'replies' => function ($q) {
                    $q->orderBy('created_at', 'asc');
                },
                'replies.sender',
            ])->find($this->selectedTicketId);
        }

        return [
            'customer' => $customer,
            'tickets' => $tickets,
            'ticketCount' => $tickets->count(),
            'selectedTicket' => $selectedTicket,
        ];
    }
}
