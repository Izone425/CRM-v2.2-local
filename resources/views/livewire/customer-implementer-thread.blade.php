<div x-data="citThread()" class="cit-wrap">
    <h2 class="cit-title">
        <i class="fas fa-comments" style="color: #6366F1;"></i>
        Implementer Thread
    </h2>
    <p class="cit-subtitle">View all conversation threads with your implementer</p>

    @if($tickets->isEmpty())
        <div class="cit-empty">
            <div class="cit-empty-icon">
                <i class="fas fa-comments" style="font-size: 48px; color: #CBD5E1;"></i>
            </div>
            <div class="cit-empty-title">No threads yet</div>
            <div class="cit-empty-desc">Conversation threads with your implementer will appear here.</div>
        </div>
    @else
        {{-- Summary --}}
        <div class="cit-summary">
            <div class="cit-stat" style="--accent: #6366F1">
                <div class="cit-stat-val">{{ $tickets->count() }}</div>
                <div class="cit-stat-label">Total Threads</div>
            </div>
            <div class="cit-stat" style="--accent: #F59E0B">
                <div class="cit-stat-val">{{ $tickets->where('status', '!=', \App\Enums\ImplementerTicketStatus::CLOSED)->count() }}</div>
                <div class="cit-stat-label">Active</div>
            </div>
            <div class="cit-stat" style="--accent: #10B981">
                <div class="cit-stat-val">{{ $tickets->where('status', \App\Enums\ImplementerTicketStatus::CLOSED)->count() }}</div>
                <div class="cit-stat-label">Closed</div>
            </div>
        </div>

        {{-- Search --}}
        <div class="cit-search-row">
            <input type="text" x-model="search" placeholder="Search threads..." class="cit-search" />
        </div>

        {{-- Thread Cards --}}
        <div class="cit-list">
            @foreach($tickets as $ticket)
                @php
                    $statusColors = [
                        'open' => ['bg' => '#EFF6FF', 'text' => '#1D4ED8', 'dot' => '#3B82F6'],
                        'pending_support' => ['bg' => '#FFFBEB', 'text' => '#B45309', 'dot' => '#F59E0B'],
                        'pending_client' => ['bg' => '#FEF2F2', 'text' => '#B91C1C', 'dot' => '#EF4444'],
                        'pending_rnd' => ['bg' => '#F3F4F6', 'text' => '#4B5563', 'dot' => '#6B7280'],
                        'closed' => ['bg' => '#ECFDF5', 'text' => '#047857', 'dot' => '#10B981'],
                    ];
                    $sc = $statusColors[$ticket->status->value] ?? $statusColors['open'];
                    $lastReply = $ticket->replies->last();
                @endphp
                <a href="/customer/implementer-tickets/{{ $ticket->id }}"
                   class="cit-card"
                   data-search="{{ strtolower($ticket->ticket_number . ' ' . $ticket->subject . ' ' . ($ticket->category ?? '') . ' ' . ($ticket->module ?? '')) }}"
                   x-show="!search || $el.dataset.search.includes(search.toLowerCase())"
                   style="animation-delay: {{ $loop->index * 40 }}ms">
                    <div class="cit-card-header">
                        <span class="cit-ticket-num">{{ $ticket->formatted_ticket_number }}</span>
                        <span class="cit-badge" style="background:{{ $sc['bg'] }}; color:{{ $sc['text'] }}">
                            <span class="cit-dot" style="background:{{ $sc['dot'] }}"></span>
                            {{ $ticket->status->label() }}
                        </span>
                    </div>
                    <div class="cit-card-subject">{{ $ticket->subject }}</div>
                    <div class="cit-card-meta">
                        @if($ticket->category)
                            <span class="cit-meta-tag">{{ $ticket->category }}</span>
                        @endif
                        @if($ticket->module)
                            <span class="cit-meta-tag">{{ $ticket->module }}</span>
                        @endif
                        <span class="cit-meta-tag">{{ ucfirst($ticket->priority ?? 'medium') }}</span>
                    </div>
                    <div class="cit-card-footer">
                        <span class="cit-replies">
                            <i class="fas fa-reply" style="font-size: 11px;"></i>
                            {{ $ticket->replies->count() }} {{ $ticket->replies->count() === 1 ? 'reply' : 'replies' }}
                        </span>
                        <span class="cit-date">{{ $ticket->created_at->format('d M Y, h:i A') }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    <script>
    function citThread() {
        return { search: '' }
    }
    </script>

    <style>
.cit-wrap {
    font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
}
.cit-title {
    font-size: 1.35rem;
    font-weight: 700;
    color: #1E293B;
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0 0 4px 0;
}
.cit-subtitle {
    font-size: 0.85rem;
    color: #94A3B8;
    margin: 0 0 24px 0;
}

/* Empty */
.cit-empty {
    text-align: center;
    padding: 60px 20px;
    background: #FAFBFC;
    border: 1px dashed #CBD5E1;
    border-radius: 12px;
}
.cit-empty-icon { margin-bottom: 16px; }
.cit-empty-title { font-size: 1rem; font-weight: 600; color: #475569; margin-bottom: 4px; }
.cit-empty-desc { font-size: 0.82rem; color: #94A3B8; }

/* Summary */
.cit-summary {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}
.cit-stat {
    background: #fff;
    border: 1px solid #E2E8F0;
    border-radius: 10px;
    padding: 14px 18px;
    position: relative;
    overflow: hidden;
}
.cit-stat::before {
    content: '';
    position: absolute;
    top: 0; left: 0;
    width: 4px; height: 100%;
    background: var(--accent);
    border-radius: 10px 0 0 10px;
}
.cit-stat-val {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1E293B;
    line-height: 1;
}
.cit-stat-label {
    font-size: 0.72rem;
    font-weight: 500;
    color: #94A3B8;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-top: 4px;
}

/* Search */
.cit-search-row { margin-bottom: 16px; }
.cit-search {
    width: 100%;
    max-width: 320px;
    height: 38px;
    padding: 0 14px;
    border: 1px solid #E2E8F0;
    border-radius: 8px;
    font-size: 0.82rem;
    color: #334155;
    background: #fff;
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s;
}
.cit-search:focus {
    border-color: #A5B4FC;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.08);
}
.cit-search::placeholder { color: #CBD5E1; }

/* Card List */
.cit-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.cit-card {
    display: block;
    background: #fff;
    border: 1px solid #E2E8F0;
    border-radius: 10px;
    padding: 16px 20px;
    text-decoration: none;
    color: inherit;
    transition: border-color 0.15s, box-shadow 0.15s, transform 0.12s;
    animation: citFadeIn 0.3s ease both;
}
.cit-card:hover {
    border-color: #A5B4FC;
    box-shadow: 0 4px 14px rgba(99,102,241,0.08);
    transform: translateY(-1px);
}

/* Card header */
.cit-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
}
.cit-ticket-num {
    font-family: 'JetBrains Mono', 'Fira Code', monospace;
    font-size: 0.76rem;
    font-weight: 600;
    color: #6366F1;
}
.cit-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 6px;
    font-size: 0.7rem;
    font-weight: 600;
}
.cit-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
}

/* Subject */
.cit-card-subject {
    font-size: 0.92rem;
    font-weight: 600;
    color: #1E293B;
    margin-bottom: 8px;
    line-height: 1.35;
}

/* Meta tags */
.cit-card-meta {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    margin-bottom: 10px;
}
.cit-meta-tag {
    font-size: 0.7rem;
    font-weight: 500;
    color: #64748B;
    background: #F1F5F9;
    padding: 2px 8px;
    border-radius: 4px;
}

/* Footer */
.cit-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 10px;
    border-top: 1px solid #F1F5F9;
}
.cit-replies {
    font-size: 0.76rem;
    color: #64748B;
    display: flex;
    align-items: center;
    gap: 5px;
}
.cit-date {
    font-size: 0.74rem;
    color: #94A3B8;
}

@keyframes citFadeIn {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 640px) {
    .cit-summary { grid-template-columns: 1fr; }
}
    </style>
</div>
