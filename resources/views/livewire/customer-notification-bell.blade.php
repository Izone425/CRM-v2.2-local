<div x-data="{ open: false }" @click.away="open = false" wire:poll.30s class="cnb-wrapper">
    <style>
        .cnb-wrapper {
            position: relative;
            display: inline-flex;
            align-items: center;
        }

        .cnb-bell-btn {
            position: relative;
            background: var(--tt-surface, #ffffff);
            border: 1px solid var(--tt-border, #e5e7eb);
            border-radius: 50%;
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            color: var(--tt-accent-dark, #003c75);
        }

        .cnb-bell-btn:hover {
            background: var(--tt-hover-bg, #f0f7ff);
            border-color: transparent;
            color: var(--tt-accent-mid, #1a6dd4);
            transform: scale(1.05);
        }

        .cnb-bell-btn svg {
            width: 16px;
            height: 16px;
        }

        .cnb-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: #EF4444;
            color: white;
            font-size: 9px;
            font-weight: 700;
            min-width: 15px;
            height: 15px;
            padding: 0 4px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            border: 1.5px solid #ffffff;
        }

        .cnb-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 380px;
            background: white;
            border-radius: 14px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(0, 0, 0, 0.05);
            z-index: 999;
            overflow: hidden;
        }

        .cnb-dropdown-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            border-bottom: 1px solid #F3F4F6;
        }

        .cnb-dropdown-title {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
        }

        .cnb-mark-all {
            font-size: 12px;
            color: #7C3AED;
            font-weight: 600;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
            transition: color 0.2s;
        }

        .cnb-mark-all:hover {
            color: #6D28D9;
            text-decoration: underline;
        }

        .cnb-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .cnb-item {
            display: flex;
            gap: 12px;
            padding: 14px 20px;
            cursor: pointer;
            transition: background 0.15s;
            border-bottom: 1px solid #F9FAFB;
            align-items: flex-start;
        }

        .cnb-item:hover {
            background: #F9FAFB;
        }

        .cnb-item.cnb-unread {
            background: #F5F3FF;
            border-left: 3px solid #7C3AED;
        }

        .cnb-item.cnb-unread:hover {
            background: #EDE9FE;
        }

        .cnb-item-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 14px;
        }

        .cnb-icon-reply { background: #DBEAFE; color: #2563EB; }
        .cnb-icon-status { background: #FEF3C7; color: #D97706; }
        .cnb-icon-closed { background: #D1FAE5; color: #059669; }
        .cnb-icon-created { background: #EDE9FE; color: #7C3AED; }
        .cnb-icon-data-file { background: #ECFEFF; color: #0E7490; }
        .cnb-icon-default { background: #F3F4F6; color: #6B7280; }

        .cnb-item-body {
            flex: 1;
            min-width: 0;
        }

        .cnb-item-title {
            font-size: 13px;
            font-weight: 600;
            color: #111827;
            margin: 0 0 2px 0;
            line-height: 1.3;
        }

        .cnb-item-message {
            font-size: 12px;
            color: #6B7280;
            margin: 0;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .cnb-item-time {
            font-size: 11px;
            color: #9CA3AF;
            margin-top: 4px;
            white-space: nowrap;
        }

        .cnb-unread-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #7C3AED;
            flex-shrink: 0;
            margin-top: 6px;
        }

        .cnb-empty {
            padding: 40px 20px;
            text-align: center;
        }

        .cnb-empty-icon {
            width: 48px;
            height: 48px;
            color: #D1D5DB;
            margin: 0 auto 12px;
        }

        .cnb-empty-text {
            font-size: 14px;
            color: #9CA3AF;
            margin: 0;
        }

        .cnb-list::-webkit-scrollbar {
            width: 6px;
        }

        .cnb-list::-webkit-scrollbar-track {
            background: transparent;
        }

        .cnb-list::-webkit-scrollbar-thumb {
            background: #E5E7EB;
            border-radius: 3px;
        }
    </style>

    <!-- Bell Button -->
    <button class="cnb-bell-btn" @click="open = !open" title="Notifications">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>
        @if($this->unreadCount > 0)
            <span class="cnb-badge">{{ $this->unreadCount > 99 ? '99+' : $this->unreadCount }}</span>
        @endif
    </button>

    <!-- Dropdown Panel -->
    <div class="cnb-dropdown" x-show="open" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1">
        <div class="cnb-dropdown-header">
            <span class="cnb-dropdown-title">Notifications</span>
            @if($this->unreadCount > 0)
                <button class="cnb-mark-all" wire:click="markAllAsRead">Mark all as read</button>
            @endif
        </div>

        <div class="cnb-list">
            @forelse($this->notifications as $notification)
                @php
                    $data = $notification->data;
                    $type = $data['type'] ?? '';
                    $isUnread = is_null($notification->read_at);

                    // Determine icon class based on notification type
                    if (str_contains($type, 'data_file')) {
                        $iconClass = 'cnb-icon-data-file';
                        $iconHtml = '<i class="fas fa-file-alt"></i>';
                    } elseif (str_contains($type, 'replied')) {
                        $iconClass = 'cnb-icon-reply';
                        $iconHtml = '<i class="fas fa-reply"></i>';
                    } elseif (str_contains($type, 'status')) {
                        $iconClass = 'cnb-icon-status';
                        $iconHtml = '<i class="fas fa-exchange-alt"></i>';
                    } elseif (str_contains($type, 'closed')) {
                        $iconClass = 'cnb-icon-closed';
                        $iconHtml = '<i class="fas fa-check-circle"></i>';
                    } elseif (str_contains($type, 'created')) {
                        $iconClass = 'cnb-icon-created';
                        $iconHtml = '<i class="fas fa-plus-circle"></i>';
                    } else {
                        $iconClass = 'cnb-icon-default';
                        $iconHtml = '<i class="fas fa-bell"></i>';
                    }
                @endphp
                <div class="cnb-item {{ $isUnread ? 'cnb-unread' : '' }}"
                     wire:click="openNotification('{{ $notification->id }}')"
                     wire:key="notif-{{ $notification->id }}">
                    <div class="cnb-item-icon {{ $iconClass }}">{!! $iconHtml !!}</div>
                    <div class="cnb-item-body">
                        <p class="cnb-item-title">{{ $data['title'] ?? 'Notification' }}</p>
                        <p class="cnb-item-message">{{ $data['message'] ?? '' }}</p>
                        <span class="cnb-item-time">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                    @if($isUnread)
                        <div class="cnb-unread-dot"></div>
                    @endif
                </div>
            @empty
                <div class="cnb-empty">
                    <svg class="cnb-empty-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                    <p class="cnb-empty-text">No notifications yet</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
