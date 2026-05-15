<?php

namespace App\Livewire;

use App\Models\CustomerDataMigrationFile;
use App\Models\SoftwareHandover;
use App\Support\DataFileSections;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class CustomerDataMigrationTemplates extends Component
{
    use WithFileUploads;

    private const SECTION_FLAG = [
        'profile'    => null,   // foundational — always enabled
        'attendance' => 'ta',
        'leave'      => 'tl',
        'claim'      => 'tc',
        'payroll'    => 'tp',
    ];

    private const GUIDE_MAP = [
        'profile.import-user'          => '26 - Import User File Guideline - Data Migration Explanation.pdf',
        'payroll.employee-information' => '34 - Import Employee Particulars Guideline.pdf',
        'payroll.employee-salary-data' => '36 - Import Employee Salary Data Guideline.pdf',
        'payroll.accumulated-item-ea'  => '38 - Import Employee Accumulated Items Guideline.pdf',
    ];

    protected string $storagePath = 'templates/data-migration-v1';

    public ?string $uploadingSection = null;
    public ?string $uploadingItem = null;
    public $uploadFile;
    public ?string $uploadRemark = null;

    public array $templateSections = [];

    protected ?SoftwareHandover $handover = null;

    public function mount(): void
    {
        $this->templateSections = DataFileSections::map();
        $this->handover = $this->resolveHandover();
    }

    public function hydrate(): void
    {
        $this->handover = $this->resolveHandover();
    }

    protected function resolveHandover(): ?SoftwareHandover
    {
        $customer = auth('customer')->user();
        if (!$customer) {
            return null;
        }
        if ($customer->sw_id && $byId = SoftwareHandover::find($customer->sw_id)) {
            return $byId;
        }
        if ($customer->lead_id) {
            return SoftwareHandover::where('lead_id', $customer->lead_id)
                ->orderByDesc('id')
                ->first();
        }
        return null;
    }

    protected function moduleEnabled(string $sectionKey): bool
    {
        $flag = self::SECTION_FLAG[$sectionKey] ?? null;
        if ($flag === null) {
            return true;
        }
        return $this->handover ? (bool) $this->handover->{$flag} : false;
    }

    public function getSections(): array
    {
        $sections = [];
        $allFiles = $this->getAllUploadedFiles();

        foreach ($this->templateSections as $sectionKey => $section) {
            $items = [];

            foreach ($section['items'] as $itemKey => $item) {
                $path = storage_path('app/public/' . $this->storagePath . '/' . $item['file']);
                $exists = file_exists($path);
                $versionKey = $sectionKey . '|' . $itemKey;

                $versions = $allFiles[$versionKey] ?? collect();
                $latest = $versions->first();

                $guideKey = $sectionKey . '.' . $itemKey;
                $guideUrl = (isset(self::GUIDE_MAP[$guideKey]) && $this->moduleEnabled($sectionKey))
                    ? route('customer.project-file-guide.view', ['key' => $guideKey])
                    : null;

                $items[$itemKey] = array_merge($item, [
                    'exists' => $exists,
                    'size' => $exists ? $this->formatFileSize(filesize($path)) : null,
                    'latestVersion' => $latest ? $latest->version : null,
                    'status' => $latest ? $latest->status : null,
                    'implementerRemark' => $latest ? $latest->implementer_remark : null,
                    'versions' => $versions,
                    'guide_url' => $guideUrl,
                ]);
            }

            $sections[$sectionKey] = [
                'label' => $section['label'],
                'icon' => $section['icon'],
                'icon_component' => $section['icon_component'] ?? null,
                'color' => $section['color'],
                'enabled' => $this->moduleEnabled($sectionKey),
                'items' => $items,
            ];
        }

        return $sections;
    }

    protected function getAllUploadedFiles()
    {
        $customer = auth('customer')->user();
        if (!$customer || !$customer->lead_id) {
            return collect();
        }

        return CustomerDataMigrationFile::where('lead_id', $customer->lead_id)
            ->orderBy('section')
            ->orderBy('item')
            ->orderBy('version', 'desc')
            ->get()
            ->groupBy(fn ($f) => $f->section . '|' . $f->item);
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
        if (!$this->moduleEnabled($sectionKey)) {
            session()->flash('error', 'This module is not included in your subscription.');
            return;
        }

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
        if (!$this->moduleEnabled($sectionKey)) {
            session()->flash('error', 'This module is not included in your subscription.');
            return;
        }

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

        if (!$this->moduleEnabled($section)) {
            session()->flash('error', 'This module is not included in your subscription.');
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
