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

        .imp-filter-select {
            padding: 8px 16px;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            background: white;
            font-size: 14px;
            color: #374151;
            cursor: pointer;
            min-width: 180px;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: none;
        }

        .imp-filter-select:focus {
            outline: none;
            border-color: #6366F1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
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

        /* === Ticket Detail Drawer === */
        body.imp-drawer-open {
            overflow: hidden !important;
        }
        body.imp-drawer-open .fi-topbar {
            display: none !important;
        }
        .imp-detail-overlay {
            position: fixed;
            inset: 0;
            z-index: 200;
            display: flex;
            justify-content: flex-end;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(4px);
            overflow: hidden;
        }
        .imp-detail-drawer {
            width: 100%;
            max-width: 920px;
            height: 100vh;
            background: white;
            box-shadow: -4px 0 24px rgba(0, 0, 0, 0.15);
            display: flex;
            flex-direction: column;
            border-radius: 0;
            overflow: hidden;
        }

        /* Detail Header */
        .imp-detail-header {
            border-bottom: 1px solid #E5E7EB;
            padding: 16px 20px;
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
            font-size: 16px;
            font-weight: 600;
            color: #111827;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .imp-detail-ticket-id {
            font-size: 12px;
            color: #6B7280;
            flex-shrink: 0;
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
            gap: 4px;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        .imp-detail-sla-badge.on_track { background: #DCFCE7; color: #166534; }
        .imp-detail-sla-badge.at_risk { background: #FEF3C7; color: #92400E; }
        .imp-detail-sla-badge.overdue { background: #FEE2E2; color: #991B1B; }
        .imp-detail-sla-badge.resolved { background: #F3F4F6; color: #374151; }
        .imp-detail-company-name {
            font-size: 13px;
            color: #6B7280;
        }
        .imp-detail-actions-bar {
            display: flex;
            gap: 8px;
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
            overflow: hidden;
            min-height: 0;
        }

        /* Left Sidebar */
        .imp-detail-sidebar {
            width: 280px;
            flex-shrink: 0;
            border-right: 1px solid #E5E7EB;
            background: #F9FAFB;
            padding: 16px;
            overflow-y: auto;
        }
        .imp-detail-sidebar-title {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
            margin: 0 0 16px 0;
        }
        .imp-detail-section {
            margin-bottom: 20px;
        }
        .imp-detail-label {
            display: block;
            font-size: 10px;
            font-weight: 600;
            color: #6B7280;
            letter-spacing: 0.05em;
            margin-bottom: 6px;
        }
        .imp-detail-card {
            background: white;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            padding: 10px;
        }
        .imp-detail-card-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .imp-detail-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .imp-detail-avatar.blue { background: #DBEAFE; color: #2563EB; }
        .imp-detail-avatar.purple { background: #EDE9FE; color: #7C3AED; }
        .imp-detail-avatar.yellow { background: #FEF3C7; color: #92400E; }
        .imp-detail-name {
            font-size: 13px;
            font-weight: 500;
            color: #111827;
            margin: 0;
        }
        .imp-detail-sublabel {
            font-size: 11px;
            color: #6B7280;
            margin: 0;
        }
        .imp-detail-email-row {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 8px;
            font-size: 11px;
            color: #6B7280;
        }

        /* Status Dropdown */
        .imp-detail-status-wrapper {
            position: relative;
        }
        .imp-detail-status-btn {
            width: 100%;
            background: white;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            padding: 8px 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: all 0.15s;
        }
        .imp-detail-status-btn:hover {
            background: #F9FAFB;
        }
        .imp-detail-status-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            margin-top: 4px;
            background: white;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            z-index: 20;
            padding: 4px 0;
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
            color: #374151;
            transition: background 0.1s;
        }
        .imp-detail-status-option:hover { background: #F9FAFB; }
        .imp-detail-status-option.active { background: #F5F3FF; font-weight: 500; color: #7C3AED; }

        /* Key Dates */
        .imp-detail-dates-card {
            background: white;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
        }
        .imp-detail-date-item {
            padding: 10px;
        }
        .imp-detail-date-item + .imp-detail-date-item {
            border-top: 1px solid #F3F4F6;
        }
        .imp-detail-date-label {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            color: #6B7280;
            margin-bottom: 2px;
        }
        .imp-detail-date-value {
            font-size: 13px;
            color: #111827;
            margin: 0;
        }

        /* Details Props */
        .imp-detail-props-card {
            background: white;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            padding: 10px;
        }
        .imp-detail-prop-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            padding: 3px 0;
        }
        .imp-detail-prop-label { color: #6B7280; }
        .imp-detail-prop-value { color: #111827; }
        .imp-detail-prop-value.priority-high { color: #DC2626; font-weight: 500; }
        .imp-detail-prop-value.priority-medium { color: #D97706; font-weight: 500; }
        .imp-detail-prop-value.priority-low { color: #2563EB; font-weight: 500; }

        /* Right Panel - Thread */
        .imp-detail-thread-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            min-width: 0;
        }
        .imp-detail-thread {
            flex: 1;
            overflow-y: auto;
            padding: 20px 16px;
            background: #F0F2F5;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .imp-detail-message {
            max-width: 78%;
            padding: 8px 12px;
            margin-bottom: 2px;
            position: relative;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.06);
        }
        .imp-detail-message.client {
            align-self: flex-start;
            background: #ffffff;
            border: none;
            border-radius: 0 12px 12px 12px;
        }
        .imp-detail-message.staff {
            align-self: flex-end;
            background: #EEF2FF;
            border: none;
            border-radius: 12px 0 12px 12px;
        }
        .imp-detail-message.internal {
            align-self: center;
            background: #FFFBEB;
            border: 1.5px dashed #FCD34D;
            border-radius: 12px;
            max-width: 85%;
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
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .imp-detail-msg-avatar svg {
            width: 14px !important;
            height: 14px !important;
        }
        .imp-detail-msg-avatar.blue { background: #DBEAFE; color: #2563EB; }
        .imp-detail-msg-avatar.purple { background: #EDE9FE; color: #7C3AED; }
        .imp-detail-msg-avatar.yellow { background: #FEF3C7; color: #92400E; }
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
            font-weight: 600;
            color: #111827;
        }
        .imp-detail-msg-badge {
            font-size: 10px;
            padding: 1px 7px;
            border-radius: 4px;
            font-weight: 500;
        }
        .imp-detail-msg-badge.blue { background: #DBEAFE; color: #1D4ED8; }
        .imp-detail-msg-badge.purple { background: #EDE9FE; color: #6D28D9; }
        .imp-detail-msg-badge.yellow { background: #FEF3C7; color: #92400E; }
        .imp-detail-msg-time {
            font-size: 10px;
            color: #9CA3AF;
            display: block;
            margin-top: 4px;
        }
        .imp-detail-message.client .imp-detail-msg-time {
            text-align: left;
        }
        .imp-detail-message.staff .imp-detail-msg-time {
            text-align: right;
        }
        .imp-detail-msg-body {
            font-size: 13px;
            color: #374151;
            line-height: 1.6;
            padding: 0;
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
            gap: 4px;
            font-size: 12px;
            color: #2563EB;
            text-decoration: none;
            padding: 3px 8px;
            background: #EFF6FF;
            border-radius: 4px;
        }
        .imp-detail-attachment-link:hover {
            background: #DBEAFE;
        }
        .imp-detail-no-messages {
            text-align: center;
            padding: 40px 20px;
            color: #6B7280;
            font-size: 13px;
        }

        /* Thread Search */
        .imp-thread-search {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #fff;
            border-bottom: 1px solid #E5E7EB;
            flex-shrink: 0;
        }
        .imp-thread-search i {
            color: #9CA3AF;
            font-size: 14px;
            flex-shrink: 0;
        }
        .imp-thread-search input {
            flex: 1;
            border: none;
            outline: none;
            font-size: 13px;
            color: #374151;
            background: transparent;
        }
        .imp-thread-search input::placeholder {
            color: #9CA3AF;
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
            opacity: 0.12 !important;
            pointer-events: none;
        }
        .imp-msg-highlight {
            box-shadow: 0 0 0 2px #667eea44;
        }

        /* Split Button */
        .imp-detail-message.client {
            position: relative;
        }
        .imp-split-btn {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 26px;
            height: 26px;
            border: none;
            background: #EFF6FF;
            color: #3B82F6;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.15s, background 0.15s;
        }
        .imp-detail-message.client:hover .imp-split-btn {
            opacity: 1;
        }
        .imp-split-btn:hover {
            background: #DBEAFE;
            color: #1D4ED8;
        }
        .imp-split-btn svg {
            width: 14px;
            height: 14px;
        }

        /* Split Drawer */
        .imp-split-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.25);
            z-index: 1100;
        }
        .imp-split-drawer {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: 440px;
            max-width: 92vw;
            background: #fff;
            z-index: 1101;
            box-shadow: -4px 0 24px rgba(0,0,0,0.12);
            display: flex;
            flex-direction: column;
            transform: translateX(100%);
            transition: transform 0.25s ease;
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
            border-top: 1px solid #E5E7EB;
            padding: 12px 16px;
            background: white;
            flex-shrink: 0;
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
            font-weight: 500;
            color: #374151;
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
            border-color: #7C3AED;
            box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.1);
        }
        .imp-detail-reply-field-row input[readonly] {
            color: #6B7280;
            cursor: default;
        }
        .imp-ccbcc-toggle {
            background: none;
            border: none;
            color: #7C3AED;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            padding: 2px 6px;
            border-radius: 4px;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .imp-ccbcc-toggle:hover {
            background: #F3F0FF;
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
            border-color: #7C3AED;
            box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.1);
        }

        /* Reply rich text editor */
        .imp-detail-reply-toolbar {
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 6px 10px;
            background: #F9FAFB;
            border: 1px solid #D1D5DB;
            border-radius: 8px 8px 0 0;
        }
        .imp-detail-reply-toolbar button {
            padding: 5px;
            border: none;
            background: none;
            border-radius: 5px;
            cursor: pointer;
            color: #6B7280;
            transition: all 0.15s;
        }
        .imp-detail-reply-toolbar button:hover {
            background: #E5E7EB;
        }
        .imp-detail-reply-toolbar button.imp-toolbar-active {
            background: #E5E7EB;
            color: #111827;
        }
        .imp-detail-reply-toolbar .imp-toolbar-divider {
            width: 1px;
            height: 18px;
            background: #D1D5DB;
            margin: 0 3px;
        }
        .imp-detail-reply-editor[contenteditable] {
            width: 100%;
            padding: 10px;
            border: 1px solid #D1D5DB;
            border-top: none;
            border-radius: 0 0 8px 8px;
            font-size: 13px;
            font-family: inherit;
            min-height: 120px;
            max-height: 250px;
            overflow-y: auto;
            line-height: 1.6;
            cursor: text;
            outline: none;
        }
        .imp-detail-reply-editor[contenteditable]:focus {
            border-color: #7C3AED;
            box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.1);
        }
        .imp-detail-reply-editor[contenteditable]:empty:before {
            content: attr(data-placeholder);
            color: #9CA3AF;
            pointer-events: none;
        }
        .imp-detail-reply-editor[contenteditable] p {
            margin: 0 0 8px 0;
        }
        .imp-detail-reply-editor[contenteditable] a {
            color: #7C3AED;
            text-decoration: underline;
        }
        .imp-detail-reply-editor.internal {
            border-color: #FDE68A;
            background: #FFFBEB;
        }
        .imp-detail-reply-editor.internal:focus {
            border-color: #D97706;
            box-shadow: 0 0 0 2px rgba(217, 119, 6, 0.1);
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
            gap: 6px;
            padding: 8px 16px;
            background: linear-gradient(135deg, #7C3AED, #2563EB);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s;
        }
        .imp-detail-send-btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        .imp-detail-send-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        .imp-detail-send-btn.internal {
            background: linear-gradient(135deg, #D97706, #B45309);
        }
    </style>

    <div class="imp-dashboard-wrapper">
        <!-- Header -->
        <div class="imp-dashboard-header">
            <div class="imp-header-left">
                <h1>Thread - Customer & Implementer</h1>
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

                <select class="imp-filter-select" wire:model.live="selectedImplementer">
                    <option value="">All Implementers</option>
                    @foreach($implementers as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>

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
                                    <span class="imp-ticket-id">{{ $ticket->formatted_ticket_number }}</span>
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
                                    <span class="imp-status-badge {{ $ticket->status->value }}">
                                        {{ $ticket->status->label() }}
                                    </span>
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
                        SLA Policy
                    </h2>
                    <button class="imp-modal-close" wire:click="$set('showSlaPolicy', false)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 20px; height: 20px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="imp-modal-body">
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
                                    <th>Priority</th>
                                    <th>First Response</th>
                                    <th>Resolution Target</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="imp-sla-priority-dot high"></span> High</td>
                                    <td>2 hours</td>
                                    <td>Same day</td>
                                </tr>
                                <tr>
                                    <td><span class="imp-sla-priority-dot medium"></span> Medium</td>
                                    <td>4 hours</td>
                                    <td>Next business day</td>
                                </tr>
                                <tr>
                                    <td><span class="imp-sla-priority-dot low"></span> Low</td>
                                    <td>8 hours</td>
                                    <td>3 business days</td>
                                </tr>
                            </tbody>
                        </table>
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
                                <span>Monday - Friday: <strong>8:00 AM - 6:00 PM (MYT)</strong></span>
                            </li>
                            <li>
                                <svg class="imp-bullet-amber" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="4"/></svg>
                                <span>Weekends and public holidays are excluded from SLA calculations</span>
                            </li>
                            <li>
                                <svg class="imp-bullet-blue" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="4"/></svg>
                                <span>Tickets submitted after <strong>3:00 PM</strong> will be processed the next business day</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Escalation Policy -->
                    <div class="imp-modal-section">
                        <h3>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                            Escalation Policy
                        </h3>
                        <ul class="imp-policy-list">
                            <li>
                                <svg class="imp-bullet-amber" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="4"/></svg>
                                <span>Auto-escalate after <strong>75%</strong> of SLA time has elapsed</span>
                            </li>
                            <li>
                                <svg class="imp-bullet-red" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="4"/></svg>
                                <span>Notify manager when ticket reaches <strong>50%</strong> of SLA deadline</span>
                            </li>
                            <li>
                                <svg class="imp-bullet-blue" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="4"/></svg>
                                <span>Escalate to <strong>R&D team</strong> for technical issues requiring code changes</span>
                            </li>
                            <li>
                                <svg class="imp-bullet-green" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="4"/></svg>
                                <span>Auto-resolve tickets with no client response after <strong>48 hours</strong></span>
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
                                <option value="License Activation">License Activation</option>
                                <option value="Data Migration">Data Migration</option>
                                <option value="Software Enquiries">Software Enquiries</option>
                                <option value="Session Enquiries">Session Enquiries</option>
                                <option value="Training Enquiries">Training Enquiries</option>
                                <option value="Enhancement/CR">Enhancement/CR</option>
                                <option value="Add-on License">Add-on License</option>
                                <option value="Others">Others</option>
                            </select>
                            @error('newTicketCategory') <div class="imp-drawer-error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

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
                                <option value="Appraisal">Appraisal</option>
                                <option value="Hire">Hire</option>
                            </select>
                            @error('newTicketModule') <div class="imp-drawer-error">{{ $message }}</div> @enderror
                        </div>

                        <!-- Status -->
                        <div class="imp-drawer-field">
                            <label>Status <span class="imp-required">*</span></label>
                            <select class="imp-drawer-select" wire:model="newTicketStatus" {{ $this->newTicketCategory === 'License Activation' ? 'disabled' : '' }}>
                                <option value="open">Open</option>
                                <option value="pending_support">Pending Support</option>
                                <option value="pending_client">Pending Client</option>
                                <option value="pending_rnd">Pending R&D</option>
                                <option value="closed">Closed</option>
                            </select>
                            @if($this->newTicketCategory === 'License Activation')
                                <div class="imp-drawer-helper">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Auto-set to "Closed" for License Activation
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Priority -->
                <div class="imp-drawer-section">
                    <div class="imp-drawer-field">
                        <label>Priority <span class="imp-required">*</span></label>
                        <select class="imp-drawer-select" wire:model="newTicketPriority">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
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
                            <option value="First Response">First Response</option>
                            <option value="Require More Time">Require More Time</option>
                            <option value="R&D Escalation">R&D Escalation</option>
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
                               placeholder="Enter email subject line">
                        @error('newTicketEmailSubject') <div class="imp-drawer-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Email Body (Rich Text Editor) -->
                <div class="imp-drawer-section">
                    <div class="imp-drawer-field"
                         x-data="{
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
                            }
                         }"
                         x-init="
                            $wire.on('templateApplied', () => {
                                $nextTick(() => { $refs.editor.innerHTML = $wire.newTicketEmailBody || ''; });
                            });
                            $wire.on('drawerReset', () => {
                                $refs.editor.innerHTML = '';
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
                        <div wire:ignore>
                            <div class="imp-drawer-textarea"
                                 contenteditable="true"
                                 x-ref="editor"
                                 @paste="handlePaste($event)"
                                 data-placeholder="Type your email message here..."></div>
                        </div>
                        @error('newTicketEmailBody') <div class="imp-drawer-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Attachments -->
                <div class="imp-drawer-section">
                    <div class="imp-drawer-field">
                        <label>Attachments</label>
                        <label for="imp-file-upload" class="imp-drawer-upload">
                            <div class="imp-drawer-upload-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 20px; height: 20px; color: #7C3AED;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                </svg>
                            </div>
                            <div class="imp-drawer-upload-text">Click to upload or drag and drop</div>
                            <div class="imp-drawer-upload-hint">PDF, PNG, JPG, XLSX up to 10MB each</div>
                        </label>
                        <input type="file"
                               id="imp-file-upload"
                               wire:model="ticketAttachments"
                               multiple
                               accept=".pdf,.png,.jpg,.jpeg,.xlsx"
                               style="display: none;">

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
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="imp-drawer-footer">
                <button type="button" class="imp-drawer-cancel" wire:click="closeCreateDrawer">Cancel</button>
                <button type="button" class="imp-drawer-submit" @click="const editor = document.querySelector('[x-ref=editor]'); if (editor) @this.set('newTicketEmailBody', editor.innerHTML); $nextTick(() => { $wire.createTicket(); });" wire:loading.attr="disabled">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                    </svg>
                    <span wire:loading.remove wire:target="createTicket">Submit & Send Email</span>
                    <span wire:loading wire:target="createTicket">Submitting...</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Ticket Detail Drawer -->
    @if($showTicketDetail && $selectedTicket)
        <div class="imp-detail-overlay"
             x-data="{ open: true }"
             x-init="document.body.classList.add('imp-drawer-open')"
             x-on:remove="document.body.classList.remove('imp-drawer-open')"
             x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click.self="document.body.classList.remove('imp-drawer-open'); $wire.closeTicketDetail()"
             @keydown.window.escape="document.body.classList.remove('imp-drawer-open'); $wire.closeTicketDetail()"
             wire:ignore.self>

            <div class="imp-detail-drawer"
                 x-show="open"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full">

                <!-- Header -->
                <div class="imp-detail-header">
                    <div class="imp-detail-header-top">
                        <div class="imp-detail-header-info">
                            <h2 class="imp-detail-title">
                                {{ $selectedTicket->subject ?? 'No Subject' }}
                            </h2>
                            <span class="imp-detail-ticket-id">{{ $selectedTicket->formatted_ticket_number }} - {{ $selectedTicket->category ?? '' }}</span>
                        </div>
                        <button wire:click="closeTicketDetail" @click="document.body.classList.remove('imp-drawer-open')" class="imp-detail-close">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 20px; height: 20px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
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
                    <div class="imp-detail-actions-bar"></div>
                </div>

                <!-- Main Content - Split View -->
                <div class="imp-detail-content">
                    <!-- Left Sidebar -->
                    <div class="imp-detail-sidebar">
                        <h3 class="imp-detail-sidebar-title">Ticket Properties</h3>

                        <!-- Client Contact -->
                        <div class="imp-detail-section">
                            <label class="imp-detail-label">CLIENT CONTACT</label>
                            <div class="imp-detail-card">
                                <div class="imp-detail-card-row">
                                    <div class="imp-detail-avatar blue">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="imp-detail-name">{{ $selectedTicket->customer?->name ?? 'Unknown' }}</p>
                                        <p class="imp-detail-sublabel">Primary Contact</p>
                                    </div>
                                </div>
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

                        <!-- Ticket Owner -->
                        <div class="imp-detail-section">
                            <label class="imp-detail-label">TICKET OWNER</label>
                            <div class="imp-detail-card">
                                <div class="imp-detail-card-row">
                                    <div class="imp-detail-avatar purple">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;">
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
                                                <div class="imp-detail-msg-body">{!! strip_tags($reply->message, '<p><br><strong><b><em><i><a><ul><ol><li>') !!}</div>
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
                                                </div>
                                            </div>
                                            <div class="imp-detail-msg-body">{!! strip_tags($reply->message, '<p><br><strong><b><em><i><a><ul><ol><li>') !!}</div>
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

                        <!-- Reply Box -->
                        <div class="imp-detail-reply-box"
                             x-data="{
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
                        >
                            <div class="imp-detail-reply-header" @click="replyOpen = !replyOpen" style="cursor: pointer;">
                                <label class="imp-detail-reply-label" style="cursor: pointer;">
                                    {{ $isInternalNote ? 'Internal Note (Private)' : 'Reply to Client' }}
                                </label>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <label class="imp-detail-internal-toggle" @click.stop>
                                        <input type="checkbox" wire:model.live="isInternalNote">
                                        <span>Internal Note</span>
                                    </label>
                                    <svg class="imp-reply-chevron" :class="{ 'expanded': replyOpen }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                    </svg>
                                </div>
                            </div>

                            <div x-show="replyOpen" x-collapse x-cloak>

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
                                    <option value="First Response">First Response</option>
                                    <option value="Require More Time">Require More Time</option>
                                    <option value="R&D Escalation">R&D Escalation</option>
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

                            </div><!-- end x-collapse wrapper -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
                            <input type="text" class="imp-split-input" wire:model="splitSubject" placeholder="Ticket subject...">
                            @error('splitSubject') <span style="color: #DC2626; font-size: 11px;">{{ $message }}</span> @enderror
                        </div>

                        {{-- Category --}}
                        <div class="imp-split-section">
                            <label class="imp-split-label">Category</label>
                            <select class="imp-split-select" wire:model="splitCategory">
                                <option value="">Select category</option>
                                <option value="License Activation">License Activation</option>
                                <option value="Data Migration">Data Migration</option>
                                <option value="Software Enquiries">Software Enquiries</option>
                                <option value="Session Enquiries">Session Enquiries</option>
                                <option value="Training Enquiries">Training Enquiries</option>
                                <option value="Enhancement/CR">Enhancement/CR</option>
                                <option value="Add-on License">Add-on License</option>
                                <option value="Others">Others</option>
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
                                <option value="Appraisal">Appraisal</option>
                                <option value="Hire">Hire</option>
                            </select>
                            @error('splitModule') <span style="color: #DC2626; font-size: 11px;">{{ $message }}</span> @enderror
                        </div>

                        {{-- Priority --}}
                        <div class="imp-split-section">
                            <label class="imp-split-label">Priority</label>
                            <select class="imp-split-select" wire:model="splitPriority">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
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
</x-filament-panels::page>
