{{-- filepath: /var/www/html/timeteccrm/resources/views/customer/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - TimeTec CRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.49.1/dist/apexcharts.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @livewireStyles

    <style>
        :root {
            --tt-primary: #00a4e0;
            --tt-accent-dark: #003c75;
            --tt-accent-mid: #1a6dd4;
            --tt-hover-bg: #f0f7ff;
            --tt-border: #e5e7eb;
            --tt-text-secondary: #6b7280;
            --tt-text-muted: #9ca3af;
            --tt-danger: #ef4444;
            --tt-surface: #ffffff;
            --tt-page-bg: #F4F8FC;
        }

        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: var(--tt-page-bg);
            background-image: url('/img/bg-gra.jpg');
            background-repeat: no-repeat;
            background-position: top center;
            background-size: 100% auto;
            background-attachment: fixed;
        }

        /* TimeTec Header */
        .tt-header {
            background: var(--tt-surface);
            border-bottom: 1px solid var(--tt-border);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            height: 56px;
        }

        .tt-header-inner {
            height: 100%;
            max-width: 100%;
            margin: 0 auto;
            padding: 0 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }

        @media (min-width: 1024px) {
            .tt-header-inner {
                padding: 0 1.5rem;
            }
        }

        .tt-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .tt-brand-logo {
            width: 34px;
            height: 34px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .tt-brand-title {
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: var(--tt-accent-dark);
            letter-spacing: -0.01em;
            white-space: nowrap;
        }

        .tt-header-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .tt-header-info {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 1px;
            line-height: 1.25;
            white-space: nowrap;
        }

        .tt-info-row {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
        }

        .tt-info-row i {
            font-size: 11px;
            color: var(--tt-accent-dark);
            width: 13px;
            text-align: center;
        }

        .tt-info-label {
            color: var(--tt-text-secondary);
            font-weight: 400;
        }

        .tt-info-value {
            color: var(--tt-accent-dark);
            font-weight: 600;
        }

        .tt-info-implementer .tt-info-row i {
            color: var(--tt-text-secondary);
        }

        .tt-info-implementer .tt-info-value {
            color: var(--tt-text-secondary);
            font-weight: 500;
            font-size: 11px;
        }

        .tt-info-implementer .tt-info-label {
            font-size: 11px;
        }

        .tt-header-divider {
            width: 1px;
            height: 28px;
            background: var(--tt-border);
            flex-shrink: 0;
        }

        .tt-logout-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            font-family: 'Poppins', sans-serif;
            font-size: 12px;
            font-weight: 600;
            color: var(--tt-danger);
            background: var(--tt-surface);
            border: 1.25px solid var(--tt-danger);
            border-radius: 999px;
            cursor: pointer;
            transition: all 0.2s ease;
            line-height: 1;
        }

        .tt-logout-btn i {
            font-size: 11px;
        }

        .tt-logout-btn:hover {
            background: var(--tt-danger);
            color: var(--tt-surface);
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.25);
        }

        @media (max-width: 768px) {
            .tt-header-info,
            .tt-header-divider {
                display: none;
            }
            .tt-brand-title {
                font-size: 13px;
            }
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .stats-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .stats-card-2 {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .stats-card-3 {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        /* Header Styles */
        .main-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
        }

        /* Sidebar Styles — collapsed rail, expand on hover */
        .sidebar {
            position: fixed;
            left: 0;
            top: 56px;
            bottom: 0;
            width: 64px;
            z-index: 50;
            background-color: var(--tt-page-bg);
            border-right: 1px solid var(--tt-border);
            overflow-x: hidden;
            overflow-y: auto;
            transition: width 0.28s cubic-bezier(.4, 0, .2, 1),
                        box-shadow 0.28s ease;
        }

        .sidebar:hover,
        .sidebar:focus-within {
            width: 280px;
            box-shadow: 6px 0 24px rgba(15, 23, 42, 0.06);
        }

        .sidebar.sidebar-force-collapsed,
        .sidebar.sidebar-force-collapsed:hover,
        .sidebar.sidebar-force-collapsed:focus-within {
            width: 64px;
            box-shadow: none;
        }

        .sidebar-menu {
            padding: 14px 8px;
            font-size: 13px;
            transition: padding 0.28s ease;
            display: flex;
            flex-direction: column;
            min-height: 100%;
        }

        .sidebar:hover .sidebar-menu,
        .sidebar:focus-within .sidebar-menu {
            padding: 14px;
        }

        /* Collapsed-state overrides — hide labels, chevrons, submenus; center icons */
        .sidebar:not(:hover):not(:focus-within) .menu-item,
        .sidebar:not(:hover):not(:focus-within) .menu-group-header,
        .sidebar.sidebar-force-collapsed .menu-item,
        .sidebar.sidebar-force-collapsed .menu-group-header {
            justify-content: center;
            padding: 10px 0;
            position: relative;
        }

        .sidebar:not(:hover):not(:focus-within) .menu-item > span:not(.sidebar-badge),
        .sidebar:not(:hover):not(:focus-within) .menu-group-header > span:not(.sidebar-badge),
        .sidebar:not(:hover):not(:focus-within) .menu-group-chevron,
        .sidebar.sidebar-force-collapsed .menu-item > span:not(.sidebar-badge),
        .sidebar.sidebar-force-collapsed .menu-group-header > span:not(.sidebar-badge),
        .sidebar.sidebar-force-collapsed .menu-group-chevron {
            display: none;
        }

        .sidebar:not(:hover):not(:focus-within) .menu-sub-items,
        .sidebar.sidebar-force-collapsed .menu-sub-items {
            display: none !important;
        }

        .sidebar:not(:hover):not(:focus-within) .menu-item:hover,
        .sidebar:not(:hover):not(:focus-within) .menu-group-header:hover,
        .sidebar.sidebar-force-collapsed .menu-item:hover,
        .sidebar.sidebar-force-collapsed .menu-group-header:hover {
            transform: none;
        }

        .sidebar:not(:hover):not(:focus-within) .menu-item i,
        .sidebar:not(:hover):not(:focus-within) .menu-group-header i,
        .sidebar.sidebar-force-collapsed .menu-item i,
        .sidebar.sidebar-force-collapsed .menu-group-header i {
            font-size: 16px;
            width: 20px;
        }

        /* Badge → small red dot at icon corner when collapsed */
        .sidebar:not(:hover):not(:focus-within) .sidebar-badge,
        .sidebar.sidebar-force-collapsed .sidebar-badge {
            position: absolute;
            top: 6px;
            right: 12px;
            min-width: 8px;
            height: 8px;
            padding: 0;
            font-size: 0;
            line-height: 1;
            border-radius: 50%;
            margin-left: 0;
            box-shadow: 0 0 0 2px var(--tt-page-bg);
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            margin-bottom: 4px;
            border-radius: 8px;
            color: #64748b;
            transition: all 0.25s ease;
            cursor: pointer;
            font-weight: 500;
            border: none;
            background: transparent;
            width: 100%;
            text-align: left;
            white-space: nowrap;
            min-width: 0;
        }

        .menu-item > span:not(.sidebar-badge):not(.menu-dot) {
            flex: 1 1 auto;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .menu-item:hover {
            background: #f1f5f9;
            color: #667eea;
            transform: translateX(2px);
        }

        .menu-item.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 3px 10px rgba(102, 126, 234, 0.28);
        }

        .menu-item i {
            font-size: 14px;
            width: 18px;
            text-align: center;
        }

        /* Collapsible menu group */
        .menu-group-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            margin-bottom: 2px;
            border-radius: 8px;
            color: #64748b;
            font-weight: 500;
            cursor: pointer;
            border: none;
            background: transparent;
            width: 100%;
            text-align: left;
            transition: all 0.25s ease;
        }
        .menu-group-header:hover {
            background: #f1f5f9;
            color: #667eea;
        }
        .menu-group-header.has-active {
            color: #667eea;
        }
        .menu-group-header i {
            font-size: 14px;
            width: 18px;
            text-align: center;
        }
        .menu-group-chevron {
            width: 12px;
            height: 12px;
            margin-left: auto;
            transition: transform 0.2s;
            color: #9CA3AF;
        }
        .menu-group-chevron.open {
            transform: rotate(90deg);
        }
        .menu-sub-items {
            padding-left: 14px;
        }
        .menu-sub-items .menu-item {
            padding: 6px 10px;
            font-size: 12px;
            margin-bottom: 2px;
            white-space: normal;
            line-height: 1.3;
        }
        .menu-sub-items .menu-item i {
            font-size: 12px;
            width: 16px;
        }
        .menu-sub-items .menu-item .menu-dot {
            width: 16px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            flex-shrink: 0;
        }
        .menu-sub-items .menu-item .menu-dot::before {
            content: "";
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: currentColor;
            opacity: 0.7;
        }
        .menu-sub-items .menu-item.active .menu-dot::before {
            opacity: 1;
        }

        /* Sidebar notification badge */
        .sidebar-badge {
            background: #EF4444;
            color: white;
            font-size: 10px;
            font-weight: 700;
            min-width: 16px;
            height: 16px;
            padding: 0 5px;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-left: auto;
            line-height: 1;
        }

        .menu-group-header .sidebar-badge {
            margin-left: 0;
        }

        .menu-item.active .sidebar-badge {
            background: white;
            color: #667eea;
        }

        /* Main Content with Sidebar */
        .main-wrapper {
            margin-left: 64px;
            padding: 56px 1.5rem 1.5rem;
            flex: 1;
            min-width: 0;
        }

        @media (max-width: 1024px) {
            .main-wrapper {
                padding: 56px 1rem 1rem;
            }
        }

        .tt-content {
            max-width: 1480px;
            margin: 0 auto;
            width: 100%;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0;
        }

        .tab-content {
            padding: 1rem 0;
            flex: 1;
            min-height: 0;
        }

        .tab-content.active {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body class="min-h-screen">
    @php
        $customer = Auth::guard('customer')->user();
        $companyName = $customer->company_name ?? 'Not Available';

        // Get the latest software handover based on lead_id
        $implementerName = 'Not Available';
        $hasProjectPlan = false;

        if ($customer->lead_id) {
            $latestHandover = \App\Models\SoftwareHandover::where('lead_id', $customer->lead_id)
                ->orderBy('id', 'desc')
                ->first();

            if ($latestHandover) {
                $implementerName = $latestHandover->implementer ?? 'Not Assigned';

                // Check if there are any project plans for this lead
                $projectPlansCount = \App\Models\ProjectPlan::where('lead_id', $customer->lead_id)
                    ->where('sw_id', $latestHandover->id)
                    ->count();

                $hasProjectPlan = $projectPlansCount > 0;
            }
        }
        // Implementer Thread notification badge count (Open + Waiting on You)
        $impThreadBadgeCount = 0;
        if ($customer && $customer->lead_id) {
            $impThreadBadgeCount = \App\Models\ImplementerTicket::where('lead_id', $customer->lead_id)
                ->whereIn('status', ['open', 'pending_rnd', 'pending_client'])
                ->count();
        }

        $hasKickOffBooking = $customer?->hasBookedKickOff() ?? false;
    @endphp

    <!-- TimeTec Header -->
    <header class="main-header tt-header" style="overflow: visible;">
        <div class="tt-header-inner">
            <div class="tt-brand">
                <img src="{{ asset('img/logo-ttc.png') }}" alt="TimeTec" class="tt-brand-logo">
                <span class="tt-brand-title">Customer Portal</span>
            </div>
            <div class="tt-header-actions">
                <div class="tt-header-info">
                    <div class="tt-info-row tt-info-company">
                        <i class="fas fa-building"></i>
                        <span class="tt-info-label">Company:</span>
                        <span class="tt-info-value">{{ $companyName }}</span>
                    </div>
                    <div class="tt-info-row tt-info-implementer">
                        <i class="fas fa-user"></i>
                        <span class="tt-info-label">Implementer:</span>
                        <span class="tt-info-value">{{ $implementerName }}</span>
                    </div>
                </div>
                <div class="tt-header-divider"></div>
                @livewire('customer-notification-bell')
                <div class="tt-header-divider"></div>
                <form method="POST" action="{{ route('filament.customer.auth.logout') }}">
                    @csrf
                    <button type="submit" class="tt-logout-btn">
                        <i class="fas fa-right-from-bracket"></i>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Left Sidebar (Below Header) -->
    <div class="sidebar">
        <div class="sidebar-menu">
            {{-- Dashboard — top-level item --}}
            <button onclick="switchTab('dashboard')"
                    id="dashboard-tab"
                    class="menu-item"
                    style="margin-bottom: 12px;">
                <i class="fas fa-th-large"></i>
                <span>Dashboard</span>
            </button>

            {{-- Implementation — collapsible group --}}
            <button class="menu-group-header" data-group="implementation" onclick="toggleGroup('implementation')">
                <i class="fas fa-book"></i>
                <span>Implementation</span>
                @if($impThreadBadgeCount > 0)
                    <span id="implementation-badge" class="sidebar-badge">{{ $impThreadBadgeCount }}</span>
                @endif
                <svg id="implementation-chevron" class="menu-group-chevron" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
                </svg>
            </button>
            <div id="implementation-sub" class="menu-sub-items" style="display: none;">
                <button onclick="switchTab('calendar')"
                        id="calendar-tab"
                        class="menu-item">
                    <span class="menu-dot" aria-hidden="true"></span>
                    <span>Meeting Schedule</span>
                </button>
                <button onclick="switchTab('softwareHandover')"
                        id="softwareHandover-tab"
                        class="menu-item">
                    <span class="menu-dot" aria-hidden="true"></span>
                    <span>Software Handover Process</span>
                </button>
                @if($hasProjectPlan)
                    <button onclick="switchTab('project')"
                            id="project-tab"
                            class="menu-item">
                        <span class="menu-dot" aria-hidden="true"></span>
                        <span>Project Plan</span>
                    </button>
                @endif
                <button onclick="switchTab('impThread')"
                        id="impThread-tab"
                        class="menu-item">
                    <span class="menu-dot" aria-hidden="true"></span>
                    <span>Implementer Thread</span>
                    @if($impThreadBadgeCount > 0)
                        <span class="sidebar-badge">{{ $impThreadBadgeCount }}</span>
                    @endif
                </button>
                <button onclick="switchTab('dataMigration')"
                        id="dataMigration-tab"
                        class="menu-item">
                    <span class="menu-dot" aria-hidden="true"></span>
                    <span>Data File</span>
                </button>
            </div>

            {{-- Training — collapsible group --}}
            <button class="menu-group-header" data-group="training" onclick="toggleGroup('training')">
                <i class="fas fa-book"></i>
                <span>Training</span>
                <svg id="training-chevron" class="menu-group-chevron" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
                </svg>
            </button>
            <div id="training-sub" class="menu-sub-items" style="display: none;">
                <button onclick="switchTab('webinar')"
                        id="webinar-tab"
                        class="menu-item">
                    <span class="menu-dot" aria-hidden="true"></span>
                    <span>Webinar Recording & Training Decks</span>
                </button>
                <button onclick="switchTab('reviewSession')"
                        id="reviewSession-tab"
                        class="menu-item">
                    <span class="menu-dot" aria-hidden="true"></span>
                    <span>Review Session Recordings</span>
                </button>
            </div>

            {{-- Support — collapsible group --}}
            <button class="menu-group-header" data-group="support" onclick="toggleGroup('support')">
                <i class="fas fa-book"></i>
                <span>Support</span>
                <svg id="support-chevron" class="menu-group-chevron" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
                </svg>
            </button>
            <div id="support-sub" class="menu-sub-items" style="display: none;">
                <a href="/customer/implementer-tickets" class="menu-item" style="text-decoration: none;">
                    <span class="menu-dot" aria-hidden="true"></span>
                    <span>Support Thread</span>
                </a>
            </div>

            {{-- Knowledge Base — collapsible group --}}
            <button class="menu-group-header" data-group="knowledgebase" onclick="toggleGroup('knowledgebase')">
                <i class="fas fa-book"></i>
                <span>Knowledge Base</span>
                <svg id="knowledgebase-chevron" class="menu-group-chevron" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
                </svg>
            </button>
            <div id="knowledgebase-sub" class="menu-sub-items" style="display: none;">
                <div style="padding: 8px 16px 8px 36px; color: #94a3b8; font-size: 13px; font-style: italic;">
                    Coming Soon
                </div>
            </div>

            {{-- Commercial — collapsible group --}}
            <button class="menu-group-header" data-group="commercial" onclick="toggleGroup('commercial')">
                <i class="fas fa-book"></i>
                <span>Commercial</span>
                <svg id="commercial-chevron" class="menu-group-chevron" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
                </svg>
            </button>
            <div id="commercial-sub" class="menu-sub-items" style="display: none;">
                <div style="padding: 8px 16px 8px 36px; color: #94a3b8; font-size: 13px; font-style: italic;">
                    Coming Soon
                </div>
            </div>

            {{-- Settings — collapsible group (anchored to bottom of sidebar) --}}
            <button class="menu-group-header" data-group="settings" onclick="toggleGroup('settings')" style="margin-top: auto;">
                <i class="fas fa-book"></i>
                <span>Settings</span>
                <svg id="settings-chevron" class="menu-group-chevron" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
                </svg>
            </button>
            <div id="settings-sub" class="menu-sub-items" style="display: none;">
                <div style="padding: 8px 16px 8px 36px; color: #94a3b8; font-size: 13px; font-style: italic;">
                    Coming Soon
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Wrapper -->
    <div class="main-wrapper">
        <!-- Main Content -->
        <main class="tt-content">
            <!-- Dashboard Tab Content (default landing) -->
            <div id="dashboard-content" class="tab-content" style="display: block;">
                @livewire('customer-dashboard')
            </div>

            <!-- Calendar Tab Content -->
            <div id="calendar-content" class="tab-content" style="display: none;">
                @livewire('customer-calendar')
            </div>

            {{-- Software Handover Process --}}
            <div id="softwareHandover-content" class="tab-content" style="display: none; min-height: 600px;">
                @livewire('customer-software-handover-process')
            </div>

            <!-- Project Plan Tab Content -->
            @if($hasProjectPlan)
                <div id="project-content" class="tab-content" style="display: none; min-height: 600px;">
                    @livewire('customer-project-plan')
                </div>
            @endif

            {{-- Data Migration Templates --}}
            <div id="dataMigration-content" class="tab-content" style="display: none;">
                @livewire('customer-data-migration-templates')
            </div>

            {{-- Webinar Recording & Training Decks --}}
            <div id="webinar-content" class="tab-content" style="display: none;">
                @livewire('customer-training-files')
            </div>

            {{-- Review Session Recordings --}}
            <div id="reviewSession-content" class="tab-content" style="display: none;">
                <div style="text-align: center; padding: 60px 20px; color: #94a3b8;">
                    <i class="fas fa-play-circle" style="font-size: 48px; margin-bottom: 16px; display: block;"></i>
                    <h3 style="font-size: 18px; font-weight: 600; color: #475569;">Review Session Recordings</h3>
                    <p>Coming soon</p>
                </div>
            </div>

            {{-- Implementer Thread --}}
            <div id="impThread-content" class="tab-content" style="display: none;">
                @livewire('customer-implementer-thread')
            </div>
        </main>
    </div>


    @livewireScripts

    <!-- Enhanced JavaScript -->
    <script>
        const hasProjectPlan = @json($hasProjectPlan);
        const hasKickOffBooking = @json($hasKickOffBooking);
        const implementationTabs = ['calendar', 'softwareHandover', 'project', 'dataMigration', 'impThread'];
        const trainingTabs = ['webinar', 'reviewSession'];
        const groupMap = {
            'implementation': implementationTabs,
            'training': trainingTabs,
        };

        function getGroupForTab(tab) {
            for (const [group, tabs] of Object.entries(groupMap)) {
                if (tabs.includes(tab)) return group;
            }
            return null;
        }

        function toggleGroup(group) {
            const sub = document.getElementById(group + '-sub');
            const chevron = document.getElementById(group + '-chevron');
            const badge = document.getElementById(group + '-badge');
            if (sub.style.display === 'none') {
                sub.style.display = 'block';
                chevron.classList.add('open');
                if (badge) badge.style.display = 'none';
                localStorage.setItem(group + 'Open', 'true');
            } else {
                sub.style.display = 'none';
                chevron.classList.remove('open');
                if (badge) badge.style.display = '';
                localStorage.setItem(group + 'Open', 'false');
            }
        }

        function switchTab(tab) {
            // Hide all tab contents using inline !important
            document.querySelectorAll('.tab-content').forEach(content => {
                content.style.setProperty('display', 'none', 'important');
                content.classList.remove('active');
            });

            // Remove active class from all buttons
            document.querySelectorAll('.menu-item').forEach(button => {
                button.classList.remove('active');
            });

            // Auto-expand parent group if sub-item selected
            const parentGroup = getGroupForTab(tab);
            if (parentGroup) {
                const sub = document.getElementById(parentGroup + '-sub');
                const chevron = document.getElementById(parentGroup + '-chevron');
                if (sub) sub.style.display = 'block';
                if (chevron) chevron.classList.add('open');
                localStorage.setItem(parentGroup + 'Open', 'true');
            }

            // Highlight parent group headers
            document.querySelectorAll('.menu-group-header').forEach(header => {
                const group = header.getAttribute('data-group');
                const tabs = groupMap[group];
                header.classList.toggle('has-active', tabs ? tabs.includes(tab) : false);
            });

            // Show selected tab content using inline !important
            const tabContent = document.getElementById(tab + '-content');
            const tabButton = document.getElementById(tab + '-tab');

            if (tabContent && tabButton) {
                tabContent.style.setProperty('display', 'block', 'important');
                tabContent.classList.add('active');
                tabButton.classList.add('active');
                localStorage.setItem('activeTab', tab);
            }

            collapseSidebarAfterClick();
        }

        function collapseSidebarAfterClick() {
            const sidebar = document.querySelector('.sidebar');
            if (!sidebar) return;

            if (document.activeElement instanceof HTMLElement) {
                document.activeElement.blur();
            }

            sidebar.classList.add('sidebar-force-collapsed');

            let cleared = false;
            const clear = () => {
                if (cleared) return;
                cleared = true;
                sidebar.classList.remove('sidebar-force-collapsed');
                sidebar.removeEventListener('mouseleave', clear);
            };
            sidebar.addEventListener('mouseleave', clear);
            setTimeout(clear, 1500);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Check URL query params for notification deep links
            const urlParams = new URLSearchParams(window.location.search);
            const urlTab = urlParams.get('tab');
            const urlTicket = urlParams.get('ticket');

            let activeTab;
            if (urlTab) {
                activeTab = urlTab;
            } else if (!hasKickOffBooking) {
                activeTab = 'calendar';
            } else {
                activeTab = localStorage.getItem('activeTab') || 'dashboard';
            }

            // If project plan doesn't exist and user tries to access it, fallback to dashboard
            if (activeTab === 'project' && !hasProjectPlan) {
                activeTab = 'dashboard';
                localStorage.setItem('activeTab', 'dashboard');
            }

            // Force initial display state via inline !important
            document.querySelectorAll('.tab-content').forEach(content => {
                content.style.setProperty('display', 'none', 'important');
            });

            // Restore all group states
            ['implementation', 'training', 'support', 'commercial'].forEach(function(group) {
                const tabs = groupMap[group] || [];
                const shouldOpen = localStorage.getItem(group + 'Open') === 'true' || tabs.includes(activeTab);
                if (shouldOpen) {
                    const sub = document.getElementById(group + '-sub');
                    const chevron = document.getElementById(group + '-chevron');
                    const badge = document.getElementById(group + '-badge');
                    if (sub) sub.style.display = 'block';
                    if (chevron) chevron.classList.add('open');
                    if (badge) badge.style.display = 'none';
                }
            });

            // Always expand Implementation on first load if no saved state
            if (!localStorage.getItem('implementationOpen')) {
                const sub = document.getElementById('implementation-sub');
                const chevron = document.getElementById('implementation-chevron');
                const badge = document.getElementById('implementation-badge');
                if (sub) sub.style.display = 'block';
                if (chevron) chevron.classList.add('open');
                if (badge) badge.style.display = 'none';
            }

            switchTab(activeTab);

            // Open specific ticket from notification deep link
            if (urlTicket && activeTab === 'impThread') {
                setTimeout(function() {
                    Livewire.dispatch('openTicketFromNotification', { ticketId: parseInt(urlTicket) });
                }, 500);
                // Clean URL params without reload
                window.history.replaceState({}, '', window.location.pathname);
            }

            // Diagnostic — check in browser console
            setTimeout(function() {
                var dbg = 'TAB DEBUG: ';
                document.querySelectorAll('.tab-content').forEach(function(el) {
                    dbg += el.id + '=' + window.getComputedStyle(el).display + ' | ';
                });
                console.log(dbg);
            }, 2000);

            // Smooth scroll to calendar
            const calendarLink = document.querySelector('a[href="#calendar"]');
            if (calendarLink) {
                calendarLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.getElementById('calendar').scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                });
            }

            // Add loading animation for action cards
            const actionCards = document.querySelectorAll('.group');
            actionCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';

                setTimeout(() => {
                    card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // Add pulse animation to notification dot
            const statsCards = document.querySelectorAll('[class*="stats-card"]');
            statsCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px) scale(1.02)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
        });
    </script>
</body>
</html>
