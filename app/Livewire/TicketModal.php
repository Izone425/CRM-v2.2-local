<?php

namespace App\Livewire;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketComment;
use App\Models\TicketLog;
use Livewire\Component;
use Livewire\WithFileUploads;
use Filament\Forms\Components\RichEditor;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TicketModal extends Component implements HasForms
{
    use InteractsWithForms;
    use WithFileUploads;

    public $selectedTicket = null;
    public $showTicketModal = false;
    public $newComment = '';
    public $attachments = [];

    // Reopen modal properties
    public $showReopenModal = false;
    public $reopenComment = '';
    public $reopenAttachments = [];

    // Image preview modal properties
    public $showImageModal = false;
    public $selectedImageUrl = '';
    public $commentSort = 'desc';

    protected $listeners = [
        'openTicketModal' => 'viewTicket',
        'closeTicketModal' => 'closeTicketModal',
        'ticket-status-updated' => '$refresh',
    ];

    public function render()
    {
        return view('livewire.ticket-modal');
    }

    public function viewTicket($ticketId): void
    {
        try {
            $this->selectedTicket = Ticket::with([
                'comments',
                'logs',
                'priority',
                'product',
                'module',
                'requestor',
                'attachments',
                'attachments.uploader',
            ])->find($ticketId);

            if ($this->selectedTicket) {
                $this->showTicketModal = true;
            }
        } catch (\Exception $e) {
            Log::error('Error viewing ticket: ' . $e->getMessage());
            $this->showTicketModal = false;
        }
    }

    public function closeTicketModal(): void
    {
        $this->showTicketModal = false;
        $this->selectedTicket = null;
        $this->newComment = '';
        $this->attachments = [];
        $this->closeReopenModal();
        $this->closeImageModal();

        // Dispatch event to parent components
        $this->dispatch('ticketModalClosed');
    }

    public function openImageModal($imageUrl): void
    {
        $this->selectedImageUrl = $imageUrl;
        $this->showImageModal = true;
    }

    public function closeImageModal(): void
    {
        $this->showImageModal = false;
        $this->selectedImageUrl = '';
    }

    public function openReopenModal($ticketId = null): void
    {
        if ($ticketId) {
            $this->selectedTicket = Ticket::with([
                'comments',
                'logs',
                'priority',
                'product',
                'module',
                'requestor',
                'attachments',
                'attachments.uploader',
            ])->find($ticketId);
        }

        $this->reopenComment = '';
        $this->reopenAttachments = [];
        $this->showReopenModal = true;
    }

    public function closeReopenModal(): void
    {
        $this->showReopenModal = false;
        $this->reopenComment = '';
        $this->reopenAttachments = [];
    }

    public function addComment(): void
    {
        if (empty(trim(strip_tags($this->newComment))) || !$this->selectedTicket) {
            return;
        }

        try {
            $userId = $this->getTicketSystemUserId();

            TicketComment::create([
                'ticket_id' => $this->selectedTicket->id,
                'user_id' => $userId,
                'comment' => $this->newComment,
                'created_at' => now()->subHours(8),
                'updated_at' => now()->subHours(8),
            ]);

            $this->newComment = '';
            $this->selectedTicket->refresh();
            $this->selectedTicket->load('comments');

            Notification::make()
                ->title('Comment Added')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Log::error('Error adding comment: ' . $e->getMessage());

            Notification::make()
                ->title('Error')
                ->danger()
                ->body('Failed to add comment')
                ->send();
        }
    }

    public function uploadAttachments(): void
    {
        $this->validate([
            'attachments.*' => 'file|max:10240',
        ]);

        if (empty($this->attachments) || !$this->selectedTicket) {
            Notification::make()
                ->title('No files selected')
                ->warning()
                ->send();
            return;
        }

        try {
            $userId = $this->getTicketSystemUserId();

            foreach ($this->attachments as $file) {
                $originalFilename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();

                $storedFilename = time() . '_' . Str::random(10) . '_' .
                                Str::slug(pathinfo($originalFilename, PATHINFO_FILENAME)) .
                                '.' . $extension;

                $path = $file->storeAs(
                    'ticket_attachments/' . date('Y/m/d'),
                    $storedFilename,
                    's3-ticketing'
                );

                $fileHash = hash_file('md5', $file->getRealPath());

                TicketAttachment::create([
                    'ticket_id' => $this->selectedTicket->id,
                    'original_filename' => $originalFilename,
                    'stored_filename' => $storedFilename,
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'file_hash' => $fileHash,
                    'uploaded_by' => $userId,
                    'created_at' => now()->subHours(8),
                    'updated_at' => now()->subHours(8),
                ]);
            }

            $this->attachments = [];
            $this->selectedTicket->refresh();
            $this->selectedTicket->load('attachments');

            Notification::make()
                ->title('Files Uploaded')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Log::error('Error uploading attachments: ' . $e->getMessage());

            Notification::make()
                ->title('Upload Failed')
                ->danger()
                ->body('Failed to upload files: ' . $e->getMessage())
                ->send();
        }
    }

    public function updateTicketStatus($ticketId, string $newStatus): void
    {
        try {
            $ticket = Ticket::findOrFail($ticketId);
            $userId = $this->getTicketSystemUserId();
            $ticketSystemUser = $this->getTicketSystemUser();
            $oldStatus = $ticket->status;

            // If changing to Closed status, also mark as passed
            if (in_array($newStatus, ['Closed', 'Closed System Configuration'])) {
                $ticket->update([
                    'status' => $newStatus,
                    'isPassed' => 1,
                    'passed_at' => now()->subHours(8),
                ]);
            } else {
                $ticket->update(['status' => $newStatus]);
            }

            // Create ticket log entry
            $changeReason = in_array($newStatus, ['Closed', 'Closed System Configuration'])
                ? 'Ticket marked as passed via status change'
                : null;

            TicketLog::create([
                'ticket_id' => $ticket->id,
                'old_value' => $oldStatus,
                'new_value' => $newStatus,
                'action' => "Changed status from '{$oldStatus}' to '{$newStatus}' for ticket {$ticket->ticket_id}.",
                'field_name' => 'status',
                'change_reason' => $changeReason,
                'updated_by' => $userId,
                'user_name' => $ticketSystemUser?->name ?? 'HRcrm User',
                'user_role' => $ticketSystemUser?->role ?? 'Support Staff',
                'change_type' => 'status_change',
                'source' => 'ticket_modal',
                'created_at' => now()->subHours(8),
                'updated_at' => now()->subHours(8),
            ]);

            // Refresh the selected ticket
            $this->selectedTicket = Ticket::with(['logs', 'comments', 'attachments.uploader', 'priority', 'product', 'module', 'requestor'])
                ->find($ticket->id);

            $statusMessage = in_array($newStatus, ['Closed', 'Closed System Configuration'])
                ? "Ticket {$ticket->ticket_id} marked as passed and status changed from {$oldStatus} to {$newStatus}"
                : "Ticket {$ticket->ticket_id} status changed from {$oldStatus} to {$newStatus}";

            Notification::make()
                ->title('Status Updated')
                ->success()
                ->body($statusMessage)
                ->send();

            // Dispatch event to refresh parent tables
            $this->dispatch('ticket-status-updated');

        } catch (\Exception $e) {
            Log::error('Error updating ticket status: ' . $e->getMessage());

            Notification::make()
                ->title('Error')
                ->danger()
                ->body('Failed to update ticket status: ' . $e->getMessage())
                ->send();
        }
    }

    public function markAsPassed(int $ticketId): void
    {
        try {
            $ticket = Ticket::find($ticketId);

            if ($ticket) {
                $userId = $this->getTicketSystemUserId();
                $ticketSystemUser = $this->getTicketSystemUser();
                $oldStatus = $ticket->status;

                $ticket->update([
                    'status' => 'Closed',
                    'isPassed' => 1,
                    'passed_at' => now()->subHours(8),
                ]);

                TicketLog::create([
                    'ticket_id' => $ticket->id,
                    'old_value' => $oldStatus,
                    'new_value' => 'Closed',
                    'action' => "Marked ticket {$ticket->ticket_id} as passed - changed status from '{$oldStatus}' to 'Closed'.",
                    'field_name' => 'status',
                    'change_reason' => 'Ticket marked as passed',
                    'updated_by' => $userId,
                    'user_name' => $ticketSystemUser?->name ?? 'HRcrm User',
                    'user_role' => $ticketSystemUser?->role ?? 'Internal Staff',
                    'change_type' => 'status_change',
                    'source' => 'ticket_modal_pass_action',
                    'created_at' => now()->subHours(8),
                    'updated_at' => now()->subHours(8),
                ]);

                Notification::make()
                    ->title('Ticket Marked as Passed')
                    ->body("Ticket {$ticket->ticket_id} has been marked as passed and closed")
                    ->success()
                    ->send();

                $this->dispatch('ticket-status-updated');
            }
        } catch (\Exception $e) {
            Log::error('Error marking ticket as passed: ' . $e->getMessage());

            Notification::make()
                ->title('Error')
                ->body('Failed to mark ticket as passed')
                ->danger()
                ->send();
        }
    }

    public function markAsFailed(int $ticketId): void
    {
        try {
            $ticket = Ticket::find($ticketId);

            if ($ticket) {
                $userId = $this->getTicketSystemUserId();
                $ticketSystemUser = $this->getTicketSystemUser();
                $oldStatus = $ticket->status;

                $ticket->update([
                    'isPassed' => 0,
                    'passed_at' => now()->subHours(8),
                    'status' => 'Reopen',
                ]);

                TicketLog::create([
                    'ticket_id' => $ticket->id,
                    'old_value' => $oldStatus,
                    'new_value' => 'Reopen',
                    'updated_by' => $userId,
                    'user_name' => $ticketSystemUser?->name ?? 'HRcrm User',
                    'user_role' => $ticketSystemUser?->role ?? 'Internal Staff',
                    'change_type' => 'status_change',
                    'source' => 'ticket_modal',
                    'remarks' => 'Ticket marked as failed and reopened',
                    'created_at' => now()->subHours(8),
                    'updated_at' => now()->subHours(8),
                ]);

                Notification::make()
                    ->title('Ticket Marked as Failed')
                    ->body("Ticket status changed to Reopen")
                    ->warning()
                    ->send();

                $this->dispatch('ticket-status-updated');
            }
        } catch (\Exception $e) {
            Log::error('Error marking ticket as failed: ' . $e->getMessage());

            Notification::make()
                ->title('Error')
                ->body('Failed to update ticket status')
                ->danger()
                ->send();
        }
    }

    public function reopenTicket(): void
    {
        try {
            $ticket = Ticket::find($this->selectedTicket->id);
            if (!$ticket) {
                throw new \Exception('Ticket not found');
            }

            $userId = $this->getTicketSystemUserId();
            $ticketSystemUser = $this->getTicketSystemUser();

            // Handle file uploads
            $uploadedImageUrls = [];
            if (!empty($this->reopenAttachments)) {
                foreach ($this->reopenAttachments as $file) {
                    try {
                        if ($file && $file instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                            $originalFilename = $file->getClientOriginalName();
                            $extension = $file->getClientOriginalExtension();

                            $storedFilename = time() . '_' . Str::random(10) . '_' .
                                            Str::slug(pathinfo($originalFilename, PATHINFO_FILENAME)) .
                                            '.' . $extension;

                            $path = $file->storeAs(
                                'ticket_attachments/' . date('Y/m/d'),
                                $storedFilename,
                                's3-ticketing'
                            );

                            $fileHash = hash_file('md5', $file->getRealPath());

                            TicketAttachment::create([
                                'ticket_id' => $ticket->id,
                                'original_filename' => $originalFilename,
                                'stored_filename' => $storedFilename,
                                'file_path' => $path,
                                'file_size' => $file->getSize(),
                                'mime_type' => $file->getMimeType(),
                                'file_hash' => $fileHash,
                                'uploaded_by' => $userId,
                                'created_at' => now()->subHours(8),
                                'updated_at' => now()->subHours(8),
                            ]);

                            if (str_starts_with($file->getMimeType(), 'image/')) {
                                $disk = Storage::disk('s3-ticketing');
                                $fileUrl = $disk->url($path);
                                $uploadedImageUrls[] = [
                                    'url' => $fileUrl,
                                    'filename' => $originalFilename
                                ];
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error('Error uploading reopen attachment: ' . $e->getMessage());
                    }
                }
            }

            // Create HTML comment
            $htmlComment = '';
            if (!empty(trim($this->reopenComment))) {
                $htmlComment .= '<p>' . nl2br(e(trim($this->reopenComment))) . '</p>';
            }

            foreach ($uploadedImageUrls as $image) {
                $htmlComment .= '<p><img src="' . $image['url'] . '" alt="' . e($image['filename']) . '" style="max-width: 100%; height: auto;" /></p>';
            }

            // Update ticket
            $oldStatus = $ticket->status;
            $ticket->status = 'Reopen';
            if (!empty($htmlComment)) {
                $ticket->reopen_reason = $htmlComment;
            } elseif (!empty(trim($this->reopenComment))) {
                $ticket->reopen_reason = trim($this->reopenComment);
            }
            $ticket->save();

            // Create comment
            if (!empty($htmlComment)) {
                TicketComment::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $userId,
                    'comment' => $htmlComment,
                    'created_at' => now()->subHours(8),
                    'updated_at' => now()->subHours(8),
                ]);
            } elseif (!empty(trim($this->reopenComment))) {
                TicketComment::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $userId,
                    'comment' => trim($this->reopenComment),
                    'created_at' => now()->subHours(8),
                    'updated_at' => now()->subHours(8),
                ]);
            }

            // Log the action
            TicketLog::create([
                'ticket_id' => $ticket->id,
                'old_value' => $oldStatus,
                'new_value' => 'Reopen',
                'action' => "Reopened ticket {$ticket->ticket_id} from '{$oldStatus}' to 'Reopen'.",
                'field_name' => 'status',
                'change_reason' => !empty($htmlComment) ? $htmlComment : (!empty(trim($this->reopenComment)) ? trim($this->reopenComment) : 'Ticket reopened without comment'),
                'updated_by' => $userId,
                'user_name' => $ticketSystemUser?->name ?? 'HRcrm User',
                'user_role' => $ticketSystemUser?->role ?? 'Support Staff',
                'change_type' => 'status_change',
                'source' => 'ticket_modal_reopen',
                'created_at' => now()->subHours(8),
                'updated_at' => now()->subHours(8),
            ]);

            $this->closeReopenModal();
            $this->selectedTicket->status = 'Reopen';

            $this->dispatch('ticket-status-updated');

            Notification::make()
                ->title('Success')
                ->success()
                ->body('Ticket has been successfully reopened')
                ->send();

        } catch (\Exception $e) {
            Log::error('Error reopening ticket: ' . $e->getMessage());

            Notification::make()
                ->title('Error')
                ->danger()
                ->body('Failed to reopen ticket: ' . $e->getMessage())
                ->send();
        }
    }

    protected function getFormSchema(): array
    {
        return [
            RichEditor::make('newComment')
                ->label('')
                ->placeholder('Add a comment...')
                ->required()
                ->toolbarButtons([
                    'attachFiles',
                    'bold',
                    'italic',
                    'underline',
                    'strike',
                    'bulletList',
                    'orderedList',
                    'h2',
                    'h3',
                    'link',
                    'undo',
                    'redo',
                ])
                ->disableToolbarButtons([
                    'codeBlock',
                ])
                ->fileAttachmentsDisk('s3-ticketing')
                ->fileAttachmentsDirectory('ticket_desc_images')
                ->fileAttachmentsVisibility('private')
        ];
    }

    // Helper methods
    private function getTicketSystemUserId(): int
    {
        $ticketSystemUser = $this->getTicketSystemUser();
        return $ticketSystemUser?->id ?? 22;
    }

    private function getTicketSystemUser(): ?object
    {
        $authUser = auth()->user();
        if (!$authUser) {
            return null;
        }

        return DB::connection('ticketingsystem_live')
            ->table('users')
            ->where('email', $authUser->email)
            ->first();
    }

    private function isImageFile($attachment): bool
    {
        if (str_starts_with($attachment->mime_type, 'image/')) {
            return true;
        }
        $extension = strtolower(pathinfo($attachment->original_filename, PATHINFO_EXTENSION));
        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp']);
    }
}
