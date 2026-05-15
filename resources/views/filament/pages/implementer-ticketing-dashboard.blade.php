<x-filament-panels::page>
    @php
        $data = $this->getViewData();
        $openCount = $data['openCount'];
        $pendingSupportCount = $data['pendingSupportCount'];
        $pendingRndCount = $data['pendingRndCount'];
        $overdueCount = $data['overdueCount'];
        $complianceRate = $data['complianceRate'];
        $avgResolutionHours = $data['avgResolutionHours'];
        $firstResponseRate = $data['firstResponseRate'];
        $pendingClientCount = $data['pendingClientCount'];
        $allTicketsCount = $data['allTicketsCount'];
        $tickets = $data['tickets'];
        $totalFiltered = $data['totalFiltered'];
        $totalPages = $data['totalPages'];
        $currentPage = $data['currentPage'];
        $perPage = $data['perPage'];
        $implementers = $data['implementers'];
        $customers = $data['customers'];
    @endphp

    <style>
        select:not(.choices) {
            background-image: none !important;
        }

        .imp-dashboard-wrapper {
            background: #F9FAFB;
            min-height: 100vh;
            padding: 0;
        }

        .imp-dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
        }

        .imp-header-left h1 {
            font-size: 24px;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }

        .imp-header-left p {
            font-size: 14px;
            color: #6B7280;
            margin: 4px 0 0 0;
        }

        .imp-header-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .imp-search-box {
            position: relative;
        }

        .imp-search-box input {
            padding: 8px 12px 8px 36px;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            font-size: 14px;
            width: 260px;
            outline: none;
            transition: all 0.2s;
            background: white;
        }

        .imp-search-box input:focus {
            border-color: #6366F1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .imp-search-box svg {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            color: #9CA3AF;
        }

        /* Searchable Filter Dropdown */
        .imp-searchable-filter {
            position: relative;
            min-width: 200px;
        }

        .imp-searchable-trigger {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            background: white;
            font-size: 14px;
            color: #374151;
            cursor: pointer;
            width: 100%;
            transition: all 0.2s;
        }

        .imp-searchable-trigger:hover {
            border-color: #9CA3AF;
        }

        .imp-searchable-trigger.open {
            border-color: #6366F1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .imp-searchable-trigger input {
            border: none;
            outline: none;
            background: transparent;
            font-size: 14px;
            color: #374151;
            width: 100%;
            padding: 0;
        }

        .imp-searchable-trigger input::placeholder {
            color: #6B7280;
        }

        .imp-searchable-chevron {
            width: 16px;
            height: 16px;
            color: #9CA3AF;
            flex-shrink: 0;
            transition: transform 0.2s;
        }

        .imp-searchable-chevron.open {
            transform: rotate(180deg);
        }

        .imp-searchable-clear {
            background: none;
            border: none;
            cursor: pointer;
            color: #9CA3AF;
            padding: 0;
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }

        .imp-searchable-clear:hover {
            color: #374151;
        }

        .imp-searchable-dropdown {
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            z-index: 50;
            max-height: 240px;
            overflow-y: auto;
        }

        .imp-searchable-option {
            padding: 8px 12px;
            font-size: 13px;
            color: #374151;
            cursor: pointer;
            transition: background 0.1s;
        }

        .imp-searchable-option:hover {
            background: #F3F4F6;
        }

        .imp-searchable-option.active {
            background: #EEF2FF;
            color: #4F46E5;
            font-weight: 600;
        }

        .imp-searchable-empty {
            padding: 12px;
            font-size: 13px;
            color: #9CA3AF;
            text-align: center;
        }

        .imp-searchable-dropdown::-webkit-scrollbar {
            width: 6px;
        }

        .imp-searchable-dropdown::-webkit-scrollbar-thumb {
            background: #D1D5DB;
            border-radius: 3px;
        }

        .imp-create-btn {
            padding: 10px 20px;
            background: #6366F1;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            text-decoration: none;
        }

        .imp-create-btn:hover {
            background: #4F46E5;
        }

        /* Stat Cards */
        .imp-stat-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .imp-stat-card {
            background: white;
            border-radius: 12px;
            padding: 14px 16px;
            border: 1px solid #E5E7EB;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .imp-stat-card:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transform: translateY(-1px);
        }

        .imp-stat-card.active {
            border: 2px solid #6366F1;
            background: #F5F3FF;
        }

        .imp-stat-card.purple { border-left: 4px solid #7C3AED; }
        .imp-stat-card.blue { border-left: 4px solid #2563EB; }
        .imp-stat-card.magenta { border-left: 4px solid #DB2777; }
        .imp-stat-card.red { border-left: 4px solid #DC2626; }

        .imp-stat-card.active.purple { border-left: 4px solid #7C3AED; border-right: 2px solid #6366F1; border-top: 2px solid #6366F1; border-bottom: 2px solid #6366F1; }
        .imp-stat-card.active.blue { border-left: 4px solid #2563EB; border-right: 2px solid #6366F1; border-top: 2px solid #6366F1; border-bottom: 2px solid #6366F1; }
        .imp-stat-card.active.magenta { border-left: 4px solid #DB2777; border-right: 2px solid #6366F1; border-top: 2px solid #6366F1; border-bottom: 2px solid #6366F1; }
        .imp-stat-card.active.red { border-left: 4px solid #DC2626; border-right: 2px solid #6366F1; border-top: 2px solid #6366F1; border-bottom: 2px solid #6366F1; }

        .imp-stat-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .imp-stat-card.purple .imp-stat-icon { background: #EDE9FE; color: #7C3AED; }
        .imp-stat-card.blue .imp-stat-icon { background: #DBEAFE; color: #2563EB; }
        .imp-stat-card.magenta .imp-stat-icon { background: #FCE7F3; color: #DB2777; }
        .imp-stat-card.red .imp-stat-icon { background: #FEE2E2; color: #DC2626; }

        .imp-stat-info {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .imp-stat-number {
            font-size: 22px;
            font-weight: 700;
            color: #111827;
            line-height: 1;
        }

        .imp-stat-label {
            font-size: 13px;
            color: #6B7280;
            font-weight: 500;
        }

        /* SLA Performance */
        .imp-sla-card {
            background: white;
            border-radius: 12px;
            padding: 16px 24px;
            border: 1px solid #E5E7EB;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
        }

        .imp-sla-title {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
            white-space: nowrap;
            min-width: 130px;
        }

        .imp-sla-metrics {
            display: flex;
            flex: 1;
            justify-content: space-evenly;
        }

        .imp-sla-metric {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .imp-sla-metric-icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .imp-sla-metric:nth-child(1) .imp-sla-metric-icon { background: #D1FAE5; color: #059669; }
        .imp-sla-metric:nth-child(2) .imp-sla-metric-icon { background: #DBEAFE; color: #2563EB; }
        .imp-sla-metric:nth-child(3) .imp-sla-metric-icon { background: #FEF3C7; color: #D97706; }

        .imp-sla-metric-value {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
            line-height: 1;
        }

        .imp-sla-metric-unit {
            font-size: 13px;
            font-weight: 500;
            color: #6B7280;
        }

        .imp-sla-metric-label {
            font-size: 12px;
            color: #9CA3AF;
            font-weight: 500;
            margin-top: 2px;
        }

        /* Tabs */
        .imp-tabs {
            display: flex;
            gap: 0;
            margin-bottom: 0;
            border-bottom: 2px solid #E5E7EB;
        }

        .imp-tab {
            padding: 12px 24px;
            font-size: 14px;
            font-weight: 500;
            color: #6B7280;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            transition: all 0.2s;
            background: none;
            border-top: none;
            border-left: none;
            border-right: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .imp-tab:hover {
            color: #374151;
        }

        .imp-tab.active {
            color: #6366F1;
            border-bottom-color: #6366F1;
            font-weight: 600;
        }

        .imp-tab-badge {
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            background: #F3F4F6;
            color: #6B7280;
        }

        .imp-tab.active .imp-tab-badge {
            background: #EEF2FF;
            color: #6366F1;
        }

        /* Table */
        .imp-table-wrapper {
            background: white;
            border-radius: 0 0 12px 12px;
            border: 1px solid #E5E7EB;
            border-top: none;
            overflow-x: auto;
        }

        .imp-table {
            width: 100%;
            border-collapse: collapse;
        }

        .imp-table thead {
            background: #FAFAFA;
            border-bottom: 2px solid #E5E7EB;
        }

        .imp-table th {
            padding: 12px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .imp-table td {
            padding: 14px 16px;
            font-size: 13px;
            color: #374151;
            border-bottom: 1px solid #F3F4F6;
        }

        .imp-table tbody tr:hover {
            background: #F9FAFB;
        }

        .imp-ticket-id {
            font-weight: 600;
            color: #111827;
        }
        .imp-ticket-id-wrap {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }
        .imp-ticket-id-wrap::after {
            content: attr(data-tooltip);
            position: absolute;
            left: 0;
            top: 100%;
            margin-top: 6px;
            padding: 6px 12px;
            background: #1E293B;
            color: #fff;
            font-size: 12px;
            font-weight: 500;
            border-radius: 6px;
            white-space: nowrap;
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.15s, visibility 0.15s;
            z-index: 10;
            pointer-events: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .imp-ticket-id-wrap:hover::after {
            opacity: 1;
            visibility: visible;
        }

        .imp-priority-badge {
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 6px;
        }

        .imp-priority-badge.critical { background: #FEE2E2; color: #DC2626; }
        .imp-priority-badge.high { background: #FEF3C7; color: #D97706; }
        .imp-priority-badge.medium { background: #DBEAFE; color: #2563EB; }
        .imp-priority-badge.low { background: #D1FAE5; color: #059669; }

        .imp-sla-badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .imp-sla-badge.on_track { background: #D1FAE5; color: #059669; }
        .imp-sla-badge.at_risk { background: #FEF3C7; color: #D97706; }
        .imp-sla-badge.overdue { background: #FEE2E2; color: #DC2626; }
        .imp-sla-badge.resolved { background: #F3F4F6; color: #6B7280; }

        .imp-status-badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .imp-status-badge.open { background: #DBEAFE; color: #2563EB; }
        .imp-status-badge.pending_support { background: #FEF3C7; color: #D97706; }
        .imp-status-badge.pending_client { background: #EDE9FE; color: #7C3AED; }
        .imp-status-badge.pending_rnd { background: #FCE7F3; color: #DB2777; }
        .imp-status-badge.closed { background: #F3F4F6; color: #6B7280; }

        .imp-implementer-cell {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .imp-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #E5E7EB;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 600;
            color: #6B7280;
            flex-shrink: 0;
        }

        .imp-actions {
            display: flex;
            gap: 8px;
        }

        .imp-action-btn {
            padding: 6px;
            border: 1px solid #E5E7EB;
            border-radius: 6px;
            background: white;
            cursor: pointer;
            color: #6B7280;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .imp-action-btn:hover {
            background: #F9FAFB;
            border-color: #D1D5DB;
            color: #374151;
        }

        .imp-action-btn svg {
            width: 16px;
            height: 16px;
        }

        /* Pagination */
        .imp-pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px;
            border-top: 1px solid #E5E7EB;
        }

        .imp-pagination-info {
            font-size: 13px;
            color: #6B7280;
        }

        .imp-pagination-controls {
            display: flex;
            gap: 4px;
            align-items: center;
        }

        .imp-page-btn {
            padding: 6px 12px;
            border: 1px solid #E5E7EB;
            border-radius: 6px;
            background: white;
            cursor: pointer;
            font-size: 13px;
            color: #374151;
            transition: all 0.2s;
        }

        .imp-page-btn:hover:not(:disabled) {
            background: #F9FAFB;
            border-color: #D1D5DB;
        }

        .imp-page-btn:disabled {
            color: #D1D5DB;
            cursor: not-allowed;
        }

        .imp-page-btn.active {
            background: #6366F1;
            border-color: #6366F1;
            color: white;
        }

        /* Empty State */
        .imp-empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .imp-empty-state svg {
            width: 64px;
            height: 64px;
            color: #D1D5DB;
            margin-bottom: 16px;
        }

        .imp-empty-state p {
            color: #9CA3AF;
            font-size: 14px;
        }

        /* SLA Policy Button */
        .imp-sla-policy-btn {
            padding: 10px 20px;
            background: #7C3AED;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .imp-sla-policy-btn:hover {
            background: #6D28D9;
        }

        /* SLA Policy Modal */
        .imp-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 50;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .imp-modal {
            background: white;
            border-radius: 16px;
            width: 100%;
            max-width: 640px;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .imp-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            border-bottom: 1px solid #E5E7EB;
            flex-shrink: 0;
        }

        .imp-modal-header h2 {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .imp-modal-close {
            background: none;
            border: none;
            cursor: pointer;
            color: #9CA3AF;
            padding: 4px;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .imp-modal-close:hover {
            background: #F3F4F6;
            color: #374151;
        }

        .imp-modal-body {
            padding: 24px;
            overflow-y: auto;
            flex: 1;
        }

        .imp-modal-section {
            margin-bottom: 24px;
        }

        .imp-modal-section:last-child {
            margin-bottom: 0;
        }

        .imp-modal-section h3 {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
            margin: 0 0 12px 0;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .imp-modal-section h3 svg {
            width: 16px;
            height: 16px;
            color: #6366F1;
        }

        .imp-sla-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            overflow: hidden;
        }

        .imp-sla-table th {
            background: #F9FAFB;
            padding: 10px 14px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: #6B7280;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            border-bottom: 1px solid #E5E7EB;
        }

        .imp-sla-table td {
            padding: 10px 14px;
            font-size: 13px;
            color: #374151;
            border-bottom: 1px solid #F3F4F6;
        }

        .imp-sla-table tbody tr:last-child td {
            border-bottom: none;
        }

        .imp-sla-priority-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 6px;
        }

        .imp-sla-priority-dot.high { background: #DC2626; }
        .imp-sla-priority-dot.medium { background: #F59E0B; }
        .imp-sla-priority-dot.low { background: #10B981; }

        .imp-policy-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .imp-policy-list li {
            padding: 8px 0;
            font-size: 13px;
            color: #374151;
            display: flex;
            align-items: flex-start;
            gap: 8px;
            border-bottom: 1px solid #F3F4F6;
        }

        .imp-policy-list li:last-child {
            border-bottom: none;
        }

        .imp-policy-list li svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .imp-policy-list li .imp-bullet-blue { color: #2563EB; }
        .imp-policy-list li .imp-bullet-amber { color: #D97706; }
        .imp-policy-list li .imp-bullet-red { color: #DC2626; }
        .imp-policy-list li .imp-bullet-green { color: #059669; }

        .imp-target-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .imp-target-card {
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            padding: 14px;
            text-align: center;
        }

        .imp-target-value {
            font-size: 22px;
            font-weight: 700;
            color: #111827;
        }

        .imp-target-label {
            font-size: 12px;
            color: #6B7280;
            margin-top: 4px;
        }

        /* SLA Gear Button */
        .imp-sla-gear-btn {
            background: #F3F4F6;
            border: 1px solid #E5E7EB;
            cursor: pointer;
            color: #6B7280;
            padding: 6px;
            border-radius: 8px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .imp-sla-gear-btn:hover {
            background: #EDE9FE;
            color: #7C3AED;
            border-color: #C4B5FD;
        }

        /* SLA Status Badges */
        .imp-status-badge {
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 10px;
            margin-left: 8px;
            letter-spacing: 0.02em;
        }

        .imp-badge-active {
            background: #ECFDF5;
            color: #059669;
        }

        .imp-badge-inactive {
            background: #FEF2F2;
            color: #DC2626;
        }

        /* SLA Config Form */
        .imp-sla-config-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .imp-config-section {
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 12px;
            padding: 18px;
        }

        .imp-config-section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .imp-config-section-header h3 {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .imp-config-section-header h3 svg {
            width: 16px;
            height: 16px;
            color: #6366F1;
        }

        /* Toggle Switch */
        .imp-config-toggle {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
            cursor: pointer;
        }

        .imp-config-toggle input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .imp-toggle-slider {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: #D1D5DB;
            border-radius: 24px;
            transition: all 0.3s;
        }

        .imp-toggle-slider::before {
            content: '';
            position: absolute;
            width: 18px;
            height: 18px;
            left: 3px;
            bottom: 3px;
            background: white;
            border-radius: 50%;
            transition: all 0.3s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.15);
        }

        .imp-config-toggle input:checked + .imp-toggle-slider {
            background: #7C3AED;
        }

        .imp-config-toggle input:checked + .imp-toggle-slider::before {
            transform: translateX(20px);
        }

        /* Config Fields */
        .imp-config-desc {
            font-size: 12px;
            color: #6B7280;
            margin: 0 0 14px 0;
            line-height: 1.5;
        }

        .imp-config-field {
            margin-bottom: 12px;
        }

        .imp-config-field:last-child {
            margin-bottom: 0;
        }

        .imp-config-field label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .imp-config-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            font-size: 14px;
            color: #111827;
            background: white;
            transition: all 0.2s;
            outline: none;
            box-sizing: border-box;
        }

        .imp-config-input:focus {
            border-color: #7C3AED;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }

        .imp-config-field-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .imp-config-input-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .imp-config-input-group .imp-config-input {
            flex: 1;
            min-width: 0;
        }

        .imp-config-unit {
            font-size: 12px;
            color: #6B7280;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .imp-config-note {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #6B7280;
            margin-top: 12px;
            padding: 10px 12px;
            background: #EFF6FF;
            border-radius: 8px;
            border: 1px solid #BFDBFE;
        }

        /* Config Actions */
        .imp-config-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding-top: 4px;
        }

        .imp-config-cancel-btn {
            padding: 10px 20px;
            background: white;
            color: #374151;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .imp-config-cancel-btn:hover {
            background: #F9FAFB;
            border-color: #9CA3AF;
        }

        .imp-config-save-btn {
            padding: 10px 20px;
            background: #7C3AED;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .imp-config-save-btn:hover {
            background: #6D28D9;
        }

        /* Create Ticket Drawer */
        .imp-drawer-modal {
            height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: white;
            box-shadow: -4px 0 24px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 640px;
            border-radius: 0;
        }

        .imp-drawer-header {
            background: linear-gradient(to right, #7C3AED, #2563EB);
            padding: 20px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
            border-radius: 0;
        }

        .imp-drawer-header h2 {
            font-size: 18px;
            font-weight: 600;
            color: white;
            margin: 0;
        }

        .imp-drawer-header p {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.75);
            margin: 4px 0 0 0;
        }

        .imp-drawer-close {
            background: rgba(255, 255, 255, 0.15);
            border: none;
            border-radius: 8px;
            padding: 8px;
            cursor: pointer;
            color: white;
            transition: all 0.2s;
        }

        .imp-drawer-close:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .imp-drawer-body {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
        }

        .imp-drawer-body::-webkit-scrollbar {
            width: 6px;
        }

        .imp-drawer-body::-webkit-scrollbar-track {
            background: #f3f4f6;
        }

        .imp-drawer-body::-webkit-scrollbar-thumb {
            background-color: #d1d5db;
            border-radius: 3px;
        }

        .imp-drawer-section {
            margin-bottom: 20px;
        }

        .imp-drawer-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .imp-drawer-field label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
        }

        .imp-drawer-field label .imp-required {
            color: #DC2626;
        }

        .imp-drawer-field .imp-badge {
            display: inline-block;
            font-size: 11px;
            background: #F3E8FF;
            color: #7C3AED;
            padding: 2px 8px;
            border-radius: 4px;
            margin-left: 6px;
            font-weight: 500;
        }

        .imp-drawer-select,
        .imp-drawer-input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #D1D5DB;
            border-radius: 10px;
            font-size: 14px;
            color: #111827;
            background: white;
            outline: none;
            transition: all 0.2s;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
        }

        .imp-drawer-select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239CA3AF' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19.5 8.25l-7.5 7.5-7.5-7.5'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 36px;
        }

        .imp-drawer-select:focus,
        .imp-drawer-input:focus,
        .imp-drawer-textarea:focus {
            border-color: #7C3AED;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }

        .imp-drawer-select:disabled {
            background-color: #F3F4F6;
            color: #9CA3AF;
            cursor: not-allowed;
        }

        .imp-drawer-helper {
            font-size: 12px;
            color: #059669;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .imp-drawer-textarea {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #D1D5DB;
            border-top: none;
            border-radius: 0 0 10px 10px;
            font-size: 14px;
            color: #111827;
            background: white;
            outline: none;
            transition: all 0.2s;
            resize: none;
            font-family: inherit;
        }

        .imp-drawer-textarea[contenteditable] {
            min-height: 200px;
            max-height: 400px;
            overflow-y: auto;
            line-height: 1.6;
            cursor: text;
        }

        .imp-drawer-textarea[contenteditable]:focus {
            border-color: #7C3AED;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }

        .imp-drawer-textarea[contenteditable]:empty:before {
            content: attr(data-placeholder);
            color: #9CA3AF;
            pointer-events: none;
        }

        .imp-drawer-textarea[contenteditable] p {
            margin: 0 0 8px 0;
        }

        .imp-drawer-textarea[contenteditable] a {
            color: #7C3AED;
            text-decoration: underline;
        }

        .imp-drawer-toolbar {
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 8px 12px;
            background: #F9FAFB;
            border: 1px solid #D1D5DB;
            border-radius: 10px 10px 0 0;
        }

        .imp-drawer-toolbar button {
            padding: 6px;
            border: none;
            background: none;
            border-radius: 6px;
            cursor: pointer;
            color: #6B7280;
            transition: all 0.15s;
        }

        .imp-drawer-toolbar button:hover {
            background: #E5E7EB;
        }

        .imp-drawer-toolbar button.imp-toolbar-active {
            background: #E5E7EB;
            color: #111827;
        }

        .imp-drawer-toolbar .imp-toolbar-divider {
            width: 1px;
            height: 20px;
            background: #D1D5DB;
            margin: 0 4px;
        }

        .imp-drawer-upload {
            border: 2px dashed #D1D5DB;
            border-radius: 10px;
            padding: 24px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background: #F9FAFB;
        }

        .imp-drawer-upload:hover {
            border-color: #7C3AED;
            background: #FAF5FF;
        }

        .imp-drawer-upload-icon {
            width: 40px;
            height: 40px;
            background: #EDE9FE;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
        }

        .imp-drawer-upload-text {
            font-size: 13px;
            font-weight: 500;
            color: #374151;
        }

        .imp-drawer-upload-hint {
            font-size: 12px;
            color: #9CA3AF;
            margin-top: 4px;
        }

        .imp-drawer-file-list {
            margin-top: 10px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .imp-drawer-file-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            background: white;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
        }

        .imp-drawer-file-info {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #374151;
        }

        .imp-drawer-file-size {
            font-size: 12px;
            color: #9CA3AF;
        }

        .imp-drawer-file-remove {
            background: none;
            border: none;
            padding: 4px;
            cursor: pointer;
            color: #9CA3AF;
            border-radius: 4px;
            transition: all 0.15s;
        }

        .imp-drawer-file-remove:hover {
            color: #DC2626;
            background: #FEF2F2;
        }

        .imp-drawer-footer {
            border-top: 1px solid #E5E7EB;
            background: #F9FAFB;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            flex-shrink: 0;
        }

        .imp-drawer-cancel {
            padding: 10px 20px;
            border: 1px solid #D1D5DB;
            border-radius: 10px;
            background: white;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            cursor: pointer;
            transition: all 0.2s;
        }

        .imp-drawer-cancel:hover {
            background: #F3F4F6;
        }

        .imp-drawer-submit {
            padding: 10px 20px;
            background: linear-gradient(to right, #7C3AED, #2563EB);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(124, 58, 237, 0.3);
        }

        .imp-drawer-submit:hover {
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.4);
        }

        .imp-drawer-submit svg {
            width: 16px;
            height: 16px;
        }

        .imp-drawer-submit[disabled],
        .imp-drawer-submit.imp-drawer-submit-disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
            box-shadow: none;
        }

        /* Add-on category block banner */
        .imp-block-banner {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 14px;
            margin-top: 8px;
            background: #FEF3C7;
            border: 1px solid #FCD34D;
            border-radius: 8px;
            color: #92400E;
            font-size: 13px;
            line-height: 1.45;
        }
        .imp-block-banner svg {
            flex-shrink: 0;
            margin-top: 1px;
        }

        .imp-fields-disabled {
            opacity: 0.45;
            pointer-events: none;
            user-select: none;
            filter: saturate(0.6);
        }

        /* Email Body drop zone */
        .imp-editor-zone { position: relative; }
        .imp-editor-zone--dragover .imp-drawer-textarea {
            outline: 2px dashed #7C3AED;
            outline-offset: -2px;
            background: rgba(124, 58, 237, 0.04);
        }
        .imp-editor-drop-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(124, 58, 237, 0.06);
            color: #7C3AED;
            font-size: 14px;
            font-weight: 600;
            pointer-events: none;
            border-radius: 8px;
        }
        .imp-editor-hint {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 8px;
            font-size: 12px;
            color: #6B7280;
            line-height: 1.4;
        }
        .imp-editor-hint svg { flex-shrink: 0; color: #9CA3AF; }
        .imp-editor-hint a { color: #7C3AED; text-decoration: underline; cursor: pointer; }

        /* Email Body flex-grow chain (fills remaining drawer height) */
        .imp-drawer-body { display: flex; flex-direction: column; }
        .imp-fields-stack {
            display: flex; flex-direction: column;
            flex: 1; min-height: 0;
        }
        .imp-drawer-section--grow {
            flex: 1; min-height: 0;
            display: flex; flex-direction: column;
        }
        .imp-drawer-section--grow > .imp-drawer-field {
            flex: 1; min-height: 0;
            display: flex; flex-direction: column;
        }
        .imp-drawer-section--grow .imp-editor-zone {
            flex: 1; min-height: 0;
            display: flex; flex-direction: column;
        }
        .imp-drawer-section--grow .imp-editor-zone__inner {
            flex: 1; min-height: 0;
            display: flex; flex-direction: column;
        }
        .imp-drawer-section--grow .imp-drawer-textarea {
            flex: 1; min-height: 200px; max-height: none;
        }

        /* Search select for company */
        .imp-drawer-search-select {
            position: relative;
        }

        .imp-drawer-search-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            font-size: 13px;
            outline: none;
            margin-bottom: 4px;
        }

        .imp-drawer-search-input:focus {
            border-color: #7C3AED;
        }

        .imp-drawer-option-list {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            background: white;
        }

        .imp-drawer-option {
            padding: 10px 14px;
            font-size: 13px;
            color: #374151;
            cursor: pointer;
            transition: background 0.15s;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }

        .imp-drawer-option:hover {
            background: #F3E8FF;
        }

        .imp-drawer-option.selected {
            background: #EDE9FE;
            font-weight: 600;
            color: #7C3AED;
        }

        .imp-drawer-selected-company {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            border: 1px solid #D1D5DB;
            border-radius: 10px;
            background: white;
            cursor: pointer;
        }

        .imp-drawer-selected-company:hover {
            background: #F9FAFB;
        }

        .imp-drawer-error {
            color: #DC2626;
            font-size: 12px;
            margin-top: 4px;
        }

        /* === Company Link === */
        .imp-company-link {
            background: none;
            border: none;
            color: #2563EB;
            font-weight: 500;
            cursor: pointer;
            padding: 0;
            font-size: inherit;
            text-decoration: none;
            transition: all 0.15s;
        }
        .imp-company-link:hover {
            text-decoration: underline;
            color: #1D4ED8;
        }

        /* === Clickable Row === */
        .imp-clickable-row {
            cursor: pointer;
            transition: background-color 0.15s;
        }
        .imp-clickable-row:hover {
            background-color: #F9FAFB !important;
        }

        /* Create drawer: hide topbar when open */
        body.imp-drawer-open {
            overflow: hidden !important;
        }
        body.imp-drawer-open .fi-topbar {
            display: none !important;
        }

        /* Ticket detail: hide topbar + reclaim chrome padding */
        body.imp-detail-fullscreen .fi-topbar { display: none !important; }
        body.imp-detail-fullscreen .fi-main { padding-top: 0 !important; }
        body.imp-detail-fullscreen .fi-page { padding: 0 !important; }

        /* === Ticket Detail Full Page === */
        .imp-fullpage-detail {
            background: transparent;
            border-radius: 0;
            border: none;
            display: flex;
            flex-direction: column;
            height: calc(100dvh - 24px);
            overflow: hidden;
            box-shadow: none;
            padding: 0 4px;
        }
        .imp-fullpage-detail::before {
            display: none;
        }
        .imp-back-row {
            padding: 8px 4px;
            border-bottom: none;
            background: transparent;
            flex-shrink: 0;
        }
        .imp-back-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: none;
            border: none;
            font-size: 13px;
            font-weight: 500;
            color: #64748B;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        .imp-back-btn:hover {
            color: #1A1D26;
            background: rgba(0, 0, 0, 0.04);
        }

        /* Detail Header */
        .imp-detail-header {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 18px 24px 16px;
            margin: 0 0 16px 0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 4px 14px rgba(0, 0, 0, 0.03);
            flex-shrink: 0;
        }
        .imp-detail-header-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .imp-detail-header-info {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
            min-width: 0;
        }
        .imp-detail-title {
            font-size: 17px;
            font-weight: 700;
            color: #1A1D26;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            letter-spacing: -0.01em;
        }
        .imp-detail-ticket-id {
            font-size: 12px;
            color: #94A3B8;
            font-weight: 500;
            flex-shrink: 0;
            background: #F1F3F8;
            padding: 2px 8px;
            border-radius: 4px;
        }
        .imp-detail-close {
            background: none;
            border: none;
            padding: 6px;
            cursor: pointer;
            border-radius: 6px;
            color: #6B7280;
            transition: background 0.15s;
            flex-shrink: 0;
        }
        .imp-detail-close:hover {
            background: #F3F4F6;
        }
        .imp-detail-header-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }
        .imp-detail-sla-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.01em;
        }
        .imp-detail-sla-badge.on_track { background: #ECFDF5; color: #065F46; box-shadow: inset 0 0 0 1px rgba(6, 95, 70, 0.12); }
        .imp-detail-sla-badge.at_risk { background: #FFFBEB; color: #854D0E; box-shadow: inset 0 0 0 1px rgba(133, 77, 14, 0.15); }
        .imp-detail-sla-badge.overdue { background: #FEF2F2; color: #991B1B; box-shadow: inset 0 0 0 1px rgba(153, 27, 27, 0.12); }
        .imp-detail-sla-badge.resolved { background: #F1F3F8; color: #475569; box-shadow: inset 0 0 0 1px rgba(71, 85, 105, 0.1); }
        .imp-detail-company-name {
            font-size: 13px;
            color: #64748B;
            font-weight: 500;
        }
        .imp-detail-actions-bar {
            display: flex;
            gap: 8px;
        }

        /* Merge Ticket Button */
        .imp-merge-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            background: #F3F4F6;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            cursor: pointer;
            transition: all 0.2s;
        }

        .imp-merge-btn:hover {
            background: #EDE9FE;
            color: #7C3AED;
            border-color: #C4B5FD;
        }

        .imp-merged-badge-link {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            background: #FEF3C7;
            border: 1px solid #FDE68A;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            color: #92400E;
            text-decoration: none;
            transition: all 0.2s;
        }

        .imp-merged-badge-link:hover {
            background: #FDE68A;
        }


        /* Merge Drawer */
        .imp-merge-search-box {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            background: white;
            margin-bottom: 16px;
        }

        .imp-merge-search-box:focus-within {
            border-color: #6366F1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .imp-merge-ticket-list {
            max-height: calc(100vh - 340px);
            overflow-y: auto;
        }

        .imp-merge-ticket-item {
            padding: 12px 14px;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: all 0.15s;
        }

        .imp-merge-ticket-item:hover {
            border-color: #C4B5FD;
            background: #FAFAFF;
        }

        .imp-merge-ticket-item.selected {
            border-color: #7C3AED;
            background: #F5F3FF;
            box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.2);
        }

        .imp-merge-ticket-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 4px;
        }

        .imp-merge-ticket-number {
            font-size: 13px;
            font-weight: 600;
            color: #111827;
        }

        .imp-merge-ticket-status {
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 6px;
        }

        .imp-merge-ticket-status.open { background: #DBEAFE; color: #1D4ED8; }
        .imp-merge-ticket-status.pending_support { background: #FEF3C7; color: #92400E; }
        .imp-merge-ticket-status.pending_client { background: #FEE2E2; color: #991B1B; }
        .imp-merge-ticket-status.pending_rnd { background: #F3F4F6; color: #4B5563; }
        .imp-merge-ticket-status.closed { background: #D1FAE5; color: #065F46; }

        .imp-merge-ticket-subject {
            font-size: 13px;
            color: #374151;
            margin-bottom: 6px;
            line-height: 1.4;
        }

        .imp-merge-ticket-meta {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #9CA3AF;
        }

        .imp-split-submit.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .imp-detail-action-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            border: 1px solid;
            transition: all 0.15s;
        }
        .imp-detail-action-btn.internal {
            background: #FFFBEB;
            color: #92400E;
            border-color: #FDE68A;
        }
        .imp-detail-action-btn.internal:hover {
            background: #FEF3C7;
        }

        /* Split Content */
        .imp-detail-content {
            flex: 1;
            display: flex;
            gap: 16px;
            overflow: hidden;
            min-height: 0;
        }

        /* Left Sidebar */
        .imp-detail-sidebar {
            width: 320px;
            flex-shrink: 0;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 14px 14px;
            overflow-y: auto;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 4px 14px rgba(0, 0, 0, 0.03);
        }
        .imp-detail-sidebar-title {
            font-size: 11px;
            font-weight: 700;
            color: #64748B;
            margin: 0 0 10px 0;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .imp-detail-section {
            margin-bottom: 8px;
        }
        .imp-detail-section::after {
            content: '';
            display: block;
            height: 1px;
            background: linear-gradient(90deg, transparent, #D8DCE5, transparent);
            margin-top: 8px;
        }
        .imp-detail-section:last-child::after {
            display: none;
        }
        .imp-detail-label {
            display: block;
            font-size: 10px;
            font-weight: 700;
            color: #94A3B8;
            letter-spacing: 0.06em;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        .imp-detail-card {
            background: #F8FAFC;
            border: 1px solid #EEF0F4;
            border-radius: 10px;
            padding: 8px 10px;
            box-shadow: none;
            transition: background 0.2s ease;
        }
        .imp-detail-card:hover {
            background: #F1F5F9;
        }
        .imp-detail-card-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .imp-detail-avatar {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }
        .imp-detail-avatar.blue { background: linear-gradient(135deg, #DBEAFE, #BFDBFE); color: #1D4ED8; }
        .imp-detail-avatar.purple { background: linear-gradient(135deg, #EDE9FE, #DDD6FE); color: #6D28D9; }
        .imp-detail-avatar.yellow { background: linear-gradient(135deg, #FEF3C7, #FDE68A); color: #92400E; }
        .imp-detail-name {
            font-size: 13px;
            font-weight: 600;
            color: #1A1D26;
            margin: 0;
        }
        .imp-detail-sublabel {
            font-size: 11px;
            color: #94A3B8;
            margin: 0;
        }
        .imp-detail-email-row {
            display: flex;
            align-items: center;
            gap: 4px;
            margin-top: 2px;
            font-size: 10px;
            color: #64748B;
        }
        .imp-detail-people-grid {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .imp-detail-person {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Status Dropdown */
        .imp-detail-status-wrapper {
            position: relative;
        }
        .imp-detail-status-btn {
            width: 100%;
            background: #F8FAFC;
            border: 1px solid #EEF0F4;
            border-radius: 10px;
            padding: 9px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }
        .imp-detail-status-btn:hover {
            border-color: #CBD5E1;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
        }
        .imp-detail-status-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            margin-top: 4px;
            background: #FFFFFF;
            border: 1px solid #E2E5EB;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12), 0 2px 8px rgba(0, 0, 0, 0.04);
            z-index: 20;
            padding: 4px;
        }
        .imp-detail-status-option {
            display: block;
            width: 100%;
            text-align: left;
            padding: 8px 12px;
            font-size: 13px;
            background: none;
            border: none;
            cursor: pointer;
            color: #475569;
            border-radius: 6px;
            transition: all 0.15s ease;
        }
        .imp-detail-status-option:hover { background: #F1F5F9; }
        .imp-detail-status-option.active { background: #F0EDFA; font-weight: 600; color: #5B5FC7; }

        /* Key Dates */
        .imp-detail-dates-card {
            background: #F8FAFC;
            border: 1px solid #EEF0F4;
            border-radius: 10px;
            box-shadow: none;
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
        .imp-detail-date-item {
            padding: 8px 10px;
        }
        .imp-detail-date-item + .imp-detail-date-item {
            border-top: none;
            border-left: 1px solid #F1F3F8;
        }
        .imp-detail-date-label {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            color: #94A3B8;
            margin-bottom: 2px;
            font-weight: 500;
        }
        .imp-detail-date-value {
            font-size: 13px;
            font-weight: 600;
            color: #1A1D26;
            margin: 0;
        }

        /* Details Props */
        .imp-detail-props-card {
            background: #F8FAFC;
            border: 1px solid #EEF0F4;
            border-radius: 10px;
            padding: 8px 10px;
            box-shadow: none;
        }
        .imp-detail-prop-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            padding: 2px 0;
        }
        .imp-detail-prop-label { color: #94A3B8; font-weight: 500; }
        .imp-detail-prop-value { color: #1A1D26; font-weight: 500; }
        .imp-detail-prop-value.priority-high { color: #DC2626; font-weight: 600; }
        .imp-detail-prop-value.priority-medium { color: #D97706; font-weight: 600; }
        .imp-detail-prop-value.priority-low { color: #2563EB; font-weight: 600; }

        /* Right Panel - Thread */
        .imp-detail-thread-panel {
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
        .imp-detail-thread {
            flex: 1;
            overflow-y: auto;
            padding: 24px 20px;
            background: #F4F2EF;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .imp-detail-message {
            max-width: 75%;
            padding: 10px 14px;
            margin-bottom: 2px;
            position: relative;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06), 0 1px 2px rgba(0, 0, 0, 0.04);
            transition: box-shadow 0.2s ease;
        }
        .imp-detail-message:hover {
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.04);
        }
        .imp-detail-message.client {
            align-self: flex-start;
            background: #FFFFFF;
            border: none;
            border-radius: 4px 16px 16px 16px;
        }
        .imp-detail-message.staff {
            align-self: flex-end;
            background: #EDEAF7;
            border: none;
            border-radius: 16px 4px 16px 16px;
        }
        .imp-detail-message.internal {
            align-self: center;
            background: #FEF9EE;
            border: 1.5px dashed #E5C76B;
            border-radius: 12px;
            max-width: 85%;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }
        .imp-detail-msg-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
        }
        .imp-detail-message.staff .imp-detail-msg-header {
            flex-direction: row-reverse;
        }
        .imp-detail-msg-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .imp-detail-msg-avatar svg {
            width: 14px !important;
            height: 14px !important;
        }
        .imp-detail-msg-avatar.blue { background: linear-gradient(135deg, #DBEAFE, #BFDBFE); color: #1D4ED8; }
        .imp-detail-msg-avatar.purple { background: linear-gradient(135deg, #EDE9FE, #DDD6FE); color: #6D28D9; }
        .imp-detail-msg-avatar.yellow { background: linear-gradient(135deg, #FEF3C7, #FDE68A); color: #92400E; }
        .imp-detail-msg-info {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }
        .imp-detail-message.staff .imp-detail-msg-info {
            flex-direction: row-reverse;
        }
        .imp-detail-msg-name {
            font-size: 12px;
            font-weight: 700;
            color: #1A1D26;
        }
        .imp-detail-msg-badge {
            font-size: 10px;
            padding: 1px 8px;
            border-radius: 10px;
            font-weight: 600;
        }
        .imp-detail-msg-badge.blue { background: #DBEAFE; color: #1E40AF; }
        .imp-detail-msg-badge.purple { background: #EDE9FE; color: #5B21B6; }
        .imp-detail-msg-badge.yellow { background: #FEF3C7; color: #854D0E; }
        .imp-detail-msg-badge.thread-label {
            background: #EEF2FF;
            color: #4338CA;
            border: 1px solid #C7D2FE;
            max-width: 260px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .imp-detail-msg-time {
            font-size: 10px;
            color: #94A3B8;
            display: block;
            margin-top: 6px;
            font-weight: 500;
        }
        .imp-detail-message.client .imp-detail-msg-time {
            text-align: left;
        }
        .imp-detail-message.staff .imp-detail-msg-time {
            text-align: right;
        }
        .imp-detail-msg-body {
            font-size: 13.5px;
            color: #334155;
            line-height: 1.65;
            padding: 0;
        }
        .imp-ticket-link {
            color: #7C3AED;
            font-weight: 600;
            cursor: pointer;
            text-decoration: underline;
            text-decoration-style: dotted;
            text-underline-offset: 2px;
        }
        .imp-ticket-link:hover {
            color: #6D28D9;
            text-decoration-style: solid;
        }
        .imp-note-edit-btn {
            margin-left: auto;
            padding: 4px;
            border: none;
            background: none;
            color: #9CA3AF;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.15s;
            flex-shrink: 0;
        }
        .imp-note-edit-btn:hover {
            background: #FEF3C7;
            color: #92400E;
        }
        .imp-note-edit-area {
            padding-left: 0;
        }
        .imp-note-edit-textarea {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #FDE68A;
            border-radius: 6px;
            font-size: 13px;
            font-family: inherit;
            line-height: 1.6;
            resize: vertical;
            background: #FFFEF5;
            color: #374151;
        }
        .imp-note-edit-textarea:focus {
            outline: none;
            border-color: #D97706;
            box-shadow: 0 0 0 2px rgba(217, 119, 6, 0.1);
        }
        .imp-note-edit-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-top: 6px;
        }
        .imp-note-edit-cancel {
            padding: 4px 12px;
            border: 1px solid #D1D5DB;
            background: white;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            color: #6B7280;
        }
        .imp-note-edit-cancel:hover {
            background: #F3F4F6;
        }
        .imp-note-edit-save {
            padding: 4px 12px;
            border: none;
            background: #D97706;
            color: white;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
        }
        .imp-note-edit-save:hover {
            background: #B45309;
        }
        .imp-note-edited-label {
            font-size: 11px;
            color: #92400E;
            font-style: italic;
        }
        .imp-note-action-btns {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 2px;
            flex-shrink: 0;
        }
        .imp-note-delete-btn {
            padding: 4px;
            border: none;
            background: none;
            color: #9CA3AF;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.15s;
        }
        .imp-note-delete-btn:hover {
            background: #FEE2E2;
            color: #DC2626;
        }
        .imp-note-delete-confirm {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #6B7280;
        }
        .imp-note-delete-yes {
            padding: 2px 10px;
            border: none;
            background: #DC2626;
            color: white;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
            cursor: pointer;
        }
        .imp-note-delete-yes:hover {
            background: #B91C1C;
        }
        .imp-note-delete-no {
            padding: 2px 10px;
            border: 1px solid #D1D5DB;
            background: white;
            color: #6B7280;
            border-radius: 4px;
            font-size: 11px;
            cursor: pointer;
        }
        .imp-note-delete-no:hover {
            background: #F3F4F6;
        }
        .imp-detail-msg-attachments {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            padding-left: 0;
            margin-top: 6px;
        }
        .imp-detail-attachment-link {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            font-weight: 500;
            color: #2563EB;
            text-decoration: none;
            padding: 4px 10px;
            background: #EFF6FF;
            border-radius: 6px;
            border: 1px solid #DBEAFE;
            transition: all 0.15s ease;
        }
        .imp-detail-attachment-link:hover {
            background: #DBEAFE;
            border-color: #BFDBFE;
            box-shadow: 0 1px 3px rgba(37, 99, 235, 0.1);
        }
        .imp-detail-no-messages {
            text-align: center;
            padding: 40px 20px;
            color: #94A3B8;
            font-size: 13px;
        }

        /* Thread Search */
        .imp-thread-search {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: #FFFFFF;
            border-bottom: 1px solid #E2E5EB;
            border-radius: 12px 12px 0 0;
            flex-shrink: 0;
        }
        .imp-thread-search i {
            color: #94A3B8;
            font-size: 14px;
            flex-shrink: 0;
        }
        .imp-thread-search input {
            flex: 1;
            border: none;
            outline: none;
            font-size: 13px;
            color: #334155;
            background: transparent;
        }
        .imp-thread-search input::placeholder {
            color: #94A3B8;
        }
        .imp-thread-search-clear {
            border: none;
            background: none;
            color: #9CA3AF;
            cursor: pointer;
            padding: 2px;
            font-size: 14px;
            line-height: 1;
            flex-shrink: 0;
        }
        .imp-thread-search-clear:hover {
            color: #6B7280;
        }
        .imp-msg-dimmed {
            opacity: 0.08 !important;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .imp-msg-highlight {
            box-shadow: 0 0 0 2px rgba(91, 95, 199, 0.25);
            transition: box-shadow 0.3s ease;
        }

        /* Split Button */
        .imp-detail-message.client {
            position: relative;
        }
        .imp-split-btn {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 28px;
            height: 28px;
            border: none;
            background: rgba(255, 255, 255, 0.9);
            color: #5B5FC7;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: all 0.2s ease;
            backdrop-filter: blur(4px);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }
        .imp-detail-message.client:hover .imp-split-btn {
            opacity: 1;
        }
        .imp-split-btn:hover {
            background: #EDEAF7;
            color: #4F46E5;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }
        .imp-split-btn svg {
            width: 14px;
            height: 14px;
        }

        /* Split Drawer */
        .imp-split-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.3);
            z-index: 1100;
            backdrop-filter: blur(2px);
        }
        .imp-split-drawer {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: 440px;
            max-width: 92vw;
            background: #FFFFFF;
            z-index: 1101;
            box-shadow: -8px 0 32px rgba(0, 0, 0, 0.12), -2px 0 8px rgba(0, 0, 0, 0.04);
            display: flex;
            flex-direction: column;
            transform: translateX(100%);
            transition: transform 0.3s cubic-bezier(0.32, 0.72, 0, 1);
        }
        .imp-split-drawer.open {
            transform: translateX(0);
        }
        .imp-split-drawer-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            border-bottom: 1px solid #E5E7EB;
            flex-shrink: 0;
        }
        .imp-split-drawer-title {
            font-size: 15px;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .imp-split-drawer-title i {
            color: #3B82F6;
        }
        .imp-split-drawer-close {
            width: 32px;
            height: 32px;
            border: none;
            background: #F1F5F9;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748B;
            font-size: 14px;
        }
        .imp-split-drawer-close:hover {
            background: #E2E8F0;
        }
        .imp-split-drawer-body {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }
        .imp-split-section {
            margin-bottom: 18px;
        }
        .imp-split-label {
            font-size: 12px;
            font-weight: 600;
            color: #6B7280;
            margin-bottom: 6px;
            display: block;
        }
        .imp-split-input {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            font-size: 13px;
            color: #374151;
            font-family: inherit;
        }
        .imp-split-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 2px rgba(102,126,234,0.12);
        }
        .imp-split-select {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            font-size: 13px;
            color: #374151;
            background: #fff;
            -webkit-appearance: menulist;
            -moz-appearance: menulist;
            appearance: menulist;
        }
        .imp-split-select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 2px rgba(102,126,234,0.12);
        }
        .imp-split-preview {
            padding: 10px 12px;
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            font-size: 12px;
            color: #6B7280;
            line-height: 1.5;
            max-height: 100px;
            overflow-y: auto;
        }
        .imp-split-drawer-footer {
            padding: 16px 20px;
            border-top: 1px solid #E5E7EB;
            flex-shrink: 0;
            display: flex;
            gap: 8px;
        }
        .imp-split-cancel-btn {
            padding: 9px 18px;
            border: 1px solid #E5E7EB;
            background: #fff;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #6B7280;
            cursor: pointer;
        }
        .imp-split-cancel-btn:hover {
            background: #F9FAFB;
        }
        .imp-split-submit-btn {
            flex: 1;
            padding: 9px 18px;
            border: none;
            background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
            color: #fff;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.2s;
        }
        .imp-split-submit-btn:hover {
            box-shadow: 0 4px 12px rgba(37,99,235,0.3);
        }

        /* Reply Box */
        .imp-detail-reply-box {
            border-top: 1px solid #E2E5EB;
            padding: 14px 20px;
            background: #FFFFFF;
            flex-shrink: 0;
            box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.03);
            border-radius: 0 0 12px 12px;
        }

        /* Collapsed reply pill (mirrors customer cit-reply-collapsed) */
        .imp-reply-collapsed {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            padding: 12px 20px;
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
        .imp-reply-collapsed:hover {
            background: linear-gradient(180deg, #F8F6FF 0%, #FFFFFF 100%);
            box-shadow: 0 -3px 12px rgba(99,102,241,0.06);
        }
        .imp-reply-collapsed:focus-visible {
            outline: none;
            background: #F4F0FF;
            box-shadow: 0 -2px 8px rgba(107,91,203,0.12);
        }
        .imp-reply-collapsed-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #EDEAF7, #DDD6FE);
            color: #6B5BCB;
            font-size: 12px;
            font-weight: 700;
            flex-shrink: 0;
            box-shadow: 0 1px 2px rgba(107,91,203,0.15);
        }
        .imp-reply-collapsed-prompt {
            flex: 1;
            font-size: 0.84rem;
            color: #94A3B8;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .imp-reply-collapsed:hover .imp-reply-collapsed-prompt {
            color: #64748B;
        }
        .imp-reply-collapsed-meta {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 8px;
            background: #F0EBFF;
            color: #6B5BCB;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 600;
            flex-shrink: 0;
        }
        .imp-reply-collapsed-icon {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #6B5BCB 0%, #4C3FA1 100%);
            color: #fff;
            border-radius: 999px;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(107,91,203,0.30);
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }
        .imp-reply-collapsed:hover .imp-reply-collapsed-icon {
            transform: translateX(2px) rotate(-8deg);
            box-shadow: 0 3px 10px rgba(107,91,203,0.40);
        }
        .imp-reply-minimize {
            margin-left: auto;
            color: #94A3B8;
        }
        .imp-reply-minimize:hover {
            background: #FEE2E2 !important;
            color: #DC2626 !important;
        }
        .imp-reply-chevron {
            width: 16px;
            height: 16px;
            color: #9CA3AF;
            transition: transform 0.2s;
        }
        .imp-reply-chevron.expanded {
            transform: rotate(180deg);
        }
        .imp-detail-reply-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .imp-detail-reply-label {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
        }
        .imp-detail-internal-toggle {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #6B7280;
            cursor: pointer;
        }
        .imp-detail-internal-toggle input {
            accent-color: #D97706;
        }
        .imp-detail-reply-textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            font-size: 13px;
            resize: none;
            font-family: inherit;
            transition: border-color 0.15s;
        }
        .imp-detail-reply-textarea:focus {
            outline: none;
            border-color: #7C3AED;
            box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.1);
        }
        .imp-detail-reply-textarea.internal {
            border-color: #FDE68A;
            background: #FFFBEB;
        }
        .imp-detail-reply-textarea.internal:focus {
            border-color: #D97706;
            box-shadow: 0 0 0 2px rgba(217, 119, 6, 0.1);
        }

        /* Reply email fields */
        .imp-detail-reply-email-fields {
            padding: 0 0 8px 0;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .imp-detail-reply-field-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .imp-detail-reply-field-row label {
            font-size: 12px;
            font-weight: 500;
            color: #6B7280;
            width: 90px;
            flex-shrink: 0;
        }
        .imp-detail-reply-field-row input {
            flex: 1;
            padding: 6px 10px;
            border: 1px solid #E5E7EB;
            border-radius: 6px;
            font-size: 13px;
            background: #F9FAFB;
        }
        .imp-detail-reply-field-row input:focus {
            outline: none;
            border-color: #5B5FC7;
            box-shadow: 0 0 0 2px rgba(91, 95, 199, 0.1);
        }
        .imp-detail-reply-field-row input[readonly] {
            color: #6B7280;
            cursor: default;
        }
        .imp-ccbcc-toggle {
            background: none;
            border: none;
            color: #5B5FC7;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            padding: 2px 6px;
            border-radius: 4px;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .imp-ccbcc-toggle:hover {
            background: #F0EDFA;
        }
        .imp-detail-reply-field-row select {
            flex: 1;
            padding: 6px 10px;
            border: 1px solid #E5E7EB;
            border-radius: 6px;
            font-size: 13px;
            background: #F9FAFB;
            cursor: pointer;
        }
        .imp-detail-reply-field-row select:focus {
            outline: none;
            border-color: #5B5FC7;
            box-shadow: 0 0 0 2px rgba(91, 95, 199, 0.1);
        }

        /* Reply rich text editor */
        .imp-detail-reply-toolbar {
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 7px 12px;
            background: #F6F7FA;
            border: 1px solid #E2E5EB;
            border-radius: 10px 10px 0 0;
        }
        .imp-detail-reply-toolbar button {
            padding: 5px;
            border: none;
            background: none;
            border-radius: 6px;
            cursor: pointer;
            color: #64748B;
            transition: all 0.15s ease;
        }
        .imp-detail-reply-toolbar button:hover {
            background: #E2E5EB;
            color: #1A1D26;
        }
        .imp-detail-reply-toolbar button.imp-toolbar-active {
            background: #E0DEF5;
            color: #5B5FC7;
        }
        .imp-detail-reply-toolbar .imp-toolbar-divider {
            width: 1px;
            height: 18px;
            background: #E2E5EB;
            margin: 0 3px;
        }
        .imp-detail-reply-editor[contenteditable] {
            width: 100%;
            padding: 12px;
            border: 1px solid #E2E5EB;
            border-top: none;
            border-radius: 0 0 10px 10px;
            font-size: 13.5px;
            font-family: inherit;
            min-height: 120px;
            max-height: 250px;
            overflow-y: auto;
            line-height: 1.65;
            color: #334155;
            cursor: text;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .imp-detail-reply-editor[contenteditable]:focus {
            border-color: #5B5FC7;
            box-shadow: 0 0 0 3px rgba(91, 95, 199, 0.08);
        }
        .imp-detail-reply-editor[contenteditable]:empty:before {
            content: attr(data-placeholder);
            color: #94A3B8;
            pointer-events: none;
        }
        .imp-detail-reply-editor[contenteditable] p {
            margin: 0 0 8px 0;
        }
        .imp-detail-reply-editor[contenteditable] a {
            color: #5B5FC7;
            text-decoration: underline;
        }
        .imp-detail-reply-editor.internal {
            border-color: #E5C76B;
            background: #FEFCF5;
        }
        .imp-detail-reply-editor.internal:focus {
            border-color: #D97706;
            box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.08);
        }
        .imp-detail-reply-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            margin-top: 8px;
        }
        .imp-detail-send-btn {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 9px 20px;
            background: #5B5FC7;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(91, 95, 199, 0.3);
        }
        .imp-detail-send-btn:hover {
            background: #4F46E5;
            box-shadow: 0 4px 12px rgba(91, 95, 199, 0.35);
            transform: translateY(-1px);
        }
        .imp-detail-send-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        .imp-detail-send-btn.internal {
            background: #B45309;
            box-shadow: 0 1px 3px rgba(180, 83, 9, 0.3);
        }
        .imp-detail-send-btn.internal:hover {
            background: #92400E;
            box-shadow: 0 4px 12px rgba(180, 83, 9, 0.35);
        }

        /* Custom Scrollbars */
        .imp-detail-thread::-webkit-scrollbar {
            width: 6px;
        }
        .imp-detail-thread::-webkit-scrollbar-track {
            background: transparent;
        }
        .imp-detail-thread::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 3px;
        }
        .imp-detail-thread::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }
        .imp-detail-sidebar::-webkit-scrollbar {
            width: 4px;
        }
        .imp-detail-sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        .imp-detail-sidebar::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 2px;
        }

        /* Reduced motion */
        @media (prefers-reduced-motion: reduce) {
            .imp-detail-message,
            .imp-detail-card,
            .imp-detail-send-btn,
            .imp-split-drawer,
            .imp-back-btn {
                transition: none !important;
            }
        }
    </style>

    @if(!$showTicketDetail || !$selectedTicket)
    <div class="imp-dashboard-wrapper">
        <!-- Header -->
        <div class="imp-dashboard-header">
            <div class="imp-header-left">
                <h1>Thread - Customer & Project</h1>
                <p>Monitor and manage implementer support tickets</p>
            </div>
            <div class="imp-header-right">
                <div class="imp-search-box">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <input type="text"
                           wire:model.live.debounce.300ms="searchQuery"
                           placeholder="Search tickets...">
                </div>

                {{-- Searchable Implementer Filter --}}
                <div class="imp-searchable-filter" x-data="{
                    open: false,
                    search: '',
                    selectedId: @entangle('selectedImplementer').live,
                    items: {{ Js::from($implementers->map(fn($name, $id) => ['id' => $id, 'name' => $name])->values()) }},
                    get selectedName() {
                        if (!this.selectedId) return '';
                        const found = this.items.find(i => i.id == this.selectedId);
                        return found ? found.name : '';
                    },
                    get filtered() {
                        if (!this.search) return this.items;
                        const s = this.search.toLowerCase();
                        return this.items.filter(i => i.name.toLowerCase().includes(s));
                    },
                    select(id) {
                        this.selectedId = id;
                        this.search = '';
                        this.open = false;
                    },
                    clear() {
                        this.selectedId = '';
                        this.search = '';
                        this.open = false;
                    }
                }" @click.away="open = false; search = ''">
                    <div class="imp-searchable-trigger" :class="{ 'open': open }" @click="open = true; $nextTick(() => $refs.impSearch.focus())">
                        <svg class="imp-searchable-chevron" :class="{ 'open': open }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                        <input type="text" x-ref="impSearch"
                               x-show="open" x-model="search"
                               placeholder="Search implementers..."
                               @keydown.escape="open = false; search = ''">
                        <span x-show="!open" x-text="selectedName || 'All Implementers'" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"></span>
                        <template x-if="selectedId">
                            <button class="imp-searchable-clear" @click.stop="clear()" title="Clear filter">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width: 14px; height: 14px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </template>
                    </div>
                    <div class="imp-searchable-dropdown" x-show="open" x-cloak x-transition>
                        <div class="imp-searchable-option" :class="{ 'active': !selectedId }" @click="clear()">All Implementers</div>
                        <template x-for="item in filtered" :key="item.id">
                            <div class="imp-searchable-option" :class="{ 'active': selectedId == item.id }" @click="select(item.id)" x-text="item.name"></div>
                        </template>
                        <div class="imp-searchable-empty" x-show="filtered.length === 0">No matches found</div>
                    </div>
                </div>

                {{-- Searchable Company Filter --}}
                <div class="imp-searchable-filter" x-data="{
                    open: false,
                    search: '',
                    selectedId: @entangle('selectedCompany').live,
                    items: {{ Js::from($companies->map(fn($name, $id) => ['id' => $id, 'name' => $name])->values()) }},
                    get selectedName() {
                        if (!this.selectedId) return '';
                        const found = this.items.find(i => i.id == this.selectedId);
                        return found ? found.name : '';
                    },
                    get filtered() {
                        if (!this.search) return this.items;
                        const s = this.search.toLowerCase();
                        return this.items.filter(i => i.name.toLowerCase().includes(s));
                    },
                    select(id) {
                        this.selectedId = id;
                        this.search = '';
                        this.open = false;
                    },
                    clear() {
                        this.selectedId = '';
                        this.search = '';
                        this.open = false;
                    }
                }" @click.away="open = false; search = ''">
                    <div class="imp-searchable-trigger" :class="{ 'open': open }" @click="open = true; $nextTick(() => $refs.compSearch.focus())">
                        <svg class="imp-searchable-chevron" :class="{ 'open': open }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                        <input type="text" x-ref="compSearch"
                               x-show="open" x-model="search"
                               placeholder="Search companies..."
                               @keydown.escape="open = false; search = ''">
                        <span x-show="!open" x-text="selectedName || 'All Companies'" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"></span>
                        <template x-if="selectedId">
                            <button class="imp-searchable-clear" @click.stop="clear()" title="Clear filter">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width: 14px; height: 14px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </template>
                    </div>
                    <div class="imp-searchable-dropdown" x-show="open" x-cloak x-transition>
                        <div class="imp-searchable-option" :class="{ 'active': !selectedId }" @click="clear()">All Companies</div>
                        <template x-for="item in filtered" :key="item.id">
                            <div class="imp-searchable-option" :class="{ 'active': selectedId == item.id }" @click="select(item.id)" x-text="item.name"></div>
                        </template>
                        <div class="imp-searchable-empty" x-show="filtered.length === 0">No matches found</div>
                    </div>
                </div>

                <button class="imp-sla-policy-btn" wire:click="$set('showSlaPolicy', true)">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    SLA Policy
                </button>

                <button class="imp-create-btn" wire:click="openCreateDrawer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 18px; height: 18px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Create New Ticket
                </button>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="imp-stat-cards">
            <div class="imp-stat-card purple {{ $this->statusFilter === 'open' ? 'active' : '' }}"
                 wire:click="filterByStatus('open')">
                <div class="imp-stat-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 22px; height: 22px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5V6.75A2.25 2.25 0 014.5 4.5h15a2.25 2.25 0 012.25 2.25v6.75m-19.5 0v4.5A2.25 2.25 0 004.5 20.25h15a2.25 2.25 0 002.25-2.25v-4.5" />
                    </svg>
                </div>
                <div class="imp-stat-info">
                    <div class="imp-stat-number">{{ $openCount }}</div>
                    <div class="imp-stat-label">Open Tickets</div>
                </div>
            </div>

            <div class="imp-stat-card blue {{ $this->statusFilter === 'pending_support' ? 'active' : '' }}"
                 wire:click="filterByStatus('pending_support')">
                <div class="imp-stat-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 22px; height: 22px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17l-5.2-3.01A2.25 2.25 0 014.5 10.08V6.75A2.25 2.25 0 016.75 4.5h10.5a2.25 2.25 0 012.25 2.25v3.33a2.25 2.25 0 01-1.72 2.08l-5.2 3.01a2.25 2.25 0 01-2.16 0z" />
                    </svg>
                </div>
                <div class="imp-stat-info">
                    <div class="imp-stat-number">{{ $pendingSupportCount }}</div>
                    <div class="imp-stat-label">Pending Implementer</div>
                </div>
            </div>

            <div class="imp-stat-card magenta {{ $this->statusFilter === 'pending_rnd' ? 'active' : '' }}"
                 wire:click="filterByStatus('pending_rnd')">
                <div class="imp-stat-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 22px; height: 22px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" />
                    </svg>
                </div>
                <div class="imp-stat-info">
                    <div class="imp-stat-number">{{ $pendingRndCount }}</div>
                    <div class="imp-stat-label">Pending R&D</div>
                </div>
            </div>

            <div class="imp-stat-card red {{ $this->statusFilter === 'overdue' ? 'active' : '' }}"
                 wire:click="filterByStatus('overdue')">
                <div class="imp-stat-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 22px; height: 22px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
                <div class="imp-stat-info">
                    <div class="imp-stat-number">{{ $overdueCount }}</div>
                    <div class="imp-stat-label">Overdue Tickets</div>
                </div>
            </div>
        </div>

        <!-- SLA Performance -->
        <div class="imp-sla-card">
            <div class="imp-sla-title">SLA Performance</div>
            <div class="imp-sla-metrics">
                <div class="imp-sla-metric">
                    <div class="imp-sla-metric-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="imp-sla-metric-value">{{ $complianceRate }}<span class="imp-sla-metric-unit">%</span></div>
                        <div class="imp-sla-metric-label">Compliance Rate</div>
                    </div>
                </div>

                <div class="imp-sla-metric">
                    <div class="imp-sla-metric-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="imp-sla-metric-value">{{ $avgResolutionHours }}<span class="imp-sla-metric-unit"> hrs</span></div>
                        <div class="imp-sla-metric-label">Avg Resolution Time</div>
                    </div>
                </div>

                <div class="imp-sla-metric">
                    <div class="imp-sla-metric-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                        </svg>
                    </div>
                    <div>
                        <div class="imp-sla-metric-value">{{ $firstResponseRate }}<span class="imp-sla-metric-unit">%</span></div>
                        <div class="imp-sla-metric-label">First Response Rate</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="imp-tabs">
            <button class="imp-tab {{ $activeTab === 'pending_client' ? 'active' : '' }}"
                    wire:click="switchTab('pending_client')">
                Pending Client Actions
                <span class="imp-tab-badge">{{ $pendingClientCount }}</span>
            </button>
            <button class="imp-tab {{ $activeTab === 'all' ? 'active' : '' }}"
                    wire:click="switchTab('all')">
                All Tickets
                <span class="imp-tab-badge">{{ $allTicketsCount }}</span>
            </button>
        </div>

        <!-- Ticket Table -->
        <div class="imp-table-wrapper">
            @if(count($tickets) > 0)
                <table class="imp-table">
                    <thead>
                        <tr>
                            <th>TICKET ID</th>
                            <th>COMPANY NAME</th>
                            <th>CATEGORY</th>
                            <th>IMPLEMENTER</th>
                            <th>SLA STATUS</th>
                            <th>STATUS</th>
                            <th>TIME REMAINING</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                            <tr wire:click="openTicketDetail({{ $ticket->id }})" class="imp-clickable-row">
                                <td>
                                    <div class="imp-ticket-id-wrap" data-tooltip="{{ $ticket->subject }}">
                                        <span class="imp-ticket-id">{{ $ticket->formatted_ticket_number }}</span>
                                    </div>
                                    @if($ticket->priority)
                                        <span class="imp-priority-badge {{ strtolower($ticket->priority) }}">
                                            {{ ucfirst($ticket->priority) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($ticket->customer_id)
                                        <a href="{{ url('/admin/implementer-client-profile/' . $ticket->customer_id) }}"
                                           class="imp-company-link"
                                           wire:click.stop>
                                            {{ $ticket->customer?->company_name ?? $ticket->customer?->name ?? '-' }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $ticket->category ?? '-' }}</td>
                                <td>
                                    <div class="imp-implementer-cell">
                                        <div class="imp-avatar">
                                            {{ $ticket->implementerUser ? strtoupper(substr($ticket->implementerUser->name, 0, 2)) : '--' }}
                                        </div>
                                        <span>{{ $ticket->implementerUser?->name ?? $ticket->implementer_name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td>
                                    @php $slaStatus = $ticket->getSlaStatus(); @endphp
                                    <span class="imp-sla-badge {{ $slaStatus }}">
                                        @if($slaStatus === 'on_track') On Track
                                        @elseif($slaStatus === 'at_risk') At Risk
                                        @elseif($slaStatus === 'overdue') Overdue
                                        @else Resolved
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    @if($ticket->isMerged())
                                        <span class="imp-status-badge" style="background: #FEF3C7; color: #92400E; font-size: 11px;">
                                            Merged to {{ $ticket->mergedInto?->formatted_ticket_number ?? 'Unknown' }}
                                        </span>
                                    @else
                                        <span class="imp-status-badge {{ $ticket->status->value }}">
                                            {{ $ticket->status->label() }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @php $timeRemaining = $ticket->getTimeRemaining(); @endphp
                                    <span style="font-weight: 500; {{ str_contains($timeRemaining, 'Overdue') ? 'color: #DC2626;' : (str_contains($timeRemaining, 'Resolved') ? 'color: #6B7280;' : 'color: #374151;') }}">
                                        {{ $timeRemaining }}
                                    </span>
                                </td>
                                <td>
                                    <div class="imp-actions">
                                        <button wire:click.stop="openTicketDetail({{ $ticket->id }})"
                                           class="imp-action-btn"
                                           title="View Ticket">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                @if($totalPages > 1)
                    <div class="imp-pagination">
                        <div class="imp-pagination-info">
                            Showing {{ (($currentPage - 1) * $perPage) + 1 }} to {{ min($currentPage * $perPage, $totalFiltered) }} of {{ $totalFiltered }} tickets
                        </div>
                        <div class="imp-pagination-controls">
                            <button class="imp-page-btn"
                                    wire:click="previousPage"
                                    {{ $currentPage <= 1 ? 'disabled' : '' }}>
                                Previous
                            </button>

                            @php
                                $startPage = max(1, $currentPage - 2);
                                $endPage = min($totalPages, $currentPage + 2);
                                if ($endPage - $startPage < 4) {
                                    if ($startPage == 1) {
                                        $endPage = min($totalPages, $startPage + 4);
                                    } else {
                                        $startPage = max(1, $endPage - 4);
                                    }
                                }
                            @endphp

                            @if($startPage > 1)
                                <button class="imp-page-btn" wire:click="goToPage(1)">1</button>
                                @if($startPage > 2)
                                    <span style="color: #9CA3AF; padding: 0 4px;">...</span>
                                @endif
                            @endif

                            @for($i = $startPage; $i <= $endPage; $i++)
                                <button class="imp-page-btn {{ $i == $currentPage ? 'active' : '' }}"
                                        wire:click="goToPage({{ $i }})">
                                    {{ $i }}
                                </button>
                            @endfor

                            @if($endPage < $totalPages)
                                @if($endPage < $totalPages - 1)
                                    <span style="color: #9CA3AF; padding: 0 4px;">...</span>
                                @endif
                                <button class="imp-page-btn" wire:click="goToPage({{ $totalPages }})">{{ $totalPages }}</button>
                            @endif

                            <button class="imp-page-btn"
                                    wire:click="nextPage"
                                    {{ $currentPage >= $totalPages ? 'disabled' : '' }}>
                                Next
                            </button>
                        </div>
                    </div>
                @endif
            @else
                <div class="imp-empty-state">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="margin: 0 auto; display: block;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859M21.75 12.75V18A2.25 2.25 0 0119.5 20.25H4.5A2.25 2.25 0 012.25 18v-5.25" />
                    </svg>
                    <p>No tickets found matching your filters.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- SLA Policy Modal -->
    @if($showSlaPolicy)
        <div class="imp-modal-overlay" wire:click.self="$set('showSlaPolicy', false)">
            <div class="imp-modal">
                <div class="imp-modal-header">
                    <h2>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 20px; height: 20px; color: #7C3AED;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                        {{ $slaConfigMode ? 'SLA Configuration' : 'SLA Policy' }}
                    </h2>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <button class="imp-sla-gear-btn" wire:click="toggleSlaConfigMode" title="{{ $slaConfigMode ? 'Back to View' : 'Configure SLA' }}">
                            @if($slaConfigMode)
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 18px; height: 18px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 18px; height: 18px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            @endif
                        </button>
                        <button class="imp-modal-close" wire:click="$set('showSlaPolicy', false)">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 20px; height: 20px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="imp-modal-body">
                    @if(!$slaConfigMode)
                    <!-- ===== VIEW MODE ===== -->

                    <!-- Response Time Matrix -->
                    <div class="imp-modal-section">
                        <h3>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Response Time Matrix
                        </h3>
                        <table class="imp-sla-table">
                            <thead>
                                <tr>
                                    <th>First Response SLA</th>
                                    <th>Resolution SLA</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>{{ $slaConfig->first_response_sla_hours ?? 24 }}</strong> hours</td>
                                    <td><strong>{{ $slaConfig->resolution_sla_hours ?? 48 }}</strong> hours</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- First Reply Deadline -->
                    <div class="imp-modal-section">
                        <h3>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            First Reply Deadline
                            @if($slaConfig->first_reply_enabled ?? true)
                                <span class="imp-status-badge imp-badge-active">Active</span>
                            @else
                                <span class="imp-status-badge imp-badge-inactive">Disabled</span>
                            @endif
                        </h3>
                        <ul class="imp-policy-list">
                            <li>
                                <svg class="imp-bullet-blue" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="4"/></svg>
                                <span>Tickets created before <strong>{{ \Carbon\Carbon::createFromFormat('H:i', $slaConfig->first_reply_cutoff_time ?? '17:30')->format('g:i A') }}</strong> must receive a reply by <strong>11:59 PM</strong> same day</span>
                            </li>
                            <li>
                                <svg class="imp-bullet-red" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="4"/></svg>
                                <span>If no reply is detected, status automatically changes to <strong>Overdue</strong></span>
                            </li>
                        </ul>
                    </div>

                    <!-- Business Hours -->
                    <div class="imp-modal-section">
                        <h3>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 0h.008v.008h-.008V7.5z" />
                            </svg>
                            Business Hours
                        </h3>
                        <ul class="imp-policy-list">
                            <li>
                                <svg class="imp-bullet-blue" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="4"/></svg>
                                <span>Monday - Friday: <strong>{{ \Carbon\Carbon::createFromFormat('H:i', $slaConfig->business_start_time ?? '08:00')->format('g:i A') }} - {{ \Carbon\Carbon::createFromFormat('H:i', $slaConfig->business_end_time ?? '18:00')->format('g:i A') }} (MYT)</strong></span>
                            </li>
                            <li>
                                <svg class="imp-bullet-amber" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="4"/></svg>
                                <span>Weekends and public holidays are excluded from SLA calculations</span>
                            </li>
                            <li>
                                <svg class="imp-bullet-blue" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="4"/></svg>
                                <span>Tickets submitted after <strong>{{ \Carbon\Carbon::createFromFormat('H:i', $slaConfig->first_reply_cutoff_time ?? '17:30')->format('g:i A') }}</strong> will be processed the next business day</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Follow-up Automation -->
                    <div class="imp-modal-section">
                        <h3>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                            Follow-up Automation
                            @if($slaConfig->followup_enabled ?? true)
                                <span class="imp-status-badge imp-badge-active">Active</span>
                            @else
                                <span class="imp-status-badge imp-badge-inactive">Disabled</span>
                            @endif
                        </h3>
                        <ul class="imp-policy-list">
                            <li>
                                <svg class="imp-bullet-amber" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="4"/></svg>
                                <span>Auto follow-up reminder after <strong>{{ $slaConfig->followup_reminder_days ?? 3 }} working days</strong> of pending client status</span>
                            </li>
                            <li>
                                <svg class="imp-bullet-red" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="4"/></svg>
                                <span>Auto-close ticket after additional <strong>{{ $slaConfig->followup_auto_close_days ?? 2 }} working days</strong> with no client response</span>
                            </li>
                            <li>
                                <svg class="imp-bullet-green" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="4"/></svg>
                                <span>Holiday and weekend logic automatically applied to all SLA triggers</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Performance Targets -->
                    <div class="imp-modal-section">
                        <h3>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                            </svg>
                            Performance Targets
                        </h3>
                        <div class="imp-target-grid">
                            <div class="imp-target-card">
                                <div class="imp-target-value" style="color: #059669;">&ge;95%</div>
                                <div class="imp-target-label">SLA Compliance</div>
                            </div>
                            <div class="imp-target-card">
                                <div class="imp-target-value" style="color: #2563EB;">&ge;98%</div>
                                <div class="imp-target-label">First Response Rate</div>
                            </div>
                            <div class="imp-target-card">
                                <div class="imp-target-value" style="color: #D97706;">&ge;4.5/5</div>
                                <div class="imp-target-label">Customer Satisfaction</div>
                            </div>
                            <div class="imp-target-card">
                                <div class="imp-target-value" style="color: #7C3AED;">&lt;24h</div>
                                <div class="imp-target-label">Avg Resolution Time</div>
                            </div>
                        </div>
                    </div>

                    @else
                    <!-- ===== CONFIG MODE ===== -->
                    <div class="imp-sla-config-form">

                        <!-- Section 1: First Reply Deadline -->
                        <div class="imp-config-section">
                            <div class="imp-config-section-header">
                                <h3>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    First Reply Deadline
                                </h3>
                                <label class="imp-config-toggle">
                                    <input type="checkbox" wire:model.live="configFirstReplyEnabled">
                                    <span class="imp-toggle-slider"></span>
                                </label>
                            </div>
                            <p class="imp-config-desc">Tickets created before cutoff time must receive a reply by 11:59 PM same day, or status changes to Overdue.</p>
                            <div class="imp-config-field">
                                <label>Cutoff Time</label>
                                <input type="time" wire:model="configFirstReplyCutoff" class="imp-config-input">
                            </div>
                        </div>

                        <!-- Section 2: Follow-up Automation -->
                        <div class="imp-config-section">
                            <div class="imp-config-section-header">
                                <h3>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 003.7 3.7 48.656 48.656 0 007.324 0 4.006 4.006 0 003.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3l-3 3" />
                                    </svg>
                                    Follow-up Automation
                                </h3>
                                <label class="imp-config-toggle">
                                    <input type="checkbox" wire:model.live="configFollowupEnabled">
                                    <span class="imp-toggle-slider"></span>
                                </label>
                            </div>
                            <p class="imp-config-desc">Automatically manage tickets in "Pending Client" status with follow-up reminders and auto-close.</p>
                            <div class="imp-config-field-row">
                                <div class="imp-config-field">
                                    <label>Follow-up Reminder After</label>
                                    <div class="imp-config-input-group">
                                        <input type="number" wire:model="configFollowupReminderDays" min="1" max="30" class="imp-config-input">
                                        <span class="imp-config-unit">working days</span>
                                    </div>
                                </div>
                                <div class="imp-config-field">
                                    <label>Auto-close After Additional</label>
                                    <div class="imp-config-input-group">
                                        <input type="number" wire:model="configFollowupAutoCloseDays" min="1" max="30" class="imp-config-input">
                                        <span class="imp-config-unit">working days</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: SLA Timings -->
                        <div class="imp-config-section">
                            <div class="imp-config-section-header">
                                <h3>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                                    </svg>
                                    SLA Timings
                                </h3>
                            </div>
                            <p class="imp-config-desc">Configure the maximum allowed hours for ticket resolution and first response.</p>
                            <div class="imp-config-field-row">
                                <div class="imp-config-field">
                                    <label>Resolution SLA</label>
                                    <div class="imp-config-input-group">
                                        <input type="number" wire:model="configResolutionSlaHours" min="1" max="720" class="imp-config-input">
                                        <span class="imp-config-unit">hours</span>
                                    </div>
                                </div>
                                <div class="imp-config-field">
                                    <label>First Response SLA</label>
                                    <div class="imp-config-input-group">
                                        <input type="number" wire:model="configFirstResponseSlaHours" min="1" max="720" class="imp-config-input">
                                        <span class="imp-config-unit">hours</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 4: Business Hours -->
                        <div class="imp-config-section">
                            <div class="imp-config-section-header">
                                <h3>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 0h.008v.008h-.008V7.5z" />
                                    </svg>
                                    Business Hours
                                </h3>
                            </div>
                            <div class="imp-config-field-row">
                                <div class="imp-config-field">
                                    <label>Start Time</label>
                                    <input type="time" wire:model="configBusinessStart" class="imp-config-input">
                                </div>
                                <div class="imp-config-field">
                                    <label>End Time</label>
                                    <input type="time" wire:model="configBusinessEnd" class="imp-config-input">
                                </div>
                            </div>
                            <p class="imp-config-note">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px; flex-shrink: 0;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                </svg>
                                Weekends and public holidays are automatically excluded from all SLA calculations.
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="imp-config-actions">
                            <button class="imp-config-cancel-btn" wire:click="toggleSlaConfigMode">Cancel</button>
                            <button class="imp-config-save-btn" wire:click="saveSlaConfig">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                                Save Configuration
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Create Ticket Drawer -->
    <div
        x-data="{ open: @entangle('showCreateDrawer') }"
        x-effect="open ? document.body.classList.add('imp-drawer-open') : document.body.classList.remove('imp-drawer-open')"
        x-show="open"
        @click.self="open = false"
        @keydown.window.escape="open = false"
        class="fixed inset-0 z-[200] flex justify-end bg-black/40 backdrop-blur-sm transition-opacity duration-200"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="display: none;"
    >
        <div
            class="imp-drawer-modal"
            x-show="open"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            @click.stop
        >
            <!-- Header -->
            <div class="imp-drawer-header">
                <div>
                    <h2>Create New Ticket</h2>
                    <p>Create and send a support ticket to the client</p>
                </div>
                <button class="imp-drawer-close" wire:click="closeCreateDrawer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 20px; height: 20px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Scrollable Form -->
            <div class="imp-drawer-body">
                <!-- Row 1: Company & Category -->
                <div class="imp-drawer-section">
                    <div class="imp-drawer-row">
                        <!-- Company -->
                        <div class="imp-drawer-field">
                            <label>Company <span class="imp-required">*</span></label>
                            <div x-data="{ showList: false }" class="imp-drawer-search-select">
                                @if($this->newTicketCustomerId)
                                    @php $selectedCustomer = $customers->firstWhere('id', $this->newTicketCustomerId); @endphp
                                    <div class="imp-drawer-selected-company" @click="showList = true">
                                        <span>{{ $selectedCustomer?->company_name ?? 'Selected' }}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px; color: #9CA3AF;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </div>
                                @else
                                    <div class="imp-drawer-selected-company" @click="showList = true" style="color: #9CA3AF;">
                                        <span>Select company</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px; color: #9CA3AF;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </div>
                                @endif

                                <div x-show="showList" @click.away="showList = false" style="position: absolute; top: 100%; left: 0; right: 0; z-index: 30; margin-top: 4px; background: white; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); overflow: hidden;">
                                    <div style="padding: 8px; border-bottom: 1px solid #E5E7EB; background: #F9FAFB;">
                                        <input type="text"
                                               wire:model.live.debounce.300ms="customerSearch"
                                               placeholder="Search companies..."
                                               class="imp-drawer-search-input"
                                               @click.stop>
                                    </div>
                                    <div class="imp-drawer-option-list">
                                        @forelse($customers as $cust)
                                            <button type="button"
                                                    class="imp-drawer-option {{ $this->newTicketCustomerId == $cust->id ? 'selected' : '' }}"
                                                    wire:click="$set('newTicketCustomerId', '{{ $cust->id }}')"
                                                    @click="showList = false">
                                                {{ $cust->company_name }}
                                            </button>
                                        @empty
                                            <div style="padding: 12px; text-align: center; color: #9CA3AF; font-size: 13px;">No companies found</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                            @error('newTicketCustomerId') <div class="imp-drawer-error">{{ $message }}</div> @enderror
                        </div>

                        <!-- Category -->
                        <div class="imp-drawer-field">
                            <label>Category <span class="imp-required">*</span></label>
                            <select class="imp-drawer-select" wire:model.live="newTicketCategory">
                                <option value="">Select category</option>
                                <option value="Enhancement">Enhancement</option>
                                <option value="Paid Customization">Paid Customization</option>
                                <option value="Others Inquiry">Others Inquiry</option>
                                <option value="Add on License">Add on License</option>
                                <option value="Add on Module">Add on Module</option>
                                <option value="Add on Device">Add on Device</option>
                            </select>
                            @error('newTicketCategory') <div class="imp-drawer-error">{{ $message }}</div> @enderror
                            <div x-show="['Add on License','Add on Module','Add on Device'].includes($wire.newTicketCategory)" x-cloak class="imp-block-banner">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" y1="8"  x2="12" y2="12"/>
                                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                                </svg>
                                <span>Add-on requests should be handled by the Sales team. This ticket cannot be submitted.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fields below Category are locked when an Add-on category is selected -->
                <div class="imp-fields-stack"
                     :class="{ 'imp-fields-disabled': ['Add on License','Add on Module','Add on Device'].includes($wire.newTicketCategory) }"
                     x-effect="$el.toggleAttribute('inert', ['Add on License','Add on Module','Add on Device'].includes($wire.newTicketCategory))">

                <!-- Row 2: Module & Status -->
                <div class="imp-drawer-section">
                    <div class="imp-drawer-row">
                        <!-- Module -->
                        <div class="imp-drawer-field">
                            <label>Module <span class="imp-required">*</span></label>
                            <select class="imp-drawer-select" wire:model="newTicketModule">
                                <option value="">Select module</option>
                                <option value="Profile">Profile</option>
                                <option value="Attendance">Attendance</option>
                                <option value="Leave">Leave</option>
                                <option value="Claim">Claim</option>
                                <option value="Payroll">Payroll</option>
                            </select>
                            @error('newTicketModule') <div class="imp-drawer-error">{{ $message }}</div> @enderror
                        </div>

                        <!-- Status -->
                        <div class="imp-drawer-field">
                            <label>Status <span class="imp-required">*</span></label>
                            <select class="imp-drawer-select" wire:model="newTicketStatus">
                                <option value="open">Open</option>
                                <option value="pending_support">Pending Support</option>
                                <option value="pending_client">Pending Client</option>
                                <option value="pending_rnd">Pending R&D</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Email Template -->
                <div class="imp-drawer-section">
                    <div class="imp-drawer-field">
                        <label>
                            Email Template
                            <span class="imp-badge">Pre-fill subject & body</span>
                        </label>
                        <select class="imp-drawer-select" wire:model="newTicketEmailTemplate" wire:change="applyEmailTemplate($event.target.value)">
                            <option value="">No Template</option>
                            @foreach($this->emailTemplates as $tmpl)
                                <option value="{{ $tmpl->id }}">{{ $tmpl->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Email Subject -->
                <div class="imp-drawer-section">
                    <div class="imp-drawer-field">
                        <label>Email Subject <span class="imp-required">*</span></label>
                        <input type="text"
                               class="imp-drawer-input"
                               wire:model="newTicketEmailSubject"
                               style="text-transform: uppercase;"
                               @input="const s=$event.target.selectionStart; $event.target.value=$event.target.value.toUpperCase(); $event.target.setSelectionRange(s,s);"
                               placeholder="Enter email subject line">
                        @error('newTicketEmailSubject') <div class="imp-drawer-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Email Body (Rich Text Editor) -->
                <div class="imp-drawer-section imp-drawer-section--grow">
                    <div class="imp-drawer-field"
                         x-data="{
                            editorDragOver: false,
                            exec(command, value = null) {
                                document.execCommand(command, false, value);
                                this.$refs.editor.focus();
                            },
                            isActive(command) {
                                return document.queryCommandState(command);
                            },
                            insertLink() {
                                const url = prompt('Enter URL:');
                                if (url) this.exec('createLink', url);
                            },
                            handlePaste(e) {
                                e.preventDefault();
                                const html = e.clipboardData.getData('text/html');
                                const text = e.clipboardData.getData('text/plain');
                                document.execCommand('insertHTML', false, html || text);
                            },
                            handleAttachmentDrop(fileList) {
                                const files = Array.from(fileList || []);
                                if (!files.length) return;
                                const allowedExts = ['doc','docx','xls','xlsx','pdf','png','jpg','jpeg'];
                                const maxBytes = 10 * 1024 * 1024;
                                const valid = [], errors = [];
                                files.forEach(f => {
                                    const ext = (f.name.split('.').pop() || '').toLowerCase();
                                    if (!allowedExts.includes(ext))   errors.push(`&quot;${f.name}&quot; — unsupported file type`);
                                    else if (f.size > maxBytes)       errors.push(`&quot;${f.name}&quot; — exceeds 10MB`);
                                    else                              valid.push(f);
                                });
                                if (errors.length) alert(errors.join('\n'));
                                if (!valid.length) return;
                                this.$wire.uploadMultiple('ticketAttachments', valid,
                                    () => {},
                                    () => alert('File upload failed.'));
                            }
                         }"
                         x-init="
                            $wire.on('templateApplied', () => {
                                $nextTick(() => { $refs.editor.innerHTML = $wire.newTicketEmailBody || ''; });
                            });
                            $wire.on('drawerReset', () => {
                                $refs.editor.innerHTML = '';
                            });
                            window.addEventListener('dragover', e => e.preventDefault());
                            window.addEventListener('drop', e => {
                                if (!e.target.closest('.imp-editor-zone')) e.preventDefault();
                            });
                         "
                    >
                        <label>Email Detail / Body <span class="imp-required">*</span></label>
                        <div class="imp-drawer-toolbar">
                            <button type="button" title="Bold" @mousedown.prevent="exec('bold')" :class="{ 'imp-toolbar-active': isActive('bold') }">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3.744h-.753v8.25h7.125a4.125 4.125 0 000-8.25H6.75zm0 0v8.25m0 0h7.875a4.875 4.875 0 010 9.75H6.75v-9.75z" /></svg>
                            </button>
                            <button type="button" title="Italic" @mousedown.prevent="exec('italic')" :class="{ 'imp-toolbar-active': isActive('italic') }">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M5.248 20.246H9.05m0 0h3.696m-3.696 0l5.893-16.502m0 0H11.25m3.696 0h3.803" /></svg>
                            </button>
                            <button type="button" title="Link" @mousedown.prevent="insertLink()">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71" /><path stroke-linecap="round" stroke-linejoin="round" d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71" /></svg>
                            </button>
                            <div class="imp-toolbar-divider"></div>
                            <button type="button" title="Attach" @mousedown.prevent="document.getElementById('imp-file-upload').click()">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" /></svg>
                            </button>
                        </div>
                        <div class="imp-editor-zone"
                             :class="{ 'imp-editor-zone--dragover': editorDragOver }"
                             @dragenter.prevent="editorDragOver = true"
                             @dragover.prevent
                             @dragleave.prevent="if (!$el.contains($event.relatedTarget)) editorDragOver = false"
                             @drop.prevent="editorDragOver = false; handleAttachmentDrop($event.dataTransfer.files)">
                            <div wire:ignore class="imp-editor-zone__inner">
                                <div class="imp-drawer-textarea"
                                     contenteditable="true"
                                     x-ref="editor"
                                     @paste="handlePaste($event)"
                                     data-placeholder="Type your email message here..."></div>
                            </div>
                            <div x-show="editorDragOver" x-cloak class="imp-editor-drop-overlay">
                                <span>Drop files to attach</span>
                            </div>
                        </div>

                        <input type="file"
                               id="imp-file-upload"
                               wire:model="ticketAttachments"
                               multiple
                               accept=".doc,.docx,.xls,.xlsx,.pdf,.png,.jpg,.jpeg"
                               style="display: none;">

                        <div class="imp-editor-hint">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/>
                            </svg>
                            <span>Drag files into the description (or <a href="#" @click.prevent="document.getElementById('imp-file-upload').click()">click to browse</a>). Word, Excel, PDF, Images &middot; max 10MB each.</span>
                        </div>

                        @if(!empty($ticketAttachments))
                            <div class="imp-drawer-file-list">
                                @foreach($ticketAttachments as $index => $file)
                                    <div class="imp-drawer-file-item">
                                        <div class="imp-drawer-file-info">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px; color: #9CA3AF;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" />
                                            </svg>
                                            <span>{{ $file->getClientOriginalName() }}</span>
                                            <span class="imp-drawer-file-size">({{ number_format($file->getSize() / 1024, 1) }} KB)</span>
                                        </div>
                                        <button type="button" class="imp-drawer-file-remove" wire:click="removeAttachment({{ $index }})">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @error('newTicketEmailBody') <div class="imp-drawer-error">{{ $message }}</div> @enderror
                        @error('ticketAttachments.*') <div class="imp-drawer-error">{{ $message }}</div> @enderror
                    </div>
                </div>
                </div>{{-- /imp-fields-disabled wrapper --}}
            </div>

            <!-- Footer -->
            <div class="imp-drawer-footer">
                <button type="button" class="imp-drawer-cancel" wire:click="closeCreateDrawer">Cancel</button>
                <button type="button" class="imp-drawer-submit"
                        @click="if (['Add on License','Add on Module','Add on Device'].includes($wire.newTicketCategory)) return; const editor = document.querySelector('[x-ref=editor]'); if (editor) @this.set('newTicketEmailBody', editor.innerHTML); $nextTick(() => { $wire.createTicket(); });"
                        wire:loading.attr="disabled"
                        :disabled="['Add on License','Add on Module','Add on Device'].includes($wire.newTicketCategory)"
                        :class="{ 'imp-drawer-submit-disabled': ['Add on License','Add on Module','Add on Device'].includes($wire.newTicketCategory) }">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                    </svg>
                    <span wire:loading.remove wire:target="createTicket">Submit & Send Email</span>
                    <span wire:loading wire:target="createTicket">Submitting...</span>
                </button>
            </div>
        </div>
    </div>
    @else

    <!-- Ticket Detail Full Page -->
    <div class="imp-fullpage-detail"
         x-data="{}"
         x-init="document.body.classList.add('imp-detail-fullscreen')"
         x-destroy="document.body.classList.remove('imp-detail-fullscreen')">
        <!-- Back Button -->
        @php
            $backLabel = 'Back to Tickets';
            if ($returnUrl) {
                if (str_contains($returnUrl, '/leads/')) {
                    $backLabel = 'Back to Thread';
                } elseif (str_contains($returnUrl, '/implementer-client-profile/')) {
                    $backLabel = 'Back to Client Profile';
                } else {
                    $backLabel = 'Back';
                }
            }
        @endphp
        <div class="imp-back-row">
            <button wire:click="closeTicketDetail" class="imp-back-btn">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                {{ $backLabel }}
            </button>
        </div>

        <!-- Header -->
        <div class="imp-detail-header">
            <div class="imp-detail-header-top">
                <div class="imp-detail-header-info">
                    <h2 class="imp-detail-title">
                        {{ $selectedTicket->subject ?? 'No Subject' }}
                    </h2>
                    <span class="imp-detail-ticket-id">{{ $selectedTicket->formatted_ticket_number }} - {{ $selectedTicket->category ?? '' }}</span>
                </div>
                @if($selectedTicket->isMerged())
                    <a href="javascript:void(0)" wire:click="openTicketDetail({{ $selectedTicket->merged_into_ticket_id }})" class="imp-merged-badge-link" style="margin-left: auto; flex-shrink: 0;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m9.86-2.07a4.5 4.5 0 00-1.242-7.244l-4.5-4.5a4.5 4.5 0 00-6.364 6.364L4.34 8.374" />
                        </svg>
                        Merged into {{ $selectedTicket->mergedInto?->formatted_ticket_number }}
                    </a>
                @else
                    <button wire:click="openMergeDrawer" class="imp-merge-btn" style="margin-left: auto; flex-shrink: 0;" title="Merge into another ticket">
                        Merge Ticket
                    </button>
                @endif
            </div>
                    <div class="imp-detail-header-meta">
                        @php $detailSlaStatus = $selectedTicket->getSlaStatus(); @endphp
                        <span class="imp-detail-sla-badge {{ $detailSlaStatus }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $selectedTicket->getTimeRemaining() }}
                        </span>
                        <span class="imp-detail-company-name">{{ $selectedTicket->customer?->company_name ?? '-' }}</span>
                    </div>
                </div>

                <!-- Main Content - Split View -->
                <div class="imp-detail-content">
                    <!-- Left Sidebar -->
                    <div class="imp-detail-sidebar">
                        <h3 class="imp-detail-sidebar-title">Ticket Properties</h3>

                        <!-- People -->
                        <div class="imp-detail-section">
                            <label class="imp-detail-label">PEOPLE</label>
                            <div class="imp-detail-card">
                                <div class="imp-detail-people-grid">
                                    <div class="imp-detail-person">
                                        <div class="imp-detail-avatar blue">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 13px; height: 13px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="imp-detail-name">{{ $selectedTicket->customer?->name ?? 'Unknown' }}</p>
                                            <p class="imp-detail-sublabel">Client</p>
                                            @if($selectedTicket->customer?->email)
                                                <div class="imp-detail-email-row">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 12px; height: 12px;">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                                    </svg>
                                                    <span>{{ $selectedTicket->customer->email }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="imp-detail-person">
                                        <div class="imp-detail-avatar purple">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 13px; height: 13px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="imp-detail-name">{{ $selectedTicket->implementerUser?->name ?? $selectedTicket->implementer_name ?? 'Unassigned' }}</p>
                                            <p class="imp-detail-sublabel">Implementer</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="imp-detail-section" x-data="{ showStatusDrop: false }">
                            <label class="imp-detail-label">STATUS</label>
                            <div class="imp-detail-status-wrapper">
                                <button @click="showStatusDrop = !showStatusDrop" class="imp-detail-status-btn">
                                    <span class="imp-status-badge {{ $selectedTicket->status->value }}">
                                        {{ $selectedTicket->status->label() }}
                                    </span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px; color: #6B7280;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                                <div x-show="showStatusDrop" @click.away="showStatusDrop = false" class="imp-detail-status-dropdown" x-cloak>
                                    @foreach(['open', 'pending_support', 'pending_client', 'pending_rnd', 'closed'] as $statusVal)
                                        <button wire:click="changeTicketStatus('{{ $statusVal }}')"
                                                @click="showStatusDrop = false"
                                                class="imp-detail-status-option {{ $selectedTicket->status->value === $statusVal ? 'active' : '' }}">
                                            {{ \App\Enums\ImplementerTicketStatus::from($statusVal)->label() }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Key Dates -->
                        <div class="imp-detail-section">
                            <label class="imp-detail-label">KEY DATES</label>
                            <div class="imp-detail-dates-card">
                                <div class="imp-detail-date-item">
                                    <div class="imp-detail-date-label">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 12px; height: 12px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                        </svg>
                                        Created
                                    </div>
                                    <p class="imp-detail-date-value">{{ $selectedTicket->created_at->format('M d, Y') }}</p>
                                </div>
                                <div class="imp-detail-date-item">
                                    <div class="imp-detail-date-label">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 12px; height: 12px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        SLA Deadline
                                    </div>
                                    <p class="imp-detail-date-value">{{ $selectedTicket->getSlaDeadline()->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Details -->
                        <div class="imp-detail-section">
                            <label class="imp-detail-label">DETAILS</label>
                            <div class="imp-detail-props-card">
                                <div class="imp-detail-prop-row">
                                    <span class="imp-detail-prop-label">Priority:</span>
                                    <span class="imp-detail-prop-value priority-{{ strtolower($selectedTicket->priority ?? 'medium') }}">
                                        {{ ucfirst($selectedTicket->priority ?? 'Medium') }}
                                    </span>
                                </div>
                                <div class="imp-detail-prop-row">
                                    <span class="imp-detail-prop-label">Category:</span>
                                    <span class="imp-detail-prop-value">{{ $selectedTicket->category ?? '-' }}</span>
                                </div>
                                <div class="imp-detail-prop-row">
                                    <span class="imp-detail-prop-label">Module:</span>
                                    <span class="imp-detail-prop-value">{{ $selectedTicket->module ?? '-' }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Status History --}}
                        <div class="imp-detail-section">
                            <label class="imp-detail-label">STATUS HISTORY</label>
                            <div style="max-height: 180px; overflow-y: auto;">
                                @php
                                    $statusLogs = \Spatie\Activitylog\Models\Activity::where('subject_type', 'App\\Models\\ImplementerTicket')
                                        ->where('subject_id', $selectedTicket->id)
                                        ->where('log_name', 'implementer_ticket')
                                        ->orderBy('created_at', 'desc')
                                        ->limit(20)
                                        ->get();
                                @endphp
                                @forelse($statusLogs as $log)
                                    <div style="padding: 6px 0; border-bottom: 1px solid #f1f5f9; font-size: 12px;">
                                        <div style="color: #334155; font-weight: 500;">{{ $log->description }}</div>
                                        <div style="color: #94a3b8; font-size: 11px; margin-top: 2px;">
                                            {{ $log->causer?->name ?? 'Customer' }}
                                            · {{ $log->created_at->format('M d, Y h:i A') }}
                                            @if(in_array($log->properties['trigger'] ?? '', ['customer_reply', 'customer_reopen']))
                                                <span style="color: #667eea; font-weight: 600;">(Auto)</span>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div style="color: #94a3b8; font-size: 12px; font-style: italic; padding: 8px 0;">No status changes recorded</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Right Panel - Thread & Reply -->
                    <div class="imp-detail-thread-panel" x-data="{ threadSearch: '' }">
                        <!-- Thread Search Bar -->
                        <div class="imp-thread-search">
                            <i class="bi bi-search"></i>
                            <input type="text" x-model="threadSearch" placeholder="Search conversation...">
                            <button class="imp-thread-search-clear" x-show="threadSearch" x-cloak @click="threadSearch = ''">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>

                        <!-- Conversation Thread -->
                        <div class="imp-detail-thread">
                            @if($selectedTicket->replies->count() > 0)
                                @foreach($selectedTicket->replies as $reply)
                                    @if($reply->is_internal_note)
                                        <!-- Internal Note -->
                                        <div class="imp-detail-message internal"
                                             x-bind:class="threadSearch && !$el.textContent.toLowerCase().includes(threadSearch.toLowerCase()) ? 'imp-msg-dimmed' : (threadSearch ? 'imp-msg-highlight' : '')"
                                             @if($reply->sender_type === 'App\\Models\\User' && $reply->sender_id === auth()->id())
                                             x-data="{ editing: false, editText: @js($reply->message), confirmDelete: false }"
                                             @endif
                                        >
                                            <div class="imp-detail-msg-header">
                                                <div class="imp-detail-msg-avatar yellow">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                                    </svg>
                                                </div>
                                                <div class="imp-detail-msg-info">
                                                    <span class="imp-detail-msg-name">{{ $reply->sender_name }}</span>
                                                    <span class="imp-detail-msg-badge yellow">Internal Only</span>
                                                    @if($reply->updated_at->gt($reply->created_at->addSecond()))
                                                        <span class="imp-detail-msg-time" style="display:inline; margin-top:0;">Edited - {{ $reply->updated_at->format('M d, g:i A') }}</span>
                                                    @else
                                                        <span class="imp-detail-msg-time" style="display:inline; margin-top:0;">{{ $reply->created_at->format('M d, g:i A') }}</span>
                                                    @endif
                                                </div>
                                                @if($reply->sender_type === 'App\\Models\\User' && $reply->sender_id === auth()->id())
                                                    <div class="imp-note-action-btns">
                                                        <button class="imp-note-edit-btn" @click="editing = !editing" title="Edit note">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px;">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                            </svg>
                                                        </button>
                                                        <button class="imp-note-delete-btn" @click="confirmDelete = true" x-show="!confirmDelete" title="Delete note">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px;">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                            </svg>
                                                        </button>
                                                        <div class="imp-note-delete-confirm" x-show="confirmDelete" x-cloak>
                                                            <span>Delete?</span>
                                                            <button class="imp-note-delete-yes" @click="$wire.deleteInternalNote({{ $reply->id }})">Yes</button>
                                                            <button class="imp-note-delete-no" @click="confirmDelete = false">No</button>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            @if($reply->sender_type === 'App\\Models\\User' && $reply->sender_id === auth()->id())
                                                <div x-show="!editing" class="imp-detail-msg-body">{!! strip_tags($reply->message, '<p><br><strong><b><em><i><a><ul><ol><li>') !!}</div>
                                                <div x-show="editing" x-cloak class="imp-note-edit-area">
                                                    <textarea x-model="editText" class="imp-note-edit-textarea" rows="3"></textarea>
                                                    <div class="imp-note-edit-actions">
                                                        <button class="imp-note-edit-cancel" @click="editing = false; editText = @js($reply->message)">Cancel</button>
                                                        <button class="imp-note-edit-save" @click="$wire.updateInternalNote({{ $reply->id }}, editText); editing = false;">Save</button>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="imp-detail-msg-body">{!! preg_replace(
                                                '/(SW_\d{6}_\d{4}|SW_\d{6}_IMP\d{4}|IMP-\d+)/',
                                                '<span class="imp-ticket-link" onclick="Livewire.dispatch(\'openTicketByNumber\', {number: \'$1\'})">$1</span>',
                                                strip_tags($reply->message, '<p><br><strong><b><em><i><a><ul><ol><li>')
                                            ) !!}</div>
                                            @endif
                                        </div>
                                    @else
                                        @php
                                            $isClient = str_contains($reply->sender_type, 'Customer');
                                        @endphp
                                        <div class="imp-detail-message {{ $isClient ? 'client' : 'staff' }}"
                                             x-bind:class="threadSearch && !$el.textContent.toLowerCase().includes(threadSearch.toLowerCase()) ? 'imp-msg-dimmed' : (threadSearch ? 'imp-msg-highlight' : '')">
                                            @if($isClient)
                                                <button class="imp-split-btn" wire:click="openSplitDrawer({{ $reply->id }})" title="Split to new ticket">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                                    </svg>
                                                </button>
                                            @endif
                                            <div class="imp-detail-msg-header">
                                                <div class="imp-detail-msg-avatar {{ $isClient ? 'blue' : 'purple' }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                                    </svg>
                                                </div>
                                                <div class="imp-detail-msg-info">
                                                    <span class="imp-detail-msg-name">{{ $reply->sender_name }}</span>
                                                    <span class="imp-detail-msg-badge {{ $isClient ? 'blue' : 'purple' }}">
                                                        {{ $isClient ? 'Client' : 'HR Support' }}
                                                    </span>
                                                    @if(!empty($reply->thread_label))
                                                        <span class="imp-detail-msg-badge thread-label" title="{{ $reply->thread_label }}">{{ $reply->thread_label }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="imp-detail-msg-body">{!! preg_replace(
                                                '/(SW_\d{6}_\d{4}|SW_\d{6}_IMP\d{4}|IMP-\d+)/',
                                                '<span class="imp-ticket-link" onclick="Livewire.dispatch(\'openTicketByNumber\', {number: \'$1\'})">$1</span>',
                                                strip_tags($reply->message, '<p><br><strong><b><em><i><a><ul><ol><li>')
                                            ) !!}</div>
                                            @if($reply->attachments)
                                                <div class="imp-detail-msg-attachments">
                                                    @foreach($reply->attachments as $attachment)
                                                        <a href="{{ Storage::url($attachment) }}" target="_blank" class="imp-detail-attachment-link">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px;">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" />
                                                            </svg>
                                                            {{ basename($attachment) }}
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                            <span class="imp-detail-msg-time">{{ $reply->created_at->format('M d, g:i A') }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <div class="imp-detail-no-messages">
                                    <p>No messages yet. Start the conversation by sending a reply.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Reply Composer (collapsed pill OR expanded panel) -->
                        @php
                            $replyUserInitial = strtoupper(substr(auth()->user()->name ?? 'U', 0, 1));
                            $pendingReplyAttachments = !empty($replyAttachments) ? count($replyAttachments) : 0;
                        @endphp
                        <div x-data="{
                                exec(command, value = null) {
                                    document.execCommand(command, false, value);
                                    this.$refs.replyEditor.focus();
                                },
                                isActive(command) {
                                    return document.queryCommandState(command);
                                },
                                insertLink() {
                                    const url = prompt('Enter URL:');
                                    if (url) this.exec('createLink', url);
                                },
                                handlePaste(e) {
                                    e.preventDefault();
                                    const html = e.clipboardData.getData('text/html');
                                    const text = e.clipboardData.getData('text/plain');
                                    document.execCommand('insertHTML', false, html || text);
                                },
                                replyOpen: false,
                                showCcBcc: false,
                                syncAndSubmit() {
                                    $wire.set('replyMessage', this.$refs.replyEditor.innerHTML);
                                    $wire.submitReply();
                                }
                             }"
                             x-init="
                                $wire.on('replyTemplateApplied', () => {
                                    replyOpen = true;
                                    $nextTick(() => { $refs.replyEditor.innerHTML = $wire.replyMessage || ''; });
                                });
                                $wire.on('replyEditorReset', () => {
                                    $refs.replyEditor.innerHTML = '';
                                    showCcBcc = false;
                                    replyOpen = false;
                                });
                             "
                             @keydown.escape.window="if (replyOpen) replyOpen = false"
                             style="flex-shrink: 0;"
                        >
                            <!-- Collapsed: single pill -->
                            <button type="button"
                                    x-show="!replyOpen"
                                    @click="replyOpen = true; $nextTick(() => $refs.replyEditor && $refs.replyEditor.focus())"
                                    class="imp-reply-collapsed"
                                    aria-label="Open reply composer">
                                <span class="imp-reply-collapsed-avatar">{{ $replyUserInitial }}</span>
                                <span class="imp-reply-collapsed-prompt">{{ $isInternalNote ? 'Click to add internal note...' : 'Click to reply to client...' }}</span>
                                @if($pendingReplyAttachments > 0)
                                    <span class="imp-reply-collapsed-meta">
                                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
                                        {{ $pendingReplyAttachments }} attached
                                    </span>
                                @endif
                                <span class="imp-reply-collapsed-icon">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                                </span>
                            </button>

                            <!-- Expanded: full composer -->
                            <div class="imp-detail-reply-box" x-show="replyOpen" x-collapse x-cloak>
                            <div class="imp-detail-reply-header">
                                <label class="imp-detail-reply-label">
                                    {{ $isInternalNote ? 'Internal Note (Private)' : 'Reply to Client' }}
                                </label>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <label class="imp-detail-internal-toggle">
                                        <input type="checkbox" wire:model.live="isInternalNote">
                                        <span>Internal Note</span>
                                    </label>
                                    <button type="button" @click="replyOpen = false" title="Minimize (Esc)" class="imp-reply-minimize" style="width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; border: none; background: none; border-radius: 5px; cursor: pointer; transition: all 0.15s;">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14"/></svg>
                                    </button>
                                </div>
                            </div>


                            <!-- TO / CC / BCC fields (hidden when Internal Note) -->
                            <div class="imp-detail-reply-email-fields" x-show="!$wire.isInternalNote" x-cloak>
                                <div class="imp-detail-reply-field-row">
                                    <label>To</label>
                                    <input type="email" wire:model="replyTo" readonly>
                                    <button type="button" class="imp-ccbcc-toggle" @click="showCcBcc = !showCcBcc" x-text="showCcBcc ? 'Hide CC/BCC' : 'CC/BCC'"></button>
                                </div>
                                <div x-show="showCcBcc" x-collapse>
                                    <div class="imp-detail-reply-field-row" style="margin-top: 6px;">
                                        <label>CC</label>
                                        <input type="text" wire:model="replyCc" placeholder="cc@example.com">
                                    </div>
                                    <div class="imp-detail-reply-field-row" style="margin-top: 6px;">
                                        <label>BCC</label>
                                        <input type="text" wire:model="replyBcc" placeholder="bcc@example.com">
                                    </div>
                                </div>
                            </div>

                            <!-- Email Template selector (hidden when Internal Note) -->
                            <div class="imp-detail-reply-field-row" x-show="!$wire.isInternalNote" x-cloak style="padding-bottom: 8px;">
                                <label>Email Template</label>
                                <select wire:model="replyEmailTemplate" wire:change="applyReplyTemplate($event.target.value)">
                                    <option value="">No Template</option>
                                    @foreach($this->emailTemplates as $tmpl)
                                        <option value="{{ $tmpl->id }}">{{ $tmpl->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Rich text toolbar -->
                            <div class="imp-detail-reply-toolbar">
                                <button type="button" title="Bold" @mousedown.prevent="exec('bold')" :class="{ 'imp-toolbar-active': isActive('bold') }">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3.744h-.753v8.25h7.125a4.125 4.125 0 000-8.25H6.75zm0 0v8.25m0 0h7.875a4.875 4.875 0 010 9.75H6.75v-9.75z" /></svg>
                                </button>
                                <button type="button" title="Italic" @mousedown.prevent="exec('italic')" :class="{ 'imp-toolbar-active': isActive('italic') }">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M5.248 20.246H9.05m0 0h3.696m-3.696 0l5.893-16.502m0 0H11.25m3.696 0h3.803" /></svg>
                                </button>
                                <button type="button" title="Link" @mousedown.prevent="insertLink()">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71" /><path stroke-linecap="round" stroke-linejoin="round" d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71" /></svg>
                                </button>
                                <div class="imp-toolbar-divider"></div>
                                <button type="button" title="Attach" @mousedown.prevent="document.getElementById('imp-reply-file-upload').click()">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" /></svg>
                                </button>
                                <input type="file" id="imp-reply-file-upload" wire:model="replyAttachments" multiple accept=".pdf,.png,.jpg,.jpeg,.xlsx" style="display: none;">
                            </div>

                            <!-- Rich text editor -->
                            <div wire:ignore>
                                <div class="imp-detail-reply-editor {{ $isInternalNote ? 'internal' : '' }}"
                                     contenteditable="true"
                                     x-ref="replyEditor"
                                     @paste="handlePaste($event)"
                                     data-placeholder="{{ $isInternalNote ? 'Add internal notes, troubleshooting steps, or team coordination notes...' : 'Type your response to the client here...' }}"></div>
                            </div>

                            @if(!empty($replyAttachments))
                                <div class="imp-drawer-file-list" style="margin-top: 8px;">
                                    @foreach($replyAttachments as $index => $file)
                                        <div class="imp-drawer-file-item">
                                            <div class="imp-drawer-file-info">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px; color: #9CA3AF;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" />
                                                </svg>
                                                <span>{{ $file->getClientOriginalName() }}</span>
                                                <span class="imp-drawer-file-size">({{ number_format($file->getSize() / 1024, 1) }} KB)</span>
                                            </div>
                                            <button type="button" class="imp-drawer-file-remove" wire:click="removeReplyAttachment({{ $index }})">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="imp-detail-reply-footer">
                                <div class="imp-detail-reply-left"></div>
                                <button @click="syncAndSubmit()"
                                        wire:loading.attr="disabled"
                                        wire:target="submitReply"
                                        class="imp-detail-send-btn {{ $isInternalNote ? 'internal' : '' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                                    </svg>
                                    <span wire:loading.remove wire:target="submitReply">
                                        {{ $isInternalNote ? 'Save Internal Note' : 'Send & Update Status' }}
                                    </span>
                                    <span wire:loading wire:target="submitReply">Sending...</span>
                                </button>
                            </div>

                            </div><!-- end .imp-detail-reply-box -->
                        </div><!-- end reply composer x-data wrapper -->
                    </div><!-- end .imp-detail-thread-panel -->
                </div><!-- end .imp-detail-content -->
    </div><!-- end .imp-fullpage-detail -->

    {{-- Split Ticket Drawer --}}
    @if($showSplitDrawer)
            <div x-data="{ splitOpen: false }"
                 x-init="$nextTick(() => splitOpen = true)"
                 x-on:close-split.window="splitOpen = false">
                <div class="imp-split-overlay" @click="$wire.closeSplitDrawer()"></div>
                <div class="imp-split-drawer" :class="{ 'open': splitOpen }">
                    <div class="imp-split-drawer-header">
                        <div class="imp-split-drawer-title">
                            <i class="bi bi-box-arrow-up-right"></i>
                            Split to New Ticket
                        </div>
                        <button class="imp-split-drawer-close" wire:click="closeSplitDrawer">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="imp-split-drawer-body">
                        {{-- Original message preview --}}
                        <div class="imp-split-section">
                            <label class="imp-split-label">Original Message</label>
                            @php
                                $splitReply = \App\Models\ImplementerTicketReply::find($splitReplyId);
                            @endphp
                            @if($splitReply)
                                <div class="imp-split-preview">{!! strip_tags($splitReply->message, '<p><br>') !!}</div>
                            @endif
                        </div>

                        {{-- Subject --}}
                        <div class="imp-split-section">
                            <label class="imp-split-label">Subject</label>
                            <input type="text" class="imp-split-input" wire:model="splitSubject" placeholder="Ticket subject..."
                                   style="text-transform: uppercase;"
                                   @input="const s=$event.target.selectionStart; $event.target.value=$event.target.value.toUpperCase(); $event.target.setSelectionRange(s,s);">
                            @error('splitSubject') <span style="color: #DC2626; font-size: 11px;">{{ $message }}</span> @enderror
                        </div>

                        {{-- Category --}}
                        <div class="imp-split-section">
                            <label class="imp-split-label">Category</label>
                            <select class="imp-split-select" wire:model="splitCategory">
                                <option value="">Select category</option>
                                <option value="Enhancement">Enhancement</option>
                                <option value="Paid Customization">Paid Customization</option>
                                <option value="Others Inquiry">Others Inquiry</option>
                                <option value="Add on License">Add on License</option>
                                <option value="Add on Module">Add on Module</option>
                                <option value="Add on Device">Add on Device</option>
                            </select>
                            @error('splitCategory') <span style="color: #DC2626; font-size: 11px;">{{ $message }}</span> @enderror
                        </div>

                        {{-- Module --}}
                        <div class="imp-split-section">
                            <label class="imp-split-label">Module</label>
                            <select class="imp-split-select" wire:model="splitModule">
                                <option value="">Select module</option>
                                <option value="Profile">Profile</option>
                                <option value="Attendance">Attendance</option>
                                <option value="Leave">Leave</option>
                                <option value="Claim">Claim</option>
                                <option value="Payroll">Payroll</option>
                            </select>
                            @error('splitModule') <span style="color: #DC2626; font-size: 11px;">{{ $message }}</span> @enderror
                        </div>

                    </div>
                    <div class="imp-split-drawer-footer">
                        <button class="imp-split-cancel-btn" wire:click="closeSplitDrawer">Cancel</button>
                        <button class="imp-split-submit-btn" wire:click="submitSplitTicket" wire:loading.attr="disabled">
                            <i class="bi bi-box-arrow-up-right"></i>
                            <span wire:loading.remove wire:target="submitSplitTicket">Create Split Ticket</span>
                            <span wire:loading wire:target="submitSplitTicket">Creating...</span>
                        </button>
                    </div>
                </div>
            </div>
    @endif
    @endif

    {{-- Merge Ticket Drawer --}}
    @if($showMergeDrawer)
        <div class="imp-split-overlay" wire:click="closeMergeDrawer"></div>
        <div class="imp-split-drawer open" style="max-width: 560px;">
            <div class="imp-split-drawer-header">
                <div class="imp-split-drawer-title">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 18px; height: 18px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m9.86-2.07a4.5 4.5 0 00-1.242-7.244l-4.5-4.5a4.5 4.5 0 00-6.364 6.364L4.34 8.374" />
                    </svg>
                    Merge Ticket
                </div>
                <button class="imp-split-drawer-close" wire:click="closeMergeDrawer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 18px; height: 18px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="imp-split-drawer-body">
                <p style="font-size: 13px; color: #6B7280; margin: 0 0 16px;">
                    Merge <strong>{{ $selectedTicket?->formatted_ticket_number ?? '' }}</strong> into another ticket. The current ticket will be closed and its conversation will appear in the target ticket.
                </p>

                <!-- Search -->
                <div class="imp-merge-search-box">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px; color: #9CA3AF; flex-shrink: 0;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <input type="text" wire:model.live.debounce.300ms="mergeSearch" placeholder="Search by ticket ID or subject..." style="border: none; outline: none; flex: 1; font-size: 14px; color: #374151; background: transparent;">
                </div>

                <!-- Ticket List -->
                <div class="imp-merge-ticket-list">
                    @forelse($this->mergeableTickets as $mTicket)
                        <div class="imp-merge-ticket-item {{ $mergeTargetTicketId == $mTicket->id ? 'selected' : '' }}"
                             wire:click="selectMergeTarget({{ $mTicket->id }})">
                            <div class="imp-merge-ticket-top">
                                <span class="imp-merge-ticket-number">{{ $mTicket->formatted_ticket_number }}</span>
                                <span class="imp-merge-ticket-status {{ $mTicket->status->value }}">{{ $mTicket->status->label() }}</span>
                            </div>
                            <div class="imp-merge-ticket-subject">{{ Str::limit($mTicket->subject, 80) }}</div>
                            <div class="imp-merge-ticket-meta">
                                <span>{{ $mTicket->implementerUser?->name ?? 'Unassigned' }}</span>
                                <span>{{ $mTicket->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    @empty
                        <div style="padding: 32px 16px; text-align: center; color: #9CA3AF; font-size: 14px;">
                            {{ $mergeSearch ? 'No matching tickets found' : 'No other tickets from this customer' }}
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="imp-split-drawer-footer">
                <button class="imp-split-cancel-btn" wire:click="closeMergeDrawer">Cancel</button>
                <button class="imp-split-submit-btn {{ !$mergeTargetTicketId ? 'disabled' : '' }}"
                        wire:click="submitMergeTicket"
                        wire:loading.attr="disabled"
                        wire:target="submitMergeTicket"
                        {{ !$mergeTargetTicketId ? 'disabled' : '' }}>
                    <span wire:loading.remove wire:target="submitMergeTicket">Merge Ticket</span>
                    <span wire:loading wire:target="submitMergeTicket">Merging...</span>
                </button>
            </div>
        </div>
    @endif
</x-filament-panels::page>
