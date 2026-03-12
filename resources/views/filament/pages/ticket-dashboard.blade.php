{{-- filepath: /var/www/html/timeteccrm/resources/views/filament/pages/ticket-dashboard.blade.php --}}
<x-filament-panels::page>
    @php
        $data = $this->getViewData();
        $softwareBugs = $data['softwareBugs'];
        $backendAssistance = $data['backendAssistance'];
        $enhancement = $data['enhancement'];
        $softwareBugsNewBreakdown = $data['softwareBugsNewBreakdown'];
        $softwareBugsInProgressBreakdown = $data['softwareBugsInProgressBreakdown'];
        $softwareBugsCompletedBreakdown = $data['softwareBugsCompletedBreakdown'];
        $softwareBugsClosedBreakdown = $data['softwareBugsClosedBreakdown'];
        $backendNewBreakdown = $data['backendNewBreakdown'];
        $backendInProgressBreakdown = $data['backendInProgressBreakdown'];
        $backendCompletedBreakdown = $data['backendCompletedBreakdown'];
        $backendClosedBreakdown = $data['backendClosedBreakdown'];
        $tickets = $data['tickets'];
        $calendar = $data['calendar'];
        $currentMonth = $data['currentMonth'];
        $currentYear = $data['currentYear'];
        $products = $data['products'];
        $modules = $data['modules'];
        $frontEndNames = $data['frontEndNames'];
        $statuses = $data['statuses'];
        // Pagination data
        $totalTickets = $data['totalTickets'];
        $totalPages = $data['totalPages'];
        $currentPage = $data['currentPage'];
        $perPage = $data['perPage'];
    @endphp

    <style>
        select:not(.choices) {
            background-image: none !important;
        }

        /* Main Layout */
        .dashboard-wrapper {
            background: #F9FAFB;
            min-height: 100vh;
            padding: 0;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        /* ✅ Add page title styling */
        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: #111827;
        }

        .filter-dropdowns {
            display: flex;
            gap: 12px;
        }

        .filter-select {
            padding: 8px 16px;
            border: 1px solid #D1D5DB;
            border-radius: 8px;
            background: white;
            font-size: 14px;
            color: #374151;
            cursor: pointer;
            min-width: 180px;
            appearance: none; /* ✅ Remove default dropdown arrow */
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: none; /* ✅ Remove any background arrow */
        }

        .filter-select:focus {
            outline: none;
            border-color: #6366F1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 570px 1fr;
            gap: 24px;
        }

        /* Left Column */
        .left-column {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        /* Category Cards */
        .category-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #E5E7EB;
        }

        .category-card.red {
            border-left: 4px solid #DC2626;
        }

        .category-card.blue {
            border-left: 4px solid #2563EB;
        }

        .category-card.green {
            border-left: 4px solid #059669;
        }

        .category-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .category-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .red .category-icon {
            background: #FEE2E2;
        }

        .blue .category-icon {
            background: #DBEAFE;
        }

        .green .category-icon {
            background: #D1FAE5;
        }

        .category-title {
            flex: 1;
            font-size: 16px;
            font-weight: 600;
            color: #111827;
        }

        .category-badge {
            background: #F3F4F6;
            color: #6B7280;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
        }

        /* Status Grid */
        .status-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }

        .status-grid.three-items {
            grid-template-columns: repeat(3, 1fr);
        }

        .status-box {
            background: #FAFAFA;
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            padding: 14px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .status-box:hover {
            background: white;
            border-color: #D1D5DB;
            transform: translateY(-1px);
        }

        .status-box.active {
            background: #1F2937;
            border-color: #1F2937;
        }

        .status-number {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
            line-height: 1;
            margin-bottom: 6px;
        }

        .status-box.active .status-number {
            color: white;
        }

        .status-text {
            font-size: 12px;
            font-weight: 500;
            color: #6B7280;
        }

        .status-box.active .status-text {
            color: #D1D5DB;
        }

        /* Enhancement Section */
        .enhancement-filters {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
            justify-content: flex-end;
        }

        .filter-pill {
            padding: 6px 14px;
            border: 1px solid #E5E7EB;
            border-radius: 6px;
            background: white;
            font-size: 12px;
            color: #6B7280;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 500;
        }

        .filter-pill:hover {
            border-color: #059669;
            color: #059669;
        }

        /* ✅ Active state for filter pills */
        .filter-pill.active {
            background: #059669;
            border-color: #059669;
            color: white;
            font-weight: 600;
        }

        .filter-pill.active:hover {
            background: #047857;
            border-color: #047857;
            color: white;
        }

        /* Calendar */
        .calendar-wrapper {
            background: white;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #E5E7EB;
            margin-top: 16px;
        }

        .calendar-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .calendar-title {
            font-size: 15px;
            font-weight: 600;
            color: #111827;
        }

        .calendar-arrows {
            display: flex;
            gap: 4px;
        }

        .arrow-btn {
            background: transparent;
            border: none;
            color: #9CA3AF;
            cursor: pointer;
            padding: 4px;
            font-size: 18px;
        }

        .arrow-btn:hover {
            color: #374151;
        }

        .calendar-table {
            width: 100%;
            border-collapse: collapse;
        }

        .calendar-table th {
            font-size: 11px;
            font-weight: 600;
            color: #9CA3AF;
            text-transform: uppercase;
            padding: 8px 4px;
            text-align: center;
        }

        .calendar-table td {
            text-align: center;
            padding: 4px;
            font-size: 13px;
            color: #374151;
        }

        .calendar-day {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .calendar-day:hover {
            background: #F3F4F6;
        }

        .calendar-day.today {
            background: #6366F1;
            color: white;
            font-weight: 600;
        }

        .calendar-day.selected {
            background: #10B981;
            color: white;
            font-weight: 600;
        }

        .calendar-day.today.selected {
            background: #059669;
            color: white;
        }

        .calendar-day.other-month {
            color: #D1D5DB;
        }

        .calendar-day.other-month:hover {
            background: #F9FAFB;
        }

        /* Right Panel */
        .ticket-panel {
            background: white;
            border-radius: 12px;
            padding: 24px;
            border: 1px solid #E5E7EB;
            display: flex;
            flex-direction: column;
            height: fit-content;
        }

        .ticket-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid #E5E7EB;
        }

        .ticket-title {
            font-size: 16px;
            font-weight: 600;
            color: #111827;
        }

        .ticket-filters {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .ticket-filter-select {
            padding: 6px 12px;
            border: 1px solid #E5E7EB;
            border-radius: 6px;
            font-size: 13px;
            background: white;
            appearance: none; /* ✅ Remove dropdown arrow */
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: none;
        }

        .close-badge {
            background: #F3F4F6;
            color: #6B7280;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            font-weight: 500;
        }

        .close-badge:hover {
            background: #E5E7EB;
        }

        .ticket-count {
            color: #9CA3AF;
            font-size: 13px;
        }

        .empty-tickets {
            text-align: center;
            padding: 80px 20px;
        }

        .empty-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 16px;
            opacity: 0.3;
        }

        .empty-text {
            color: #9CA3AF;
            font-size: 14px;
        }

        .status-badge-wrapper:hover .status-tooltip {
            opacity: 1 !important;
        }

        /* Fast-response module tooltip */
        .module-cell {
            position: relative;
            overflow: visible;
        }

        .module-cell:hover {
            background: #F9FAFB;
            font-weight: 600;
        }

        .module-tooltip {
            position: fixed;
            padding: 10px 14px;
            background: #1F2937;
            color: white;
            font-size: 13px;
            font-weight: 500;
            border-radius: 6px;
            z-index: 9999;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            max-width: 600px;
            min-width: 300px;
            white-space: normal;
            line-height: 1.5;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity 0.15s ease-in-out, visibility 0.15s ease-in-out;
        }

        .module-cell:hover .module-tooltip {
            opacity: 1;
            visibility: visible;
        }

        /* Status breakdown tooltip */
        .status-breakdown-tooltip {
            position: fixed;
            padding: 14px 16px;
            background: #1F2937;
            color: white;
            font-size: 13px;
            font-weight: 500;
            border-radius: 8px;
            z-index: 9999;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.24);
            min-width: 250px;
            max-width: 400px;
            white-space: normal;
            line-height: 1.6;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity 0.2s ease-in-out, visibility 0.2s ease-in-out;
        }

        .status-breakdown-tooltip .frontend-item {
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .status-breakdown-tooltip .frontend-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .status-breakdown-tooltip .frontend-item:first-child {
            padding-top: 0;
        }

        .status-breakdown-tooltip .frontend-name {
            font-weight: 600;
            color: #60A5FA;
            margin-bottom: 4px;
            font-size: 13px;
        }

        .status-breakdown-tooltip .ticket-count {
            color: #D1D5DB;
            font-size: 12px;
            margin-bottom: 4px;
        }

        .status-breakdown-tooltip .ticket-list {
            color: #9CA3AF;
            font-size: 11px;
            margin-left: 8px;
            line-height: 1.4;
        }
    </style>

    <script>
        let tooltipEl = null;

        function showTooltip(event, title) {
            if (!title) return;

            // Create tooltip if it doesn't exist
            if (!tooltipEl) {
                tooltipEl = document.createElement('div');
                tooltipEl.className = 'module-tooltip';
                document.body.appendChild(tooltipEl);
            }

            // Set content and position
            tooltipEl.textContent = title;
            tooltipEl.style.opacity = '1';
            tooltipEl.style.visibility = 'visible';

            // Position below the cell
            const rect = event.currentTarget.getBoundingClientRect();
            tooltipEl.style.left = rect.left + 'px';
            tooltipEl.style.top = (rect.bottom + 8) + 'px';
        }

        function showTicketTooltip(event, module, company, title) {
            if (!module && !company && !title) return;

            // Create tooltip if it doesn't exist
            if (!tooltipEl) {
                tooltipEl = document.createElement('div');
                tooltipEl.className = 'module-tooltip';
                document.body.appendChild(tooltipEl);
            }

            // Build multi-line content
            let content = '';
            if (module) content += '<div style="margin-bottom: 4px;"><strong>Module:</strong> ' + module + '</div>';
            if (company) content += '<div style="margin-bottom: 4px;"><strong>Company:</strong> ' + company + '</div>';
            if (title) content += '<div><strong>Title:</strong> ' + title + '</div>';

            // Set content and position
            tooltipEl.innerHTML = content;
            tooltipEl.style.opacity = '1';
            tooltipEl.style.visibility = 'visible';

            // Position below the cell
            const rect = event.currentTarget.getBoundingClientRect();
            tooltipEl.style.left = rect.left + 'px';
            tooltipEl.style.top = (rect.bottom + 8) + 'px';
        }

        function hideTooltip(event) {
            if (tooltipEl) {
                tooltipEl.style.opacity = '0';
                tooltipEl.style.visibility = 'hidden';
            }
        }

        // Show status breakdown tooltip (for Software Bugs In Progress)
        function showBreakdownTooltip(event, breakdownData) {
            if (!breakdownData || Object.keys(breakdownData).length === 0) return;

            // Create tooltip if it doesn't exist
            if (!tooltipEl) {
                tooltipEl = document.createElement('div');
                tooltipEl.className = 'status-breakdown-tooltip';
                document.body.appendChild(tooltipEl);
            }

            // Build HTML content
            let content = '';
            for (const [frontend, data] of Object.entries(breakdownData)) {
                content += `
                    <div class="frontend-item">
                        <div class="frontend-name">${frontend} <span style="color: #9CA3AF;">(${data.count})</span></div>
                    </div>
                `;
            }

            // Set content and position
            tooltipEl.innerHTML = content;
            tooltipEl.className = 'status-breakdown-tooltip';
            tooltipEl.style.opacity = '1';
            tooltipEl.style.visibility = 'visible';

            // Position below the status box
            const rect = event.currentTarget.getBoundingClientRect();
            tooltipEl.style.left = rect.left + 'px';
            tooltipEl.style.top = (rect.bottom + 8) + 'px';
        }

        // Close column toggle when clicking outside
        document.addEventListener('click', function(event) {
            const columnToggle = event.target.closest('[wire\\:click="toggleColumnVisibility"]');
            const dropdown = event.target.closest('[wire\\:click\\.stop]');

            if (!columnToggle && !dropdown) {
                @this.call('closeColumnToggle');
            }
        });
    </script>

    <div class="dashboard-wrapper">
        <!-- ✅ Header with Title -->
        <div class="dashboard-header">
            <h1 class="page-title">Ticket Dashboard</h1>
        </div>

        <style>
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
        </style>

        <!-- Main Grid -->
        <div class="dashboard-grid">
            <!-- Left Column -->
            <div class="left-column">
                <!-- Software Bugs -->
                <div class="category-card red">
                    <div class="category-header">
                        <div class="category-icon">📋</div>
                        <div class="category-title">Software Bugs</div>
                        <div class="category-badge">{{ $softwareBugs['total'] }}</div>
                    </div>
                    <div class="status-grid">
                        <div class="status-box {{ $selectedCategory === 'softwareBugs' && $selectedStatus === 'New' ? 'active' : '' }}"
                            wire:click="selectCategory('softwareBugs', 'New')"
                            onmouseenter="showBreakdownTooltip(event, {{ json_encode($softwareBugsNewBreakdown) }})"
                            onmouseleave="hideTooltip(event)">
                            <div class="status-number">{{ $softwareBugs['new'] }}</div>
                            <div class="status-text">New</div>
                        </div>
                        <div class="status-box {{ $selectedCategory === 'softwareBugs' && $selectedStatus === 'In Progress' ? 'active' : '' }}"
                            wire:click="selectCategory('softwareBugs', 'In Progress')"
                            onmouseenter="showBreakdownTooltip(event, {{ json_encode($softwareBugsInProgressBreakdown) }})"
                            onmouseleave="hideTooltip(event)">
                            <div class="status-number">{{ $softwareBugs['progress'] }}</div>
                            <div class="status-text">In Progress</div>
                        </div>
                        <div class="status-box {{ $selectedCategory === 'softwareBugs' && ($selectedStatus === 'Completed' || $selectedStatus === 'Tickets: Live') ? 'active' : '' }}"
                            wire:click="selectCategory('softwareBugs', 'Completed')"
                            onmouseenter="showBreakdownTooltip(event, {{ json_encode($softwareBugsCompletedBreakdown) }})"
                            onmouseleave="hideTooltip(event)">
                            <div class="status-number">{{ $softwareBugs['completed'] }}</div>
                            <div class="status-text">Completed</div>
                        </div>
                        <div class="status-box {{ $selectedCategory === 'softwareBugs' && $selectedStatus === 'Closed' ? 'active' : '' }}"
                            wire:click="selectCategory('softwareBugs', 'Closed')"
                            onmouseenter="showBreakdownTooltip(event, {{ json_encode($softwareBugsClosedBreakdown) }})"
                            onmouseleave="hideTooltip(event)">
                            <div class="status-number">{{ $softwareBugs['closed'] }}</div>
                            <div class="status-text">Closed</div>
                        </div>
                    </div>
                </div>

                <!-- Backend Assistance -->
                <div class="category-card blue">
                    <div class="category-header">
                        <div class="category-icon">💻</div>
                        <div class="category-title">Backend Assistance</div>
                        <div class="category-badge">{{ $backendAssistance['total'] }}</div>
                    </div>
                    <div class="status-grid">
                        <div class="status-box {{ $selectedCategory === 'backendAssistance' && $selectedStatus === 'New' ? 'active' : '' }}"
                            wire:click="selectCategory('backendAssistance', 'New')"
                            onmouseenter="showBreakdownTooltip(event, {{ json_encode($backendNewBreakdown) }})"
                            onmouseleave="hideTooltip(event)">
                            <div class="status-number">{{ $backendAssistance['new'] }}</div>
                            <div class="status-text">New</div>
                        </div>
                        <div class="status-box {{ $selectedCategory === 'backendAssistance' && $selectedStatus === 'In Progress' ? 'active' : '' }}"
                            wire:click="selectCategory('backendAssistance', 'In Progress')"
                            onmouseenter="showBreakdownTooltip(event, {{ json_encode($backendInProgressBreakdown) }})"
                            onmouseleave="hideTooltip(event)">
                            <div class="status-number">{{ $backendAssistance['progress'] }}</div>
                            <div class="status-text">In Progress</div>
                        </div>
                        <div class="status-box {{ $selectedCategory === 'backendAssistance' && ($selectedStatus === 'Completed' || $selectedStatus === 'Tickets: Live') ? 'active' : '' }}"
                            wire:click="selectCategory('backendAssistance', 'Completed')"
                            onmouseenter="showBreakdownTooltip(event, {{ json_encode($backendCompletedBreakdown) }})"
                            onmouseleave="hideTooltip(event)">
                            <div class="status-number">{{ $backendAssistance['completed'] }}</div>
                            <div class="status-text">Completed</div>
                        </div>
                        <div class="status-box {{ $selectedCategory === 'backendAssistance' && $selectedStatus === 'Closed' ? 'active' : '' }}"
                            wire:click="selectCategory('backendAssistance', 'Closed')"
                            onmouseenter="showBreakdownTooltip(event, {{ json_encode($backendClosedBreakdown) }})"
                            onmouseleave="hideTooltip(event)">
                            <div class="status-number">{{ $backendAssistance['closed'] }}</div>
                            <div class="status-text">Closed</div>
                        </div>
                    </div>
                </div>

                <!-- Enhancement Workflow -->
                <div class="category-card green">
                    <div class="category-header">
                        <div class="category-icon">⭐</div>
                        <div class="category-title">Enhancement Workflow</div>
                        <div class="category-badge">{{ $enhancement['total'] }}</div>
                    </div>
                    <div class="enhancement-filters">
                        <div class="filter-pill {{ $selectedEnhancementType === 'critical' ? 'active' : '' }}"
                             wire:click="selectEnhancementType('critical')">
                            Critical
                        </div>
                        <div class="filter-pill {{ $selectedEnhancementType === 'paid' ? 'active' : '' }}"
                             wire:click="selectEnhancementType('paid')">
                            Paid
                        </div>
                        <div class="filter-pill {{ $selectedEnhancementType === 'non-critical' ? 'active' : '' }}"
                             wire:click="selectEnhancementType('non-critical')">
                            Non Critical
                        </div>
                    </div>
                    <div class="status-grid three-items">
                        <div class="status-box {{ $selectedCategory === 'enhancement' && $selectedEnhancementStatus === 'New' ? 'active' : '' }}"
                             wire:click="$set('selectedEnhancementStatus', '{{ $selectedEnhancementStatus === 'New' ? null : 'New' }}'); $set('selectedCategory', '{{ $selectedEnhancementStatus === 'New' ? null : 'enhancement' }}');">
                            <div class="status-number">{{ $enhancement['new'] }}</div>
                            <div class="status-text">New</div>
                        </div>
                        <div class="status-box {{ $selectedCategory === 'enhancement' && $selectedEnhancementStatus === 'Pending Release' ? 'active' : '' }}"
                             wire:click="$set('selectedEnhancementStatus', '{{ $selectedEnhancementStatus === 'Pending Release' ? null : 'Pending Release' }}'); $set('selectedCategory', '{{ $selectedEnhancementStatus === 'Pending Release' ? null : 'enhancement' }}');">
                            <div class="status-number">{{ $enhancement['pending_release'] }}</div>
                            <div class="status-text">Pending Release</div>
                        </div>
                        <div class="status-box {{ $selectedCategory === 'enhancement' && $selectedEnhancementStatus === 'System Go Live' ? 'active' : '' }}"
                             wire:click="$set('selectedEnhancementStatus', '{{ $selectedEnhancementStatus === 'System Go Live' ? null : 'System Go Live' }}'); $set('selectedCategory', '{{ $selectedEnhancementStatus === 'System Go Live' ? null : 'enhancement' }}');">
                            <div class="status-number">{{ $enhancement['system_go_live'] }}</div>
                            <div class="status-text">System Go Live</div>
                        </div>
                    </div>

                    <!-- Calendar -->
                    <div class="calendar-wrapper">
                        <div class="calendar-nav">
                            <button class="arrow-btn" wire:click="previousMonth">‹</button>
                            <div class="calendar-title">{{ $calendar['month'] }}</div>
                            <button class="arrow-btn" wire:click="nextMonth">›</button>
                        </div>
                        <table class="calendar-table">
                            <thead>
                                <tr>
                                    <th>MON</th>
                                    <th>TUE</th>
                                    <th>WED</th>
                                    <th>THU</th>
                                    <th>FRI</th>
                                    <th>SAT</th>
                                    <th>SUN</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $firstDay = $calendar['first_day_of_week'];
                                    $daysInMonth = $calendar['days_in_month'];
                                    $currentDate = $calendar['current_date'];
                                    $adjustedFirstDay = $firstDay == 0 ? 6 : $firstDay - 1;

                                    $prevMonthDate = \Carbon\Carbon::create($currentYear, $currentMonth, 1)->subMonth();
                                    $prevMonthDays = $prevMonthDate->daysInMonth;

                                    $days = [];

                                    for ($i = $adjustedFirstDay - 1; $i >= 0; $i--) {
                                        $days[] = [
                                            'day' => $prevMonthDays - $i,
                                            'class' => 'other-month',
                                            'year' => $prevMonthDate->year,
                                            'month' => $prevMonthDate->month,
                                        ];
                                    }

                                    for ($day = 1; $day <= $daysInMonth; $day++) {
                                        $isToday = $day == $currentDate->day &&
                                                  $currentMonth == $currentDate->month &&
                                                  $currentYear == $currentDate->year;

                                        $dateString = \Carbon\Carbon::create($currentYear, $currentMonth, $day)->format('Y-m-d');
                                        $isSelected = $selectedDate === $dateString;

                                        $class = $isToday ? 'today' : '';
                                        if ($isSelected) {
                                            $class .= ' selected';
                                        }

                                        $days[] = [
                                            'day' => $day,
                                            'class' => trim($class),
                                            'year' => $currentYear,
                                            'month' => $currentMonth,
                                        ];
                                    }

                                    $totalCells = count($days);
                                    $remainingCells = (7 - ($totalCells % 7)) % 7;

                                    $nextMonthDate = \Carbon\Carbon::create($currentYear, $currentMonth, 1)->addMonth();

                                    for ($day = 1; $day <= $remainingCells; $day++) {
                                        $days[] = [
                                            'day' => $day,
                                            'class' => 'other-month',
                                            'year' => $nextMonthDate->year,
                                            'month' => $nextMonthDate->month,
                                        ];
                                    }

                                    $weeks = array_chunk($days, 7);
                                @endphp

                                @foreach($weeks as $week)
                                    <tr>
                                        @foreach($week as $dayData)
                                            <td>
                                                <div class="calendar-day {{ $dayData['class'] }}"
                                                     wire:click="selectDate({{ $dayData['year'] }}, {{ $dayData['month'] }}, {{ $dayData['day'] }})">
                                                    {{ $dayData['day'] }}
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Panel - Ticket Listing -->
            <div class="ticket-panel">
                <div class="ticket-header">
                    <div>
                        <div class="ticket-title">Ticket Listing
                            <span class="ticket-count">{{ count($tickets) }} of {{ $totalTickets }}</span>
                        </div>
                    </div>
                    <div class="ticket-filters">
                        {{-- Search Ticket ID --}}
                        <div style="position: relative;">
                            <input type="text"
                                   wire:model.live.debounce.300ms="searchTicketId"
                                   placeholder="Search ID..."
                                   style="padding: 8px 12px 8px 32px; border: 1px solid #E5E7EB; border-radius: 6px; font-size: 13px; width: 120px; outline: none; transition: all 0.2s;"
                                   onfocus="this.style.borderColor='#6366F1'; this.style.boxShadow='0 0 0 3px rgba(99, 102, 241, 0.1)'"
                                   onblur="this.style.borderColor='#E5E7EB'; this.style.boxShadow='none'">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#9CA3AF" style="width: 16px; height: 16px; position: absolute; left: 10px; top: 50%; transform: translateY(-50%);">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </div>

                        {{-- Column Toggle Button --}}
                        {{-- <div style="position: relative;">
                            <button type="button" wire:click="toggleColumnVisibility"
                                    style="padding: 8px 12px; border: 1px solid #E5E7EB; border-radius: 6px; background: white; cursor: pointer; display: flex; align-items: center; gap: 6px; font-size: 13px; color: #6B7280; transition: all 0.2s;"
                                    onmouseover="this.style.background='#F9FAFB'; this.style.borderColor='#D1D5DB'"
                                    onmouseout="this.style.background='white'; this.style.borderColor='#E5E7EB'">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 4.5v15m6-15v15m-10.875 0h15.75c.621 0 1.125-.504 1.125-1.125V5.625c0-.621-.504-1.125-1.125-1.125H4.125C3.504 4.5 3 5.004 3 5.625v12.75c0 .621.504 1.125 1.125 1.125z" />
                                </svg>
                                Columns
                            </button>

                            @if($showColumnToggle)
                                <div style="position: absolute; top: calc(100% + 8px); right: 0; background: white; border: 1px solid #E5E7EB; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); z-index: 50; min-width: 180px; padding: 8px;"
                                     wire:click.stop>
                                    <div style="padding: 8px 12px; font-size: 12px; font-weight: 600; color: #6B7280; text-transform: uppercase; border-bottom: 1px solid #F3F4F6; margin-bottom: 4px;">
                                        Toggle Columns
                                    </div>

                                    <label style="display: flex; align-items: center; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                           onmouseover="this.style.background='#F9FAFB'"
                                           onmouseout="this.style.background='white'">
                                        <input type="checkbox" wire:click="toggleColumn('id')" {{ $visibleColumns['id'] ? 'checked' : '' }}
                                               style="margin-right: 8px; cursor: pointer;">
                                        <span style="font-size: 13px; color: #374151;">ID</span>
                                    </label>

                                    <label style="display: flex; align-items: center; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                           onmouseover="this.style.background='#F9FAFB'"
                                           onmouseout="this.style.background='white'">
                                        <input type="checkbox" wire:click="toggleColumn('module')" {{ $visibleColumns['module'] ? 'checked' : '' }}
                                               style="margin-right: 8px; cursor: pointer;">
                                        <span style="font-size: 13px; color: #374151;">Module</span>
                                    </label>

                                    <label style="display: flex; align-items: center; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                           onmouseover="this.style.background='#F9FAFB'"
                                           onmouseout="this.style.background='white'">
                                        <input type="checkbox" wire:click="toggleColumn('eta')" {{ $visibleColumns['eta'] ? 'checked' : '' }}
                                               style="margin-right: 8px; cursor: pointer;">
                                        <span style="font-size: 13px; color: #374151;">ETA</span>
                                    </label>

                                    <label style="display: flex; align-items: center; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                           onmouseover="this.style.background='#F9FAFB'"
                                           onmouseout="this.style.background='white'">
                                        <input type="checkbox" wire:click="toggleColumn('status')" {{ $visibleColumns['status'] ? 'checked' : '' }}
                                               style="margin-right: 8px; cursor: pointer;">
                                        <span style="font-size: 13px; color: #374151;">Status</span>
                                    </label>

                                    <label style="display: flex; align-items: center; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                           onmouseover="this.style.background='#F9FAFB'"
                                           onmouseout="this.style.background='white'">
                                        <input type="checkbox" wire:click="toggleColumn('frontend')" {{ $visibleColumns['frontend'] ? 'checked' : '' }}
                                               style="margin-right: 8px; cursor: pointer;">
                                        <span style="font-size: 13px; color: #374151;">Front End</span>
                                    </label>

                                    <label style="display: flex; align-items: center; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                           onmouseover="this.style.background='#F9FAFB'"
                                           onmouseout="this.style.background='white'">
                                        <input type="checkbox" wire:click="toggleColumn('passfail')" {{ $visibleColumns['passfail'] ? 'checked' : '' }}
                                               style="margin-right: 8px; cursor: pointer;">
                                        <span style="font-size: 13px; color: #374151;">Pass/Fail</span>
                                    </label>
                                </div>
                            @endif
                        </div> --}}

                        {{-- Filter Icon Button --}}
                        <button type="button" wire:click="openFilterModal"
                                style="padding: 8px 12px; border: 1px solid #E5E7EB; border-radius: 6px; background: white; cursor: pointer; display: flex; align-items: center; gap: 6px; font-size: 13px; color: #6B7280; transition: all 0.2s;"
                                onmouseover="this.style.background='#F9FAFB'; this.style.borderColor='#D1D5DB'"
                                onmouseout="this.style.background='white'; this.style.borderColor='#E5E7EB'">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                            </svg>
                            Filters
                            @if($selectedFrontEnd || $selectedTicketStatus || $etaStartDate || $etaEndDate)
                                <span style="background: #6366F1; color: white; border-radius: 10px; padding: 2px 6px; font-size: 11px; font-weight: 600;">
                                    {{ collect([$selectedFrontEnd, $selectedTicketStatus, $etaStartDate, $etaEndDate])->filter()->count() }}
                                </span>
                            @endif
                        </button>

                        {{-- Show individual status badges when In Progress or Closed is selected --}}
                        @if(($selectedStatus === 'In Progress' || $selectedStatus === 'Closed') && !empty($selectedCombinedStatuses))
                            @foreach($selectedCombinedStatuses as $individualStatus)
                                <span class="close-badge" wire:click="removeIndividualStatus('{{ $individualStatus }}')">
                                    {{ $individualStatus }} ✕
                                </span>
                            @endforeach
                        @elseif($selectedStatus || $selectedEnhancementStatus)
                            <span class="close-badge" wire:click="selectCategory(null, null)">
                                {{ $selectedStatus ?? $selectedEnhancementStatus }} ✕
                            </span>
                        @endif
                        @if($selectedEnhancementType)
                            <span class="close-badge" wire:click="selectEnhancementType(null)">
                                {{ ucfirst($selectedEnhancementType) }} Enhancement ✕
                            </span>
                        @endif
                        @if($selectedDate)
                            <span class="close-badge" wire:click="$set('selectedDate', null)">
                                {{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }} ✕
                            </span>
                        @endif
                        @if($selectedPriority)
                            <span class="close-badge" wire:click="$set('selectedPriority', null)">
                                Priority: {{ $selectedPriority }} ✕
                            </span>
                        @endif
                        @if($selectedProduct && $selectedProduct !== 'All Products')
                            <span class="close-badge" wire:click="$set('selectedProduct', 'All Products')">
                                Product: {{ $selectedProduct }} ✕
                            </span>
                        @endif
                        @if($selectedModule && $selectedModule !== 'All Modules')
                            <span class="close-badge" wire:click="$set('selectedModule', 'All Modules')">
                                Module: {{ $selectedModule }} ✕
                            </span>
                        @endif
                        @if($selectedFrontEnd)
                            <span class="close-badge" wire:click="$set('selectedFrontEnd', null)">
                                Front End: {{ $selectedFrontEnd }} ✕
                            </span>
                        @endif
                        @if($selectedTicketStatus)
                            <span class="close-badge" wire:click="$set('selectedTicketStatus', null)">
                                Status: {{ $selectedTicketStatus }} ✕
                            </span>
                        @endif
                        @if($etaStartDate)
                            <span class="close-badge" wire:click="$set('etaStartDate', null)">
                                From: {{ \Carbon\Carbon::parse($etaStartDate)->format('d M Y') }} ✕
                            </span>
                        @endif
                        @if($etaEndDate)
                            <span class="close-badge" wire:click="$set('etaEndDate', null)">
                                To: {{ \Carbon\Carbon::parse($etaEndDate)->format('d M Y') }} ✕
                            </span>
                        @endif
                        @if($searchTicketId)
                            <span class="close-badge" wire:click="$set('searchTicketId', '')">
                                ID: {{ $searchTicketId }} ✕
                            </span>
                        @endif
                    </div>
                </div>

                @if(count($tickets) > 0)
                    <div style="overflow-x: auto; position: relative;">
                        {{-- Column Toggle Button - Positioned Absolutely --}}
                        <div style="position: absolute; top: 12px; right: 24px; z-index: 40;">
                            <button type="button" wire:click="toggleColumnVisibility"
                                    style="cursor: pointer; display: inline-flex; align-items: center; gap: 4px; font-size: 12px; color: #6B7280; transition: all 0.2s;"
                                    onmouseover="this.style.background='#F9FAFB'; this.style.borderColor='#D1D5DB'"
                                    onmouseout="this.style.background='white'; this.style.borderColor='#E5E7EB'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5z"/>
                                </svg>
                            </button>

                            {{-- Column Toggle Dropdown --}}
                            @if($showColumnToggle)
                                <div style="position: absolute; top: calc(100% + 8px); right: 0; background: white; border: 1px solid #E5E7EB; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); z-index: 50; min-width: 180px; padding: 8px;"
                                     wire:click.stop>
                                    <div style="padding: 8px 12px; font-size: 12px; font-weight: 600; color: #6B7280; text-transform: uppercase; border-bottom: 1px solid #F3F4F6; margin-bottom: 4px;">
                                        Toggle Columns
                                    </div>

                                    <label style="display: flex; align-items: center; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                           onmouseover="this.style.background='#F9FAFB'"
                                           onmouseout="this.style.background='white'">
                                        <input type="checkbox" wire:click="toggleColumn('id')" {{ $visibleColumns['id'] ? 'checked' : '' }}
                                               style="margin-right: 8px; cursor: pointer;">
                                        <span style="font-size: 13px; color: #374151;">ID</span>
                                    </label>

                                    <label style="display: flex; align-items: center; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                           onmouseover="this.style.background='#F9FAFB'"
                                           onmouseout="this.style.background='white'">
                                        <input type="checkbox" wire:click="toggleColumn('module')" {{ $visibleColumns['module'] ? 'checked' : '' }}
                                               style="margin-right: 8px; cursor: pointer;">
                                        <span style="font-size: 13px; color: #374151;">Module</span>
                                    </label>

                                    <label style="display: flex; align-items: center; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                           onmouseover="this.style.background='#F9FAFB'"
                                           onmouseout="this.style.background='white'">
                                        <input type="checkbox" wire:click="toggleColumn('eta')" {{ $visibleColumns['eta'] ? 'checked' : '' }}
                                               style="margin-right: 8px; cursor: pointer;">
                                        <span style="font-size: 13px; color: #374151;">ETA</span>
                                    </label>

                                    <label style="display: flex; align-items: center; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                           onmouseover="this.style.background='#F9FAFB'"
                                           onmouseout="this.style.background='white'">
                                        <input type="checkbox" wire:click="toggleColumn('status')" {{ $visibleColumns['status'] ? 'checked' : '' }}
                                               style="margin-right: 8px; cursor: pointer;">
                                        <span style="font-size: 13px; color: #374151;">Status</span>
                                    </label>

                                    <label style="display: flex; align-items: center; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                           onmouseover="this.style.background='#F9FAFB'"
                                           onmouseout="this.style.background='white'">
                                        <input type="checkbox" wire:click="toggleColumn('frontend')" {{ $visibleColumns['frontend'] ? 'checked' : '' }}
                                               style="margin-right: 8px; cursor: pointer;">
                                        <span style="font-size: 13px; color: #374151;">Front End</span>
                                    </label>

                                    <label style="display: flex; align-items: center; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                           onmouseover="this.style.background='#F9FAFB'"
                                           onmouseout="this.style.background='white'">
                                        <input type="checkbox" wire:click="toggleColumn('completion_date')" {{ $visibleColumns['completion_date'] ? 'checked' : '' }}
                                               style="margin-right: 8px; cursor: pointer;">
                                        <span style="font-size: 13px; color: #374151;">Completion Date</span>
                                    </label>

                                    <label style="display: flex; align-items: center; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                           onmouseover="this.style.background='#F9FAFB'"
                                           onmouseout="this.style.background='white'">
                                        <input type="checkbox" wire:click="toggleColumn('overdue')" {{ $visibleColumns['overdue'] ? 'checked' : '' }}
                                               style="margin-right: 8px; cursor: pointer;">
                                        <span style="font-size: 13px; color: #374151;">Overdue</span>
                                    </label>

                                    <label style="display: flex; align-items: center; padding: 8px 12px; cursor: pointer; border-radius: 6px; transition: background 0.2s;"
                                           onmouseover="this.style.background='#F9FAFB'"
                                           onmouseout="this.style.background='white'">
                                        <input type="checkbox" wire:click="toggleColumn('passfail')" {{ $visibleColumns['passfail'] ? 'checked' : '' }}
                                               style="margin-right: 8px; cursor: pointer;">
                                        <span style="font-size: 13px; color: #374151;">Pass/Fail</span>
                                    </label>
                                </div>
                            @endif
                        </div>

                        <table style="width: 100%; border-collapse: collapse;">
                            <thead style="background: #FAFAFA; border-bottom: 2px solid #E5E7EB;">
                                <tr>
                                    @if($visibleColumns['id'])
                                        <th style="padding: 12px; text-align: left; font-size: 12px; color: #6B7280; font-weight: 600;">ID</th>
                                    @endif
                                    @if($visibleColumns['module'])
                                        <th style="padding: 12px; text-align: left; font-size: 12px; color: #6B7280; font-weight: 600;">MODULE</th>
                                    @endif
                                    @if($visibleColumns['eta'])
                                        <th style="padding: 12px; text-align: left; font-size: 12px; color: #6B7280; font-weight: 600; cursor: pointer;" wire:click="toggleEtaSort">
                                            ETA
                                            @if($etaSortDirection === 'asc')
                                                <span style="margin-left: 4px;">↑</span>
                                            @elseif($etaSortDirection === 'desc')
                                                <span style="margin-left: 4px;">↓</span>
                                            @endif
                                        </th>
                                    @endif
                                    @if($visibleColumns['status'])
                                        <th style="padding: 12px; text-align: left; font-size: 12px; color: #6B7280; font-weight: 600;">STATUS</th>
                                    @endif
                                    @if($visibleColumns['frontend'])
                                        <th style="padding: 12px; text-align: left; font-size: 12px; color: #6B7280; font-weight: 600;">FRONT END</th>
                                    @endif
                                    @if($visibleColumns['completion_date'])
                                        <th style="padding: 12px; text-align: left; font-size: 12px; color: #6B7280; font-weight: 600;">COMPLETION DATE</th>
                                    @endif
                                    @if($visibleColumns['overdue'])
                                        <th style="padding: 12px; text-align: left; font-size: 12px; color: #6B7280; font-weight: 600;">OVERDUE</th>
                                    @endif
                                    @if($visibleColumns['passfail'])
                                        <th style="padding: 12px; text-align: center; font-size: 12px; color: #6B7280; font-weight: 600;">PASS/FAIL</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tickets as $ticket)
                                    <tr style="border-bottom: 1px solid #F3F4F6;">
                                        @if($visibleColumns['id'])
                                            <td style="padding: 12px; font-size: 13px; font-weight: 600; cursor: pointer;"
                                                wire:click="viewTicket({{ $ticket->id }})"
                                                onmouseenter="showTicketTooltip(event, '{{ addslashes($ticket->module->name ?? '-') }}', '{{ addslashes($ticket->company_name ?? '-') }}', '{{ addslashes($ticket->title ?? '') }}')"
                                                onmouseleave="hideTooltip(event)">
                                                {{ $ticket->ticket_id }}
                                            </td>
                                        @endif
                                        @if($visibleColumns['module'])
                                            <td style="padding: 12px; font-size: 13px; cursor: pointer;" wire:click="viewTicket({{ $ticket->id }})">
                                                {{ $ticket->module->name ?? '-' }}
                                            </td>
                                        @endif
                                        @if($visibleColumns['eta'])
                                            <td style="padding: 12px; font-size: 13px; color: #6B7280; cursor: pointer;" wire:click="viewTicket({{ $ticket->id }})">
                                                {{ $ticket->eta_release ? $ticket->eta_release->addHours(8)->format('d M Y') : '-' }}
                                            </td>
                                        @endif
                                        @if($visibleColumns['status'])
                                            <td style="padding: 12px; cursor: pointer;" wire:click="viewTicket({{ $ticket->id }})">
                                                <span style="padding: 4px 8px; border-radius: 4px; font-size: 11px; background: #F3F4F6; color: #6B7280;">
                                                    {{ $ticket->status }}
                                                </span>
                                            </td>
                                        @endif
                                        @if($visibleColumns['frontend'])
                                            <td style="padding: 12px; font-size: 13px; cursor: pointer; max-width: 120px; word-break: break-word; line-height: 1.4;" wire:click="viewTicket({{ $ticket->id }})">
                                                {{ $ticket->requestor->name ?? $ticket->requestor ?? '-' }}
                                            </td>
                                        @endif
                                        @if($visibleColumns['completion_date'])
                                            <td style="padding: 12px; font-size: 13px; color: #6B7280; cursor: pointer;" wire:click="viewTicket({{ $ticket->id }})">
                                                @php
                                                    $completionLog = \Illuminate\Support\Facades\DB::connection('ticketingsystem_live')
                                                        ->table('ticket_logs')
                                                        ->where('ticket_id', $ticket->id)
                                                        ->whereIn('new_value', ['Completed', 'Live'])
                                                        ->orderBy('created_at', 'desc')
                                                        ->first();
                                                    $completionDate = $completionLog ? \Carbon\Carbon::parse($completionLog->created_at)->addHours(8)->format('d M Y H:i') : '-';
                                                @endphp
                                                {{ $completionDate }}
                                            </td>
                                        @endif
                                        @if($visibleColumns['overdue'])
                                            <td style="padding: 12px; font-size: 13px; cursor: pointer;" wire:click="viewTicket({{ $ticket->id }})">
                                                @php
                                                    $completionLog = \Illuminate\Support\Facades\DB::connection('ticketingsystem_live')
                                                        ->table('ticket_logs')
                                                        ->where('ticket_id', $ticket->id)
                                                        ->whereIn('new_value', ['Completed', 'Live'])
                                                        ->orderBy('created_at', 'desc')
                                                        ->first();

                                                    if ($completionLog) {
                                                        $completionDateTime = \Carbon\Carbon::parse($completionLog->created_at)->addHours(8)->startOfDay();
                                                        $today = \Carbon\Carbon::now()->startOfDay();
                                                        $daysDiff = $today->diffInDays($completionDateTime, false);

                                                        if ($daysDiff == 0) {
                                                            $overdueText = '0 day';
                                                            $overdueStyle = 'color: #6B7280;';
                                                        } else {
                                                            $overdueText = '-' . abs($daysDiff) . ' day' . (abs($daysDiff) > 1 ? 's' : '');
                                                            $overdueStyle = 'color: #DC2626; font-weight: 700;';
                                                        }
                                                    } else {
                                                        $overdueText = '-';
                                                        $overdueStyle = 'color: #6B7280;';
                                                    }
                                                @endphp
                                                <span style="{{ $overdueStyle }}">{{ $overdueText }}</span>
                                            </td>
                                        @endif
                                        @if($visibleColumns['passfail'])
                                            @php
                                                $authUser = auth()->user();
                                                $ticketSystemUser = null;
                                                if ($authUser) {
                                                    $ticketSystemUser = \Illuminate\Support\Facades\DB::connection('ticketingsystem_live')
                                                        ->table('users')
                                                        ->where('email', $authUser->email)
                                                        ->first();
                                                }
                                                $isOwner = $ticketSystemUser && $ticket->requestor_id == $ticketSystemUser->id;
                                            @endphp
                                            <td style="padding: 12px; text-align: center;" onclick="event.stopPropagation();">
                                                @if(in_array($ticket->status, ['Completed', 'Tickets: Live', 'Closed']))
                                                    @if($ticket->isPassed == 0)
                                                        @if($isOwner)
                                                            <div style="display: inline-flex; gap: 8px;">
                                                                <button wire:click="markAsPassed({{ $ticket->id }})"
                                                                        style="padding: 6px 12px; background: #10B981; color: white; border: none; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s;"
                                                                        onmouseover="this.style.background='#059669'"
                                                                        onmouseout="this.style.background='#10B981'">
                                                                    ✓ Pass
                                                                </button>
                                                                <button wire:click="markAsFailed({{ $ticket->id }})"
                                                                        style="padding: 6px 12px; background: #EF4444; color: white; border: none; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s;"
                                                                        onmouseover="this.style.background='#DC2626'"
                                                                        onmouseout="this.style.background='#EF4444'">
                                                                    ✕ Fail
                                                                </button>
                                                            </div>
                                                        @else
                                                            <div style="display: inline-flex; gap: 8px;">
                                                                <button disabled
                                                                        style="padding: 6px 12px; background: #D1D5DB; color: #9CA3AF; border: none; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: not-allowed; opacity: 0.5;">
                                                                    ✓ Pass
                                                                </button>
                                                                <button disabled
                                                                        style="padding: 6px 12px; background: #D1D5DB; color: #9CA3AF; border: none; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: not-allowed; opacity: 0.5;">
                                                                    ✕ Fail
                                                                </button>
                                                            </div>
                                                        @endif
                                                    @else
                                                        <div style="display: inline-flex; align-items: center; gap: 8px;">
                                                            <span style="padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; {{ $ticket->isPassed == 1 ? 'background: #D1FAE5; color: #059669;' : 'background: #FEE2E2; color: #DC2626;' }}">
                                                                {{ $ticket->isPassed == 1 ? '✓ Passed' : '✕ Failed' }}
                                                            </span>
                                                            @if($ticket->passed_at)
                                                                <span style="font-size: 11px; color: #9CA3AF;">
                                                                    {{ $ticket->passed_at->addHours(8)->format('d M Y H:i') }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                @else
                                                    <span style="font-size: 12px; color: #9CA3AF;">-</span>
                                                @endif
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($totalPages > 1)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 0; border-top: 1px solid #E5E7EB; margin-top: 16px;">
                            <div style="font-size: 13px; color: #6B7280;">
                                Showing {{ (($currentPage - 1) * $perPage) + 1 }} to {{ min($currentPage * $perPage, $totalTickets) }} of {{ $totalTickets }} tickets
                            </div>
                            <div style="display: flex; gap: 8px; align-items: center;">
                                {{-- Previous Button --}}
                                <button wire:click="previousPage"
                                        {{ $currentPage <= 1 ? 'disabled' : '' }}
                                        style="padding: 8px 12px; border: 1px solid #E5E7EB; border-radius: 6px; background: white; cursor: {{ $currentPage <= 1 ? 'not-allowed' : 'pointer' }}; font-size: 13px; color: {{ $currentPage <= 1 ? '#D1D5DB' : '#374151' }}; transition: all 0.2s;">
                                    ← Previous
                                </button>

                                {{-- Page Numbers --}}
                                <div style="display: flex; gap: 4px;">
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
                                        <button wire:click="goToPage(1)"
                                                style="width: 36px; height: 36px; border: 1px solid #E5E7EB; border-radius: 6px; background: white; cursor: pointer; font-size: 13px; color: #374151;">
                                            1
                                        </button>
                                        @if($startPage > 2)
                                            <span style="padding: 8px 4px; color: #9CA3AF;">...</span>
                                        @endif
                                    @endif

                                    @for($page = $startPage; $page <= $endPage; $page++)
                                        <button wire:click="goToPage({{ $page }})"
                                                style="width: 36px; height: 36px; border: 1px solid {{ $page == $currentPage ? '#6366F1' : '#E5E7EB' }}; border-radius: 6px; background: {{ $page == $currentPage ? '#6366F1' : 'white' }}; cursor: pointer; font-size: 13px; color: {{ $page == $currentPage ? 'white' : '#374151' }}; font-weight: {{ $page == $currentPage ? '600' : '400' }};">
                                            {{ $page }}
                                        </button>
                                    @endfor

                                    @if($endPage < $totalPages)
                                        @if($endPage < $totalPages - 1)
                                            <span style="padding: 8px 4px; color: #9CA3AF;">...</span>
                                        @endif
                                        <button wire:click="goToPage({{ $totalPages }})"
                                                style="width: 36px; height: 36px; border: 1px solid #E5E7EB; border-radius: 6px; background: white; cursor: pointer; font-size: 13px; color: #374151;">
                                            {{ $totalPages }}
                                        </button>
                                    @endif
                                </div>

                                {{-- Next Button --}}
                                <button wire:click="nextPage"
                                        {{ $currentPage >= $totalPages ? 'disabled' : '' }}
                                        style="padding: 8px 12px; border: 1px solid #E5E7EB; border-radius: 6px; background: white; cursor: {{ $currentPage >= $totalPages ? 'not-allowed' : 'pointer' }}; font-size: 13px; color: {{ $currentPage >= $totalPages ? '#D1D5DB' : '#374151' }}; transition: all 0.2s;">
                                    Next →
                                </button>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="empty-tickets">
                        <div class="empty-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M8 15s1.5 2 4 2 4-2 4-2"/>
                                <line x1="9" y1="9" x2="9.01" y2="9"/>
                                <line x1="15" y1="9" x2="15.01" y2="9"/>
                            </svg>
                        </div>
                        <div class="empty-text">No tickets found for this filter</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Ticket Modal Component --}}
    <livewire:ticket-modal />

    {{-- Filter Modal --}}
    @if($showFilterModal)
        <div style="position: fixed; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; background: rgba(0, 0, 0, 0.5);"
             wire:click="closeFilterModal">
            <div style="background: white; border-radius: 16px; padding: 0; width: 700px; max-width: 90vw; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); max-height: 90vh; overflow: hidden; display: flex; flex-direction: column;"
                 wire:click.stop>

                {{-- Modal Header --}}
                <div style="padding: 24px 28px; border-bottom: 2px solid #F3F4F6; background: linear-gradient(to bottom, #ffffff, #fafbfc);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <h3 style="font-size: 20px; font-weight: 700; color: #111827; margin: 0; line-height: 1.3;">Filter Tickets</h3>
                        </div>
                        <button wire:click="closeFilterModal"
                                style="background: #F3F4F6; border: none; color: #6B7280; cursor: pointer; padding: 8px; border-radius: 8px; font-size: 20px; line-height: 1; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; transition: all 0.2s;"
                                onmouseover="this.style.background='#E5E7EB'; this.style.color='#374151'"
                                onmouseout="this.style.background='#F3F4F6'; this.style.color='#6B7280'">
                            ×
                        </button>
                    </div>
                </div>

                {{-- Filter Form - Scrollable --}}
                <div style="padding: 28px; overflow-y: auto; flex: 1;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">

                        {{-- Priority Filter --}}
                        <div>
                            <label style="display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 8px;">
                                Priority
                            </label>
                            <select class="ticket-filter-select" wire:model="selectedPriority"
                                    style="width: 100%; padding: 11px 14px; border: 1.5px solid #D1D5DB; border-radius: 10px; font-size: 14px; background: white; transition: all 0.2s; color: #374151; font-weight: 500;"
                                    onfocus="this.style.borderColor='#6366F1'; this.style.boxShadow='0 0 0 3px rgba(99, 102, 241, 0.1)'"
                                    onblur="this.style.borderColor='#D1D5DB'; this.style.boxShadow='none'">
                                <option value="">All Priorities</option>
                                @foreach($priorities as $priority)
                                    <option value="{{ $priority['name'] }}">{{ $priority['label'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Product Filter --}}
                        <div>
                            <label style="display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 8px;">
                                Product
                            </label>
                            <select class="ticket-filter-select" wire:model="selectedProduct"
                                    style="width: 100%; padding: 11px 14px; border: 1.5px solid #D1D5DB; border-radius: 10px; font-size: 14px; background: white; transition: all 0.2s; color: #374151; font-weight: 500;"
                                    onfocus="this.style.borderColor='#10B981'; this.style.boxShadow='0 0 0 3px rgba(16, 185, 129, 0.1)'"
                                    onblur="this.style.borderColor='#D1D5DB'; this.style.boxShadow='none'">
                                <option>All Products</option>
                                @foreach($products as $product)
                                    <option value="{{ $product }}">{{ $product }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Module Filter --}}
                        <div>
                            <label style="display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 8px;">
                                Module
                            </label>
                            <select class="ticket-filter-select" wire:model="selectedModule"
                                    style="width: 100%; padding: 11px 14px; border: 1.5px solid #D1D5DB; border-radius: 10px; font-size: 14px; background: white; transition: all 0.2s; color: #374151; font-weight: 500;"
                                    onfocus="this.style.borderColor='#F59E0B'; this.style.boxShadow='0 0 0 3px rgba(245, 158, 11, 0.1)'"
                                    onblur="this.style.borderColor='#D1D5DB'; this.style.boxShadow='none'">
                                <option>All Modules</option>
                                @foreach($modules as $module)
                                    <option value="{{ $module }}">{{ $module }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Front End Filter --}}
                        <div>
                            <label style="display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 8px;">
                                Front End
                            </label>
                            <select class="ticket-filter-select" wire:model="selectedFrontEnd"
                                    style="width: 100%; padding: 11px 14px; border: 1.5px solid #D1D5DB; border-radius: 10px; font-size: 14px; background: white; transition: all 0.2s; color: #374151; font-weight: 500;"
                                    onfocus="this.style.borderColor='#EC4899'; this.style.boxShadow='0 0 0 3px rgba(236, 72, 153, 0.1)'"
                                    onblur="this.style.borderColor='#D1D5DB'; this.style.boxShadow='none'">
                                <option value="">All Front End</option>
                                @foreach($frontEndNames as $frontEnd)
                                    <option value="{{ $frontEnd }}">{{ $frontEnd }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Status Filter - Full Width --}}
                        <div style="grid-column: 1 / -1;">
                            <label style="display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 8px;">
                                Ticket Status
                            </label>
                            <select class="ticket-filter-select" wire:model="selectedTicketStatus"
                                    style="width: 100%; padding: 11px 14px; border: 1.5px solid #D1D5DB; border-radius: 10px; font-size: 14px; background: white; transition: all 0.2s; color: #374151; font-weight: 500;"
                                    onfocus="this.style.borderColor='#8B5CF6'; this.style.boxShadow='0 0 0 3px rgba(139, 92, 246, 0.1)'"
                                    onblur="this.style.borderColor='#D1D5DB'; this.style.boxShadow='none'">
                                <option value="">All Status</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}">{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- ETA Date Range - Full Width with Better Design --}}
                        <div style="grid-column: 1 / -1;">
                            <label style="display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 12px;">
                                ETA Date Range
                            </label>
                            <div style="display: grid; grid-template-columns: 1fr auto 1fr; gap: 12px; align-items: center;">
                                <div style="position: relative;">
                                    <label style="display: block; font-size: 11px; color: #6B7280; margin-bottom: 6px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Start Date</label>
                                    <input type="date" wire:model="etaStartDate"
                                           style="width: 100%; padding: 11px 14px; border: 1.5px solid #D1D5DB; border-radius: 10px; font-size: 14px; background: white; transition: all 0.2s; color: #374151; font-weight: 500;"
                                           onfocus="this.style.borderColor='#EF4444'; this.style.boxShadow='0 0 0 3px rgba(239, 68, 68, 0.1)'"
                                           onblur="this.style.borderColor='#D1D5DB'; this.style.boxShadow='none'">
                                </div>
                                <div style="padding-top: 20px; color: #9CA3AF; font-weight: 600;">→</div>
                                <div style="position: relative;">
                                    <label style="display: block; font-size: 11px; color: #6B7280; margin-bottom: 6px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">End Date</label>
                                    <input type="date" wire:model="etaEndDate"
                                           style="width: 100%; padding: 11px 14px; border: 1.5px solid #D1D5DB; border-radius: 10px; font-size: 14px; background: white; transition: all 0.2s; color: #374151; font-weight: 500;"
                                           onfocus="this.style.borderColor='#EF4444'; this.style.boxShadow='0 0 0 3px rgba(239, 68, 68, 0.1)'"
                                           onblur="this.style.borderColor='#D1D5DB'; this.style.boxShadow='none'">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Modal Footer --}}
                <div style="padding: 20px 28px; border-top: 2px solid #F3F4F6; background: #FAFBFC; display: flex; justify-content: space-between; align-items: center;">
                    <button type="button" wire:click="clearAllFilters"
                            style="padding: 10px 20px; background: white; border: 1.5px solid #E5E7EB; border-radius: 10px; font-size: 14px; color: #6B7280; font-weight: 600; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 8px;"
                            onmouseover="this.style.background='#FEF2F2'; this.style.borderColor='#FCA5A5'; this.style.color='#DC2626'"
                            onmouseout="this.style.background='white'; this.style.borderColor='#E5E7EB'; this.style.color='#6B7280'">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Clear All Filters
                    </button>
                    <button type="button" wire:click="closeFilterModal"
                            style="padding: 10px 28px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 10px; font-size: 14px; color: white; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 6px -1px rgba(102, 126, 234, 0.3); display: flex; align-items: center; gap: 8px;"
                            onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 12px -2px rgba(102, 126, 234, 0.4)'"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(102, 126, 234, 0.3)'">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        Apply Filters
                    </button>
                </div>
            </div>
        </div>
    @endif

    <x-filament-actions::modals />
</x-filament-panels::page>
