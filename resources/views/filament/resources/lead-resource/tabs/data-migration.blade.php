@php
    use App\Models\CustomerDataMigrationFile;

    $lead = $this->record;

    $templateSections = [
        'profile' => [
            'label' => 'Profile', 'icon' => 'fas fa-user', 'color' => '#7C3AED',
            'items' => [
                'import-user' => ['label' => 'Import User'],
            ],
        ],
        'attendance' => [
            'label' => 'Attendance', 'icon' => 'fas fa-calendar-check', 'color' => '#6366F1',
            'items' => [
                'clocking-schedule' => ['label' => 'Clocking Schedule'],
            ],
        ],
        'leave' => [
            'label' => 'Leave', 'icon' => 'fas fa-umbrella-beach', 'color' => '#EF4444',
            'items' => [
                'leave-policy' => ['label' => 'Leave Policy'],
            ],
        ],
        'claim' => [
            'label' => 'Claim', 'icon' => 'fas fa-money-bill-wave', 'color' => '#F59E0B',
            'items' => [
                'claim-policy' => ['label' => 'Claim Policy'],
            ],
        ],
        'payroll' => [
            'label' => 'Payroll', 'icon' => 'fas fa-file-invoice-dollar', 'color' => '#10B981',
            'items' => [
                'employee-information' => ['label' => 'Payroll Employee Information'],
                'employee-salary-data' => ['label' => 'Employee Salary Data'],
                'accumulated-item-ea' => ['label' => 'Accumulated Item EA'],
                'basic-info' => ['label' => 'Payroll Basic Info'],
            ],
        ],
    ];

    $allFiles = CustomerDataMigrationFile::where('lead_id', $lead->id)
        ->orderBy('section')
        ->orderBy('item')
        ->orderBy('version', 'desc')
        ->get()
        ->groupBy(fn ($f) => $f->section . '|' . $f->item);

    $totalUploads = CustomerDataMigrationFile::where('lead_id', $lead->id)->count();

    // Build JSON data for Alpine
    $filesJson = [];
    foreach ($allFiles as $key => $versions) {
        foreach ($versions as $file) {
            $filesJson[$file->id] = [
                'id' => $file->id,
                'section' => $file->section,
                'item' => $file->item,
                'version' => $file->version,
                'file_name' => $file->file_name,
                'remark' => $file->remark,
                'implementer_remark' => $file->implementer_remark ?? '',
                'status' => $file->status,
                'created_at' => $file->created_at->format('M d, Y H:i'),
                'download_url' => route('admin.data-migration-file.download', $file->id),
            ];
        }
    }
@endphp

