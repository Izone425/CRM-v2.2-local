<x-filament-panels::page>
    <style>
        .imp-client-wrapper {
            background: #F9FAFB;
            min-height: 100vh;
            padding: 0;
        }

        /* Back Link */
        .imp-client-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #4B5563;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 24px;
            transition: color 0.15s;
        }
        .imp-client-back:hover {
            color: #111827;
        }

        /* Client Details Card */
        .imp-client-card {
            background: white;
            border-radius: 12px;
            border: 1px solid #E5E7EB;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            padding: 24px;
            margin-bottom: 24px;
        }
        .imp-client-card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 24px;
        }
        .imp-client-card-identity {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .imp-client-avatar {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #7C3AED, #2563EB);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .imp-client-avatar svg {
            width: 32px;
            height: 32px;
            color: white;
        }
        .imp-client-name {
            font-size: 24px;
            font-weight: 600;
            color: #111827;
            margin: 0;
        }
        .imp-client-subtitle {
            font-size: 14px;
            color: #6B7280;
            margin: 2px 0 0 0;
        }
        .imp-client-crm-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #F5F3FF;
            color: #6D28D9;
            border: 1px solid #DDD6FE;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.15s;
            white-space: nowrap;
        }
        .imp-client-crm-btn:hover {
            background: #EDE9FE;
            border-color: #C4B5FD;
            color: #5B21B6;
        }

        /* Info Grid */
        .imp-client-info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }
        .imp-client-info-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .imp-client-info-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .imp-client-info-icon.blue { background: #EFF6FF; }
        .imp-client-info-icon.blue svg { color: #2563EB; }
        .imp-client-info-icon.green { background: #F0FDF4; }
        .imp-client-info-icon.green svg { color: #16A34A; }
        .imp-client-info-icon.orange { background: #FFF7ED; }
        .imp-client-info-icon.orange svg { color: #EA580C; }
        .imp-client-info-icon svg {
            width: 20px;
            height: 20px;
        }
        .imp-client-info-label {
            font-size: 12px;
            color: #6B7280;
            margin: 0;
        }
        .imp-client-info-value {
            font-size: 14px;
            font-weight: 500;
            color: #111827;
            margin: 2px 0 0 0;
        }

        /* Tickets Section */
        .imp-client-tickets-card {
            background: white;
            border-radius: 12px;
            border: 1px solid #E5E7EB;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        .imp-client-tickets-header {
            padding: 16px 24px;
            border-bottom: 1px solid #E5E7EB;
        }
        .imp-client-tickets-title {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            margin: 0;
        }
        .imp-client-tickets-count {
            font-size: 14px;
            color: #6B7280;
            font-weight: 400;
            margin-left: 8px;
        }

        /* Tickets Table */
        .imp-client-table {
            width: 100%;
            border-collapse: collapse;
        }
        .imp-client-table thead {
            background: #F9FAFB;
            border-bottom: 1px solid #E5E7EB;
        }
        .imp-client-table th {
            padding: 12px 24px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .imp-client-table tbody tr {
            border-bottom: 1px solid #F3F4F6;
            transition: background 0.15s;
        }
        .imp-client-table tbody tr:hover {
            background: #F9FAFB;
        }
        .imp-client-table td {
            padding: 16px 24px;
            font-size: 14px;
            color: #374151;
        }
        .imp-client-ticket-id {
            font-weight: 600;
            color: #111827;
        }
        .imp-client-cat-primary {
            font-weight: 500;
            color: #111827;
        }
        .imp-client-cat-secondary {
            font-size: 13px;
            color: #6B7280;
        }

        /* Status Badge */
        .imp-client-status-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            border: 1px solid;
        }
        .imp-client-status-badge.open { background: #DBEAFE; color: #1D4ED8; border-color: #BFDBFE; }
        .imp-client-status-badge.pending_support { background: #FEF3C7; color: #92400E; border-color: #FDE68A; }
        .imp-client-status-badge.pending_client { background: #EDE9FE; color: #6D28D9; border-color: #DDD6FE; }
        .imp-client-status-badge.pending_rnd { background: #FCE7F3; color: #BE185D; border-color: #FBCFE8; }
        .imp-client-status-badge.closed { background: #DCFCE7; color: #166534; border-color: #BBF7D0; }

        /* Priority */
        .imp-client-priority-high { color: #DC2626; font-weight: 500; }
        .imp-client-priority-medium { color: #D97706; font-weight: 500; }
        .imp-client-priority-low { color: #2563EB; font-weight: 500; }

        /* Ticket Row */
        .imp-client-ticket-row {
            cursor: pointer;
            transition: background 0.15s ease;
        }
        .imp-client-ticket-row:hover {
            background: #F0F4FF;
        }

        /* Action Button */
        .imp-client-view-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px;
            color: #2563EB;
            background: none;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.15s;
            text-decoration: none;
        }
        .imp-client-view-btn:hover {
            background: #EFF6FF;
        }
        .imp-client-view-btn svg {
            width: 16px;
            height: 16px;
        }

        /* Empty State */
        .imp-client-empty {
            text-align: center;
            padding: 48px 24px;
            color: #6B7280;
            font-size: 14px;
        }

    </style>

    <div class="imp-client-wrapper">
        <!-- Back Link -->
        <a href="{{ route('filament.admin.pages.implementer-ticketing-dashboard') }}" class="imp-client-back">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Back to Ticketing Dashboard
        </a>

        <!-- Client Details Card -->
        @if($customer)
            <div class="imp-client-card">
                <div class="imp-client-card-header">
                    <div class="imp-client-card-identity">
                        <div class="imp-client-avatar">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="imp-client-name">{{ $customer->name }}</h1>
                            <p class="imp-client-subtitle">{{ $customer->company_name ?? 'No Company' }}</p>
                        </div>
                    </div>
                    @if($customer->lead_id)
                        <a href="{{ route('filament.admin.resources.leads.view', $customer->lead_id) }}" class="imp-client-crm-btn" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                            </svg>
                            View Company CRM
                        </a>
                    @endif
                </div>

                <div class="imp-client-info-grid">
                    <div class="imp-client-info-item">
                        <div class="imp-client-info-icon blue">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                            </svg>
                        </div>
                        <div>
                            <p class="imp-client-info-label">Company</p>
                            <p class="imp-client-info-value">{{ $customer->company_name ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="imp-client-info-item">
                        <div class="imp-client-info-icon green">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                        </div>
                        <div>
                            <p class="imp-client-info-label">Email</p>
                            <p class="imp-client-info-value">{{ $customer->email ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="imp-client-info-item">
                        <div class="imp-client-info-icon orange">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                            </svg>
                        </div>
                        <div>
                            <p class="imp-client-info-label">Phone</p>
                            <p class="imp-client-info-value">{{ $customer->phone ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Tickets Raised by This User -->
        <div class="imp-client-tickets-card">
            <div class="imp-client-tickets-header">
                <h2 class="imp-client-tickets-title">
                    Tickets Raised by This User
                    <span class="imp-client-tickets-count">({{ $ticketCount }} total)</span>
                </h2>
            </div>

            @if($ticketCount > 0)
                <table class="imp-client-table">
                    <thead>
                        <tr>
                            <th>TICKET ID</th>
                            <th>CATEGORY & MODULE</th>
                            <th>STATUS</th>
                            <th>PRIORITY</th>
                            <th>CREATED</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                            <tr class="imp-client-ticket-row"
                                onclick="window.location.href='/admin/implementer-ticketing-dashboard?ticket={{ $ticket->id }}&from=/admin/implementer-client-profile/{{ $customerId }}'">
                                <td>
                                    <span class="imp-client-ticket-id">{{ $ticket->formatted_ticket_number }}</span>
                                </td>
                                <td>
                                    <div>
                                        <div class="imp-client-cat-primary">{{ $ticket->category ?? '-' }}</div>
                                        <div class="imp-client-cat-secondary">{{ $ticket->module ?? '' }}</div>
                                    </div>
                                </td>
                                <td>
                                    <span class="imp-client-status-badge {{ $ticket->status->value }}">
                                        {{ $ticket->status->label() }}
                                    </span>
                                </td>
                                <td>
                                    <span class="imp-client-priority-{{ strtolower($ticket->priority ?? 'medium') }}">
                                        {{ ucfirst($ticket->priority ?? 'Medium') }}
                                    </span>
                                </td>
                                <td style="color: #6B7280;">
                                    {{ $ticket->created_at->format('n/j/Y') }}
                                </td>
                                <td>
                                    <a href="/admin/implementer-ticketing-dashboard?ticket={{ $ticket->id }}&from=/admin/implementer-client-profile/{{ $customerId }}"
                                       class="imp-client-view-btn"
                                       title="View">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="imp-client-empty">
                    No tickets found for this user
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
