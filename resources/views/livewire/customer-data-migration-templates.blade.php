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
        .dmt-version-badge {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            padding: 2px 8px;
            border-radius: 10px;
            background: #dcfce7;
            color: #16a34a;
            font-size: 11px;
            font-weight: 600;
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
    </style>

    <div class="dmt-container">
        <h2 class="dmt-title">Data Migration Templates</h2>
        <p class="dmt-subtitle">Download Excel templates, fill them in, and upload back for processing</p>

        @if(session()->has('success'))
            <div class="dmt-flash dmt-flash-success">{{ session('success') }}</div>
        @endif
        @if(session()->has('error'))
            <div class="dmt-flash dmt-flash-error">{{ session('error') }}</div>
        @endif

        <div class="dmt-grid">
            @foreach($this->getSections() as $sectionKey => $section)
                <div class="dmt-card">
                    <div class="dmt-card-header">
                        <div class="dmt-card-icon" style="background: {{ $section['color'] }}15; color: {{ $section['color'] }};">
                            <i class="{{ $section['icon'] }}"></i>
                        </div>
                        <div class="dmt-card-name">{{ $section['label'] }}</div>
                    </div>

                    @foreach($section['items'] as $itemKey => $item)
                        <div class="dmt-sub-item">
                            <div class="dmt-sub-item-row">
                                <div class="dmt-sub-item-info">
                                    <div class="dmt-sub-item-label">
                                        {{ $item['label'] }}
                                        @if($item['latestVersion'])
                                            <span class="dmt-version-badge">
                                                <i class="fas fa-check"></i> v{{ $item['latestVersion'] }} uploaded
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
                                            <i class="fas fa-download"></i> Download
                                        </button>
                                        <button wire:click="startUpload('{{ $sectionKey }}', '{{ $itemKey }}')" class="dmt-upload-btn">
                                            <i class="fas fa-upload"></i> Upload
                                        </button>
                                    @else
                                        <span class="dmt-coming-soon">Coming soon</span>
                                    @endif
                                </div>
                            </div>

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
    </div>
</div>