<style>
    .dm-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(420px, 1fr));
        gap: 16px;
    }
    .dm-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 20px;
    }
    .dm-card-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
        padding-bottom: 10px;
        border-bottom: 1px solid #f1f5f9;
    }
    .dm-card-icon {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }
    .dm-card-title {
        font-size: 15px;
        font-weight: 600;
        color: #1e293b;
    }
    .dm-item {
        padding: 10px 0;
        border-bottom: 1px solid #f8fafc;
    }
    .dm-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .dm-item-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 4px;
    }
    .dm-item-label {
        font-size: 13px;
        font-weight: 600;
        color: #334155;
    }
    .dm-badge {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 600;
    }
    .dm-badge-pending { background: #fef3c7; color: #d97706; }
    .dm-badge-reviewed { background: #dbeafe; color: #2563eb; }
    .dm-badge-accepted { background: #dcfce7; color: #16a34a; }
    .dm-badge-rejected { background: #fef2f2; color: #dc2626; }
    .dm-badge-none { background: #f1f5f9; color: #94a3b8; }

    .dm-version-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 6px 10px;
        margin-bottom: 3px;
        background: #f8fafc;
        border-radius: 6px;
        font-size: 12px;
        cursor: pointer;
        transition: background 0.15s;
    }
    .dm-version-row:hover {
        background: #eef2ff;
    }
    .dm-version-meta {
        color: #64748b;
        font-size: 11px;
    }
    .dm-version-open {
        color: #667eea;
        font-size: 11px;
        font-weight: 600;
        flex-shrink: 0;
    }
    .dm-no-files {
        color: #94a3b8;
        font-size: 12px;
        font-style: italic;
        padding: 4px 0;
    }
    .dm-toggle {
        font-size: 11px;
        color: #667eea;
        cursor: pointer;
        background: none;
        border: none;
        font-weight: 600;
        padding: 2px 0;
    }
    .dm-toggle:hover {
        text-decoration: underline;
    }
    .dm-summary {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-bottom: 16px;
        font-size: 13px;
        color: #64748b;
    }
    .dm-summary strong {
        color: #1e293b;
    }

    /* Slide-over */
    .dm-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.3);
        z-index: 999;
    }
    .dm-slider {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        width: 420px;
        max-width: 90vw;
        background: #fff;
        z-index: 1000;
        box-shadow: -4px 0 20px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        transform: translateX(100%);
        transition: transform 0.25s ease;
    }
    .dm-slider.open {
        transform: translateX(0);
    }
    .dm-slider-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid #e2e8f0;
        flex-shrink: 0;
    }
    .dm-slider-title {
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
    }
    .dm-slider-close {
        width: 32px;
        height: 32px;
        border: none;
        background: #f1f5f9;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        font-size: 14px;
    }
    .dm-slider-close:hover {
        background: #e2e8f0;
    }
    .dm-slider-body {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
    }
    .dm-slider-section {
        margin-bottom: 20px;
    }
    .dm-slider-label {
        font-size: 11px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }
    .dm-slider-value {
        font-size: 13px;
        color: #334155;
        line-height: 1.5;
    }
    .dm-slider-remark-box {
        padding: 10px 12px;
        background: #f8fafc;
        border-radius: 8px;
        font-size: 13px;
        color: #475569;
        font-style: italic;
        line-height: 1.5;
    }
    .dm-slider-textarea {
        width: 100%;
        min-height: 80px;
        padding: 10px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 13px;
        color: #334155;
        resize: vertical;
        font-family: inherit;
    }
    .dm-slider-textarea:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 2px rgba(102,126,234,0.15);
    }
    .dm-dropdown {
        position: relative;
        width: 100%;
    }
    .dm-dropdown-trigger {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 13px;
        color: #334155;
        background: #fff;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        text-align: left;
    }
    .dm-dropdown-trigger:hover {
        border-color: #cbd5e1;
    }
    .dm-dropdown-trigger.active {
        border-color: #667eea;
        box-shadow: 0 0 0 2px rgba(102,126,234,0.15);
    }
    .dm-dropdown-arrow {
        font-size: 10px;
        color: #94a3b8;
    }
    .dm-dropdown-menu {
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        z-index: 50;
        overflow: hidden;
    }
    .dm-dropdown-option {
        padding: 9px 12px;
        font-size: 13px;
        color: #334155;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .dm-dropdown-option:hover {
        background: #f8fafc;
    }
    .dm-dropdown-option.selected {
        background: #eef2ff;
        color: #667eea;
        font-weight: 600;
    }
    .dm-dropdown-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .dm-dot-pending { background: #d97706; }
    .dm-dot-reviewed { background: #2563eb; }
    .dm-dot-accepted { background: #16a34a; }
    .dm-dot-rejected { background: #dc2626; }
    .dm-slider-footer {
        padding: 16px 20px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        gap: 8px;
        flex-shrink: 0;
    }
    .dm-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 8px 16px;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }
    .dm-btn-primary {
        background: #667eea;
        color: #fff;
        flex: 1;
    }
    .dm-btn-primary:hover {
        background: #5a6fd6;
    }
    .dm-btn-primary:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .dm-btn-download {
        background: #eff6ff;
        color: #2563eb;
    }
    .dm-btn-download:hover {
        background: #dbeafe;
    }
    .dm-slider-toast {
        padding: 8px 12px;
        background: #dcfce7;
        color: #16a34a;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-align: center;
        margin-bottom: 12px;
    }
</style>

<div x-data="dmTab()" x-cloak>
    <div class="dm-summary">
        <i class="fas fa-file-alt" style="color: #667eea;"></i>
        <span><strong>{{ $totalUploads }}</strong> file(s) uploaded by customer</span>
    </div>

    <div class="dm-grid">
        @foreach($templateSections as $sectionKey => $section)
            <div class="dm-card">
                <div class="dm-card-header">
                    <div class="dm-card-icon" style="background: {{ $section['color'] }}15; color: {{ $section['color'] }};">
                        <i class="{{ $section['icon'] }}"></i>
                    </div>
                    <div class="dm-card-title">{{ $section['label'] }}</div>
                </div>

                @foreach($section['items'] as $itemKey => $item)
                    @php
                        $fileKey = $sectionKey . '|' . $itemKey;
                        $versions = $allFiles[$fileKey] ?? collect();
                        $latest = $versions->first();
                    @endphp
                    <div class="dm-item">
                        <div class="dm-item-header">
                            <span class="dm-item-label">{{ $item['label'] }}</span>
                            @if($latest)
                                <span class="dm-badge dm-badge-{{ $latest->status }}">
                                    {{ ucfirst($latest->status) }} (v{{ $latest->version }})
                                </span>
                            @else
                                <span class="dm-badge dm-badge-none">No uploads</span>
                            @endif
                        </div>

                        @if($versions->isNotEmpty())
                            {{-- Latest version --}}
                            <div class="dm-version-row" @click="openSlider({{ $latest->id }}, '{{ $item['label'] }}')">
                                <div>
                                    <div><strong>v{{ $latest->version }}</strong> &mdash; {{ $latest->file_name }}</div>
                                    <div class="dm-version-meta">{{ $latest->created_at->format('M d, Y H:i') }}</div>
                                </div>
                                <span class="dm-version-open"><i class="fas fa-chevron-right"></i></span>
                            </div>

                            {{-- Older versions --}}
                            @if($versions->count() > 1)
                                <button class="dm-toggle"
                                    @click="expanded['{{ $fileKey }}'] = !expanded['{{ $fileKey }}']"
                                    x-text="expanded['{{ $fileKey }}'] ? 'Hide older versions' : 'Show {{ $versions->count() - 1 }} older version(s)'">
                                </button>

                                <template x-if="expanded['{{ $fileKey }}']">
                                    <div>
                                        @foreach($versions->skip(1) as $file)
                                            <div class="dm-version-row" @click="openSlider({{ $file->id }}, '{{ $item['label'] }}')">
                                                <div>
                                                    <div><strong>v{{ $file->version }}</strong> &mdash; {{ $file->file_name }}</div>
                                                    <div class="dm-version-meta">{{ $file->created_at->format('M d, Y H:i') }}</div>
                                                </div>
                                                <span class="dm-version-open"><i class="fas fa-chevron-right"></i></span>
                                            </div>
                                        @endforeach
                                    </div>
                                </template>
                            @endif
                        @else
                            <div class="dm-no-files">No files uploaded yet</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

    {{-- Slide-over panel --}}
    <template x-if="sliderOpen">
        <div>
            <div class="dm-overlay" @click="closeSlider()"></div>
            <div class="dm-slider" :class="{ 'open': sliderVisible }">
                <div class="dm-slider-header">
                    <div class="dm-slider-title" x-text="sliderTitle"></div>
                    <button class="dm-slider-close" @click="closeSlider()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="dm-slider-body">
                    <template x-if="saved">
                        <div class="dm-slider-toast">Changes saved successfully</div>
                    </template>

                    <div class="dm-slider-section">
                        <div class="dm-slider-label">File Details</div>
                        <div class="dm-slider-value">
                            <div><strong>Version:</strong> <span x-text="'v' + activeFile.version"></span></div>
                            <div><strong>File:</strong> <span x-text="activeFile.file_name"></span></div>
                            <div><strong>Uploaded:</strong> <span x-text="activeFile.created_at"></span></div>
                        </div>
                    </div>

                    <template x-if="activeFile.remark">
                        <div class="dm-slider-section">
                            <div class="dm-slider-label">Customer Remark</div>
                            <div class="dm-slider-remark-box" x-text="activeFile.remark"></div>
                        </div>
                    </template>

                    <div class="dm-slider-section">
                        <div class="dm-slider-label">Status</div>
                        <div class="dm-dropdown" x-data="{ dropdownOpen: false }" @click.outside="dropdownOpen = false">
                            <button type="button" class="dm-dropdown-trigger" :class="{ 'active': dropdownOpen }" @click="dropdownOpen = !dropdownOpen">
                                <span style="display:flex;align-items:center;gap:8px;">
                                    <span class="dm-dropdown-dot" :class="'dm-dot-' + editStatus"></span>
                                    <span x-text="statusLabels[editStatus]"></span>
                                </span>
                                <span class="dm-dropdown-arrow"><i class="fas fa-chevron-down"></i></span>
                            </button>
                            <div class="dm-dropdown-menu" x-show="dropdownOpen" x-transition>
                                <template x-for="opt in ['pending','reviewed','accepted','rejected']" :key="opt">
                                    <div class="dm-dropdown-option"
                                         :class="{ 'selected': editStatus === opt }"
                                         @click="editStatus = opt; dropdownOpen = false">
                                        <span class="dm-dropdown-dot" :class="'dm-dot-' + opt"></span>
                                        <span x-text="statusLabels[opt]"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="dm-slider-section">
                        <div class="dm-slider-label">Implementer Remark</div>
                        <textarea class="dm-slider-textarea"
                            x-model="editRemark"
                            placeholder="Add your remark or notes here..."></textarea>
                    </div>
                </div>
                <div class="dm-slider-footer">
                    <a :href="activeFile.download_url" class="dm-btn dm-btn-download">
                        <i class="fas fa-download"></i> Download
                    </a>
                    <button class="dm-btn dm-btn-primary" @click="saveChanges()" :disabled="saving">
                        <span x-show="!saving"><i class="fas fa-check"></i> Save Changes</span>
                        <span x-show="saving">Saving...</span>
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
    function dmTab() {
        return {
            expanded: {},
            sliderOpen: false,
            sliderVisible: false,
            sliderTitle: '',
            activeFile: {},
            editStatus: 'pending',
            editRemark: '',
            saving: false,
            saved: false,
            filesData: @json($filesJson),
            statusLabels: { pending: 'Pending', reviewed: 'Reviewed', accepted: 'Accepted', rejected: 'Rejected' },

            openSlider(fileId, label) {
                const file = this.filesData[fileId];
                if (!file) return;
                this.activeFile = file;
                this.sliderTitle = label + ' (v' + file.version + ')';
                this.editStatus = file.status;
                this.editRemark = file.implementer_remark || '';
                this.saved = false;
                this.sliderOpen = true;
                this.$nextTick(() => { this.sliderVisible = true; });
            },

            closeSlider() {
                this.sliderVisible = false;
                setTimeout(() => { this.sliderOpen = false; }, 250);
            },

            async saveChanges() {
                this.saving = true;
                this.saved = false;
                try {
                    const res = await fetch(`/admin/api/data-migration-file/${this.activeFile.id}/update`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            status: this.editStatus,
                            implementer_remark: this.editRemark,
                        }),
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.filesData[this.activeFile.id].status = this.editStatus;
                        this.filesData[this.activeFile.id].implementer_remark = this.editRemark;
                        this.saved = true;
                    }
                } catch (e) {
                    console.error(e);
                } finally {
                    this.saving = false;
                }
            }
        };
    }
</script>
