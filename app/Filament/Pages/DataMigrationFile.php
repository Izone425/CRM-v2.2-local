<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class DataMigrationFile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-up';
    protected static string $view = 'filament.pages.data-migration-file';
    protected static ?string $slug = 'data-migration-file';
    protected static ?string $navigationLabel = 'Data Migration V2';
    protected static bool $shouldRegisterNavigation = false;

    public function getTitle(): string
    {
        return 'Data Migration Templates';
    }

    public static array $templateSections = [
        'user-data'   => ['label' => 'Profile',    'file' => 'user-data.xlsx',  'icon' => 'fas fa-user',               'color' => '#7C3AED'],
        'attendance'  => ['label' => 'Attendance',  'file' => 'attendance.xlsx', 'icon' => 'fas fa-calendar-check',     'color' => '#6366F1'],
        'leave'       => ['label' => 'Leave',       'file' => 'leave.xlsx',      'icon' => 'fas fa-umbrella-beach',     'color' => '#EF4444'],
        'claim'       => ['label' => 'Claim',       'file' => 'claim.xlsx',      'icon' => 'fas fa-money-bill-wave',    'color' => '#F59E0B'],
        'payroll'     => ['label' => 'Payroll',     'file' => 'payroll.xlsx',    'icon' => 'fas fa-file-invoice-dollar', 'color' => '#10B981'],
    ];

    protected static string $storagePath = 'templates/data-migration';

    public function getSections(): array
    {
        $sections = [];

        foreach (static::$templateSections as $key => $section) {
            $filePath = static::$storagePath . '/' . $section['file'];
            $exists = Storage::disk('public')->exists($filePath);

            $sections[$key] = array_merge($section, [
                'key' => $key,
                'exists' => $exists,
                'size' => $exists ? $this->formatFileSize(Storage::disk('public')->size($filePath)) : null,
                'lastModified' => $exists ? date('M d, Y H:i', Storage::disk('public')->lastModified($filePath)) : null,
            ]);
        }

        return $sections;
    }

    protected function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('uploadTemplate')
                ->label('Upload Template')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    Select::make('section')
                        ->label('Section')
                        ->options(collect(static::$templateSections)->pluck('label', 'label')->mapWithKeys(function ($label, $key) {
                            // Find actual key by label
                            foreach (static::$templateSections as $sectionKey => $section) {
                                if ($section['label'] === $label) {
                                    return [$sectionKey => $section['label']];
                                }
                            }
                            return [];
                        })->toArray())
                        ->required(),

                    FileUpload::make('file')
                        ->label('Template File')
                        ->disk('public')
                        ->directory('temp-uploads')
                        ->acceptedFileTypes([
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'text/csv',
                        ])
                        ->maxSize(10240)
                        ->required(),
                ])
                ->action(function (array $data) {
                    $sectionKey = $data['section'];
                    $section = static::$templateSections[$sectionKey] ?? null;

                    if (!$section) {
                        Notification::make()->title('Invalid section')->danger()->send();
                        return;
                    }

                    $uploadedFile = $data['file'];
                    if (is_array($uploadedFile)) {
                        $uploadedFile = $uploadedFile[0];
                    }

                    $targetPath = static::$storagePath . '/' . $section['file'];

                    // Delete existing file if present
                    if (Storage::disk('public')->exists($targetPath)) {
                        Storage::disk('public')->delete($targetPath);
                    }

                    // Move uploaded file to target path
                    if (Storage::disk('public')->exists($uploadedFile)) {
                        Storage::disk('public')->move($uploadedFile, $targetPath);
                    }

                    // Clean up temp directory
                    Storage::disk('public')->deleteDirectory('temp-uploads');

                    Notification::make()
                        ->title($section['label'] . ' template uploaded successfully')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function downloadTemplate(string $sectionKey)
    {
        $section = static::$templateSections[$sectionKey] ?? null;
        if (!$section) {
            return;
        }

        $filePath = static::$storagePath . '/' . $section['file'];

        if (!Storage::disk('public')->exists($filePath)) {
            Notification::make()
                ->title('File not found')
                ->danger()
                ->send();
            return;
        }

        return response()->download(
            storage_path('app/public/' . $filePath),
            $section['file']
        );
    }

    public function deleteTemplate(string $sectionKey): void
    {
        $section = static::$templateSections[$sectionKey] ?? null;
        if (!$section) {
            return;
        }

        $filePath = static::$storagePath . '/' . $section['file'];

        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);

            Notification::make()
                ->title($section['label'] . ' template deleted')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('File not found')
                ->warning()
                ->send();
        }
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user || !($user instanceof \App\Models\User)) {
            return false;
        }
        return $user->hasRouteAccess('filament.admin.pages.data-migration-file');
    }
}
