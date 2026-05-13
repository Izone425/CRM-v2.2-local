<div class="imp-hero" x-show="selectedGroup === null" x-transition.opacity.duration.300ms>
    <div class="imp-hero-head">
        <div>
            <h2 class="imp-hero-hello">{{ $impGreeting }}, {{ $impFirstName }}</h2>
            <span class="imp-hero-date">It's {{ now()->format('l, F j') }}</span>
        </div>
        <div class="imp-hero-meta" aria-live="polite">
            <span class="imp-hero-pulse"></span>
            <span class="imp-hero-meta-label">Live</span>
        </div>
    </div>

    <div class="imp-today-strip">
        <button type="button" class="imp-today-tile imp-today-due"
            @click="setSelectedGroup('follow-up'); setSelectedStat('follow-up-today')">
            <span class="imp-today-count">{{ $impDueTodayTotal }}</span>
            <span class="imp-today-label">Due Today</span>
            <span class="imp-today-hint">{{ $impTileHints['due_today'] }}</span>
        </button>
        <button type="button" class="imp-today-tile imp-today-overdue"
            @click="setSelectedGroup('thread'); setSelectedStat('thread-overdue')">
            <span class="imp-today-count">{{ $impOverdueTotal }}</span>
            <span class="imp-today-label">Overdue</span>
            <span class="imp-today-hint">{{ $impTileHints['overdue'] }}</span>
        </button>
        <button type="button" class="imp-today-tile imp-today-new"
            @click="setSelectedGroup('thread'); setSelectedStat('thread-all')">
            <span class="imp-today-count">{{ $impNewSinceYesterdayTotal }}</span>
            <span class="imp-today-label">New Since Yesterday</span>
            <span class="imp-today-hint">{{ $impTileHints['new'] }}</span>
        </button>
    </div>

    @if (count($impActionInbox))
        <div class="imp-inbox">
            <div class="imp-inbox-head">
                <span class="imp-inbox-head-title">Needs your attention</span>
                <span class="imp-inbox-head-sub">{{ count($impActionInbox) }} item{{ count($impActionInbox) === 1 ? '' : 's' }} · sorted by urgency</span>
            </div>
            <ul class="imp-inbox-list">
                @foreach ($impActionInbox as $item)
                    <li class="imp-inbox-item imp-inbox-{{ $item['severity'] }}"
                        @if (!empty($item['url']))
                            onclick="window.location.href={{ json_encode($item['url']) }}"
                        @else
                            @click="setSelectedGroup('{{ $item['group'] }}'); setSelectedStat('{{ $item['stat'] }}')"
                        @endif
                        role="button" tabindex="0"
                        @if (!empty($item['url']))
                            @keydown.enter.prevent="window.location.href={{ json_encode($item['url']) }}"
                        @else
                            @keydown.enter.prevent="setSelectedGroup('{{ $item['group'] }}'); setSelectedStat('{{ $item['stat'] }}')"
                        @endif>
                        <span class="imp-inbox-dot" aria-hidden="true"></span>
                        <div class="imp-inbox-body">
                            <span class="imp-inbox-title">{{ $item['title'] }}</span>
                            <div class="imp-inbox-meta">
                                <span class="imp-inbox-type-chip" style="--imp-type-color: {{ $item['type_color'] }};">{{ $item['type'] }}</span>
                                <span class="imp-inbox-age">{{ $item['age'] }}</span>
                            </div>
                        </div>
                        <span class="imp-inbox-chev" aria-hidden="true">›</span>
                    </li>
                @endforeach
            </ul>
            @if ($impOverdueTotal > count($impActionInbox))
                <div class="imp-inbox-footer">
                    <a @click="setSelectedGroup('follow-up'); setSelectedStat('follow-up-overdue')">
                        View all overdue ({{ $impOverdueTotal }}) →
                    </a>
                </div>
            @endif
        </div>
    @else
        <div class="imp-allclear">
            <div class="imp-allclear-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><polyline points="9 12 12 15 17 9"/>
                </svg>
            </div>
            <p class="imp-allclear-title">All clear</p>
            <p class="imp-allclear-sub">Nothing urgent right now. Enjoy the calm.</p>
        </div>
    @endif
</div>
