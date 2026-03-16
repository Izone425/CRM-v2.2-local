<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Pages\Page;

class SupportThreadEmailTemplate extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static string $view = 'filament.pages.support-thread-email-template';
    protected static ?string $title = '';
    protected static bool $shouldRegisterNavigation = false;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user || !($user instanceof User)) {
            return false;
        }
        return in_array($user->role_id, [1, 3]);
    }
}
