<?php

namespace App\Filament\Resources\LeadResource\Tabs;

use Filament\Forms\Components\ViewField;

class ThreadTabs
{
    public static function getSchema(): array
    {
        return [
            ViewField::make('thread_view')
                ->view('filament.resources.lead-resource.tabs.thread')
                ->live()
                ->dehydrated(false),
        ];
    }
}
