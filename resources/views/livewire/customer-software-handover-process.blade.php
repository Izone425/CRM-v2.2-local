<div class="cshp-container">
<style>
    .cshp-container {
        max-width: 800px;
    }
    .cshp-title {
        font-size: 28px;
        font-weight: 700;
        color: #667eea;
        margin-bottom: 4px;
    }
    .cshp-subtitle {
        font-size: 14px;
        color: #64748b;
        margin-bottom: 24px;
    }
    .cshp-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
    }
    .cshp-file-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.15s;
    }
    .cshp-file-row:last-child {
        border-bottom: none;
    }
    .cshp-file-row:hover {
        background: #fafbff;
    }
    .cshp-file-info {
        display: flex;
        align-items: center;
        gap: 14px;
        flex: 1;
        min-width: 0;
    }
    .cshp-file-icon {
        width: 40px;
        height: 40px;
        background: #f1f5f9;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        color: #64748b;
        flex-shrink: 0;
    }
    .cshp-file-icon.pdf { background: #fef2f2; color: #dc2626; }
    .cshp-file-icon.doc { background: #eff6ff; color: #2563eb; }
    .cshp-file-icon.xls { background: #dcfce7; color: #16a34a; }
    .cshp-file-details {
        min-width: 0;
    }
    .cshp-file-name {
        font-size: 14px;
        font-weight: 600;
        color: #1e293b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .cshp-file-meta {
        font-size: 12px;
        color: #94a3b8;
        margin-top: 2px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .cshp-version-tag {
        display: inline-flex;
        align-items: center;
        padding: 1px 8px;
        background: #eef2ff;
        color: #667eea;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 700;
    }
    .cshp-btn-download {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        background: #667eea;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
        flex-shrink: 0;
    }
    .cshp-btn-download:hover {
        background: #5a6fd6;
    }
    .cshp-empty {
        text-align: center;
        padding: 60px 20px;
    }
    .cshp-empty i {
        font-size: 48px;
        color: #cbd5e1;
        margin-bottom: 16px;
        display: block;
    }
    .cshp-empty-title {
        font-size: 16px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 4px;
    }
    .cshp-empty-sub {
        font-size: 13px;
        color: #94a3b8;
    }
    .cshp-remark {
        font-size: 11px;
        color: #64748b;
        font-style: italic;
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 300px;
    }
</style>

    <h2 class="cshp-title">Software Handover Process</h2>
    <p class="cshp-subtitle">Download software handover files provided by your implementer</p>

    <div class="cshp-card">
        @if($files->isEmpty())
            <div class="cshp-empty">
                <i class="fas fa-folder-open"></i>
                <div class="cshp-empty-title">No handover files available yet</div>
                <div class="cshp-empty-sub">Your implementer will upload software handover files here once ready</div>
            </div>
        @else
            @foreach($files as $file)
                @php
                    $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
                    $iconClass = match(true) {
                        $ext === 'pdf' => 'pdf',
                        in_array($ext, ['doc', 'docx']) => 'doc',
                        in_array($ext, ['xls', 'xlsx']) => 'xls',
                        default => '',
                    };
                    $icon = match(true) {
                        $ext === 'pdf' => 'fas fa-file-pdf',
                        in_array($ext, ['doc', 'docx']) => 'fas fa-file-word',
                        in_array($ext, ['xls', 'xlsx']) => 'fas fa-file-excel',
                        default => 'fas fa-file',
                    };
                @endphp
                <div class="cshp-file-row">
                    <div class="cshp-file-info">
                        <div class="cshp-file-icon {{ $iconClass }}">
                            <i class="{{ $icon }}"></i>
                        </div>
                        <div class="cshp-file-details">
                            <div class="cshp-file-name" title="{{ $file->file_name }}">{{ $file->file_name }}</div>
                            <div class="cshp-file-meta">
                                <span class="cshp-version-tag">v{{ $file->version }}</span>
                                <span>{{ $file->created_at->format('M d, Y, h:i A') }}</span>
                                <span>by {{ $file->uploader?->name ?? 'Staff' }}</span>
                            </div>
                            @if($file->remark)
                                <div class="cshp-remark" title="{{ $file->remark }}">{{ $file->remark }}</div>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('customer.software-handover-process.download', $file->id) }}" class="cshp-btn-download" style="text-decoration: none; color: #fff;">
                        <i class="fas fa-download"></i>
                        Download
                    </a>
                </div>
            @endforeach
        @endif
    </div>
</div>
