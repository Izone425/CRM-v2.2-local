<div>
    <style>
        .ctf-container {
            max-width: 1000px;
        }
        .ctf-title {
            font-size: 28px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 4px;
        }
        .ctf-subtitle {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 24px;
        }
        .ctf-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 28px;
            flex-wrap: wrap;
        }
        .ctf-tab {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #64748b;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .ctf-tab:hover {
            border-color: #667eea;
            color: #667eea;
            background: #f0f0ff;
        }
        .ctf-tab-active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border-color: transparent;
        }
        .ctf-tab-active:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border-color: transparent;
        }
        .ctf-module-group {
            margin-bottom: 28px;
        }
        .ctf-module-label {
            font-size: 15px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .ctf-module-icon {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            color: #fff;
        }
        .ctf-module-icon.attendance { background: #667eea; }
        .ctf-module-icon.leave { background: #f59e0b; }
        .ctf-module-icon.payroll { background: #10b981; }
        .ctf-module-icon.general { background: #64748b; }
        .ctf-file-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            margin-bottom: 8px;
            transition: all 0.2s ease;
        }
        .ctf-file-row:hover {
            border-color: #cbd5e1;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }
        .ctf-file-info {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
            flex: 1;
        }
        .ctf-file-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: #667eea;
            font-size: 16px;
        }
        .ctf-file-details {
            min-width: 0;
        }
        .ctf-file-name {
            font-size: 14px;
            font-weight: 500;
            color: #1e293b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .ctf-file-meta {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 2px;
        }
        .ctf-btn-download {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 16px;
            border-radius: 8px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            font-size: 13px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }
        .ctf-btn-download:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        .ctf-btn-open {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 16px;
            border-radius: 8px;
            background: #f0f0ff;
            color: #667eea;
            font-size: 13px;
            font-weight: 500;
            border: 1px solid #667eea;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            flex-shrink: 0;
        }
        .ctf-btn-open:hover {
            background: #667eea;
            color: #fff;
        }
        .ctf-empty {
            text-align: center;
            padding: 60px 20px;
            color: #94a3b8;
        }
        .ctf-empty i {
            font-size: 48px;
            margin-bottom: 16px;
            display: block;
            color: #cbd5e1;
        }
        .ctf-empty h3 {
            font-size: 18px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 4px;
        }
        .ctf-empty p {
            font-size: 14px;
        }
        .ctf-version-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            background: #f1f5f9;
            color: #64748b;
            font-size: 11px;
            font-weight: 600;
            margin-left: 4px;
        }
    </style>

    <div class="ctf-container">
        <h2 class="ctf-title">Webinar Recording & Training Decks</h2>
        <p class="ctf-subtitle">Access your training materials, SOPs, guidelines, and recordings.</p>

        {{-- Sub-tabs --}}
        <div class="ctf-tabs">
            @foreach($tabs as $key => $tab)
                <button wire:click="switchTab('{{ $key }}')"
                        wire:loading.attr="disabled"
                        class="ctf-tab {{ $activeTab === $key ? 'ctf-tab-active' : '' }}">
                    <i class="{{ $tab['icon'] }}"></i>
                    <span>{{ $tab['label'] }}</span>
                </button>
            @endforeach
        </div>

        {{-- File listing grouped by module_type --}}
        @php $files = $this->files; @endphp

        @if($files->isEmpty())
            <div class="ctf-empty">
                <i class="fas fa-folder-open"></i>
                <h3>No files available</h3>
                <p>No {{ strtolower(str_replace('_', ' ', $activeTab)) }} files have been uploaded yet.</p>
            </div>
        @else
            @foreach($files as $moduleType => $moduleFiles)
                <div class="ctf-module-group">
                    <div class="ctf-module-label">
                        @switch($moduleType)
                            @case('Attendance')
                                <span class="ctf-module-icon attendance"><i class="fas fa-clock"></i></span>
                                DAY 1 — ATTENDANCE MODULE
                                @break
                            @case('Leave_Claim')
                                <span class="ctf-module-icon leave"><i class="fas fa-calendar-check"></i></span>
                                DAY 2 — LEAVE & CLAIM MODULE
                                @break
                            @case('Payroll')
                                <span class="ctf-module-icon payroll"><i class="fas fa-money-bill-wave"></i></span>
                                DAY 3 — PAYROLL MODULE
                                @break
                            @default
                                <span class="ctf-module-icon general"><i class="fas fa-file-alt"></i></span>
                                {{ $moduleType ?: 'General' }}
                        @endswitch
                    </div>

                    @foreach($moduleFiles as $file)
                        <div class="ctf-file-row">
                            <div class="ctf-file-info">
                                <div class="ctf-file-icon">
                                    @if($file->is_link)
                                        <i class="fas fa-link"></i>
                                    @else
                                        <i class="fas fa-file-pdf"></i>
                                    @endif
                                </div>
                                <div class="ctf-file-details">
                                    <div class="ctf-file-name">
                                        {{ $file->file_name }}
                                        @if($file->version)
                                            <span class="ctf-version-badge">{{ $file->version }}</span>
                                        @endif
                                    </div>
                                    <div class="ctf-file-meta">
                                        Uploaded {{ $file->created_at->format('d M Y') }}
                                    </div>
                                </div>
                            </div>

                            @if($file->is_link)
                                <a href="{{ $file->file_path }}" target="_blank" class="ctf-btn-open">
                                    <i class="fas fa-external-link-alt"></i>
                                    <span>Open</span>
                                </a>
                            @else
                                <button wire:click="downloadFile({{ $file->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="downloadFile({{ $file->id }})"
                                        class="ctf-btn-download">
                                    <span wire:loading.remove wire:target="downloadFile({{ $file->id }})">
                                        <i class="fas fa-download"></i>
                                    </span>
                                    <span wire:loading wire:target="downloadFile({{ $file->id }})">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </span>
                                    <span>Download</span>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endforeach
        @endif
    </div>
</div>
