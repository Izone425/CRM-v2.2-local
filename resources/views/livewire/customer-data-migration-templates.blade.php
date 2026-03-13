@php
    $sections = $this->getSections();

    // Build JSON data for Alpine slide-over
    $filesJson = [];
    foreach ($sections as $sectionKey => $section) {
        foreach ($section['items'] as $itemKey => $item) {
            foreach ($item['versions'] as $file) {
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
                    'download_url' => route('customer.data-migration-file.download', $file->id),
                ];
            }
        }
    }
@endphp

<div>
    <style>
        .dmt-container {
            max-width: 1000px;
        }
        .dmt-title {
            font-size: 28px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 4px;
        }
        .dmt-subtitle {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 32px;
        }
        .dmt-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 20px;
        }
        @media (max-width: 600px) {
            .dmt-grid {
                grid-template-columns: 1fr;
            }
        }
        .dmt-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px;
            transition: all 0.3s ease;
        }
        .dmt-card:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
            border-color: #cbd5e1;
        }
        .dmt-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }
        .dmt-card-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }
        .dmt-card-name {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
        }
        .dmt-sub-item {
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .dmt-sub-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .dmt-sub-item-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .dmt-sub-item-info {
            flex: 1;
            min-width: 0;
        }
        .dmt-sub-item-label {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 2px;
        }
        .dmt-sub-item-size {
            font-size: 12px;
            color: #94a3b8;
        }
        .dmt-sub-item-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }
        .dmt-download-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 16px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }
        .dmt-download-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        .dmt-download-btn i {
            font-size: 11px;
        }
        .dmt-upload-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 16px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }
        .dmt-upload-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }
        .dmt-upload-btn i {
            font-size: 11px;
        }
        .dmt-coming-soon {
            font-size: 12px;
            color: #94a3b8;
            font-style: italic;
            flex-shrink: 0;
        }
        .dmt-badge {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 600;
        }
        .dmt-badge-pending { background: #fef3c7; color: #d97706; }
        .dmt-badge-reviewed { background: #dbeafe; color: #2563eb; }
        .dmt-badge-accepted { background: #dcfce7; color: #16a34a; }
        .dmt-badge-rejected { background: #fef2f2; color: #dc2626; }
        .dmt-badge-none { background: #f1f5f9; color: #94a3b8; }

        .dmt-version-row {
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
        .dmt-version-row:hover {
            background: #eef2ff;
        }
        .dmt-version-meta {
            color: #64748b;
            font-size: 11px;
        }
        .dmt-version-open {
            color: #667eea;
            font-size: 11px;
            font-weight: 600;
            flex-shrink: 0;
        }
        .dmt-no-files {
            color: #94a3b8;
            font-size: 12px;
            font-style: italic;
            padding: 4px 0;
        }
        .dmt-toggle {
            font-size: 11px;
            color: #667eea;
            cursor: pointer;
            background: none;
            border: none;
            font-weight: 600;
            padding: 2px 0;
        }
        .dmt-toggle:hover {
            text-decoration: underline;
        }
        .dmt-upload-form {
            margin-top: 10px;
            padding: 12px;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .dmt-upload-form input[type="file"] {
            width: 100%;
            font-size: 12px;
            margin-bottom: 8px;
        }
        .dmt-upload-form textarea {
            width: 100%;
            font-size: 12px;
            padding: 8px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            resize: vertical;
            min-height: 60px;
            margin-bottom: 8px;
        }
        .dmt-upload-form textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        .dmt-upload-form-actions {
            display: flex;
            gap: 6px;
            justify-content: flex-end;
        }
        .dmt-cancel-btn {
            padding: 6px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            background: #fff;
            color: #64748b;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
        }
        .dmt-submit-btn {
            padding: 6px 14px;
            border: none;
            border-radius: 6px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
        }
        .dmt-submit-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .dmt-flash {
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 16px;
        }
        .dmt-flash-success {
            background: #dcfce7;
            color: #16a34a;
        }
        .dmt-flash-error {
            background: #fef2f2;
            color: #dc2626;
        }

        /* Slide-over */
        .dmt-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.3);
            z-index: 999;
        }
        .dmt-slider {
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
        .dmt-slider.open {
            transform: translateX(0);
        }
        .dmt-slider-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid #e2e8f0;
            flex-shrink: 0;
        }
        .dmt-slider-title {
            font-size: 15px;
            font-weight: 700;
            color: #1e293b;
        }
        .dmt-slider-close {
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
        .dmt-slider-close:hover {
            background: #e2e8f0;
        }
        .dmt-slider-body {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }
        .dmt-slider-section {
            margin-bottom: 20px;
        }
        .dmt-slider-label {
            font-size: 11px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .dmt-slider-value {
            font-size: 13px;
            color: #334155;
            line-height: 1.5;
        }
        .dmt-slider-remark-box {
            padding: 10px 12px;
            background: #f8fafc;
            border-radius: 8px;
            font-size: 13px;
            color: #475569;
            font-style: italic;
            line-height: 1.5;
        }
        .dmt-slider-implementer-box {
            padding: 10px 12px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            border-left: 3px solid #3b82f6;
        }
        .dmt-slider-implementer-header {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 600;
            color: #3b82f6;
            margin-bottom: 4px;
        }
        .dmt-slider-implementer-text {
            font-size: 13px;
            color: #334155;
            line-height: 1.5;
            white-space: pre-wrap;
        }
        .dmt-slider-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 12px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
        }
        .dmt-slider-footer {
            padding: 16px 20px;
            border-top: 1px solid #e2e8f0;
            flex-shrink: 0;
        }
        .dmt-btn-download-slider {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }
        .dmt-btn-download-slider:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            color: #fff;
        }
        .dmt-slider-no-remark {
            font-size: 12px;
            color: #94a3b8;
            font-style: italic;
        }
    </style>

    <div class="dmt-container" x-data="dmtCustomer()" x-cloak>
        <h2 class="dmt-title">Data Migration Templates</h2>
        <p class="dmt-subtitle">Download Excel templates, fill them in, and upload back for processing</p>

        @if(session()->has('success'))
            <div class="dmt-flash dmt-flash-success">{{ session('success') }}</div>
        @endif
        @if(session()->has('error'))
            <div class="dmt-flash dmt-flash-error">{{ session('error') }}</div>
        @endif

        <div class="dmt-grid">
            @foreach($sections as $sectionKey => $section)
                <div class="dmt-card">
                    <div class="dmt-card-header">
                        <div class="dmt-card-icon" style="background: {{ $section['color'] }}15; color: {{ $section['color'] }};">
                            <i class="{{ $section['icon'] }}"></i>
                        </div>
                        <div class="dmt-card-name">{{ $section['label'] }}</div>
                    </div>

                    @foreach($section['items'] as $itemKey => $item)
                        @php
                            $fileKey = $sectionKey . '|' . $itemKey;
                            $versions = $item['versions'];
                            $latest = $versions->first();
                        @endphp
                        <div class="dmt-sub-item">
                            <div class="dmt-sub-item-row">
                                <div class="dmt-sub-item-info">
                                    <div class="dmt-sub-item-label">
                                        {{ $item['label'] }}
                                        @if($latest)
                                            <span class="dmt-badge dmt-badge-{{ $latest->status }}">
                                                {{ ucfirst($latest->status) }} (v{{ $latest->version }})
                                            </span>
                                        @endif
                                    </div>
                                    @if($item['exists'])
                                        <div class="dmt-sub-item-size">{{ $item['size'] }}</div>
                                    @endif
                                </div>
                                <div class="dmt-sub-item-actions">
                                    @if($item['exists'])
                                        <button wire:click="downloadTemplate('{{ $sectionKey }}', '{{ $itemKey }}')" class="dmt-download-btn">
                                            <i class="fas fa-download"></i> Template
                                        </button>
                                        <button wire:click="startUpload('{{ $sectionKey }}', '{{ $itemKey }}')" class="dmt-upload-btn">
                                            <i class="fas fa-upload"></i> Upload
                                        </button>
                                    @else
                                        <span class="dmt-coming-soon">Coming soon</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Version list --}}
                            @if($versions->isNotEmpty())
                                {{-- Latest version row --}}
                                <div class="dmt-version-row" @click="openSlider({{ $latest->id }}, '{{ addslashes($item['label']) }}')">
                                    <div>
                                        <div><strong>v{{ $latest->version }}</strong> &mdash; {{ $latest->file_name }}</div>
                                        <div class="dmt-version-meta">{{ $latest->created_at->format('M d, Y H:i') }}</div>
                                    </div>
                                    <span class="dmt-version-open"><i class="fas fa-chevron-right"></i></span>
                                </div>

                                {{-- Older versions --}}
                                @if($versions->count() > 1)
                                    <button class="dmt-toggle"
                                        @click="expanded['{{ $fileKey }}'] = !expanded['{{ $fileKey }}']"
                                        x-text="expanded['{{ $fileKey }}'] ? 'Hide older versions' : 'Show {{ $versions->count() - 1 }} older version(s)'">
                                    </button>

                                    <template x-if="expanded['{{ $fileKey }}']">
                                        <div>
                                            @foreach($versions->skip(1) as $file)
                                                <div class="dmt-version-row" @click="openSlider({{ $file->id }}, '{{ addslashes($item['label']) }}')">
                                                    <div>
                                                        <div><strong>v{{ $file->version }}</strong> &mdash; {{ $file->file_name }}</div>
                                                        <div class="dmt-version-meta">{{ $file->created_at->format('M d, Y H:i') }}</div>
                                                    </div>
                                                    <span class="dmt-version-open"><i class="fas fa-chevron-right"></i></span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </template>
                                @endif
                            @else
                                <div class="dmt-no-files">No files uploaded yet</div>
                            @endif

                            @if($uploadingSection === $sectionKey && $uploadingItem === $itemKey)
                                <div class="dmt-upload-form">
                                    <input type="file" wire:model="uploadFile" accept=".xlsx,.xls,.csv">
                                    @error('uploadFile') <div style="color: #dc2626; font-size: 11px; margin-bottom: 6px;">{{ $message }}</div> @enderror

                                    <textarea wire:model="uploadRemark" placeholder="Add a remark (optional)..."></textarea>
                                    @error('uploadRemark') <div style="color: #dc2626; font-size: 11px; margin-bottom: 6px;">{{ $message }}</div> @enderror

                                    <div class="dmt-upload-form-actions">
                                        <button wire:click="cancelUpload" class="dmt-cancel-btn">Cancel</button>
                                        <button wire:click="submitUpload" class="dmt-submit-btn" wire:loading.attr="disabled" wire:target="uploadFile, submitUpload">
                                            <span wire:loading.remove wire:target="submitUpload">Submit</span>
                                            <span wire:loading wire:target="submitUpload">Uploading...</span>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        {{-- Read-only slide-over panel --}}
        <template x-if="sliderOpen">
            <div>
                <div class="dmt-overlay" @click="closeSlider()"></div>
                <div class="dmt-slider" :class="{ 'open': sliderVisible }">
                    <div class="dmt-slider-header">
                        <div class="dmt-slider-title" x-text="sliderTitle"></div>
                        <button class="dmt-slider-close" @click="closeSlider()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="dmt-slider-body">
                        <div class="dmt-slider-section">
                            <div class="dmt-slider-label">File Details</div>
                            <div class="dmt-slider-value">
                                <div><strong>Version:</strong> <span x-text="'v' + activeFile.version"></span></div>
                                <div><strong>File:</strong> <span x-text="activeFile.file_name"></span></div>
                                <div><strong>Uploaded:</strong> <span x-text="activeFile.created_at"></span></div>
                            </div>
                        </div>

                        <div class="dmt-slider-section">
                            <div class="dmt-slider-label">Status</div>
                            <span class="dmt-slider-status-badge"
                                :class="{
                                    'dmt-badge-pending': activeFile.status === 'pending',
                                    'dmt-badge-reviewed': activeFile.status === 'reviewed',
                                    'dmt-badge-accepted': activeFile.status === 'accepted',
                                    'dmt-badge-rejected': activeFile.status === 'rejected',
                                }"
                                x-text="statusLabels[activeFile.status] || activeFile.status">
                            </span>
                        </div>

                        <template x-if="activeFile.remark">
                            <div class="dmt-slider-section">
                                <div class="dmt-slider-label">Your Remark</div>
                                <div class="dmt-slider-remark-box" x-text="activeFile.remark"></div>
                            </div>
                        </template>

                        <div class="dmt-slider-section">
                            <div class="dmt-slider-label">Implementer Comment</div>
                            <template x-if="activeFile.implementer_remark">
                                <div class="dmt-slider-implementer-box">
                                    <div class="dmt-slider-implementer-header">
                                        <i class="fas fa-comment-dots"></i> Feedback from Implementer
                                    </div>
                                    <div class="dmt-slider-implementer-text" x-text="activeFile.implementer_remark"></div>
                                </div>
                            </template>
                            <template x-if="!activeFile.implementer_remark">
                                <div class="dmt-slider-no-remark">No comments yet</div>
                            </template>
                        </div>
                    </div>
                    <div class="dmt-slider-footer">
                        <a :href="activeFile.download_url" class="dmt-btn-download-slider">
                            <i class="fas fa-download"></i> Download File
                        </a>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
    function dmtCustomer() {
        return {
            expanded: {},
            sliderOpen: false,
            sliderVisible: false,
            sliderTitle: '',
            activeFile: {},
            filesData: @json($filesJson),
            statusLabels: { pending: 'Pending', reviewed: 'Reviewed', accepted: 'Accepted', rejected: 'Rejected' },

            openSlider(fileId, label) {
                const file = this.filesData[fileId];
                if (!file) return;
                this.activeFile = file;
                this.sliderTitle = label + ' (v' + file.version + ')';
                this.sliderOpen = true;
                this.$nextTick(() => { this.sliderVisible = true; });
            },

            closeSlider() {
                this.sliderVisible = false;
                setTimeout(() => { this.sliderOpen = false; }, 250);
            },
        };
    }
</script>
