<?php

namespace App\Filament\Pages;

use App\Models\EmailTemplate;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ImplementerThreadEmailTemplate extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static string $view = 'filament.pages.implementer-thread-email-template';
    protected static ?string $title = '';
    protected static bool $shouldRegisterNavigation = false;

    // Template list
    public $templates = [];

    // Modal state
    public $showModal = false;
    public $editingTemplateId = null;

    // Form fields
    public $templateName = '';
    public $templateSubject = '';
    public $templateCategory = '';
    public $templateContent = '';

    // Delete confirmation
    public $showDeleteConfirm = false;
    public $deletingTemplateId = null;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user || !($user instanceof User)) {
            return false;
        }
        return in_array($user->role_id, [1, 3]);
    }

    public function mount(): void
    {
        $this->loadTemplates();
    }

    public function loadTemplates(): void
    {
        $this->templates = EmailTemplate::implementerThread()
            ->with('creator')
            ->latest()
            ->get()
            ->toArray();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal($id): void
    {
        $template = EmailTemplate::implementerThread()->find($id);
        if (!$template) return;

        $this->editingTemplateId = $id;
        $this->templateName = $template->name;
        $this->templateSubject = $template->subject;
        $this->templateCategory = $template->category ?? '';
        $this->templateContent = $template->content;
        $this->showModal = true;
        $this->dispatch('editTemplateLoaded');
    }

    public function saveTemplate(): void
    {
        $this->validate([
            'templateName' => 'required|string|max:255',
            'templateSubject' => 'required|string|max:255',
            'templateCategory' => 'nullable|string|max:100',
            'templateContent' => 'required|string',
        ]);

        if ($this->editingTemplateId) {
            $template = EmailTemplate::implementerThread()->find($this->editingTemplateId);
            if ($template) {
                $template->update([
                    'name' => $this->templateName,
                    'subject' => $this->templateSubject,
                    'content' => $this->templateContent,
                    'category' => $this->templateCategory ?: null,
                ]);
            }
        } else {
            EmailTemplate::create([
                'name' => $this->templateName,
                'subject' => $this->templateSubject,
                'content' => $this->templateContent,
                'type' => 'implementer_thread',
                'category' => $this->templateCategory ?: null,
                'created_by' => auth()->id(),
            ]);
        }

        $isEditing = (bool) $this->editingTemplateId;

        $this->closeModal();
        $this->loadTemplates();

        Notification::make()
            ->title($isEditing ? 'Template updated' : 'Template created')
            ->success()
            ->send();
    }

    public function duplicateTemplate($id): void
    {
        $template = EmailTemplate::implementerThread()->find($id);
        if (!$template) return;

        $this->editingTemplateId = null;
        $this->templateName = $template->name . ' (Copy)';
        $this->templateSubject = $template->subject;
        $this->templateCategory = $template->category ?? '';
        $this->templateContent = $template->content;
        $this->showModal = true;
        $this->dispatch('editTemplateLoaded');
    }

    public function confirmDelete($id): void
    {
        $this->deletingTemplateId = $id;
        $this->showDeleteConfirm = true;
    }

    public function deleteTemplate(): void
    {
        if ($this->deletingTemplateId) {
            EmailTemplate::where('id', $this->deletingTemplateId)
                ->where('type', 'implementer_thread')
                ->delete();

            $this->showDeleteConfirm = false;
            $this->deletingTemplateId = null;
            $this->loadTemplates();

            Notification::make()
                ->title('Template deleted')
                ->success()
                ->send();
        }
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = false;
        $this->deletingTemplateId = null;
    }

    private function resetForm(): void
    {
        $this->editingTemplateId = null;
        $this->templateName = '';
        $this->templateSubject = '';
        $this->templateCategory = '';
        $this->templateContent = '';
    }
}
