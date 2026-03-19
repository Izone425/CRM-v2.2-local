<?php

namespace App\Filament\Resources\LeadResource\Tabs;

use Filament\Forms\Components\ViewField;

class SoftwareHandoverProcessTabs
{
    public static function getSchema(): array
    {
        return [
            ViewField::make('software_handover_process_view')
                ->view('filament.resources.lead-resource.tabs.software-handover-process')
                ->live()
                ->dehydrated(false),
        ];
    }
}
