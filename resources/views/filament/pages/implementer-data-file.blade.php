<x-filament-panels::page>
    <style>
        .dmt-admin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
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
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }
        .dmt-admin-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }
        .dmt-admin-title {
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
        .dmt-sub-item-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 6px;
        }
        .dmt-sub-item-label {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
        }
        .dmt-admin-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            flex-shrink: 0;
        }
        .dmt-admin-badge-uploaded {
            background: #dcfce7;
            color: #16a34a;
        }
        .dmt-admin-badge-empty {
            background: #f1f5f9;
            color: #94a3b8;
        }
        .dmt-sub-item-info {
            font-size: 12px;
            color: #94a3b8;
            margin-bottom: 8px;
        }
        .dmt-sub-item-actions {
            display: flex;
            gap: 6px;
        }
        .dmt-admin-btn {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 5px 12px;
            border: none;
            border-radius: 6px;
            font-size: 11px;
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
        @foreach($this->getSections() as $sectionKey => $section)
            <div class="dmt-admin-card">
                <div class="dmt-admin-header">
                    <div class="dmt-admin-icon" style="background: {{ $section['color'] }}15; color: {{ $section['color'] }};">
                        <i class="{{ $section['icon'] }}"></i>
                    </div>
                    <div class="dmt-admin-title">{{ $section['label'] }}</div>
                </div>

                @foreach($section['items'] as $itemKey => $item)
                    <div class="dmt-sub-item">
                        <div class="dmt-sub-item-header">
                            <span class="dmt-sub-item-label">{{ $item['label'] }}</span>
                            @if($item['exists'])
                                <span class="dmt-admin-badge dmt-admin-badge-uploaded">
                                    <i class="fas fa-check-circle"></i> Uploaded
                                </span>
                            @else
                                <span class="dmt-admin-badge dmt-admin-badge-empty">
                                    <i class="fas fa-minus-circle"></i> Not Uploaded
                                </span>
                            @endif
                        </div>

                        @if($item['exists'])
                            <div class="dmt-sub-item-info">
                                {{ $item['size'] }} &bull; {{ $item['lastModified'] }}
                            </div>
                        @endif

                        <div class="dmt-sub-item-actions">
                            <button
                                class="dmt-admin-btn dmt-admin-btn-download"
                                wire:click="downloadTemplate('{{ $sectionKey }}', '{{ $itemKey }}')"
                                @if(!$item['exists']) disabled @endif
                            >
                                <i class="fas fa-download"></i> Download
                            </button>
                            <button
                                class="dmt-admin-btn dmt-admin-btn-delete"
                                wire:click="deleteTemplate('{{ $sectionKey }}', '{{ $itemKey }}')"
                                wire:confirm="Are you sure you want to delete {{ $item['label'] }}?"
                                @if(!$item['exists']) disabled @endif
                            >
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
