<x-filament-panels::page>
    <style>
        .dmt-admin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .dmt-admin-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px;
            transition: all 0.2s ease;
        }
        .dmt-admin-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        }
        .dmt-admin-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }
        .dmt-admin-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }
        .dmt-admin-title {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
        }
        .dmt-admin-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }
        .dmt-admin-badge-uploaded {
            background: #dcfce7;
            color: #16a34a;
        }
        .dmt-admin-badge-empty {
            background: #f1f5f9;
            color: #94a3b8;
        }
        .dmt-admin-info {
            margin-top: 12px;
            font-size: 13px;
            color: #64748b;
            line-height: 1.6;
        }
        .dmt-admin-actions {
            display: flex;
            gap: 8px;
            margin-top: 16px;
        }
        .dmt-admin-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 7px 14px;
            border: none;
            border-radius: 7px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .dmt-admin-btn:hover {
            transform: translateY(-1px);
        }
        .dmt-admin-btn-download {
            background: #eff6ff;
            color: #2563eb;
        }
        .dmt-admin-btn-download:hover {
            background: #dbeafe;
        }
        .dmt-admin-btn-delete {
            background: #fef2f2;
            color: #dc2626;
        }
        .dmt-admin-btn-delete:hover {
            background: #fee2e2;
        }
        .dmt-admin-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            transform: none;
        }
    </style>

    <div class="dmt-admin-grid">
        @foreach($this->getSections() as $key => $section)
            <div class="dmt-admin-card">
                <div class="dmt-admin-header">
                    <div class="dmt-admin-icon" style="background: {{ $section['color'] }}15; color: {{ $section['color'] }};">
                        <i class="{{ $section['icon'] }}"></i>
                    </div>
                    <div>
                        <div class="dmt-admin-title">{{ $section['label'] }}</div>
                        @if($section['exists'])
                            <span class="dmt-admin-badge dmt-admin-badge-uploaded">
                                <i class="fas fa-check-circle"></i> Uploaded
                            </span>
                        @else
                            <span class="dmt-admin-badge dmt-admin-badge-empty">
                                <i class="fas fa-minus-circle"></i> Not Uploaded
                            </span>
                        @endif
                    </div>
                </div>

                @if($section['exists'])
                    <div class="dmt-admin-info">
                        <div><strong>File:</strong> {{ $section['file'] }}</div>
                        <div><strong>Size:</strong> {{ $section['size'] }}</div>
                        <div><strong>Updated:</strong> {{ $section['lastModified'] }}</div>
                    </div>
                @else
                    <div class="dmt-admin-info">
                        <div>No template uploaded yet. Use the <strong>Upload Template</strong> button above to upload <strong>{{ $section['file'] }}</strong>.</div>
                    </div>
                @endif

                <div class="dmt-admin-actions">
                    <button
                        class="dmt-admin-btn dmt-admin-btn-download"
                        wire:click="downloadTemplate('{{ $key }}')"
                        @if(!$section['exists']) disabled @endif
                    >
                        <i class="fas fa-download"></i> Download
                    </button>
                    <button
                        class="dmt-admin-btn dmt-admin-btn-delete"
                        wire:click="deleteTemplate('{{ $key }}')"
                        wire:confirm="Are you sure you want to delete the {{ $section['label'] }} template?"
                        @if(!$section['exists']) disabled @endif
                    >
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
