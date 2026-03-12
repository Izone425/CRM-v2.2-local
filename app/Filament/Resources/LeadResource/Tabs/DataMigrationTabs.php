<?php

namespace App\Filament\Resources\LeadResource\Tabs;

use Filament\Forms\Components\ViewField;

class DataMigrationTabs
{
    public static function getSchema(): array
    {
        return [
            ViewField::make('data_migration_view')
                ->view('filament.resources.lead-resource.tabs.data-migration')
                ->live()
                ->dehydrated(false),
        ];
    }
}
