<?php

namespace App\Notifications;

use App\Models\CustomerDataMigrationFile;
use Illuminate\Notifications\Notification;

class DataFileAssignedByImplementerNotification extends Notification
{
    public function __construct(
        public CustomerDataMigrationFile $file,
        public string $sectionLabel,
        public string $itemLabel,
        public string $implementerName
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'data_file.assigned_by_implementer',
            'title' => 'Implementer assigned a Data File',
            'message' => $this->implementerName . ' assigned "' . $this->file->file_name . '" to ' . $this->sectionLabel . ' / ' . $this->itemLabel . ' (v' . $this->file->version . ').',
            'action_url' => '/customer/dashboard?tab=dataMigration',
            'priority' => 'normal',
            'entity_type' => 'customer_data_migration_file',
            'entity_id' => $this->file->id,
            'action_by' => $this->implementerName,
            'metadata' => [
                'section' => $this->file->section,
                'item' => $this->file->item,
                'version' => $this->file->version,
                'file_name' => $this->file->file_name,
            ],
        ];
    }
}
