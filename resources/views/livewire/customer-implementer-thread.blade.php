<div x-data="citApp()" class="cit-wrap">

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- DASHBOARD VIEW --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @if($currentView === 'dashboard')
    @php
        $statusMap = [
            'open' => ['bg' => '#EFF6FF', 'text' => '#1D4ED8', 'dot' => '#3B82F6', 'label' => 'Open'],
            'pending_support' => ['bg' => '#FFFBEB', 'text' => '#B45309', 'dot' => '#F59E0B', 'label' => 'Pending Support'],
            'pending_client' => ['bg' => '#F5F3FF', 'text' => '#6D28D9', 'dot' => '#8B5CF6', 'label' => 'Pending Client'],
            'pending_rnd' => ['bg' => '#F3F4F6', 'text' => '#4B5563', 'dot' => '#6B7280', 'label' => 'Pending R&D'],
            'closed' => ['bg' => '#ECFDF5', 'text' => '#047857', 'dot' => '#10B981', 'label' => 'Closed'],
        ];
        $priorityMap = [
            'low' => ['bg' => '#F3F4F6', 'text' => '#6B7280'],
            'medium' => ['bg' => '#FFFBEB', 'text' => '#B45309'],
            'high' => ['bg' => '#FEF2F2', 'text' => '#B91C1C'],
            'urgent' => ['bg' => '#EF4444', 'text' => '#FFFFFF'],
        ];
    @endphp
    <div class="cit-dashboard">

        {{-- Header --}}
        @php $canCreate = $this->canCreateTicket; @endphp
        <div class="cit-header">
            <div>
                <h1 class="cit-title">Project Thread</h1>
                <p class="cit-subtitle">Manage and track your support requests</p>
            </div>
            <div class="cit-create-wrap" x-data="{ tip: false }">
                <button
                    wire:click="openCreateModal"
                    @if(!$canCreate) disabled aria-disabled="true" @endif
                    @if(!$canCreate) @mouseenter="tip = true" @mouseleave="tip = false" @endif
                    class="cit-btn-primary {{ !$canCreate ? 'cit-btn-disabled' : '' }}"
                >
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                    Create New Ticket
                </button>
                @if(!$canCreate)
                    <div x-show="tip" x-cloak x-transition class="cit-tooltip">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                        <span>Available after your implementer sends the first session summary.</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="cit-stats-grid">
            <div class="cit-stat-card {{ $statusFilter === 'open' ? 'cit-stat-active' : '' }}" wire:click="filterByStatus('open')" style="--stat-color: #3B82F6">
                <div class="cit-stat-content">
                    <span class="cit-stat-label">Open Tickets</span>
                    <span class="cit-stat-value">{{ $statusCounts['open'] }}</span>
                </div>
                <div class="cit-stat-icon" style="background: #EFF6FF; color: #3B82F6;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                </div>
            </div>
            <div class="cit-stat-card {{ $statusFilter === 'pending_support' ? 'cit-stat-active' : '' }}" wire:click="filterByStatus('pending_support')" style="--stat-color: #F59E0B">
                <div class="cit-stat-content">
                    <span class="cit-stat-label">In Progress</span>
                    <span class="cit-stat-value">{{ $statusCounts['pending_support'] }}</span>
                </div>
                <div class="cit-stat-icon" style="background: #FFFBEB; color: #F59E0B;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                </div>
            </div>
            <div class="cit-stat-card {{ $statusFilter === 'pending_client' ? 'cit-stat-active' : '' }}" wire:click="filterByStatus('pending_client')" style="--stat-color: #8B5CF6">
                <div class="cit-stat-content">
                    <span class="cit-stat-label">Waiting on You</span>
                    <span class="cit-stat-value">{{ $statusCounts['pending_client'] }}</span>
                </div>
                <div class="cit-stat-icon" style="background: #F5F3FF; color: #8B5CF6;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                </div>
            </div>
            <div class="cit-stat-card {{ $statusFilter === 'closed' ? 'cit-stat-active' : '' }}" wire:click="filterByStatus('closed')" style="--stat-color: #10B981">
                <div class="cit-stat-content">
                    <span class="cit-stat-label">Resolved</span>
                    <span class="cit-stat-value">{{ $statusCounts['closed'] }}</span>
                </div>
                <div class="cit-stat-icon" style="background: #ECFDF5; color: #10B981;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg>
                </div>
            </div>
        </div>

        {{-- Search & Filter --}}
        <div class="cit-search-row">
            <div class="cit-search-wrap">
                <svg class="cit-search-icon" width="18" height="18" fill="none" stroke="#94A3B8" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search tickets by ID, subject, or category..." class="cit-search-input" />
            </div>
            <button @click="showFilters = !showFilters" class="cit-filter-btn" :class="showFilters && 'cit-filter-active'">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/></svg>
                Filters
            </button>
        </div>

        {{-- Filter Panel --}}
        <div x-show="showFilters" x-cloak x-transition:enter="cit-filter-enter" x-transition:enter-start="cit-filter-enter-start" x-transition:enter-end="cit-filter-enter-end" class="cit-filter-panel">
            <div class="cit-filter-grid">
                {{-- Status Filter --}}
                <div class="cit-filter-group" x-data="{ open: false }">
                    <label class="cit-filter-label">Status</label>
                    <div class="cit-dropdown" @click.away="open = false">
                        <button class="cit-dropdown-btn" @click="open = !open">
                            <span>{{ $statusFilter ? ucwords(str_replace('_', ' ', $statusFilter)) : 'All Status' }}</span>
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" :style="open && 'transform:rotate(180deg)'"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <div x-show="open" x-cloak class="cit-dropdown-menu">
                            <div class="cit-dropdown-item {{ !$statusFilter ? 'cit-dropdown-selected' : '' }}" wire:click="$set('statusFilter', '')" @click="open = false">All Status</div>
                            <div class="cit-dropdown-item {{ $statusFilter === 'open' ? 'cit-dropdown-selected' : '' }}" wire:click="$set('statusFilter', 'open')" @click="open = false">Open</div>
                            <div class="cit-dropdown-item {{ $statusFilter === 'pending_support' ? 'cit-dropdown-selected' : '' }}" wire:click="$set('statusFilter', 'pending_support')" @click="open = false">Pending Support</div>
                            <div class="cit-dropdown-item {{ $statusFilter === 'pending_client' ? 'cit-dropdown-selected' : '' }}" wire:click="$set('statusFilter', 'pending_client')" @click="open = false">Pending Client</div>
                            <div class="cit-dropdown-item {{ $statusFilter === 'pending_rnd' ? 'cit-dropdown-selected' : '' }}" wire:click="$set('statusFilter', 'pending_rnd')" @click="open = false">Pending R&D</div>
                            <div class="cit-dropdown-item {{ $statusFilter === 'closed' ? 'cit-dropdown-selected' : '' }}" wire:click="$set('statusFilter', 'closed')" @click="open = false">Closed</div>
                        </div>
                    </div>
                </div>

                {{-- Category Filter --}}
                <div class="cit-filter-group" x-data="{ open: false }">
                    <label class="cit-filter-label">Category</label>
                    <div class="cit-dropdown" @click.away="open = false">
                        <button class="cit-dropdown-btn" @click="open = !open">
                            <span>{{ $categoryFilter ?: 'All Categories' }}</span>
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" :style="open && 'transform:rotate(180deg)'"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <div x-show="open" x-cloak class="cit-dropdown-menu">
                            <div class="cit-dropdown-item {{ !$categoryFilter ? 'cit-dropdown-selected' : '' }}" wire:click="$set('categoryFilter', '')" @click="open = false">All Categories</div>
                            @foreach(['License Activation', 'Data Migration', 'Software Enquiries', 'Session Enquiries', 'Session Summary', 'Training Enquiries', 'Enhancement/CR', 'Add On License', 'Others'] as $cat)
                                <div class="cit-dropdown-item {{ $categoryFilter === $cat ? 'cit-dropdown-selected' : '' }}" wire:click="$set('categoryFilter', '{{ $cat }}')" @click="open = false">{{ $cat }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Module Filter --}}
                <div class="cit-filter-group" x-data="{ open: false }">
                    <label class="cit-filter-label">Module</label>
                    <div class="cit-dropdown" @click.away="open = false">
                        <button class="cit-dropdown-btn" @click="open = !open">
                            <span>{{ $moduleFilter ?: 'All Modules' }}</span>
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" :style="open && 'transform:rotate(180deg)'"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <div x-show="open" x-cloak class="cit-dropdown-menu">
                            <div class="cit-dropdown-item {{ !$moduleFilter ? 'cit-dropdown-selected' : '' }}" wire:click="$set('moduleFilter', '')" @click="open = false">All Modules</div>
                            @foreach(['Profile', 'Attendance', 'Leave', 'Claim', 'Payroll', 'Appraisal', 'Hire'] as $mod)
                                <div class="cit-dropdown-item {{ $moduleFilter === $mod ? 'cit-dropdown-selected' : '' }}" wire:click="$set('moduleFilter', '{{ $mod }}')" @click="open = false">{{ $mod }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="cit-filter-actions">
                <button wire:click="resetFilters" @click="showFilters = false" class="cit-filter-clear">Clear All Filters</button>
            </div>
        </div>

        {{-- Ticket Table --}}
        @if($tickets->isEmpty())
            <div class="cit-empty">
                <div class="cit-empty-icon">
                    <svg width="56" height="56" fill="none" stroke="#CBD5E1" stroke-width="1.2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                </div>
                @if($search || $statusFilter || $categoryFilter || $moduleFilter)
                    <div class="cit-empty-title">No tickets match your filters</div>
                    <div class="cit-empty-desc">Try adjusting your search or filters to find what you're looking for.</div>
                    <button wire:click="resetFilters" class="cit-btn-outline" style="margin-top: 12px;">Clear Filters</button>
                @else
                    @if($this->canCreateTicket)
                        <div class="cit-empty-title">No threads yet</div>
                        <div class="cit-empty-desc">Create your first support ticket to get started with your implementer.</div>
                        <button wire:click="openCreateModal" class="cit-btn-primary" style="margin-top: 16px; font-size: 0.82rem;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                            Create New Ticket
                        </button>
                    @else
                        <div class="cit-empty-title">Waiting for your first session summary</div>
                        <div class="cit-empty-desc">Your implementer will start the conversation by sending the first session summary. You'll be able to reply and create new tickets here once that happens.</div>
                        <div class="cit-empty-hint">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                            <span>Check back here after your kickoff session.</span>
                        </div>
                    @endif
                @endif
            </div>
        @else
            {{-- Desktop Table --}}
            <div class="cit-table-wrap">
                <div class="cit-table-header-bar">
                    <span class="cit-table-title">Ticket History</span>
                    <span class="cit-table-count">{{ $tickets->count() }} {{ $tickets->count() === 1 ? 'ticket' : 'tickets' }}</span>
                </div>
                <div class="cit-table-scroll">
                    <table class="cit-table">
                        <thead>
                            <tr>
                                <th>Ticket ID</th>
                                <th>Subject</th>
                                <th>Category</th>
                                <th>Module</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>SLA</th>
                                <th>Last Updated</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tickets as $ticket)
                                @php
                                    $statusMap = [
                                        'open' => ['bg' => '#EFF6FF', 'text' => '#1D4ED8', 'dot' => '#3B82F6', 'label' => 'Open'],
                                        'pending_support' => ['bg' => '#FFFBEB', 'text' => '#B45309', 'dot' => '#F59E0B', 'label' => 'Pending Support'],
                                        'pending_client' => ['bg' => '#F5F3FF', 'text' => '#6D28D9', 'dot' => '#8B5CF6', 'label' => 'Pending Client'],
                                        'pending_rnd' => ['bg' => '#F3F4F6', 'text' => '#4B5563', 'dot' => '#6B7280', 'label' => 'Pending R&D'],
                                        'closed' => ['bg' => '#ECFDF5', 'text' => '#047857', 'dot' => '#10B981', 'label' => 'Closed'],
                                    ];
                                    $sc = $statusMap[$ticket->status->value] ?? $statusMap['open'];

                                    $priorityMap = [
                                        'low' => ['bg' => '#F3F4F6', 'text' => '#6B7280'],
                                        'medium' => ['bg' => '#FFFBEB', 'text' => '#B45309'],
                                        'high' => ['bg' => '#FEF2F2', 'text' => '#B91C1C'],
                                        'urgent' => ['bg' => '#EF4444', 'text' => '#FFFFFF'],
                                    ];
                                    $pc = $priorityMap[$ticket->priority ?? 'medium'] ?? $priorityMap['medium'];

                                    $slaStatus = $ticket->getSlaStatus();
                                    $slaMap = [
                                        'on_track' => ['bg' => '#ECFDF5', 'text' => '#047857', 'border' => '#A7F3D0', 'label' => 'On Track'],
                                        'at_risk' => ['bg' => '#FFFBEB', 'text' => '#B45309', 'border' => '#FDE68A', 'label' => $ticket->getTimeRemaining()],
                                        'overdue' => ['bg' => '#FEF2F2', 'text' => '#B91C1C', 'border' => '#FECACA', 'label' => 'Overdue'],
                                        'resolved' => ['bg' => '#F3F4F6', 'text' => '#6B7280', 'border' => '#E5E7EB', 'label' => 'Resolved'],
                                    ];
                                    $sl = $slaMap[$slaStatus] ?? $slaMap['on_track'];

                                    $lastReply = $ticket->replies->last();
                                    $lastUpdated = $lastReply ? $lastReply->created_at : $ticket->updated_at;
                                @endphp
                                <tr wire:click="openTicketDetail({{ $ticket->id }})" class="cit-table-row" style="animation-delay: {{ $loop->index * 30 }}ms">
                                    <td><span class="cit-ticket-id">{{ $ticket->formatted_ticket_number }}</span></td>
                                    <td><span class="cit-ticket-subject">{{ Str::limit($ticket->subject, 45) }}</span></td>
                                    <td><span class="cit-meta-text">{{ $ticket->category ?? '-' }}</span></td>
                                    <td><span class="cit-meta-text">{{ $ticket->module ?? '-' }}</span></td>
                                    <td>
                                        <span class="cit-badge-sm" style="background:{{ $pc['bg'] }}; color:{{ $pc['text'] }}">
                                            {{ ucfirst($ticket->priority ?? 'medium') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($ticket->isMerged())
                                            <span class="cit-status-badge" style="background:#FEF3C7; color:#92400E">
                                                <span class="cit-dot" style="background:#D97706"></span>
                                                Merged to {{ $ticket->mergedInto?->formatted_ticket_number }}
                                            </span>
                                        @else
                                            <span class="cit-status-badge" style="background:{{ $sc['bg'] }}; color:{{ $sc['text'] }}">
                                                <span class="cit-dot" style="background:{{ $sc['dot'] }}"></span>
                                                {{ $sc['label'] }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="cit-sla-badge {{ $slaStatus === 'overdue' ? 'cit-sla-pulse' : '' }}" style="background:{{ $sl['bg'] }}; color:{{ $sl['text'] }}; border-color:{{ $sl['border'] }}">
                                            @if($slaStatus === 'overdue')
                                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                                            @endif
                                            {{ $sl['label'] }}
                                        </span>
                                    </td>
                                    <td><span class="cit-date-text">{{ $lastUpdated->diffForHumans() }}</span></td>
                                    <td>
                                        <span class="cit-view-btn">
                                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Mobile Card List --}}
            <div class="cit-mobile-list">
                @foreach($tickets as $ticket)
                    @php
                        $sc = $statusMap[$ticket->status->value] ?? $statusMap['open'];
                    @endphp
                    <div wire:click="openTicketDetail({{ $ticket->id }})" class="cit-mobile-card" style="animation-delay: {{ $loop->index * 40 }}ms">
                        <div class="cit-mobile-card-top">
                            <span class="cit-ticket-id">{{ $ticket->formatted_ticket_number }}</span>
                            <span class="cit-status-badge" style="background:{{ $sc['bg'] }}; color:{{ $sc['text'] }}">
                                <span class="cit-dot" style="background:{{ $sc['dot'] }}"></span>
                                {{ $sc['label'] }}
                            </span>
                        </div>
                        <div class="cit-mobile-card-subject">{{ $ticket->subject }}</div>
                        <div class="cit-mobile-card-meta">
                            @if($ticket->category)<span class="cit-tag">{{ $ticket->category }}</span>@endif
                            @if($ticket->module)<span class="cit-tag">{{ $ticket->module }}</span>@endif
                            <span class="cit-tag">{{ ucfirst($ticket->priority ?? 'medium') }}</span>
                        </div>
                        <div class="cit-mobile-card-bottom">
                            <span class="cit-reply-count">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                                {{ $ticket->replies->count() }} {{ $ticket->replies->count() === 1 ? 'reply' : 'replies' }}
                            </span>
                            <span class="cit-date-text">{{ $ticket->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- DETAIL VIEW --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @if($currentView === 'detail' && $selectedTicket)
    @php
        $statusMap = [
            'open' => ['bg' => '#EFF6FF', 'text' => '#1D4ED8', 'dot' => '#3B82F6', 'label' => 'Open'],
            'pending_support' => ['bg' => '#FFFBEB', 'text' => '#B45309', 'dot' => '#F59E0B', 'label' => 'Pending Support'],
            'pending_client' => ['bg' => '#F5F3FF', 'text' => '#6D28D9', 'dot' => '#8B5CF6', 'label' => 'Pending Client'],
            'pending_rnd' => ['bg' => '#F3F4F6', 'text' => '#4B5563', 'dot' => '#6B7280', 'label' => 'Pending R&D'],
            'closed' => ['bg' => '#ECFDF5', 'text' => '#047857', 'dot' => '#10B981', 'label' => 'Closed'],
        ];
        $priorityMap = [
            'low' => ['bg' => '#F3F4F6', 'text' => '#6B7280'],
            'medium' => ['bg' => '#FFFBEB', 'text' => '#B45309'],
            'high' => ['bg' => '#FEF2F2', 'text' => '#B91C1C'],
            'urgent' => ['bg' => '#EF4444', 'text' => '#FFFFFF'],
        ];
        $slaMap = [
            'on_track' => ['bg' => '#ECFDF5', 'text' => '#047857', 'border' => '#A7F3D0', 'label' => 'On Track'],
            'at_risk' => ['bg' => '#FFFBEB', 'text' => '#B45309', 'border' => '#FDE68A', 'label' => $selectedTicket->getTimeRemaining()],
            'overdue' => ['bg' => '#FEF2F2', 'text' => '#B91C1C', 'border' => '#FECACA', 'label' => 'Overdue'],
            'resolved' => ['bg' => '#F3F4F6', 'text' => '#6B7280', 'border' => '#E5E7EB', 'label' => 'Resolved'],
        ];
    @endphp
    <div class="cit-detail" x-init="$nextTick(() => { if($refs.threadArea) $refs.threadArea.scrollTop = $refs.threadArea.scrollHeight })">

        {{-- Top Bar --}}
        <div class="cit-detail-topbar">
            <button wire:click="backToDashboard" class="cit-back-btn">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                Back to Tickets
            </button>
        </div>

        @php
            $sc = $statusMap[$selectedTicket->status->value] ?? $statusMap['open'];
            $pc = $priorityMap[$selectedTicket->priority ?? 'medium'] ?? $priorityMap['medium'];
            $slaStatus = $selectedTicket->getSlaStatus();
            $sl = $slaMap[$slaStatus] ?? $slaMap['on_track'];
        @endphp

        {{-- 2-Column Layout --}}
        <div class="cit-detail-columns">

            {{-- LEFT COLUMN: Ticket Details --}}
            <div class="cit-detail-left">
                {{-- Header Card --}}
                <div class="cit-detail-header">
                    <div class="cit-detail-header-top">
                        <div>
                            <div class="cit-detail-title-row">
                                <h2 class="cit-detail-subject">{{ $selectedTicket->subject }}</h2>
                                @if($slaStatus === 'overdue')
                                    <span class="cit-overdue-badge">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                                        Overdue
                                    </span>
                                @endif
                            </div>
                            <p class="cit-detail-id">Ticket ID: <span>{{ $selectedTicket->formatted_ticket_number }}</span></p>
                        </div>
                    </div>

                    {{-- Metadata Grid --}}
                    <div class="cit-detail-meta-grid">
                        <div class="cit-detail-meta-item">
                            <span class="cit-detail-meta-label">Status</span>
                            @if($selectedTicket->isMerged())
                                <span class="cit-status-badge" style="background:#FEF3C7; color:#92400E">
                                    <span class="cit-dot" style="background:#D97706"></span>
                                    Merged to {{ $selectedTicket->mergedInto?->formatted_ticket_number }}
                                </span>
                            @else
                                <span class="cit-status-badge" style="background:{{ $sc['bg'] }}; color:{{ $sc['text'] }}">
                                    <span class="cit-dot" style="background:{{ $sc['dot'] }}"></span>
                                    {{ $sc['label'] }}
                                </span>
                            @endif
                        </div>
                        <div class="cit-detail-meta-item">
                            <span class="cit-detail-meta-label">Priority</span>
                            <span class="cit-badge-sm" style="background:{{ $pc['bg'] }}; color:{{ $pc['text'] }}">
                                {{ ucfirst($selectedTicket->priority ?? 'medium') }}
                            </span>
                        </div>
                        <div class="cit-detail-meta-item">
                            <span class="cit-detail-meta-label">Category</span>
                            <span class="cit-detail-meta-value">{{ $selectedTicket->category ?? '-' }}</span>
                        </div>
                        <div class="cit-detail-meta-item">
                            <span class="cit-detail-meta-label">Module</span>
                            <span class="cit-detail-meta-value">{{ $selectedTicket->module ?? '-' }}</span>
                        </div>
                        <div class="cit-detail-meta-item">
                            <span class="cit-detail-meta-label">Assigned To</span>
                            <span class="cit-detail-meta-value">{{ $selectedTicket->implementer_name ?? 'Unassigned' }}</span>
                        </div>
                        <div class="cit-detail-meta-item">
                            <span class="cit-detail-meta-label">Created</span>
                            <span class="cit-detail-meta-value">{{ $selectedTicket->created_at->format('d M Y, h:i A') }}</span>
                        </div>
                    </div>

                    {{-- Follow-up alert --}}
                    @if(($followupCount ?? 0) >= 1)
                        <div class="cit-detail-followup-alert">
                            <div class="cit-detail-followup-alert__icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                            </div>
                            <div class="cit-detail-followup-alert__body">
                                <div class="cit-detail-followup-alert__title">Follow-ups sent <span class="cit-detail-followup-alert__count">{{ $followupCount }}</span></div>
                                <div class="cit-detail-followup-alert__sub">Please respond so we can move this ticket forward.</div>
                            </div>
                        </div>
                    @endif

                    {{-- SLA Info --}}
                    @if($slaStatus !== 'resolved')
                        <div class="cit-sla-card {{ $slaStatus === 'overdue' ? 'cit-sla-card-overdue' : '' }}">
                            <div class="cit-sla-card-row">
                                <div class="cit-sla-card-left">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                                    <span>SLA Deadline</span>
                                </div>
                                <span class="cit-sla-badge" style="background:{{ $sl['bg'] }}; color:{{ $sl['text'] }}; border-color:{{ $sl['border'] }}">{{ $sl['label'] }}</span>
                            </div>
                            <div class="cit-sla-card-date">{{ $selectedTicket->getSlaDeadline()->format('d M Y, h:i A') }}</div>
                            <div class="cit-sla-card-remaining">{{ $selectedTicket->getTimeRemaining() }}</div>
                        </div>
                    @endif
                </div>

                {{-- Escalation Notice --}}
                @if($slaStatus === 'overdue')
                    <div class="cit-escalation-card">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                        <div>
                            <strong>Escalation Notice</strong>
                            <p>This ticket has exceeded the SLA deadline. Our team has been notified and is prioritizing your request.</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- RIGHT COLUMN: Thread Activity --}}
            <div class="cit-detail-right" x-data="{ threadSearch: '' }">

                {{-- Thread Title --}}
                <div class="cit-thread-title">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                    Thread Activity
                </div>

                {{-- Thread Search Bar --}}
                <div class="cit-thread-search">
                    <svg class="cit-thread-search-icon" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                    <input type="text" x-model="threadSearch" placeholder="Search thread..." />
                    <button x-show="threadSearch" @click="threadSearch = ''" class="cit-thread-search-clear" x-cloak>&times;</button>
                </div>

                {{-- Thread Messages --}}
                <div class="cit-thread-area" x-ref="threadArea">
                    @forelse($selectedTicket->replies as $reply)
                        @php
                            $isCustomer = $reply->sender_type === 'App\Models\Customer';
                        @endphp
                        <div class="cit-bubble {{ $isCustomer ? 'cit-bubble-customer' : 'cit-bubble-staff' }}"
                             style="animation-delay: {{ $loop->index * 50 }}ms"
                             x-bind:class="{ 'cit-msg-dimmed': threadSearch && !$el.textContent.toLowerCase().includes(threadSearch.toLowerCase()), 'cit-msg-highlight': threadSearch && $el.textContent.toLowerCase().includes(threadSearch.toLowerCase()) }">
                            <div class="cit-bubble-header">
                                <div class="cit-bubble-avatar {{ $isCustomer ? 'blue' : 'purple' }}">
                                    @if($isCustomer)
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    @else
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                    @endif
                                </div>
                                <div class="cit-bubble-info">
                                    <span class="cit-bubble-name">{{ $reply->sender_name }}</span>
                                    <span class="cit-bubble-badge {{ $isCustomer ? 'blue' : 'purple' }}">{{ $reply->getSenderTypeLabel() }}</span>
                                    @if(!empty($reply->thread_label))
                                        @php
                                            $displayLabel = preg_match('/^\s*follow[\s\-]?up\b/i', $reply->thread_label)
                                                ? 'Follow Up'
                                                : $reply->thread_label;
                                        @endphp
                                        <span class="cit-bubble-badge cit-bubble-badge--thread-label" title="{{ $displayLabel }}">{{ $displayLabel }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="cit-bubble-body">{!! $reply->message !!}</div>
                            @if($reply->attachments && count($reply->attachments))
                                <div class="cit-attachment-list">
                                    @foreach($reply->attachments as $att)
                                        <a href="{{ Storage::disk('public')->url($att) }}" target="_blank" class="cit-attachment-chip">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
                                            {{ basename($att) }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                            <span class="cit-bubble-time">{{ $reply->created_at->format('d M Y, h:i A') }}</span>
                        </div>
                    @empty
                        <div class="cit-thread-empty">
                            <svg width="32" height="32" fill="none" stroke="#CBD5E1" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                            <span>No replies yet. Start the conversation!</span>
                        </div>
                    @endforelse
                </div>

                {{-- Reply Section: collapsed bar by default, expands on click --}}
                @if($selectedTicket->status !== \App\Enums\ImplementerTicketStatus::CLOSED)
                @php
                    $customerInitial = strtoupper(substr(auth('customer')->user()->name ?? 'C', 0, 1));
                    $pendingAttachments = $replyAttachments ? count($replyAttachments) : 0;
                @endphp

                {{-- Collapsed reply trigger --}}
                <button type="button"
                        x-show="!replyOpen"
                        @click="openReply()"
                        @keydown.enter.prevent="openReply()"
                        class="cit-reply-collapsed"
                        aria-label="Open reply composer">
                    <span class="cit-reply-collapsed-avatar">{{ $customerInitial }}</span>
                    <span class="cit-reply-collapsed-prompt">Click to reply...</span>
                    @if($pendingAttachments > 0)
                        <span class="cit-reply-collapsed-meta">
                            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
                            {{ $pendingAttachments }} attached
                        </span>
                    @endif
                    <span class="cit-reply-collapsed-icon">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                    </span>
                </button>

                {{-- Expanded reply composer --}}
                <div class="cit-reply-box"
                     x-show="replyOpen"
                     x-transition:enter="cit-reply-enter"
                     x-transition:enter-start="cit-reply-enter-start"
                     x-transition:enter-end="cit-reply-enter-end"
                     @keydown.escape.window="if(replyOpen) closeReply()"
                     style="display: none;">
                    <div class="cit-reply-toolbar" wire:ignore>
                        <button type="button" @click="exec('bold')" title="Bold" class="cit-toolbar-btn">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M15.6 10.79c.97-.67 1.65-1.77 1.65-2.79 0-2.26-1.75-4-4-4H7v14h7.04c2.09 0 3.71-1.7 3.71-3.79 0-1.52-.86-2.82-2.15-3.42zM10 6.5h3c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5h-3v-3zm3.5 9H10v-3h3.5c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5z"/></svg>
                        </button>
                        <button type="button" @click="exec('italic')" title="Italic" class="cit-toolbar-btn">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M10 4v3h2.21l-3.42 8H6v3h8v-3h-2.21l3.42-8H18V4z"/></svg>
                        </button>
                        <button type="button" @click="insertLink()" title="Link" class="cit-toolbar-btn">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
                        </button>
                        <span class="cit-toolbar-divider"></span>
                        <button type="button" @click="$refs.replyFileInput.click()" title="Attach File" class="cit-toolbar-btn">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
                        </button>
                        <button type="button" @click="closeReply()" title="Minimize (Esc)" class="cit-toolbar-btn cit-reply-minimize">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14"/></svg>
                        </button>
                    </div>

                    <div wire:ignore>
                        <div x-ref="replyEditor"
                             contenteditable="true"
                             class="cit-reply-editor"
                             data-placeholder="Type your reply..."
                             @paste.prevent="handlePaste($event)"></div>
                    </div>

                    <input type="file"
                           x-ref="replyFileInput"
                           wire:model.live="replyAttachments"
                           wire:key="cit-reply-file-{{ $selectedTicketId }}"
                           multiple
                           accept=".pdf,.png,.jpg,.jpeg,.xlsx,.xls,.doc,.docx,.csv,.txt"
                           class="cit-hidden" />

                    @if($replyAttachments && count($replyAttachments))
                        <div class="cit-file-list">
                            @foreach($replyAttachments as $i => $file)
                                <div class="cit-file-item">
                                    <svg width="14" height="14" fill="none" stroke="#1a6dd4" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg>
                                    <span class="cit-file-name">{{ $file->getClientOriginalName() }}</span>
                                    <button wire:click="removeReplyAttachment({{ $i }})" class="cit-file-remove">&times;</button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="cit-reply-footer">
                        <button @click="syncAndSubmit()" class="cit-btn-primary cit-reply-send" wire:loading.attr="disabled" wire:target="submitReply">
                            <svg wire:loading.remove wire:target="submitReply" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                            <span wire:loading.remove wire:target="submitReply">Send Reply</span>
                            <span wire:loading wire:target="submitReply">Sending...</span>
                        </button>
                    </div>
                </div>
                @else
                    <div class="cit-closed-notice" @if($selectedTicket->isMerged()) style="background: #FEF3C7; border-color: #FDE68A; color: #92400E;" @endif>
                        @if($selectedTicket->isMerged())
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m9.86-2.07a4.5 4.5 0 00-1.242-7.244l-4.5-4.5a4.5 4.5 0 00-6.364 6.364L4.34 8.374" /></svg>
                            This ticket has been merged to <strong>{{ $selectedTicket->mergedInto?->formatted_ticket_number }}</strong>.
                        @else
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg>
                            This ticket has been resolved and closed.
                            <button wire:click="reopenTicket" class="cit-reopen-btn" wire:loading.attr="disabled" wire:target="reopenTicket">
                                <span wire:loading.remove wire:target="reopenTicket">Reopen Ticket</span>
                                <span wire:loading wire:target="reopenTicket">Reopening...</span>
                            </button>
                        @endif
                    </div>
                @endif
            </div>

        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- CREATE TICKET MODAL --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @if($showCreateModal)
    <div class="cit-modal-overlay" @click.self="$wire.closeCreateModal()">
        <div class="cit-modal" @click.stop>
            <div class="cit-modal-header">
                <h2>Create New Ticket</h2>
                <button wire:click="closeCreateModal" class="cit-modal-close">&times;</button>
            </div>

            <div class="cit-modal-body">
                {{-- Category --}}
                <div class="cit-form-group" x-data="{ open: false }">
                    <label class="cit-form-label">Category <span class="cit-required">*</span></label>
                    <div class="cit-dropdown" @click.away="open = false">
                        <button type="button" class="cit-dropdown-btn cit-form-input" @click="open = !open">
                            <span style="{{ !$newCategory ? 'color:#94A3B8' : '' }}">{{ $newCategory ?: 'Select Category' }}</span>
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" :style="open && 'transform:rotate(180deg)'"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <div x-show="open" x-cloak class="cit-dropdown-menu">
                            @foreach(['License Activation', 'Data Migration', 'Software Enquiries', 'Session Enquiries', 'Session Summary', 'Training Enquiries', 'Enhancement/CR', 'Add On License', 'Others'] as $cat)
                                <div class="cit-dropdown-item {{ $newCategory === $cat ? 'cit-dropdown-selected' : '' }}" wire:click="$set('newCategory', '{{ $cat }}')" @click="open = false">{{ $cat }}</div>
                            @endforeach
                        </div>
                    </div>
                    @error('newCategory') <span class="cit-error">{{ $message }}</span> @enderror
                </div>

                {{-- Module --}}
                <div class="cit-form-group" x-data="{ open: false }">
                    <label class="cit-form-label">Module <span class="cit-required">*</span></label>
                    <div class="cit-dropdown" @click.away="open = false">
                        <button type="button" class="cit-dropdown-btn cit-form-input" @click="open = !open">
                            <span style="{{ !$newModule ? 'color:#94A3B8' : '' }}">{{ $newModule ?: 'Select Module' }}</span>
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" :style="open && 'transform:rotate(180deg)'"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <div x-show="open" x-cloak class="cit-dropdown-menu">
                            @foreach(['Profile', 'Attendance', 'Leave', 'Claim', 'Payroll', 'Appraisal', 'Hire'] as $mod)
                                <div class="cit-dropdown-item {{ $newModule === $mod ? 'cit-dropdown-selected' : '' }}" wire:click="$set('newModule', '{{ $mod }}')" @click="open = false">{{ $mod }}</div>
                            @endforeach
                        </div>
                    </div>
                    @error('newModule') <span class="cit-error">{{ $message }}</span> @enderror
                </div>

                {{-- Priority --}}
                <div class="cit-form-group" x-data="{ open: false }">
                    <label class="cit-form-label">Priority <span class="cit-required">*</span></label>
                    <div class="cit-dropdown" @click.away="open = false">
                        <button type="button" class="cit-dropdown-btn cit-form-input" @click="open = !open">
                            <span>{{ ucfirst($newPriority) }}</span>
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" :style="open && 'transform:rotate(180deg)'"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <div x-show="open" x-cloak class="cit-dropdown-menu">
                            @foreach(['low', 'medium', 'high', 'urgent'] as $pri)
                                <div class="cit-dropdown-item {{ $newPriority === $pri ? 'cit-dropdown-selected' : '' }}" wire:click="$set('newPriority', '{{ $pri }}')" @click="open = false">{{ ucfirst($pri) }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Subject --}}
                <div class="cit-form-group">
                    <label class="cit-form-label">Subject <span class="cit-required">*</span></label>
                    <input type="text" wire:model="newSubject" class="cit-form-input" placeholder="Brief description of the issue" />
                    @error('newSubject') <span class="cit-error">{{ $message }}</span> @enderror
                </div>

                {{-- Description --}}
                <div class="cit-form-group">
                    <label class="cit-form-label">Description <span class="cit-required">*</span></label>
                    <div class="cit-reply-toolbar" wire:ignore>
                        <button type="button" @click="execCreate('bold')" title="Bold" class="cit-toolbar-btn">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M15.6 10.79c.97-.67 1.65-1.77 1.65-2.79 0-2.26-1.75-4-4-4H7v14h7.04c2.09 0 3.71-1.7 3.71-3.79 0-1.52-.86-2.82-2.15-3.42zM10 6.5h3c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5h-3v-3zm3.5 9H10v-3h3.5c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5z"/></svg>
                        </button>
                        <button type="button" @click="execCreate('italic')" title="Italic" class="cit-toolbar-btn">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M10 4v3h2.21l-3.42 8H6v3h8v-3h-2.21l3.42-8H18V4z"/></svg>
                        </button>
                        <button type="button" @click="insertLinkCreate()" title="Link" class="cit-toolbar-btn">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
                        </button>
                    </div>
                    <div wire:ignore>
                        <div x-ref="descEditor"
                             contenteditable="true"
                             class="cit-reply-editor cit-desc-editor"
                             data-placeholder="Provide detailed description of your request..."
                             @paste.prevent="handlePaste($event)"></div>
                    </div>
                    @error('newDescription') <span class="cit-error">{{ $message }}</span> @enderror
                </div>

                {{-- Attachments --}}
                <div class="cit-form-group">
                    <label class="cit-form-label">Attachments</label>
                    <div class="cit-upload-zone" @click="$refs.newFileInput.click()">
                        <svg width="28" height="28" fill="none" stroke="#94A3B8" stroke-width="1.5" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg>
                        <span>Click to upload files</span>
                    </div>
                    <input type="file"
                           x-ref="newFileInput"
                           wire:model.live="newAttachments"
                           wire:key="cit-new-file-input"
                           multiple
                           accept=".pdf,.png,.jpg,.jpeg,.xlsx,.xls,.doc,.docx,.csv,.txt"
                           class="cit-hidden" />
                    @if($newAttachments && count($newAttachments))
                        <div class="cit-file-list" style="margin-top: 8px;">
                            @foreach($newAttachments as $i => $file)
                                <div class="cit-file-item">
                                    <svg width="14" height="14" fill="none" stroke="#1a6dd4" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg>
                                    <span class="cit-file-name">{{ $file->getClientOriginalName() }}</span>
                                    <button wire:click="removeNewAttachment({{ $i }})" class="cit-file-remove">&times;</button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="cit-modal-footer">
                <button wire:click="closeCreateModal" class="cit-btn-outline">Cancel</button>
                <button @click="syncAndCreate()" class="cit-btn-primary" wire:loading.attr="disabled" wire:target="createTicket">
                    <span wire:loading.remove wire:target="createTicket">Submit Ticket</span>
                    <span wire:loading wire:target="createTicket">Creating...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- ALPINE.JS --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <script>
    function citApp() {
        return {
            showFilters: false,
            replyOpen: false,
            init() {
                if (this.$wire) {
                    this.$wire.on('reply-sent', () => { this.replyOpen = false; });
                }
            },
            openReply() {
                this.replyOpen = true;
                this.$nextTick(() => {
                    const editor = this.$refs.replyEditor;
                    if (editor) {
                        editor.focus();
                        // Move cursor to end
                        const range = document.createRange();
                        range.selectNodeContents(editor);
                        range.collapse(false);
                        const sel = window.getSelection();
                        sel.removeAllRanges();
                        sel.addRange(range);
                    }
                });
            },
            closeReply() {
                this.replyOpen = false;
            },
            exec(command, value = null) {
                document.execCommand(command, false, value);
                this.$refs.replyEditor?.focus();
            },
            execCreate(command, value = null) {
                document.execCommand(command, false, value);
                this.$refs.descEditor?.focus();
            },
            insertLink() {
                const url = prompt('Enter URL:');
                if (url) this.exec('createLink', url);
            },
            insertLinkCreate() {
                const url = prompt('Enter URL:');
                if (url) this.execCreate('createLink', url);
            },
            handlePaste(e) {
                e.preventDefault();
                const text = e.clipboardData.getData('text/plain');
                document.execCommand('insertText', false, text);
            },
            syncAndSubmit() {
                const html = this.$refs.replyEditor?.innerHTML || '';
                if (!html.replace(/<[^>]*>/g, '').trim()) return;
                this.$wire.set('replyMessage', html);
                this.$nextTick(() => this.$wire.submitReply());
            },
            syncAndCreate() {
                const html = this.$refs.descEditor?.innerHTML || '';
                this.$wire.set('newDescription', html);
                this.$nextTick(() => this.$wire.createTicket());
            }
        }
    }
    </script>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- CSS --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <style>
/* ── Base ── */
.cit-wrap {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 0;
}
.cit-hidden { display: none !important; }
[x-cloak] { display: none !important; }

/* ── Header ── */
.cit-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 14px; gap: 16px; flex-wrap: wrap; }
.cit-title {
    font-size: 1.25rem; font-weight: 700; margin: 0 0 2px; letter-spacing: -0.01em;
    background: linear-gradient(135deg, #003c75 0%, #1a6dd4 100%);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    background-clip: text;
}
.cit-subtitle { font-size: 0.76rem; color: #64748B; margin: 0; }

/* ── Buttons ── */
.cit-btn-primary {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 16px; border: none; border-radius: 8px;
    background: linear-gradient(135deg, #1a6dd4 0%, #003c75 100%);
    color: #fff; font-size: 0.78rem; font-weight: 600;
    cursor: pointer; transition: all 0.2s; white-space: nowrap;
}
.cit-btn-primary:hover { box-shadow: 0 6px 20px rgba(26,109,212,0.35); transform: translateY(-1px); }
.cit-btn-primary:disabled { opacity: 0.6; cursor: not-allowed; transform: none; box-shadow: none; }

/* ── Gated create button ── */
.cit-create-wrap { position: relative; display: inline-block; }
.cit-btn-disabled {
    opacity: 0.5; cursor: not-allowed;
    background: linear-gradient(135deg, #94A3B8 0%, #64748B 100%);
    filter: grayscale(0.3);
}
.cit-btn-disabled:hover { transform: none; box-shadow: none; }
.cit-tooltip {
    position: absolute; top: calc(100% + 10px); right: 0;
    background: #0F172A; color: #fff;
    padding: 10px 14px; border-radius: 8px;
    font-size: 0.78rem; font-weight: 500; line-height: 1.4;
    max-width: 280px; min-width: 220px;
    display: inline-flex; align-items: flex-start; gap: 8px;
    box-shadow: 0 10px 25px rgba(15,23,42,0.25);
    z-index: 50;
}
.cit-tooltip svg { color: #FBBF24; margin-top: 1px; }
.cit-tooltip::before {
    content: ''; position: absolute; top: -5px; right: 18px;
    width: 10px; height: 10px; background: #0F172A;
    transform: rotate(45deg);
}
.cit-empty-hint {
    margin-top: 14px; display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 14px; border-radius: 999px;
    background: #FEF3C7; color: #92400E;
    font-size: 0.76rem; font-weight: 500;
}
.cit-empty-hint svg { color: #B45309; }
.cit-btn-outline {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 20px; border: 1px solid #E2E8F0; border-radius: 10px;
    background: #fff; color: #475569; font-size: 0.82rem; font-weight: 500;
    cursor: pointer; transition: all 0.15s;
}
.cit-btn-outline:hover { background: #F8FAFC; border-color: #CBD5E1; }

/* ── Stats ── */
.cit-stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 12px; }
.cit-stat-card {
    background: #fff; border: 1px solid #E2E8F0; border-radius: 10px; padding: 10px 14px;
    display: flex; align-items: center; justify-content: space-between;
    cursor: pointer; transition: all 0.2s; position: relative; overflow: hidden;
}
.cit-stat-card::before {
    content: ''; position: absolute; left: 0; top: 0; width: 3px; height: 100%;
    background: var(--stat-color); border-radius: 10px 0 0 10px; opacity: 0.5; transition: opacity 0.2s;
}
.cit-stat-card:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(0,0,0,0.05); }
.cit-stat-card:hover::before { opacity: 1; }
.cit-stat-active { border-color: var(--stat-color); box-shadow: 0 3px 10px rgba(0,0,0,0.07); }
.cit-stat-active::before { opacity: 1; }
.cit-stat-content { display: flex; flex-direction: column; gap: 1px; }
.cit-stat-label { font-size: 0.72rem; color: #64748B; font-weight: 500; }
.cit-stat-value { font-size: 1.25rem; font-weight: 700; color: #1E293B; line-height: 1.1; }
.cit-stat-icon { width: 32px; height: 32px; border-radius: 9px; display: flex; align-items: center; justify-content: center; }

/* ── Search ── */
.cit-search-row { display: flex; gap: 8px; margin-bottom: 12px; }
.cit-search-wrap { flex: 1; position: relative; }
.cit-search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; }
.cit-search-input {
    width: 100%; height: 34px; padding: 0 12px 0 36px;
    border: 1px solid #E2E8F0; border-radius: 8px; font-size: 0.78rem; color: #334155;
    background: #fff; outline: none; transition: border-color 0.15s, box-shadow 0.15s;
}
.cit-search-input:focus { border-color: #1a6dd4; box-shadow: 0 0 0 3px rgba(26,109,212,0.1); }
.cit-search-input::placeholder { color: #CBD5E1; }
.cit-filter-btn {
    display: inline-flex; align-items: center; gap: 6px; padding: 0 14px; height: 34px;
    border: 1px solid #E2E8F0; border-radius: 8px; background: #fff; color: #475569;
    font-size: 0.78rem; font-weight: 500; cursor: pointer; transition: all 0.15s;
}
.cit-filter-btn:hover { background: #F8FAFC; }
.cit-filter-active { background: #F5F3FF; border-color: #C4B5FD; color: #6D28D9; }

/* ── Filter Panel ── */
.cit-filter-panel { background: #fff; border: 1px solid #E2E8F0; border-radius: 12px; padding: 20px; margin-bottom: 16px; }
.cit-filter-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
.cit-filter-label { display: block; font-size: 0.74rem; font-weight: 600; color: #64748B; margin-bottom: 6px; }
.cit-filter-actions { margin-top: 12px; display: flex; justify-content: flex-end; }
.cit-filter-clear { background: none; border: none; color: #1a6dd4; font-size: 0.8rem; font-weight: 500; cursor: pointer; }
.cit-filter-clear:hover { color: #003c75; text-decoration: underline; }
.cit-filter-enter { transition: all 0.2s ease; }
.cit-filter-enter-start { opacity: 0; transform: translateY(-8px); }
.cit-filter-enter-end { opacity: 1; transform: translateY(0); }

/* ── Dropdown ── */
.cit-dropdown { position: relative; }
.cit-dropdown-btn {
    width: 100%; height: 38px; padding: 0 12px;
    display: flex; align-items: center; justify-content: space-between;
    border: 1px solid #E2E8F0; border-radius: 8px; background: #F9FAFB;
    font-size: 0.82rem; color: #334155; cursor: pointer; transition: border-color 0.15s;
}
.cit-dropdown-btn:hover { border-color: #CBD5E1; }
.cit-dropdown-btn svg { transition: transform 0.15s; flex-shrink: 0; }
.cit-dropdown-menu {
    position: absolute; top: calc(100% + 4px); left: 0; right: 0;
    background: #fff; border: 1px solid #E2E8F0; border-radius: 8px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.1); z-index: 50;
    max-height: 200px; overflow-y: auto;
}
.cit-dropdown-item {
    padding: 8px 14px; font-size: 0.82rem; color: #334155; cursor: pointer; transition: background 0.1s;
}
.cit-dropdown-item:hover { background: #EFF6FF; color: #1a6dd4; }
.cit-dropdown-selected { background: #EFF6FF; color: #1a6dd4; font-weight: 600; }

/* ── Table ── */
.cit-table-wrap { background: #fff; border: 1px solid #E2E8F0; border-radius: 12px; overflow: hidden; }
.cit-table-header-bar {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 20px; border-bottom: 1px solid #E2E8F0;
    background: linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 100%);
}
.cit-table-title { font-size: 0.92rem; font-weight: 700; color: #1E293B; }
.cit-table-count { font-size: 0.76rem; color: #64748B; }
.cit-table-scroll { overflow-x: auto; }
.cit-table { width: 100%; border-collapse: collapse; }
.cit-table thead { background: #F9FAFB; border-bottom: 1px solid #E2E8F0; }
.cit-table th {
    padding: 10px 16px; text-align: left; font-size: 0.7rem; font-weight: 600;
    color: #64748B; white-space: nowrap;
}
.cit-table-row {
    cursor: pointer; transition: background 0.1s; animation: citFadeIn 0.3s ease both;
}
.cit-table-row:hover { background: #F9FAFB; }
.cit-table-row td { padding: 12px 16px; border-bottom: 1px solid #F1F5F9; }
.cit-ticket-id {
    font-family: 'JetBrains Mono', 'Fira Code', monospace;
    font-size: 0.78rem; font-weight: 600; color: #1a6dd4;
}
.cit-ticket-subject { font-size: 0.84rem; font-weight: 500; color: #1E293B; }
.cit-meta-text { font-size: 0.8rem; color: #64748B; }
.cit-date-text { font-size: 0.78rem; color: #94A3B8; }
.cit-view-btn { color: #1a6dd4; cursor: pointer; }

/* ── Badges ── */
.cit-status-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 10px; border-radius: 6px; font-size: 0.72rem; font-weight: 600; white-space: nowrap;
}
.cit-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
.cit-badge-sm { display: inline-block; padding: 2px 8px; border-radius: 5px; font-size: 0.72rem; font-weight: 600; }
.cit-sla-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 9px; border-radius: 6px; font-size: 0.7rem; font-weight: 600;
    border: 1px solid transparent; white-space: nowrap;
}
.cit-sla-pulse { animation: citPulse 2s infinite; }
.cit-tag { font-size: 0.7rem; font-weight: 500; color: #64748B; background: #F1F5F9; padding: 2px 8px; border-radius: 4px; }

/* ── Mobile Card List ── */
.cit-mobile-list { display: none; flex-direction: column; gap: 10px; }
.cit-mobile-card {
    background: #fff; border: 1px solid #E2E8F0; border-radius: 10px; padding: 14px 16px;
    cursor: pointer; transition: all 0.15s; animation: citFadeIn 0.3s ease both;
}
.cit-mobile-card:hover { border-color: #93C5FD; box-shadow: 0 4px 14px rgba(26,109,212,0.08); }
.cit-mobile-card-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; }
.cit-mobile-card-subject { font-size: 0.88rem; font-weight: 600; color: #1E293B; margin-bottom: 8px; line-height: 1.3; }
.cit-mobile-card-meta { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 10px; }
.cit-mobile-card-bottom { display: flex; justify-content: space-between; align-items: center; padding-top: 10px; border-top: 1px solid #F1F5F9; }
.cit-reply-count { display: flex; align-items: center; gap: 5px; font-size: 0.76rem; color: #64748B; }

/* ── Empty ── */
.cit-empty {
    text-align: center; padding: 60px 20px; background: #FAFBFC;
    border: 1px dashed #CBD5E1; border-radius: 12px;
}
.cit-empty-icon { margin-bottom: 16px; }
.cit-empty-title { font-size: 1rem; font-weight: 600; color: #475569; margin-bottom: 4px; }
.cit-empty-desc { font-size: 0.82rem; color: #94A3B8; max-width: 360px; margin: 0 auto; }

/* ── Detail View: 2-Column Layout ── */
.cit-detail {
    display: flex;
    flex-direction: column;
    flex: 1;
    min-height: 0;
    overflow: hidden;
    margin: 0 -32px;
    padding: 8px 16px 0;
}
.cit-detail-topbar { flex-shrink: 0; padding: 4px 0 8px; }
.cit-back-btn {
    display: inline-flex; align-items: center; gap: 6px;
    background: none; border: none; font-size: 0.84rem; color: #64748B;
    cursor: pointer; padding: 0; margin-bottom: 0; transition: color 0.15s;
}
.cit-back-btn:hover { color: #1E293B; }
.cit-detail-columns {
    flex: 1;
    display: flex;
    gap: 16px;
    overflow: hidden;
    min-height: 0;
}

/* ── Left Column ── */
.cit-detail-left {
    width: 340px;
    flex-shrink: 0;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 14px;
    padding-right: 4px;
}
.cit-detail-header { background: #fff; border: 1px solid #E2E8F0; border-radius: 12px; padding: 20px 24px; margin-bottom: 0; }
.cit-detail-header-top { margin-bottom: 16px; }
.cit-detail-title-row { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.cit-detail-subject { font-size: 1.15rem; font-weight: 700; color: #1E293B; margin: 0; }
.cit-overdue-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 10px; background: #FEF2F2; color: #B91C1C; border: 1px solid #FECACA;
    border-radius: 6px; font-size: 0.72rem; font-weight: 600; animation: citPulse 2s infinite;
}
.cit-detail-id { font-size: 0.84rem; color: #64748B; margin-top: 4px; }
.cit-detail-id span { color: #1a6dd4; font-family: 'JetBrains Mono', monospace; font-weight: 600; }
.cit-detail-meta-grid {
    display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;
    padding-top: 14px; border-top: 1px solid #F1F5F9;
}
.cit-detail-meta-item { display: flex; flex-direction: column; gap: 3px; }
.cit-detail-meta-label { font-size: 0.7rem; font-weight: 600; color: #94A3B8; }
.cit-detail-meta-value { font-size: 0.82rem; color: #334155; }

/* ── SLA Card ── */
.cit-sla-card {
    margin-top: 14px; padding: 12px 16px; border-radius: 10px;
    background: linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 100%);
    border: 1px solid #E0E7FF;
}
.cit-sla-card-overdue { background: linear-gradient(135deg, #FEF2F2 0%, #FFFBEB 100%); border-color: #FECACA; }
.cit-sla-card-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px; }
.cit-sla-card-left { display: flex; align-items: center; gap: 6px; font-size: 0.8rem; font-weight: 600; color: #475569; }
.cit-sla-card-date { font-size: 0.8rem; color: #334155; }
.cit-sla-card-remaining { font-size: 0.72rem; color: #64748B; margin-top: 2px; }

/* ── Follow-up alert ── */
.cit-detail-followup-alert {
    display: flex;
    gap: 10px;
    align-items: flex-start;
    padding: 12px 14px;
    margin-top: 14px;
    background: #FEF3C7;
    border: 1px solid #FDE68A;
    border-radius: 10px;
    color: #92400E;
}
.cit-detail-followup-alert__icon { flex-shrink: 0; margin-top: 1px; }
.cit-detail-followup-alert__title { font-weight: 600; font-size: 0.8rem; display: flex; align-items: center; gap: 8px; }
.cit-detail-followup-alert__count {
    background: #92400E;
    color: #FEF3C7;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 1px 8px;
    border-radius: 999px;
    line-height: 1.5;
}
.cit-detail-followup-alert__sub { font-size: 0.72rem; margin-top: 2px; opacity: 0.85; }

/* ── Description ── */
.cit-description-card { background: #fff; border: 1px solid #E2E8F0; border-radius: 12px; padding: 18px 20px; margin-bottom: 0; }
.cit-section-title { display: flex; align-items: center; gap: 8px; font-size: 0.9rem; font-weight: 700; color: #1E293B; margin: 0 0 12px; }
.cit-description-body { font-size: 0.84rem; color: #475569; line-height: 1.7; }

/* ── Right Column: Thread Panel ── */
.cit-detail-right {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    min-width: 0;
    background: #FFFFFF;
    border: 1px solid #E2E8F0;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 4px 14px rgba(0,0,0,0.03);
}

/* ── Thread Title ── */
.cit-thread-title {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 16px;
    font-size: 0.88rem;
    font-weight: 700;
    color: #1E293B;
    border-bottom: 1px solid #F1F5F9;
    flex-shrink: 0;
}
.cit-thread-title svg { color: #1a6dd4; }

/* ── Thread Search ── */
.cit-thread-search {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: #FFFFFF;
    border-bottom: 1px solid #E2E5EB;
    border-radius: 0;
    flex-shrink: 0;
}
.cit-thread-search-icon { color: #94A3B8; flex-shrink: 0; }
.cit-thread-search input {
    flex: 1; border: none; outline: none;
    font-size: 0.82rem; color: #334155; background: transparent;
}
.cit-thread-search input::placeholder { color: #94A3B8; }
.cit-thread-search-clear {
    border: none; background: none; color: #9CA3AF;
    cursor: pointer; padding: 2px; font-size: 14px; line-height: 1; flex-shrink: 0;
}
.cit-thread-search-clear:hover { color: #6B7280; }

/* ── Thread Area (parchment) ── */
.cit-thread-area {
    flex: 1;
    overflow-y: auto;
    padding: 20px 16px;
    background: #F4F2EF;
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.cit-thread-empty {
    text-align: center; padding: 40px 20px; color: #94A3B8;
    display: flex; flex-direction: column; align-items: center; gap: 8px; font-size: 0.84rem;
}

/* ── WhatsApp-Style Bubbles ── */
.cit-bubble {
    max-width: 75%;
    padding: 10px 14px;
    position: relative;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
    transition: box-shadow 0.2s ease;
    animation: citSlideUp 0.3s ease both;
}
.cit-bubble:hover {
    box-shadow: 0 2px 6px rgba(0,0,0,0.08), 0 1px 2px rgba(0,0,0,0.04);
}
.cit-bubble-customer {
    align-self: flex-end;
    background: #DBEAFE;
    border-radius: 16px 4px 16px 16px;
}
.cit-bubble-staff {
    align-self: flex-start;
    background: #FFFFFF;
    border-radius: 4px 16px 16px 16px;
}
.cit-bubble-header {
    display: flex; align-items: center; gap: 8px; margin-bottom: 4px;
}
.cit-bubble-customer .cit-bubble-header { flex-direction: row-reverse; }
.cit-bubble-customer .cit-bubble-info { flex-direction: row-reverse; }
.cit-bubble-avatar {
    width: 30px; height: 30px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.cit-bubble-avatar svg { width: 14px !important; height: 14px !important; }
.cit-bubble-avatar.blue { background: linear-gradient(135deg, #DBEAFE, #BFDBFE); color: #1D4ED8; }
.cit-bubble-avatar.purple { background: linear-gradient(135deg, #EDE9FE, #DDD6FE); color: #6D28D9; }
.cit-bubble-info { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.cit-bubble-name { font-size: 12px; font-weight: 700; color: #1A1D26; }
.cit-bubble-badge { font-size: 10px; padding: 1px 8px; border-radius: 10px; font-weight: 600; }
.cit-bubble-badge.blue { background: #DBEAFE; color: #1E40AF; }
.cit-bubble-badge.purple { background: #EDE9FE; color: #5B21B6; }
.cit-bubble-badge.cit-bubble-badge--thread-label { background: #EEF2FF; color: #4338CA; border: 1px solid #C7D2FE; max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.cit-bubble-body { font-size: 13.5px; color: #334155; line-height: 1.65; word-wrap: break-word; }
.cit-bubble-body p { margin: 0 0 6px; }
.cit-bubble-time { font-size: 10px; color: #94A3B8; display: block; margin-top: 6px; font-weight: 500; }
.cit-bubble-customer .cit-bubble-time { text-align: right; }
.cit-bubble-staff .cit-bubble-time { text-align: left; }

/* ── Search Highlighting ── */
.cit-msg-dimmed { opacity: 0.08 !important; pointer-events: none; transition: opacity 0.3s ease; }
.cit-msg-highlight { box-shadow: 0 0 0 2px rgba(26, 109, 212, 0.25); transition: box-shadow 0.3s ease; }

/* ── Attachments ── */
.cit-attachment-list { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 6px; }
.cit-attachment-chip {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px; background: #EFF6FF; border-radius: 6px;
    font-size: 0.74rem; color: #2563EB; text-decoration: none; transition: background 0.15s;
}
.cit-attachment-chip:hover { background: #DBEAFE; }

/* ── Collapsed Reply Bar ── */
.cit-reply-collapsed {
    display: flex;
    align-items: center;
    gap: 12px;
    width: 100%;
    padding: 12px 16px;
    border: none;
    border-top: 1px solid #E2E5EB;
    background: #FFFFFF;
    border-radius: 0 0 12px 12px;
    cursor: pointer;
    text-align: left;
    transition: background 0.18s ease, box-shadow 0.18s ease;
    flex-shrink: 0;
    box-shadow: 0 -2px 8px rgba(0,0,0,0.025);
    font-family: inherit;
}
.cit-reply-collapsed:hover {
    background: linear-gradient(180deg, #F8FAFF 0%, #FFFFFF 100%);
    box-shadow: 0 -3px 12px rgba(26,109,212,0.06);
}
.cit-reply-collapsed:focus-visible {
    outline: none;
    background: #F0F4FF;
    box-shadow: 0 -2px 8px rgba(26,109,212,0.12);
}
.cit-reply-collapsed-avatar {
    width: 28px; height: 28px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #DBEAFE, #BFDBFE);
    color: #1D4ED8;
    font-size: 12px; font-weight: 700;
    flex-shrink: 0;
    box-shadow: 0 1px 2px rgba(29,78,216,0.15);
}
.cit-reply-collapsed-prompt {
    flex: 1;
    font-size: 0.84rem;
    color: #94A3B8;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.cit-reply-collapsed:hover .cit-reply-collapsed-prompt {
    color: #64748B;
}
.cit-reply-collapsed-meta {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 8px;
    background: #EFF6FF;
    color: #2563EB;
    border-radius: 999px;
    font-size: 10px;
    font-weight: 600;
    flex-shrink: 0;
}
.cit-reply-collapsed-icon {
    width: 32px; height: 32px;
    display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #1a6dd4 0%, #003c75 100%);
    color: #fff;
    border-radius: 999px;
    flex-shrink: 0;
    box-shadow: 0 2px 6px rgba(26,109,212,0.30);
    transition: transform 0.18s ease, box-shadow 0.18s ease;
}
.cit-reply-collapsed:hover .cit-reply-collapsed-icon {
    transform: translateX(2px) rotate(-8deg);
    box-shadow: 0 3px 10px rgba(26,109,212,0.40);
}
.cit-reply-minimize {
    margin-left: auto;
    color: #94A3B8;
}
.cit-reply-minimize:hover {
    background: #FEE2E2 !important;
    color: #DC2626 !important;
}

/* ── Reply expand transition ── */
.cit-reply-enter {
    transition: transform 0.22s cubic-bezier(0.32, 0.72, 0.30, 1.00),
                opacity 0.18s ease,
                max-height 0.22s ease;
    overflow: hidden;
}
.cit-reply-enter-start {
    opacity: 0;
    transform: translateY(8px);
    max-height: 0;
}
.cit-reply-enter-end {
    opacity: 1;
    transform: translateY(0);
    max-height: 480px;
}

/* ── Reply Box ── */
.cit-reply-box {
    border-top: 1px solid #E2E5EB;
    padding: 14px 16px;
    background: #FFFFFF;
    flex-shrink: 0;
    box-shadow: 0 -4px 12px rgba(0,0,0,0.03);
    border-radius: 0 0 12px 12px;
}
.cit-reply-toolbar {
    display: flex; align-items: center; gap: 2px;
    padding: 6px 8px; background: #F9FAFB; border: 1px solid #E2E8F0;
    border-radius: 8px 8px 0 0; border-bottom: none;
}
.cit-toolbar-btn {
    width: 30px; height: 28px; display: flex; align-items: center; justify-content: center;
    border: none; background: none; border-radius: 5px; color: #64748B;
    cursor: pointer; transition: all 0.1s;
}
.cit-toolbar-btn:hover { background: #E5E7EB; color: #1E293B; }
.cit-toolbar-divider { width: 1px; height: 18px; background: #E2E8F0; margin: 0 4px; }
.cit-reply-editor {
    min-height: 80px; max-height: 180px; overflow-y: auto;
    padding: 10px 12px; border: 1px solid #E2E8F0; border-top: none;
    border-radius: 0 0 8px 8px; font-size: 0.84rem; color: #334155;
    line-height: 1.6; outline: none; background: #fff;
}
.cit-reply-editor:focus { border-color: #1a6dd4; box-shadow: 0 0 0 2px rgba(26,109,212,0.08); }
.cit-reply-editor:empty::before { content: attr(data-placeholder); color: #CBD5E1; }
.cit-reply-editor p { margin: 0 0 6px; }
.cit-reply-editor a { color: #1a6dd4; text-decoration: underline; }
.cit-desc-editor { min-height: 160px; }
.cit-reply-footer { display: flex; justify-content: flex-end; margin-top: 8px; }
.cit-reply-send { font-size: 0.82rem; }

/* ── Closed / Escalation ── */
.cit-closed-notice {
    margin-top: 0; padding: 14px 18px; background: #ECFDF5; border: 1px solid #A7F3D0;
    border-radius: 0 0 12px 12px; display: flex; align-items: center; gap: 8px;
    font-size: 0.84rem; color: #047857; font-weight: 500;
}
.cit-reopen-btn {
    margin-left: auto; padding: 6px 14px; background: #1a6dd4; color: #fff;
    border: none; border-radius: 6px; font-size: 0.78rem; font-weight: 600;
    cursor: pointer; transition: background 0.2s; white-space: nowrap;
}
.cit-reopen-btn:hover { background: #5a6fd6; }
.cit-reopen-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.cit-escalation-card {
    display: flex; gap: 12px; padding: 14px 18px; background: #FEF2F2;
    border: 1px solid #FECACA; border-radius: 12px; color: #991B1B;
}
.cit-escalation-card strong { font-size: 0.84rem; display: block; margin-bottom: 2px; }
.cit-escalation-card p { font-size: 0.78rem; margin: 0; opacity: 0.85; }
.cit-escalation-card svg { flex-shrink: 0; margin-top: 2px; }

/* ── File List ── */
.cit-file-list { display: flex; flex-direction: column; gap: 6px; margin-top: 8px; }
.cit-file-item {
    display: flex; align-items: center; gap: 8px; padding: 6px 10px;
    background: #F9FAFB; border-radius: 6px; border: 1px solid #E2E8F0;
}
.cit-file-name { flex: 1; font-size: 0.78rem; color: #334155; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.cit-file-remove {
    background: none; border: none; color: #94A3B8; font-size: 1.1rem;
    cursor: pointer; padding: 0 4px; line-height: 1;
}
.cit-file-remove:hover { color: #EF4444; }

/* ── Modal ── */
.cit-modal-overlay {
    position: fixed; inset: 0; background: rgba(0,0,0,0.4);
    z-index: 100; display: flex; align-items: center; justify-content: center; padding: 16px;
}
.cit-modal {
    background: #fff; border-radius: 16px; width: 100%; max-width: 560px;
    max-height: 90vh; display: flex; flex-direction: column;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2); animation: citSlideUp 0.25s ease;
}
.cit-modal-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 18px 24px;
    background: linear-gradient(135deg, #1a6dd4 0%, #003c75 100%);
    border-radius: 16px 16px 0 0;
}
.cit-modal-header h2 { font-size: 1.05rem; font-weight: 700; color: #fff; margin: 0; }
.cit-modal-close {
    width: 32px; height: 32px; border-radius: 8px; border: none;
    background: rgba(255,255,255,0.15); color: #fff; font-size: 1.2rem;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    transition: background 0.15s;
}
.cit-modal-close:hover { background: rgba(255,255,255,0.3); }
.cit-modal-body { flex: 1; overflow-y: auto; padding: 20px 24px; }
.cit-modal-footer {
    display: flex; justify-content: flex-end; gap: 10px;
    padding: 14px 24px; border-top: 1px solid #E2E8F0; background: #F9FAFB;
    border-radius: 0 0 16px 16px;
}

/* ── Form ── */
.cit-form-group { margin-bottom: 16px; }
.cit-form-label { display: block; font-size: 0.8rem; font-weight: 600; color: #475569; margin-bottom: 6px; }
.cit-required { color: #EF4444; }
.cit-form-input {
    width: 100%; height: 40px; padding: 0 12px;
    border: 1px solid #E2E8F0; border-radius: 8px; background: #F9FAFB;
    font-size: 0.84rem; color: #334155; outline: none; transition: border-color 0.15s, box-shadow 0.15s;
    box-sizing: border-box;
}
.cit-form-input:focus { border-color: #1a6dd4; box-shadow: 0 0 0 3px rgba(26,109,212,0.1); }
.cit-form-input::placeholder { color: #94A3B8; }
.cit-error { display: block; font-size: 0.74rem; color: #EF4444; margin-top: 4px; }

/* ── Upload Zone ── */
.cit-upload-zone {
    display: flex; flex-direction: column; align-items: center; gap: 6px;
    padding: 24px; border: 2px dashed #E2E8F0; border-radius: 10px;
    background: #F9FAFB; cursor: pointer; transition: all 0.15s;
}
.cit-upload-zone:hover { border-color: #C4B5FD; background: #FAFAFE; }
.cit-upload-zone span { font-size: 0.8rem; color: #64748B; }

/* ── Animations ── */
@keyframes citFadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
@keyframes citSlideUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
@keyframes citPulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.6; } }

/* ── Responsive ── */
@media (max-width: 900px) {
    .cit-detail { height: auto; overflow: visible; margin: 0; padding: 0; }
    .cit-detail-columns { flex-direction: column; overflow-y: auto; }
    .cit-detail-left { width: 100%; overflow-y: visible; }
    .cit-detail-right { min-height: 400px; }
}
@media (max-width: 768px) {
    .cit-stats-grid { grid-template-columns: repeat(2, 1fr); }
    .cit-table-wrap { display: none; }
    .cit-mobile-list { display: flex; }
    .cit-filter-grid { grid-template-columns: 1fr; }
    .cit-detail-meta-grid { grid-template-columns: repeat(2, 1fr); }
    .cit-header { flex-direction: column; }
    .cit-search-row { flex-direction: column; }
    .cit-modal { max-width: 100%; margin: 16px; }
}
@media (max-width: 480px) {
    .cit-stats-grid { grid-template-columns: 1fr; }
    .cit-detail-meta-grid { grid-template-columns: 1fr; }
}

/* ── Scrollbar ── */
.cit-thread-area::-webkit-scrollbar,
.cit-detail-left::-webkit-scrollbar { width: 5px; }
.cit-thread-area::-webkit-scrollbar-track,
.cit-detail-left::-webkit-scrollbar-track { background: transparent; }
.cit-thread-area::-webkit-scrollbar-thumb,
.cit-detail-left::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 3px; }
.cit-thread-area::-webkit-scrollbar-thumb:hover,
.cit-detail-left::-webkit-scrollbar-thumb:hover { background: #94A3B8; }
.cit-dropdown-menu::-webkit-scrollbar { width: 4px; }
.cit-dropdown-menu::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 2px; }
    </style>
</div>
