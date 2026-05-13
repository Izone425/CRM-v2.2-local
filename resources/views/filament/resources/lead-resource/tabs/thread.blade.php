@php
    use App\Models\ImplementerTicket;
    use App\Enums\ImplementerTicketStatus;

    $lead = $this->record;
    // Use the live request URL so the encrypted lead ID + active tab survive the round-trip.
    // (LeadResource::getUrl() returns the raw integer ID, which ViewLeadRecord::mount can't decrypt.)
    $leadViewUrl = urlencode(request()->fullUrl());

    $tickets = ImplementerTicket::where('lead_id', $lead->id)
        ->with(['customer', 'implementerUser', 'replies'])
        ->orderBy('created_at', 'desc')
        ->get();

    $totalTickets = $tickets->count();
    $openTickets = $tickets->where('status', '!=', ImplementerTicketStatus::CLOSED)->count();
    $overdueTickets = $tickets->filter(fn($t) => $t->isOverdue())->count();
    $closedTickets = $tickets->where('status', ImplementerTicketStatus::CLOSED)->count();
@endphp

<div x-data="thrTicketList()" class="thr-wrap">
    {{-- Summary Bar --}}
    <div class="thr-summary">
        <div class="thr-stat" style="--accent: #3B82F6">
            <div class="thr-stat-val">{{ $totalTickets }}</div>
            <div class="thr-stat-label">Total</div>
        </div>
        <div class="thr-stat" style="--accent: #F59E0B">
            <div class="thr-stat-val">{{ $openTickets }}</div>
            <div class="thr-stat-label">Active</div>
        </div>
        <div class="thr-stat" style="--accent: #EF4444">
            <div class="thr-stat-val">{{ $overdueTickets }}</div>
            <div class="thr-stat-label">Overdue</div>
        </div>
        <div class="thr-stat" style="--accent: #10B981">
            <div class="thr-stat-val">{{ $closedTickets }}</div>
            <div class="thr-stat-label">Closed</div>
        </div>
    </div>

    @if($tickets->isEmpty())
        <div class="thr-empty">
            <div class="thr-empty-icon">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
            </div>
            <div class="thr-empty-title">No threads found</div>
            <div class="thr-empty-desc">No project threads have been created for this company yet.</div>
        </div>
    @else
        {{-- Filter row --}}
        <div class="thr-filter-row">
            <input type="text" x-model="search" placeholder="Search tickets..." class="thr-search-input" />
            <div class="thr-filter-pills">
                <button @click="statusFilter = ''" class="thr-pill" :class="{ 'thr-pill-active': statusFilter === '' }">All</button>
                <button @click="statusFilter = 'open'" class="thr-pill" :class="{ 'thr-pill-active': statusFilter === 'open' }">Open</button>
                <button @click="statusFilter = 'pending_support'" class="thr-pill" :class="{ 'thr-pill-active': statusFilter === 'pending_support' }">Pending Support</button>
                <button @click="statusFilter = 'pending_client'" class="thr-pill" :class="{ 'thr-pill-active': statusFilter === 'pending_client' }">Pending Client</button>
                <button @click="statusFilter = 'pending_rnd'" class="thr-pill" :class="{ 'thr-pill-active': statusFilter === 'pending_rnd' }">Pending R&D</button>
                <button @click="statusFilter = 'closed'" class="thr-pill" :class="{ 'thr-pill-active': statusFilter === 'closed' }">Closed</button>
            </div>
        </div>

        {{-- Ticket Table --}}
        <div class="thr-table-wrap">
            <table class="thr-table">
                <thead>
                    <tr>
                        <th class="thr-th" style="width:110px">Ticket #</th>
                        <th class="thr-th">Subject</th>
                        <th class="thr-th" style="width:130px">Status</th>
                        <th class="thr-th" style="width:90px">Priority</th>
                        <th class="thr-th" style="width:110px">Category</th>
                        <th class="thr-th" style="width:100px">SLA</th>
                        <th class="thr-th" style="width:80px">Replies</th>
                        <th class="thr-th" style="width:110px">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                        <tr class="thr-row"
                            data-status="{{ $ticket->status->value }}"
                            data-search="{{ strtolower($ticket->ticket_number . ' ' . $ticket->subject . ' ' . ($ticket->category ?? '') . ' ' . ($ticket->module ?? '')) }}"
                            x-show="matchesFilter($el)"
                            x-transition:enter="thr-row-enter"
                            @click="window.location.href = '/admin/implementer-ticketing-dashboard?ticket={{ $ticket->id }}&from={{ $leadViewUrl }}'"
                            style="cursor: pointer; animation-delay: {{ $loop->index * 30 }}ms"
                        >
                            <td class="thr-td">
                                <span class="thr-ticket-num">{{ $ticket->formatted_ticket_number }}</span>
                            </td>
                            <td class="thr-td">
                                <div class="thr-subject">{{ $ticket->subject }}</div>
                                @if($ticket->module)
                                    <div class="thr-module">{{ $ticket->module }}</div>
                                @endif
                            </td>
                            <td class="thr-td">
                                @php
                                    $statusColors = [
                                        'open' => ['bg' => '#EFF6FF', 'text' => '#1D4ED8', 'dot' => '#3B82F6'],
                                        'pending_support' => ['bg' => '#FFFBEB', 'text' => '#B45309', 'dot' => '#F59E0B'],
                                        'pending_client' => ['bg' => '#FEF2F2', 'text' => '#B91C1C', 'dot' => '#EF4444'],
                                        'pending_rnd' => ['bg' => '#F3F4F6', 'text' => '#4B5563', 'dot' => '#6B7280'],
                                        'closed' => ['bg' => '#ECFDF5', 'text' => '#047857', 'dot' => '#10B981'],
                                    ];
                                    $sc = $statusColors[$ticket->status->value] ?? $statusColors['open'];
                                @endphp
                                <span class="thr-badge" style="background:{{ $sc['bg'] }}; color:{{ $sc['text'] }}">
                                    <span class="thr-dot" style="background:{{ $sc['dot'] }}"></span>
                                    {{ $ticket->status->label() }}
                                </span>
                            </td>
                            <td class="thr-td">
                                @php
                                    $prioColors = [
                                        'low' => ['bg' => '#F0FDF4', 'text' => '#15803D'],
                                        'medium' => ['bg' => '#FFF7ED', 'text' => '#C2410C'],
                                        'high' => ['bg' => '#FEF2F2', 'text' => '#DC2626'],
                                        'critical' => ['bg' => '#FEF2F2', 'text' => '#991B1B'],
                                    ];
                                    $pc = $prioColors[$ticket->priority ?? 'medium'] ?? $prioColors['medium'];
                                @endphp
                                <span class="thr-prio" style="background:{{ $pc['bg'] }}; color:{{ $pc['text'] }}">
                                    {{ ucfirst($ticket->priority ?? 'Medium') }}
                                </span>
                            </td>
                            <td class="thr-td">
                                <span class="thr-cat">{{ $ticket->category ?? '-' }}</span>
                            </td>
                            <td class="thr-td">
                                @php
                                    $slaStatus = $ticket->getSlaStatus();
                                    $slaColors = [
                                        'on_track' => ['bg' => '#ECFDF5', 'text' => '#047857'],
                                        'at_risk' => ['bg' => '#FFFBEB', 'text' => '#B45309'],
                                        'overdue' => ['bg' => '#FEF2F2', 'text' => '#DC2626'],
                                        'resolved' => ['bg' => '#F3F4F6', 'text' => '#6B7280'],
                                    ];
                                    $slc = $slaColors[$slaStatus] ?? $slaColors['on_track'];
                                @endphp
                                <span class="thr-sla" style="background:{{ $slc['bg'] }}; color:{{ $slc['text'] }}">
                                    {{ $ticket->getTimeRemaining() }}
                                </span>
                            </td>
                            <td class="thr-td">
                                <span class="thr-reply-count">{{ $ticket->replies->count() }}</span>
                            </td>
                            <td class="thr-td">
                                <span class="thr-date">{{ $ticket->created_at->format('d M Y') }}</span>
                                <span class="thr-time">{{ $ticket->created_at->format('h:i A') }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<script>
function thrTicketList() {
    return {
        search: '',
        statusFilter: '',
        matchesFilter(el) {
            const row = el;
            const status = row.dataset.status || '';
            const searchText = row.dataset.search || '';

            if (this.statusFilter && status !== this.statusFilter) return false;
            if (this.search && !searchText.includes(this.search.toLowerCase())) return false;

            return true;
        }
    }
}
</script>

<style>
/* ── Thread Tab ── */
.thr-wrap {
    padding: 4px 0;
    font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
}

/* Summary Stats */
.thr-summary {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}
.thr-stat {
    background: #fff;
    border: 1px solid #E2E8F0;
    border-radius: 10px;
    padding: 16px 20px;
    position: relative;
    overflow: hidden;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.thr-stat::before {
    content: '';
    position: absolute;
    top: 0; left: 0;
    width: 4px; height: 100%;
    background: var(--accent);
    border-radius: 10px 0 0 10px;
}
.thr-stat:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}
.thr-stat-val {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1E293B;
    line-height: 1;
    letter-spacing: -0.02em;
}
.thr-stat-label {
    font-size: 0.75rem;
    font-weight: 500;
    color: #94A3B8;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-top: 4px;
}

/* Empty State */
.thr-empty {
    text-align: center;
    padding: 60px 20px;
    background: #FAFBFC;
    border: 1px dashed #CBD5E1;
    border-radius: 12px;
}
.thr-empty-icon { margin-bottom: 16px; opacity: 0.6; }
.thr-empty-title {
    font-size: 1.05rem;
    font-weight: 600;
    color: #475569;
    margin-bottom: 6px;
}
.thr-empty-desc {
    font-size: 0.85rem;
    color: #94A3B8;
}

/* Filter Row */
.thr-filter-row {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 16px;
    flex-wrap: wrap;
}
.thr-search-input {
    flex: 0 0 240px;
    height: 36px;
    padding: 0 12px;
    border: 1px solid #E2E8F0;
    border-radius: 8px;
    font-size: 0.82rem;
    color: #334155;
    background: #fff;
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s;
}
.thr-search-input:focus {
    border-color: #93C5FD;
    box-shadow: 0 0 0 3px rgba(59,130,246,0.08);
}
.thr-search-input::placeholder {
    color: #CBD5E1;
}
.thr-filter-pills {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}
.thr-pill {
    height: 32px;
    padding: 0 14px;
    border: 1px solid #E2E8F0;
    border-radius: 16px;
    background: #fff;
    font-size: 0.76rem;
    font-weight: 500;
    color: #64748B;
    cursor: pointer;
    transition: all 0.15s ease;
    white-space: nowrap;
}
.thr-pill:hover {
    background: #F8FAFC;
    border-color: #CBD5E1;
}
.thr-pill-active {
    background: #1E293B !important;
    color: #fff !important;
    border-color: #1E293B !important;
}

/* Table */
.thr-table-wrap {
    border: 1px solid #E2E8F0;
    border-radius: 10px;
    overflow: hidden;
    background: #fff;
}
.thr-table {
    width: 100%;
    border-collapse: collapse;
}
.thr-th {
    padding: 11px 16px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #94A3B8;
    background: #F8FAFC;
    text-align: left;
    border-bottom: 1px solid #E2E8F0;
    white-space: nowrap;
}
.thr-row {
    transition: background 0.12s ease;
    animation: thrFadeIn 0.3s ease both;
}
.thr-row:hover {
    background: #F1F5F9;
}
.thr-row:not(:last-child) .thr-td {
    border-bottom: 1px solid #F1F5F9;
}
.thr-td {
    padding: 12px 16px;
    font-size: 0.82rem;
    color: #334155;
    vertical-align: middle;
}

/* Ticket Number */
.thr-ticket-num {
    font-family: 'JetBrains Mono', 'Fira Code', monospace;
    font-size: 0.78rem;
    font-weight: 600;
    color: #3B82F6;
    letter-spacing: -0.01em;
}

/* Subject */
.thr-subject {
    font-weight: 500;
    color: #1E293B;
    line-height: 1.35;
    max-width: 320px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.thr-module {
    font-size: 0.72rem;
    color: #94A3B8;
    margin-top: 2px;
}

/* Badges */
.thr-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 0.72rem;
    font-weight: 600;
    white-space: nowrap;
}
.thr-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    flex-shrink: 0;
}
.thr-prio {
    display: inline-block;
    padding: 3px 9px;
    border-radius: 5px;
    font-size: 0.72rem;
    font-weight: 600;
}
.thr-cat {
    font-size: 0.8rem;
    color: #64748B;
}
.thr-sla {
    display: inline-block;
    padding: 3px 9px;
    border-radius: 5px;
    font-size: 0.72rem;
    font-weight: 600;
    white-space: nowrap;
}

/* Reply Count */
.thr-reply-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 26px;
    height: 24px;
    padding: 0 7px;
    background: #F1F5F9;
    border-radius: 12px;
    font-size: 0.76rem;
    font-weight: 600;
    color: #475569;
}

/* Date */
.thr-date {
    display: block;
    font-size: 0.8rem;
    color: #334155;
    font-weight: 500;
}
.thr-time {
    display: block;
    font-size: 0.7rem;
    color: #94A3B8;
    margin-top: 1px;
}

/* Animation */
@keyframes thrFadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Responsive */
@media (max-width: 768px) {
    .thr-summary { grid-template-columns: repeat(2, 1fr); }
    .thr-search-input { flex: 1 1 100%; }
    .thr-table-wrap { overflow-x: auto; }
    .thr-table { min-width: 700px; }
}
</style>
