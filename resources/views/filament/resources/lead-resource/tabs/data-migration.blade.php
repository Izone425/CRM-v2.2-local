@php
    use App\Models\CustomerDataMigrationFile;
    use App\Models\Customer;
    use App\Models\ImplementerTicketReply;
    use App\Support\DataFileSections;
    use Illuminate\Support\Facades\Storage;

    $lead = $this->record;

    $formatBytes = function (?int $bytes): string {
        if ($bytes === null) return '—';
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    };

    $rawFiles = ImplementerTicketReply::query()
        ->whereHas('ticket', fn ($q) => $q->where('lead_id', $lead->id))
        ->where('sender_type', Customer::class)
        ->whereNotNull('attachments')
        ->with(['ticket:id,ticket_number'])
        ->latest()
        ->limit(200)
        ->get()
        ->flatMap(function ($reply) use ($formatBytes) {
            return collect($reply->attachments ?? [])->map(function ($path) use ($reply, $formatBytes) {
                $exists = Storage::disk('public')->exists($path);
                return [
                    'path'          => $path,
                    'name'          => basename($path),
                    'size'          => $exists ? $formatBytes(Storage::disk('public')->size($path)) : '—',
                    'exists'        => $exists,
                    'reply_id'      => $reply->id,
                    'ticket_id'     => $reply->implementer_ticket_id,
                    'ticket_number' => $reply->ticket->ticket_number ?? null,
                    'uploaded_at'   => $reply->created_at,
                ];
            });
        })
        ->values();

    $templateSections = DataFileSections::map();

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
                'uploaded_by_type' => $file->uploaded_by_type ?? 'customer',
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

    /* Raw Files panel — sits as a 6th cell in the dm-grid, beside Payroll */
    .dmrf-panel {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    .dmrf-table-wrap {
        flex: 1;
        overflow-y: auto;
        max-height: 360px;
    }
    .dmrf-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 18px;
        background: linear-gradient(135deg, #f8fafc 0%, #eef2f7 100%);
        border-bottom: 1px solid #e2e8f0;
    }
    .dmrf-title {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 700;
        color: #003c75;
    }
    .dmrf-title i {
        color: #1a6dd4;
    }
    .dmrf-count {
        background: #1a6dd4;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 9px;
        border-radius: 999px;
        min-width: 22px;
        text-align: center;
    }
    .dmrf-empty {
        padding: 28px 18px;
        text-align: center;
        color: #94a3b8;
        font-size: 13px;
        font-style: italic;
    }
    .dmrf-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .dmrf-table thead th {
        text-align: left;
        padding: 10px 16px;
        background: #fafbfc;
        color: #64748b;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        border-bottom: 1px solid #e2e8f0;
    }
    .dmrf-table tbody tr {
        transition: background 0.15s;
    }
    .dmrf-table tbody tr:hover {
        background: #f8fafc;
    }
    .dmrf-table tbody tr + tr td {
        border-top: 1px solid #f1f5f9;
    }
    .dmrf-table td {
        padding: 11px 16px;
        color: #334155;
        vertical-align: middle;
    }
    .dmrf-file {
        font-weight: 600;
        color: #1e293b;
        word-break: break-all;
    }
    .dmrf-file .dmrf-missing {
        margin-left: 6px;
        font-size: 10px;
        font-weight: 700;
        color: #dc2626;
        background: #fef2f2;
        padding: 1px 6px;
        border-radius: 4px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    .dmrf-tlink {
        color: #1a6dd4;
        font-weight: 600;
        font-family: 'JetBrains Mono', ui-monospace, monospace;
        text-decoration: none;
    }
    .dmrf-tlink:hover {
        color: #003c75;
        text-decoration: underline;
    }
    .dmrf-date {
        color: #64748b;
        font-size: 12px;
    }
    .dmrf-dl {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        background: #eff6ff;
        color: #1a6dd4;
        border-radius: 6px;
        text-decoration: none;
        transition: all 0.15s;
    }
    .dmrf-dl:hover {
        background: #1a6dd4;
        color: #fff;
        transform: translateY(-1px);
    }
    .dmrf-dl[aria-disabled="true"] {
        opacity: 0.4;
        pointer-events: none;
    }

    /* Drag source: Raw Files rows */
    .dmrf-table tbody tr[draggable="true"] { cursor: grab; }
    .dmrf-table tbody tr[draggable="true"]:active { cursor: grabbing; }
    .dmrf-table tbody tr.dm-drag-active { opacity: 0.45; cursor: grabbing; }

    /* Drop targets: module item containers */
    .dm-item.dm-drop-target {
        transition: background 0.15s, box-shadow 0.15s, border-color 0.15s;
    }
    .dm-item.dm-drag-over {
        background: #eef6ff;
        box-shadow: inset 0 0 0 2px #1a6dd4;
        border-radius: 8px;
    }
    .dm-item.dm-drop-disabled { opacity: 0.55; pointer-events: none; }

    /* Implementer-uploaded badge on version rows */
    .dm-from-implementer {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #ecfeff;
        color: #0e7490;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        padding: 2px 6px;
        border-radius: 4px;
        margin-left: 6px;
        letter-spacing: 0.4px;
    }
    .dm-from-implementer i { font-size: 9px; }

    /* Un-assign (X) button on implementer rows */
    .dm-unassign {
        background: transparent;
        border: 0;
        color: #94a3b8;
        cursor: pointer;
        padding: 4px 6px;
        border-radius: 4px;
        font-size: 12px;
        transition: all 0.15s;
        margin-left: 4px;
    }
    .dm-unassign:hover { background: #fef2f2; color: #dc2626; }

    /* Drop spinner */
    .dm-drop-spinner {
        display: inline-block;
        width: 12px;
        height: 12px;
        border: 2px solid #cbd5e1;
        border-top-color: #1a6dd4;
        border-radius: 50%;
        animation: dm-spin 0.7s linear infinite;
        margin-left: 6px;
        vertical-align: middle;
    }
    @keyframes dm-spin { to { transform: rotate(360deg); } }

    /* Floating drop toast */
    .dm-drop-toast {
        position: fixed;
        top: 80px;
        right: 24px;
        z-index: 1100;
        padding: 10px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        animation: dm-toast-in 0.2s ease-out;
    }
    .dm-drop-toast.dm-drop-toast-success { background: #dcfce7; color: #16a34a; }
    .dm-drop-toast.dm-drop-toast-error { background: #fef2f2; color: #dc2626; }
    @keyframes dm-toast-in { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }
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
                    <div class="dm-item dm-drop-target"
                        :class="{
                            'dm-drag-over':  dropHover === '{{ $fileKey }}' && dragSourcePath !== null,
                            'dm-drop-disabled': dropping === '{{ $fileKey }}'
                        }"
                        @dragover.prevent="dragSourcePath !== null && (dropHover = '{{ $fileKey }}')"
                        @dragleave="dropHover === '{{ $fileKey }}' && (dropHover = null)"
                        @drop.prevent="onDrop($event, '{{ $sectionKey }}', '{{ $itemKey }}')">
                        <div class="dm-item-header">
                            <span class="dm-item-label">{{ $item['label'] }}</span>
                            @if($latest)
                                <span class="dm-badge dm-badge-{{ $latest->status }}">
                                    {{ ucfirst($latest->status) }} (v{{ $latest->version }})
                                </span>
                            @else
                                <span class="dm-badge dm-badge-none">No uploads</span>
                            @endif
                            <span x-show="dropping === '{{ $fileKey }}'" class="dm-drop-spinner" title="Assigning..."></span>
                        </div>

                        @if($versions->isNotEmpty())
                            {{-- Latest version --}}
                            <div class="dm-version-row" @click="openSlider({{ $latest->id }}, '{{ $item['label'] }}')">
                                <div>
                                    <div>
                                        <strong>v{{ $latest->version }}</strong> &mdash; {{ $latest->file_name }}
                                        @if(($latest->uploaded_by_type ?? 'customer') === 'implementer')
                                            <span class="dm-from-implementer" title="Assigned by implementer">
                                                <i class="fas fa-user-tie"></i> From Implementer
                                            </span>
                                        @endif
                                    </div>
                                    <div class="dm-version-meta">{{ $latest->created_at->format('M d, Y H:i') }}</div>
                                </div>
                                <span style="display:inline-flex;align-items:center;">
                                    @if(($latest->uploaded_by_type ?? 'customer') === 'implementer')
                                        <button type="button" class="dm-unassign"
                                            @click.stop="confirmUnassign({{ $latest->id }}, @js($latest->file_name))"
                                            title="Un-assign (delete) this implementer-uploaded file">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                    <span class="dm-version-open"><i class="fas fa-chevron-right"></i></span>
                                </span>
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
                                                    <div>
                                                        <strong>v{{ $file->version }}</strong> &mdash; {{ $file->file_name }}
                                                        @if(($file->uploaded_by_type ?? 'customer') === 'implementer')
                                                            <span class="dm-from-implementer" title="Assigned by implementer">
                                                                <i class="fas fa-user-tie"></i> From Implementer
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="dm-version-meta">{{ $file->created_at->format('M d, Y H:i') }}</div>
                                                </div>
                                                <span style="display:inline-flex;align-items:center;">
                                                    @if(($file->uploaded_by_type ?? 'customer') === 'implementer')
                                                        <button type="button" class="dm-unassign"
                                                            @click.stop="confirmUnassign({{ $file->id }}, @js($file->file_name))"
                                                            title="Un-assign (delete) this implementer-uploaded file">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @endif
                                                    <span class="dm-version-open"><i class="fas fa-chevron-right"></i></span>
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </template>
                            @endif
                        @else
                            <div class="dm-no-files">No files uploaded yet — drop a Raw File here to assign</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endforeach

        {{-- Raw Files panel: customer-uploaded ticket attachments aggregated for this lead --}}
        <div class="dmrf-panel">
            <div class="dmrf-header">
                <span class="dmrf-title"><i class="fas fa-paperclip"></i> Raw Files (from tickets)</span>
                <span class="dmrf-count">{{ $rawFiles->count() }}</span>
            </div>
            @if($rawFiles->isNotEmpty())
                <div class="dmrf-table-wrap">
                    <table class="dmrf-table">
                        <thead>
                            <tr>
                                <th>File</th>
                                <th style="width: 80px;">Size</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rawFiles as $f)
                                @if($f['exists'])
                                    <tr draggable="true"
                                        @dragstart="onDragStart($event, {{ (int) $f['reply_id'] }}, @js($f['path']), @js($f['name']))"
                                        @dragend="onDragEnd()"
                                        :class="{ 'dm-drag-active': dragSourcePath === @js($f['path']) }"
                                        title="Drag onto a module slot to assign this file">
                                @else
                                    <tr>
                                @endif
                                    <td class="dmrf-file" title="{{ ($f['ticket_number'] ? 'From ' . $f['ticket_number'] . ' — ' : '') . 'Uploaded ' . $f['uploaded_at']->format('M d, Y H:i') }}">
                                        {{ $f['name'] }}
                                        @if(!$f['exists'])
                                            <span class="dmrf-missing">missing</span>
                                        @endif
                                    </td>
                                    <td>{{ $f['size'] }}</td>
                                    <td>
                                        @if($f['exists'])
                                            <a href="{{ route('admin.implementer-ticket-attachment.download', ['path' => $f['path']]) }}"
                                               class="dmrf-dl"
                                               title="Download {{ $f['name'] }} — uploaded {{ $f['uploaded_at']->diffForHumans() }}">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @else
                                            <span class="dmrf-dl" aria-disabled="true" title="File missing on disk"><i class="fas fa-download"></i></span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="dmrf-empty">No attachments from ticket replies yet.</div>
            @endif
        </div>
    </div>

    {{-- Floating drop toast --}}
    <template x-if="dropToast">
        <div class="dm-drop-toast" :class="dropToast && dropToast.kind === 'error' ? 'dm-drop-toast-error' : 'dm-drop-toast-success'">
            <span x-text="dropToast ? dropToast.msg : ''"></span>
        </div>
    </template>

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

            // Drag-drop state
            dragSourcePath: null,
            dragSourceName: null,
            dragSourceReplyId: null,
            dropHover: null,
            dropping: null,
            dropToast: null,

            onDragStart(evt, replyId, path, name) {
                this.dragSourcePath = path;
                this.dragSourceName = name;
                this.dragSourceReplyId = replyId;
                if (evt.dataTransfer) {
                    evt.dataTransfer.effectAllowed = 'copy';
                    evt.dataTransfer.setData('text/plain', name);
                }
            },
            onDragEnd() {
                this.dragSourcePath = null;
                this.dragSourceName = null;
                this.dragSourceReplyId = null;
                this.dropHover = null;
            },
            async onDrop(evt, section, item) {
                if (!this.dragSourcePath) return;
                const slot = section + '|' + item;
                const sourceName = this.dragSourceName;
                this.dropHover = null;
                this.dropping = slot;
                try {
                    const resp = await fetch('/admin/api/data-migration-file/assign-from-raw', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            lead_id: {{ (int) $lead->id }},
                            section: section,
                            item: item,
                            ticket_reply_id: this.dragSourceReplyId,
                            attachment_path: this.dragSourcePath,
                        }),
                    });
                    if (!resp.ok) {
                        const err = await resp.json().catch(() => ({}));
                        const msg = err.error || err.message || ('HTTP ' + resp.status + ' — failed to assign file');
                        this.flashToast(msg, 'error');
                        return;
                    }
                    await resp.json();
                    this.flashToast('Assigned ' + sourceName + ' to slot.', 'success');
                    setTimeout(() => window.location.reload(), 600);
                } catch (e) {
                    this.flashToast('Network error during assignment', 'error');
                } finally {
                    this.dropping = null;
                    this.dragSourcePath = null;
                    this.dragSourceName = null;
                    this.dragSourceReplyId = null;
                }
            },
            confirmUnassign(fileId, fileName) {
                if (!confirm('Un-assign "' + fileName + '"? This will permanently delete the implementer-uploaded copy. The original ticket attachment is unaffected.')) return;
                fetch('/admin/api/data-migration-file/' + fileId + '/unassign', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                }).then(r => {
                    if (!r.ok) return r.json().then(err => { throw new Error(err.error || 'Failed'); });
                    this.flashToast('Un-assigned.', 'success');
                    setTimeout(() => window.location.reload(), 500);
                }).catch(e => this.flashToast(e.message, 'error'));
            },
            flashToast(msg, kind) {
                this.dropToast = { msg, kind };
                setTimeout(() => { this.dropToast = null; }, 2500);
            },

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
