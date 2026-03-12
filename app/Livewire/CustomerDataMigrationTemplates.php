<?php

namespace App\Livewire;

use App\Models\CustomerDataMigrationFile;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class CustomerDataMigrationTemplates extends Component
{
    use WithFileUploads;

    protected string $storagePath = 'templates/data-migration-v1';

    public ?string $uploadingSection = null;
    public ?string $uploadingItem = null;
    public $uploadFile;
    public ?string $uploadRemark = null;

    public array $templateSections = [
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

    public function getSections(): array
    {
        $sections = [];
        $uploadedVersions = $this->getUploadedVersions();

        foreach ($this->templateSections as $sectionKey => $section) {
            $items = [];

            foreach ($section['items'] as $itemKey => $item) {
                $path = storage_path('app/public/' . $this->storagePath . '/' . $item['file']);
                $exists = file_exists($path);
                $versionKey = $sectionKey . '|' . $itemKey;

                $items[$itemKey] = array_merge($item, [
                    'exists' => $exists,
                    'size' => $exists ? $this->formatFileSize(filesize($path)) : null,
                    'latestVersion' => $uploadedVersions[$versionKey] ?? null,
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

    protected function getUploadedVersions(): array
    {
        $customer = auth('customer')->user();
        if (!$customer || !$customer->lead_id) {
            return [];
        }

        $files = CustomerDataMigrationFile::where('lead_id', $customer->lead_id)
            ->selectRaw('section, item, MAX(version) as latest_version')
            ->groupBy('section', 'item')
            ->get();

        $versions = [];
        foreach ($files as $file) {
            $versions[$file->section . '|' . $file->item] = $file->latest_version;
        }

        return $versions;
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

    public function downloadTemplate(string $sectionKey, string $itemKey)
    {
        $section = $this->templateSections[$sectionKey] ?? null;
        $item = $section['items'][$itemKey] ?? null;

        if (!$item) {
            session()->flash('message', 'Template not found.');
            return;
        }

        $path = storage_path('app/public/' . $this->storagePath . '/' . $item['file']);

        if (!file_exists($path)) {
            session()->flash('message', 'Template not available yet.');
            return;
        }

        return response()->download($path, $item['file']);
    }

    public function startUpload(string $sectionKey, string $itemKey): void
    {
        $this->uploadingSection = $sectionKey;
        $this->uploadingItem = $itemKey;
        $this->uploadFile = null;
        $this->uploadRemark = null;
    }

    public function cancelUpload(): void
    {
        $this->uploadingSection = null;
        $this->uploadingItem = null;
        $this->uploadFile = null;
        $this->uploadRemark = null;
    }

    public function submitUpload(): void
    {
        $this->validate([
            'uploadFile' => 'required|file|max:10240|mimes:xlsx,xls,csv',
            'uploadRemark' => 'nullable|string|max:500',
        ]);

        $customer = auth('customer')->user();

        if (!$customer || !$customer->lead_id) {
            session()->flash('error', 'Unable to upload. Please contact support.');
            return;
        }

        $section = $this->uploadingSection;
        $item = $this->uploadingItem;

        if (!isset($this->templateSections[$section]['items'][$item])) {
            session()->flash('error', 'Invalid template selection.');
            return;
        }

        $version = CustomerDataMigrationFile::nextVersion($customer->lead_id, $section, $item);
        $ext = $this->uploadFile->getClientOriginalExtension();
        $originalName = $this->uploadFile->getClientOriginalName();

        $storagePath = "customer-data-migration/{$customer->lead_id}/{$section}/{$item}";
        $fileName = "v{$version}.{$ext}";

        $filePath = $this->uploadFile->storeAs($storagePath, $fileName, 'public');

        CustomerDataMigrationFile::create([
            'lead_id' => $customer->lead_id,
            'customer_id' => $customer->id,
            'section' => $section,
            'item' => $item,
            'version' => $version,
            'file_name' => $originalName,
            'file_path' => $filePath,
            'remark' => $this->uploadRemark,
            'status' => 'pending',
        ]);

        $this->cancelUpload();
        session()->flash('success', 'File uploaded successfully (v' . $version . ').');
    }

    public function render()
    {
        return view('livewire.customer-data-migration-templates');
    }
}
