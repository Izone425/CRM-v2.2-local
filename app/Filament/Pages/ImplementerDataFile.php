<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class ImplementerDataFile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.implementer-data-file';
    protected static ?string $slug = 'implementer-data-file';
    protected static ?string $navigationLabel = 'Data Migration V1';
    protected static bool $shouldRegisterNavigation = false;

    public function getTitle(): string
    {
        return 'Data Migration Templates (V1)';
    }

    public static array $templateSections = [
        'profile' => [
            'label' => 'Profile',
            'icon' => 'fas fa-user',
            'color' => '#7C3AED',
            'items' => [
                'import-user' => ['label' => 'Import User', 'file' => 'profile-import-user.xlsx'],
            ],
        ],
        'attendance' => [
            'label' => 'Attendance',
            'icon' => 'fas fa-calendar-check',
            'color' => '#6366F1',
            'items' => [
                'clocking-schedule' => ['label' => 'Clocking Schedule', 'file' => 'attendance-clocking-schedule.xlsx'],
            ],
        ],
        'leave' => [
            'label' => 'Leave',
            'icon' => 'fas fa-umbrella-beach',
            'color' => '#EF4444',
            'items' => [
                'leave-policy' => ['label' => 'Leave Policy', 'file' => 'leave-leave-policy.xlsx'],
            ],
        ],
        'claim' => [
            'label' => 'Claim',
            'icon' => 'fas fa-money-bill-wave',
            'color' => '#F59E0B',
            'items' => [
                'claim-policy' => ['label' => 'Claim Policy', 'file' => 'claim-claim-policy.xlsx'],
            ],
        ],
        'payroll' => [
            'label' => 'Payroll',
            'icon' => 'fas fa-file-invoice-dollar',
            'color' => '#10B981',
            'items' => [
                'employee-information' => ['label' => 'Payroll Employee Information', 'file' => 'payroll-employee-information.xlsx'],
                'employee-salary-data' => ['label' => 'Employee Salary Data', 'file' => 'payroll-employee-salary-data.xlsx'],
                'accumulated-item-ea' => ['label' => 'Accumulated Item EA', 'file' => 'payroll-accumulated-item-ea.xlsx'],
                'basic-info' => ['label' => 'Payroll Basic Info', 'file' => 'payroll-basic-info.xlsx'],
            ],
        ],
    ];

    protected static string $storagePath = 'templates/data-migration-v1';

    public function getSections(): array
    {
        $sections = [];

        foreach (static::$templateSections as $sectionKey => $section) {
            $items = [];

            foreach ($section['items'] as $itemKey => $item) {
                $filePath = static::$storagePath . '/' . $item['file'];
                $exists = Storage::disk('public')->exists($filePath);

                $items[$itemKey] = array_merge($item, [
                    'exists' => $exists,
                    'size' => $exists ? $this->formatFileSize(Storage::disk('public')->size($filePath)) : null,
                    'lastModified' => $exists ? date('M d, Y H:i', Storage::disk('public')->lastModified($filePath)) : null,
                ]);
            }

            $sections[$sectionKey] = [
                'label' => $section['label'],
                'icon' => $section['icon'],
                'color' => $section['color'],
                'items' => $items,
            ];
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
        // Build flat options list: "Section - SubItem"
        $options = [];
        foreach (static::$templateSections as $sectionKey => $section) {
            foreach ($section['items'] as $itemKey => $item) {
                $options[$sectionKey . '|' . $itemKey] = $section['label'] . ' - ' . $item['label'];
            }
        }

        return [
            Action::make('uploadTemplate')
                ->label('Upload Template')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    Select::make('section_item')
                        ->label('Template Type')
                        ->options($options)
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
                    [$sectionKey, $itemKey] = explode('|', $data['section_item']);

                    $section = static::$templateSections[$sectionKey] ?? null;
                    $item = $section['items'][$itemKey] ?? null;

                    if (!$section || !$item) {
                        Notification::make()->title('Invalid selection')->danger()->send();
                        return;
                    }

                    $uploadedFile = $data['file'];
                    if (is_array($uploadedFile)) {
                        $uploadedFile = $uploadedFile[0];
                    }

                    $targetPath = static::$storagePath . '/' . $item['file'];

                    if (Storage::disk('public')->exists($targetPath)) {
                        Storage::disk('public')->delete($targetPath);
                    }

                    if (Storage::disk('public')->exists($uploadedFile)) {
                        Storage::disk('public')->move($uploadedFile, $targetPath);
                    }

                    Storage::disk('public')->deleteDirectory('temp-uploads');

                    Notification::make()
                        ->title($item['label'] . ' template uploaded successfully')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function downloadTemplate(string $sectionKey, string $itemKey)
    {
        $section = static::$templateSections[$sectionKey] ?? null;
        $item = $section['items'][$itemKey] ?? null;

        if (!$item) {
            return;
        }

        $filePath = static::$storagePath . '/' . $item['file'];

        if (!Storage::disk('public')->exists($filePath)) {
            Notification::make()
                ->title('File not found')
                ->danger()
                ->send();
            return;
        }

        return response()->download(
            storage_path('app/public/' . $filePath),
            $item['file']
        );
    }

    public function deleteTemplate(string $sectionKey, string $itemKey): void
    {
        $section = static::$templateSections[$sectionKey] ?? null;
        $item = $section['items'][$itemKey] ?? null;

        if (!$item) {
            return;
        }

        $filePath = static::$storagePath . '/' . $item['file'];

        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);

            Notification::make()
                ->title($item['label'] . ' template deleted')
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
        return $user->hasRouteAccess('filament.admin.pages.implementer-data-file');
    }
}
