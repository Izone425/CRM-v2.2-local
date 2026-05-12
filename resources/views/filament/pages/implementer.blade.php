<style>
    /* Container styling */
    .implementer-container {
        grid-column: 1 / -1;
        width: 100%;
    }

    /* Main layout with grid setup */
    .dashboard-layout {
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 15px;
    }

    /* Group column styling */
    .group-column {
        padding-right: 10px;
        width: 330px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        align-content: start;
    }

    .group-box {
        background-color: white;
        width: 100%;
        flex: 1 1 0;
        min-height: 56px;
        max-height: 96px;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        cursor: pointer;
        transition: all 0.2s ease;
        border-top: none;
        border-left: 4px solid transparent;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 14px;
    }

    .group-box:hover {
        background-color: #f9fafb;
        transform: translateX(3px);
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }

    .group-box.selected {
        background-color: #f9fafb;
        transform: translateX(5px);
        border-left-width: 6px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.15);
    }

    .group-info {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
        min-width: 0;
    }

    .group-icon {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
    }

    .group-title {
        font-size: 13.5px;
        font-weight: 600;
        line-height: 1.2;
        color: #1f2937;
        text-align: left;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .group-desc {
        font-size: 12px;
        color: #6b7280;
    }

    .group-count {
        font-size: 13px;
        font-weight: 700;
        line-height: 1;
        min-width: 32px;
        height: 24px;
        padding: 2px 8px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        text-align: center;
    }

    .group-count.is-zero {
        color: #6b7280 !important;
        background-color: #f3f4f6 !important;
    }

    /* GROUP COLORS */
    .group-project-status { border-left-color: #2563eb; }
    .group-project-status .group-count { color: #2563eb; background-color: rgba(37, 99, 235, 0.10); }
    .group-project-status.selected { background-color: rgba(37, 99, 235, 0.05); }

    .group-license { border-left-color: #8b5cf6; }
    .group-license .group-count { color: #8b5cf6; background-color: rgba(139, 92, 246, 0.10); }
    .group-license.selected { background-color: rgba(139, 92, 246, 0.05); }

    .group-migration { border-left-color: #10b981; }
    .group-migration .group-count { color: #10b981; background-color: rgba(16, 185, 129, 0.10); }
    .group-migration.selected { background-color: rgba(16, 185, 129, 0.05); }

    .group-follow-up { border-left-color: #f59e0b; }
    .group-follow-up .group-count { color: #f59e0b; background-color: rgba(245, 158, 11, 0.10); }
    .group-follow-up.selected { background-color: rgba(245, 158, 11, 0.05); }

    .group-ticketing { border-left-color: #ec4899; }
    .group-ticketing .group-count { color: #ec4899; background-color: rgba(236, 72, 153, 0.10); }
    .group-ticketing.selected { background-color: rgba(236, 72, 153, 0.05); }

    .group-new-request { border-left-color: #06b6d4; }
    .group-new-request .group-count { color: #06b6d4; background-color: rgba(6, 182, 212, 0.10); }
    .group-new-request.selected { background-color: rgba(6, 182, 212, 0.05); }

    .group-implementer-request { border-left-color: #6366f1; }
    .group-implementer-request .group-count { color: #6366f1; background-color: rgba(99, 102, 241, 0.10); }
    .group-implementer-request.selected { background-color: rgba(99, 102, 241, 0.05); }

    .group-project-closing { border-left-color: #ef4444; }
    .group-project-closing .group-count { color: #ef4444; background-color: rgba(239, 68, 68, 0.10); }
    .group-project-closing.selected { background-color: rgba(239, 68, 68, 0.05); }

    /* Category column styling */
    .category-column {
        padding-right: 10px;
    }

    /* Category container */
    .category-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 10px;
        border-right: 1px solid #e5e7eb;
        padding-right: 10px;
        max-height: 75vh;
        overflow-y: auto;
    }

    /* Stat box styling */
    .stat-box {
        background-color: white;
        width: 100%;
        min-height: 65px;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        cursor: pointer;
        transition: all 0.2s ease;
        border-left: 4px solid transparent;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 15px;
        margin-bottom: 8px;
    }

    .stat-box:hover {
        background-color: #f9fafb;
        transform: translateX(3px);
    }

    .stat-box.selected {
        background-color: #f9fafb;
        transform: translateX(5px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.15);
    }

    .stat-info {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
    }

    .stat-count {
        font-size: 20px;
        font-weight: bold;
        margin: 0;
        line-height: 1.2;
    }

    .stat-label {
        color: #6b7280;
        font-size: 13px;
        font-weight: 500;
        line-height: 1.2;
    }

    /* Content area */
    .content-column {
        min-height: 600px;
    }

    .content-area {
        min-height: 600px;
    }

    .content-area .fi-ta {
        margin-top: 0;
    }

    .content-area .fi-ta-content {
        padding: 0.75rem !important;
    }

    /* Hint message */
    .hint-message {
        text-align: center;
        background-color: #f9fafb;
        border-radius: 0.5rem;
        border: 1px dashed #d1d5db;
        height: 530px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .hint-message h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .hint-message p {
        color: #6b7280;
    }

    /* Column headers */
    .column-header {
        font-size: 14px;
        font-weight: 600;
        color: #4b5563;
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 1px solid #e5e7eb;
    }

    /* STAT BOX COLORS - PROJECT STATUS */
    .status-all { border-left: 4px solid #6b7280; }
    .status-all .stat-count { color: #6b7280; }

    .status-open { border-left: 4px solid #2563eb; }
    .status-open .stat-count { color: #2563eb; }

    .status-closed { border-left: 4px solid #10b981; }
    .status-closed .stat-count { color: #10b981; }

    .status-delay { border-left: 4px solid #f59e0b; }
    .status-delay .stat-count { color: #f59e0b; }

    .status-inactive { border-left: 4px solid #ef4444; }
    .status-inactive .stat-count { color: #ef4444; }

    /* STAT BOX COLORS - LICENSE CERTIFICATION */
    .license-pending { border-left: 4px solid #8b5cf6; }
    .license-pending .stat-count { color: #8b5cf6; }

    .license-completed { border-left: 4px solid #a855f7; }
    .license-completed .stat-count { color: #a855f7; }

    /* STAT BOX COLORS - DATA MIGRATION */
    .migration-pending { border-left: 4px solid #10b981; }
    .migration-pending .stat-count { color: #10b981; }

    .migration-completed { border-left: 4px solid #34d399; }
    .migration-completed .stat-count { color: #34d399; }

    /* STAT BOX COLORS - PROJECT FOLLOW UP */
    .follow-up-today { border-left: 4px solid #f59e0b; }
    .follow-up-today .stat-count { color: #f59e0b; }

    .follow-up-overdue { border-left: 4px solid #f97316; }
    .follow-up-overdue .stat-count { color: #f97316; }

    .follow-up-future { border-left: 4px solid #ff6a00; }
    .follow-up-future .stat-count { color: #fa6800; }

    /* STAT BOX COLORS - TICKETING SYSTEM */
    .ticketing-today { border-left: 4px solid #ec4899; }
    .ticketing-today .stat-count { color: #ec4899; }

    .ticketing-overdue { border-left: 4px solid #d946ef; }
    .ticketing-overdue .stat-count { color: #d946ef; }

    /* STAT BOX COLORS - NEW REQUEST */
    .customization-pending { border-left: 4px solid #06b6d4; }
    .customization-pending .stat-count { color: #06b6d4; }

    .customization-completed { border-left: 4px solid #0ea5e9; }
    .customization-completed .stat-count { color: #0ea5e9; }

    .enhancement-pending { border-left: 4px solid #0284c7; }
    .enhancement-pending .stat-count { color: #0284c7; }

    .enhancement-completed { border-left: 4px solid #0369a1; }
    .enhancement-completed .stat-count { color: #0369a1; }

    /* Styles for Implementer Request sub-tabs */
    .request-pending { border-left: 4px solid #6366f1; }
    .request-pending .stat-count { color: #6366f1; }

    .request-approved { border-left: 4px solid #8b5cf6; }
    .request-approved .stat-count { color: #8b5cf6; }

    .request-rejected { border-left: 4px solid #ef4444; }
    .request-rejected .stat-count { color: #ef4444; }

    .request-cancelled { border-left: 4px solid #94a3b8; }
    .request-cancelled .stat-count { color: #94a3b8; }

    /* GROUP COLOR — Thread */
    .group-thread { border-left-color: #0d9488; }
    .group-thread .group-count { color: #0d9488; background-color: rgba(13, 148, 136, 0.10); }
    .group-thread.selected { background-color: rgba(13, 148, 136, 0.05); }

    /* STAT BOX COLORS — Thread */
    .thread-all { border-left: 4px solid #14b8a6; }
    .thread-all .stat-count { color: #14b8a6; }
    .thread-overdue { border-left: 4px solid #dc2626; }
    .thread-overdue .stat-count { color: #dc2626; }
    .thread-today { border-left: 4px solid #f59e0b; }
    .thread-today .stat-count { color: #f59e0b; }
    .thread-upcoming { border-left: 4px solid #10b981; }
    .thread-upcoming .stat-count { color: #10b981; }

    .group-no-respond { border-left-color: #e11d48; }
    .group-no-respond .group-count { color: #e11d48; background-color: rgba(225, 29, 72, 0.10); }
    .group-no-respond.selected { background-color: rgba(225, 29, 72, 0.05); }

    /* Follow up count status colors */
    .follow-up-none { border-left: 4px solid #64748b; }
    .follow-up-none .stat-count { color: #64748b; }

    .follow-up-1 { border-left: 4px solid #f59e0b; }
    .follow-up-1 .stat-count { color: #f59e0b; }

    .follow-up-2 { border-left: 4px solid #f97316; }
    .follow-up-2 .stat-count { color: #f97316; }

    .follow-up-3 { border-left: 4px solid #ef4444; }
    .follow-up-3 .stat-count { color: #ef4444; }

    .follow-up-4 { border-left: 4px solid #dc2626; }
    .follow-up-4 .stat-count { color: #dc2626; }

    /* Selected states for follow-up counts */
    .stat-box.selected.follow-up-none { background-color: rgba(100, 116, 139, 0.05); border-left-width: 6px; }
    .stat-box.selected.follow-up-1 { background-color: rgba(245, 158, 11, 0.05); border-left-width: 6px; }
    .stat-box.selected.follow-up-2 { background-color: rgba(249, 115, 22, 0.05); border-left-width: 6px; }
    .stat-box.selected.follow-up-3 { background-color: rgba(239, 68, 68, 0.05); border-left-width: 6px; }
    .stat-box.selected.follow-up-4 { background-color: rgba(220, 38, 38, 0.05); border-left-width: 6px; }

    /* Selected states for categories */
    .stat-box.selected.status-all { background-color: rgba(107, 114, 128, 0.05); border-left-width: 6px; }
    .stat-box.selected.status-open { background-color: rgba(37, 99, 235, 0.05); border-left-width: 6px; }
    .stat-box.selected.status-closed { background-color: rgba(16, 185, 129, 0.05); border-left-width: 6px; }
    .stat-box.selected.status-delay { background-color: rgba(245, 158, 11, 0.05); border-left-width: 6px; }
    .stat-box.selected.status-inactive { background-color: rgba(239, 68, 68, 0.05); border-left-width: 6px; }

    .stat-box.selected.license-pending { background-color: rgba(139, 92, 246, 0.05); border-left-width: 6px; }
    .stat-box.selected.license-completed { background-color: rgba(168, 85, 247, 0.05); border-left-width: 6px; }

    .stat-box.selected.migration-pending { background-color: rgba(16, 185, 129, 0.05); border-left-width: 6px; }
    .stat-box.selected.migration-completed { background-color: rgba(52, 211, 153, 0.05); border-left-width: 6px; }

    .stat-box.selected.follow-up-today { background-color: rgba(245, 158, 11, 0.05); border-left-width: 6px; }
    .stat-box.selected.follow-up-overdue { background-color: rgba(249, 115, 22, 0.05); border-left-width: 6px; }

    .stat-box.selected.ticketing-today { background-color: rgba(236, 72, 153, 0.05); border-left-width: 6px; }
    .stat-box.selected.ticketing-overdue { background-color: rgba(217, 70, 239, 0.05); border-left-width: 6px; }

    .stat-box.selected.customization-pending { background-color: rgba(6, 182, 212, 0.05); border-left-width: 6px; }
    .stat-box.selected.customization-completed { background-color: rgba(14, 165, 233, 0.05); border-left-width: 6px; }
    .stat-box.selected.enhancement-pending { background-color: rgba(2, 132, 199, 0.05); border-left-width: 6px; }
    .stat-box.selected.enhancement-completed { background-color: rgba(3, 105, 161, 0.05); border-left-width: 6px; }

    .stat-box.selected.request-pending { background-color: rgba(99, 102, 241, 0.05); border-left-width: 6px; }
    .stat-box.selected.request-approved { background-color: rgba(139, 92, 246, 0.05); border-left-width: 6px; }
    .stat-box.selected.request-rejected { background-color: rgba(239, 68, 68, 0.05); border-left-width: 6px; }
    .stat-box.selected.request-cancelled { background-color: rgba(148, 163, 184, 0.05); border-left-width: 6px; }

    .stat-box.selected.thread-all { background-color: rgba(20, 184, 166, 0.05); border-left-width: 6px; }
    .stat-box.selected.thread-overdue { background-color: rgba(220, 38, 38, 0.05); border-left-width: 6px; }
    .stat-box.selected.thread-today { background-color: rgba(245, 158, 11, 0.05); border-left-width: 6px; }
    .stat-box.selected.thread-upcoming { background-color: rgba(16, 185, 129, 0.05); border-left-width: 6px; }
    /* Animation for tab switching */
    [x-transition] {
        transition: all 0.2s ease-out;
    }

    /* Responsive adjustments */
    @media (max-width: 1024px) {
        .dashboard-layout {
            grid-template-columns: 100%;
            grid-template-rows: auto auto;
        }

        .group-column {
            width: 100%;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .group-box { min-height: 60px; }

        .category-container {
            grid-template-columns: repeat(3, 1fr);
            border-right: none;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 15px;
            margin-bottom: 15px;
            max-height: none;
        }
    }

    @media (max-width: 768px) {
        .group-column,
        .category-container {
            grid-template-columns: repeat(2, 1fr);
        }

        .stat-box:hover,
        .group-box:hover {
            transform: none;
        }

        .stat-box.selected,
        .group-box.selected {
            transform: none;
        }
    }

    @media (max-width: 640px) {
        .group-column,
        .category-container {
            grid-template-columns: 1fr;
        }
    }
</style>

@php
    // Project Status Counts
    $allProjects = app(\App\Livewire\ImplementerDashboard\ImplementerProjectAll::class)
        ->getAllSoftwareHandover()
        ->count();

    $openProjects = app(\App\Livewire\ImplementerDashboard\ImplementerProjectOpen::class)
        ->getAllSoftwareHandover()
        ->count();

    $closedProjects = app(\App\Livewire\ImplementerDashboard\ImplementerProjectClosed::class)
        ->getAllSoftwareHandover()
        ->count();

    $delayProjects = app(\App\Livewire\ImplementerDashboard\ImplementerProjectDelay::class)
        ->getAllSoftwareHandover()
        ->count();

    $inactiveProjects = app(\App\Livewire\ImplementerDashboard\ImplementerProjectInactive::class)
        ->getAllSoftwareHandover()
        ->count();

    // License Certification Counts
    $pendingLicenseCount = app(\App\Livewire\ImplementerDashboard\ImplementerLicense::class)
        ->getOverdueSoftwareHandovers()
        ->count();

    $completedLicenseCount = app(\App\Livewire\ImplementerDashboard\ImplementerLicenseCompleted::class)
        ->getOverdueHardwareHandovers()
        ->count();

    // Data Migration Counts
    $pendingMigrationCount = app(\App\Livewire\ImplementerDashboard\ImplementerMigration::class)
        ->getOverdueHardwareHandovers()
        ->count();

    $completedMigrationCount = app(\App\Livewire\ImplementerDashboard\ImplementerMigrationCompleted::class)
        ->getOverdueHardwareHandovers()
        ->count();

    // Ticket Reminder Counts
    $ticketCompletedTodayCount = app(\App\Livewire\ImplementerDashboard\TicketReminderCompletedToday::class)
        ->getCompletedTicketsQuery()
        ->count();

    $ticketCompletedOverdueCount = app(\App\Livewire\ImplementerDashboard\TicketReminderCompletedOverdue::class)
        ->getCompletedTicketsQuery()
        ->count();

    $ticketAllStatusCount = app(\App\Livewire\ImplementerDashboard\TicketReminderAllStatus::class)
        ->getCompletedTicketsQuery()
        ->count();

    $ticketReminderTotal = $ticketCompletedTodayCount + $ticketCompletedOverdueCount;

    // Project Follow Up Counts
    $followUpToday = app(\App\Livewire\ImplementerDashboard\ImplementerFollowUpToday::class)
        ->getOverdueHardwareHandovers()
        ->count();

    $followUpOverdue = app(\App\Livewire\ImplementerDashboard\ImplementerFollowUpOverdue::class)
        ->getOverdueHardwareHandovers()
        ->count();

    $followUpFuture = app(\App\Livewire\ImplementerDashboard\ImplementerFollowUpFuture::class)
        ->getOverdueHardwareHandovers()
        ->count();

    $sessionsToday = app(\App\Livewire\ImplementerDashboard\SessionToday::class)
        ->getAppointments()
        ->count();

    $sessionsTomorrow = app(\App\Livewire\ImplementerDashboard\SessionTomorrow::class)
        ->getAppointments()
        ->count();

    $followUpAll = app(\App\Livewire\ImplementerDashboard\ImplementerFollowUpAll::class)
        ->getOverdueHardwareHandovers()
        ->count();

    $followUpNone = app(\App\Livewire\ImplementerDashboard\ProjectFollowUpNone::class)
        ->getOverdueHardwareHandovers()
        ->count();

    $followUp1 = app(\App\Livewire\ImplementerDashboard\ProjectFollowUpOne::class)
        ->getOverdueHardwareHandovers()
        ->count();

    $followUp2 = app(\App\Livewire\ImplementerDashboard\ProjectFollowUpTwo::class)
        ->getOverdueHardwareHandovers()
        ->count();

    $followUp3 = app(\App\Livewire\ImplementerDashboard\ProjectFollowUpThree::class)
        ->getOverdueHardwareHandovers()
        ->count();

    $followUp4 = app(\App\Livewire\ImplementerDashboard\ProjectFollowUpFour::class)
        ->getOverdueHardwareHandovers()
        ->count();

    // Ticketing System Counts
    $internalTicketsToday = 0; // Replace with actual count
    $internalTicketsOverdue = 0; // Replace with actual count
    $externalTicketsToday = 0; // Replace with actual count
    $externalTicketsOverdue = 0;

    // New Request Counts
    $customizationPending = 0; // Example count
    $customizationCompleted = 0;
    $enhancementPending = 0;
    $enhancementCompleted = 0;

    // Calculate totals for main categories
    $projectStatusTotal = $allProjects;
    $licenseTotal = $pendingLicenseCount + $pendingMigrationCount;
    $migrationTotal = $pendingMigrationCount + $completedMigrationCount;
    $followUpTotal = $followUpToday + $followUpOverdue;
    $ticketingTotal = $internalTicketsToday + $internalTicketsOverdue + $externalTicketsToday + $externalTicketsOverdue;
    $requestTotal = $customizationPending + $customizationCompleted + $enhancementPending + $enhancementCompleted;
    $sessionsTotal = $sessionsToday + $sessionsTomorrow;

    $pendingRequestCount = app(\App\Livewire\ImplementerDashboard\ImplementerRequestPendingApproval::class)
        ->getImplementerPendingRequests()
        ->count();

    $approvedRequestCount = app(\App\Livewire\ImplementerDashboard\ImplementerRequestApproved::class)
        ->getImplementerPendingRequests()
        ->count();

    $rejectedRequestCount = app(\App\Livewire\ImplementerDashboard\ImplementerRequestRejected::class)
        ->getImplementerPendingRequests()
        ->count();

    $cancelledRequestCount = app(\App\Livewire\ImplementerDashboard\ImplementerRequestCancelled::class)
        ->getImplementerPendingRequests()
        ->count();

    $threadComponent = app(\App\Livewire\ImplementerDashboard\ImplementerThreadPendingAction::class);
    $threadComponent->selectedUser = $selectedUser ?? session('selectedUser');
    $threadTotal    = $threadComponent->getAllCount();
    $threadOverdue  = $threadComponent->getOverdueCount();
    $threadDueToday = $threadComponent->getDueTodayCount();
    $threadUpcoming = $threadComponent->getUpcomingCount();

    $implementerRequestTotal = $pendingRequestCount;
    $noRespondProjects = $followUpNone + $followUp1 + $followUp2 + $followUp3 + $followUp4;

    $implementerRequestTotal = $pendingRequestCount;
    $noRespondProjects = $followUpNone + $followUp1 + $followUp2 + $followUp3 + $followUp4;

    $sessionReminderPending = app(\App\Livewire\ImplementerDashboard\ImplementerSessionPending::class)
        ->getAppointments()
        ->count();

    $sessionReminderFuture = app(\App\Livewire\ImplementerDashboard\ImplementerSessionFuture::class)
        ->getAppointments()
        ->count();

    $sessionReminderCompleted = app(\App\Livewire\ImplementerDashboard\ImplementerSessionCompleted::class)
        ->getAppointments()
        ->count();

    $sessionReminderTotal = $sessionReminderPending;

    // Project Closing Counts
    $projectClosingNew = app(\App\Livewire\ImplementerDashboard\ProjectClosingNew::class)
        ->getNewHandoverRequests()
        ->count();

    $projectClosingApproved = app(\App\Livewire\ImplementerDashboard\ProjectClosingApproved::class)
        ->getApprovedHandoverRequests()
        ->count();

    $projectClosingTotal = $projectClosingNew;
@endphp

<div id="implementer-container" class="implementer-container"
    x-data="{
        selectedGroup: null,
        selectedStat: null,

        setSelectedGroup(value) {
            if (this.selectedGroup === value) {
                this.selectedGroup = null;
                this.selectedStat = null;
            } else {
                this.selectedGroup = value;
                this.selectedStat = null;
            }
        },

        setSelectedStat(value) {
            if (this.selectedStat === value) {
                this.selectedStat = null;
            } else {
                this.selectedStat = value;
            }
        },

        init() {
            this.selectedGroup = null;
            this.selectedStat = null;
        }
    }"
    x-init="init()">

    <div class="dashboard-layout" wire:poll.300s>
        <!-- Left sidebar with main category groups -->
        <div class="group-column">
            <!-- NO1 - PROJECT STATUS -->
            <div class="group-box group-project-status"
                :class="{'selected': selectedGroup === 'project-status'}"
                @click="setSelectedGroup('project-status')">
                <div class="group-info">
                    <x-heroicon-o-chart-bar class="group-icon" style="color: #2563eb;" />
                    <div class="group-title">Project Status</div>
                </div>
                <div class="group-count {{ $projectStatusTotal == 0 ? 'is-zero' : '' }}">{{ $projectStatusTotal }}</div>
            </div>

            <!-- NO7 - DEMO SESSION -->
            {{-- <div class="group-box group-new-request"
                :class="{'selected': selectedGroup === 'new-request'}"
                @click="setSelectedGroup('appointment')">
                <div class="group-info">
                    <div class="group-title">Project Session</div>
                </div>
                <div class="group-count">{{ $sessionsTotal }}</div>
            </div> --}}

            <!-- NO2 - LICENSE CERTIFICATION -->
            <div class="group-box group-license"
                :class="{'selected': selectedGroup === 'license'}"
                @click="setSelectedGroup('license')">
                <div class="group-info">
                    <x-heroicon-o-clipboard-document-list class="group-icon" style="color: #8b5cf6;" />
                    <div class="group-title">Project Task</div>
                </div>
                <div class="group-count {{ $licenseTotal == 0 ? 'is-zero' : '' }}">{{ $licenseTotal }}</div>
            </div>

            <!-- NO3 - TICKET REMINDER -->
            <div class="group-box group-ticketing"
                :class="{'selected': selectedGroup === 'ticket-reminder'}"
                @click="setSelectedGroup('ticket-reminder')">
                <div class="group-info">
                    <x-heroicon-o-ticket class="group-icon" style="color: #ec4899;" />
                    <div class="group-title">Ticket Reminder</div>
                </div>
                <div class="group-count {{ $ticketReminderTotal == 0 ? 'is-zero' : '' }}">{{ $ticketReminderTotal }}</div>
            </div>

            <!-- NO4 - SESSION REMINDER -->
            <div class="group-box group-migration"
                :class="{'selected': selectedGroup === 'session-reminder'}"
                @click="setSelectedGroup('session-reminder')">
                <div class="group-info">
                    <x-heroicon-o-calendar-days class="group-icon" style="color: #10b981;" />
                    <div class="group-title">Session Reminder</div>
                </div>
                <div class="group-count {{ $sessionReminderTotal == 0 ? 'is-zero' : '' }}">{{ $sessionReminderTotal }}</div>
            </div>

            <!-- NO5 - PROJECT FOLLOW UP -->
            <div class="group-box group-follow-up"
                :class="{'selected': selectedGroup === 'follow-up'}"
                @click="setSelectedGroup('follow-up')">
                <div class="group-info">
                    <x-heroicon-o-bell-alert class="group-icon" style="color: #f59e0b;" />
                    <div class="group-title">Follow Up Reminder</div>
                </div>
                <div class="group-count {{ $followUpTotal == 0 ? 'is-zero' : '' }}">{{ $followUpTotal }}</div>
            </div>

            <!-- NO6 - PROJECT CLOSING -->
            <div class="group-box group-project-closing"
                :class="{'selected': selectedGroup === 'project-closing'}"
                @click="setSelectedGroup('project-closing')">
                <div class="group-info">
                    <x-heroicon-o-check-circle class="group-icon" style="color: #ef4444;" />
                    <div class="group-title">Project Closing</div>
                </div>
                <div class="group-count {{ $projectClosingTotal == 0 ? 'is-zero' : '' }}">{{ $projectClosingTotal }}</div>
            </div>

            <!-- NO7 - IMPLEMENTER REQUEST -->
            <div class="group-box group-implementer-request"
                :class="{'selected': selectedGroup === 'implementer-request'}"
                @click="setSelectedGroup('implementer-request')">
                <div class="group-info">
                    <x-heroicon-o-inbox-arrow-down class="group-icon" style="color: #6366f1;" />
                    <div class="group-title">Session Request</div>
                </div>
                <div class="group-count {{ $implementerRequestTotal == 0 ? 'is-zero' : '' }}">{{ $implementerRequestTotal }}</div>
            </div>

            <!-- NO8 - THREAD -->
            <div class="group-box group-thread"
                :class="{'selected': selectedGroup === 'thread'}"
                @click="setSelectedGroup('thread')">
                <div class="group-info">
                    <x-heroicon-o-chat-bubble-left-right class="group-icon" style="color: #0d9488;" />
                    <div class="group-title">Thread</div>
                </div>
                <div class="group-count {{ $threadTotal == 0 ? 'is-zero' : '' }}">{{ $threadTotal }}</div>
            </div>

            <!-- NO5 - PROJECT FOLLOW UP -->
            {{-- <div class="group-box group-no-respond"
                :class="{'selected': selectedGroup === 'no-respond'}"
                @click="setSelectedGroup('no-respond')">
                <div class="group-info">
                    <div class="group-title">Follow Up Count</div>
                </div>
                <div class="group-count">{{ $noRespondProjects }}</div>
            </div> --}}

            {{-- <!-- NO5 - TICKETING SYSTEM -->
            <div class="group-box group-ticketing"
                :class="{'selected': selectedGroup === 'ticketing'}"
                @click="setSelectedGroup('ticketing')">
                <div class="group-info">
                    <div class="group-title">Ticketing System</div>
                </div>
                <div class="group-count">{{ $ticketingTotal }}</div>
            </div> --}}

            {{-- <!-- NO6 - NEW REQUEST -->
            <div class="group-box group-new-request"
                :class="{'selected': selectedGroup === 'new-request'}"
                @click="setSelectedGroup('new-request')">
                <div class="group-info">
                    <div class="group-title">New Request</div>
                </div>
                <div class="group-count">{{ $requestTotal }}</div>
            </div> --}}
        </div>

        <!-- Right content column -->
        <div class="content-column">
            <!-- PROJECT STATUS Sub-tabs -->
            <div class="category-container" x-show="selectedGroup === 'project-status'" x-transition>
                <div class="stat-box status-all"
                    :class="{'selected': selectedStat === 'status-all'}"
                    @click="setSelectedStat('status-all')">
                    <div class="stat-info">
                        <div class="stat-label">All Projects</div>
                    </div>
                    <div class="stat-count">{{ $allProjects }}</div>
                </div>

                <div class="stat-box status-closed"
                    :class="{'selected': selectedStat === 'status-closed'}"
                    @click="setSelectedStat('status-closed')">
                    <div class="stat-info">
                        <div class="stat-label">Closed</div>
                    </div>
                    <div class="stat-count">{{ $closedProjects }}</div>
                </div>

                <div class="stat-box status-open"
                    :class="{'selected': selectedStat === 'status-open'}"
                    @click="setSelectedStat('status-open')">
                    <div class="stat-info">
                        <div class="stat-label">Open</div>
                    </div>
                    <div class="stat-count">{{ $openProjects }}</div>
                </div>

                <div class="stat-box status-delay"
                    :class="{'selected': selectedStat === 'status-delay'}"
                    @click="setSelectedStat('status-delay')">
                    <div class="stat-info">
                        <div class="stat-label">Delay</div>
                    </div>
                    <div class="stat-count">{{ $delayProjects }}</div>
                </div>

                <div class="stat-box status-inactive"
                    :class="{'selected': selectedStat === 'status-inactive'}"
                    @click="setSelectedStat('status-inactive')">
                    <div class="stat-info">
                        <div class="stat-label">Inactive</div>
                    </div>
                    <div class="stat-count">{{ $inactiveProjects }}</div>
                </div>
            </div>

            <!-- Appointment Sub-tabs -->
            <div class="category-container" x-show="selectedGroup === 'appointment'" x-transition>
                <div class="stat-box customization-pending"
                    :class="{'selected': selectedStat === 'sessions-today'}"
                    @click="setSelectedStat('sessions-today')">
                    <div class="stat-info">
                        <div class="stat-label">Sessions Today</div>
                    </div>
                    <div class="stat-count">{{ $sessionsToday }}</div>
                </div>

                <div class="stat-box customization-completed"
                    :class="{'selected': selectedStat === 'sessions-tomorrow'}"
                    @click="setSelectedStat('sessions-tomorrow')">
                    <div class="stat-info">
                        <div class="stat-label">Sessions Tomorrow</div>
                    </div>
                    <div class="stat-count">{{ $sessionsTomorrow }}</div>
                </div>
            </div>

            <!-- SESSION REMINDER Sub-tabs -->
            <div class="category-container" x-show="selectedGroup === 'session-reminder'" x-transition>
                <div class="stat-box migration-pending"
                    :class="{'selected': selectedStat === 'session-reminder-pending'}"
                    @click="setSelectedStat('session-reminder-pending')">
                    <div class="stat-info">
                        <div class="stat-label">Pending</div>
                    </div>
                    <div class="stat-count">{{ $sessionReminderPending }}</div>
                </div>

                <div class="stat-box license-pending"
                    :class="{'selected': selectedStat === 'session-reminder-future'}"
                    @click="setSelectedStat('session-reminder-future')">
                    <div class="stat-info">
                        <div class="stat-label">Future</div>
                    </div>
                    <div class="stat-count">{{ $sessionReminderFuture }}</div>
                </div>

                <div class="stat-box migration-completed"
                    :class="{'selected': selectedStat === 'session-reminder-completed'}"
                    @click="setSelectedStat('session-reminder-completed')">
                    <div class="stat-info">
                        <div class="stat-label">Completed</div>
                    </div>
                    <div class="stat-count">{{ $sessionReminderCompleted }}</div>
                </div>
            </div>

            <!-- LICENSE CERTIFICATION Sub-tabs -->
            <div class="category-container" x-show="selectedGroup === 'license'" x-transition>
                <div class="stat-box license-pending"
                    :class="{'selected': selectedStat === 'license-pending'}"
                    @click="setSelectedStat('license-pending')">
                    <div class="stat-info">
                        <div class="stat-label">License Certificate | Pending</div>
                    </div>
                    <div class="stat-count">{{ $pendingLicenseCount }}</div>
                </div>

                <div class="stat-box license-completed"
                    :class="{'selected': selectedStat === 'license-completed'}"
                    @click="setSelectedStat('license-completed')">
                    <div class="stat-info">
                        <div class="stat-label">License Certificate | Completed</div>
                    </div>
                    <div class="stat-count">{{ $completedLicenseCount }}</div>
                </div>

                <div class="stat-box migration-pending"
                    :class="{'selected': selectedStat === 'migration-pending'}"
                    @click="setSelectedStat('migration-pending')">
                    <div class="stat-info">
                        <div class="stat-label">Data Migration | Pending</div>
                    </div>
                    <div class="stat-count">{{ $pendingMigrationCount }}</div>
                </div>

                <div class="stat-box migration-completed"
                    :class="{'selected': selectedStat === 'migration-completed'}"
                    @click="setSelectedStat('migration-completed')">
                    <div class="stat-info">
                        <div class="stat-label">Data Migration | Completed</div>
                    </div>
                    <div class="stat-count">{{ $completedMigrationCount }}</div>
                </div>
            </div>

            <!-- TICKET REMINDER Sub-tabs -->
            <div class="category-container" x-show="selectedGroup === 'ticket-reminder'" x-transition>
                <div class="stat-box license-pending"
                    :class="{'selected': selectedStat === 'ticket-reminder-completed-today'}"
                    @click="setSelectedStat('ticket-reminder-completed-today')">
                    <div class="stat-info">
                        <div class="stat-label">Completed Today</div>
                    </div>
                    <div class="stat-count">{{ $ticketCompletedTodayCount }}</div>
                </div>

                <div class="stat-box license-completed"
                    :class="{'selected': selectedStat === 'ticket-reminder-completed-overdue'}"
                    @click="setSelectedStat('ticket-reminder-completed-overdue')">
                    <div class="stat-info">
                        <div class="stat-label">Completed Overdue</div>
                    </div>
                    <div class="stat-count">{{ $ticketCompletedOverdueCount }}</div>
                </div>

                <div class="stat-box migration-pending"
                    :class="{'selected': selectedStat === 'ticket-reminder-all-status'}"
                    @click="setSelectedStat('ticket-reminder-all-status')">
                    <div class="stat-info">
                        <div class="stat-label">All Status</div>
                    </div>
                    <div class="stat-count">{{ $ticketAllStatusCount }}</div>
                </div>
            </div>

            <!-- IMPLEMENTER REQUEST Sub-tabs -->
            <div class="category-container" style="grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));" x-show="selectedGroup === 'implementer-request'" x-transition>
                <div class="stat-box request-pending"
                    :class="{'selected': selectedStat === 'request-pending'}"
                    @click="setSelectedStat('request-pending')">
                    <div class="stat-info">
                        <div class="stat-label">Pending Approval</div>
                    </div>
                    <div class="stat-count">{{ $pendingRequestCount }}</div>
                </div>

                <div class="stat-box request-approved"
                    :class="{'selected': selectedStat === 'request-approved'}"
                    @click="setSelectedStat('request-approved')">
                    <div class="stat-info">
                        <div class="stat-label">Approved</div>
                    </div>
                    <div class="stat-count">{{ $approvedRequestCount }}</div>
                </div>

                <div class="stat-box request-rejected"
                    :class="{'selected': selectedStat === 'request-rejected'}"
                    @click="setSelectedStat('request-rejected')">
                    <div class="stat-info">
                        <div class="stat-label">Rejected</div>
                    </div>
                    <div class="stat-count">{{ $rejectedRequestCount }}</div>
                </div>

                <div class="stat-box request-cancelled"
                    :class="{'selected': selectedStat === 'request-cancelled'}"
                    @click="setSelectedStat('request-cancelled')">
                    <div class="stat-info">
                        <div class="stat-label">Cancelled</div>
                    </div>
                    <div class="stat-count">{{ $cancelledRequestCount }}</div>
                </div>
            </div>

            <!-- THREAD Sub-tabs -->
            <div class="category-container" style="grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));"
                 x-show="selectedGroup === 'thread'" x-transition>
                <div class="stat-box thread-all"
                    :class="{'selected': selectedStat === 'thread-all'}"
                    @click="setSelectedStat('thread-all'); $wire.dispatch('thread-filter-changed', { value: 'all' })">
                    <div class="stat-info">
                        <div class="stat-label">All</div>
                    </div>
                    <div class="stat-count">{{ $threadTotal }}</div>
                </div>

                <div class="stat-box thread-overdue"
                    :class="{'selected': selectedStat === 'thread-overdue'}"
                    @click="setSelectedStat('thread-overdue'); $wire.dispatch('thread-filter-changed', { value: 'overdue' })">
                    <div class="stat-info">
                        <div class="stat-label">Overdue</div>
                    </div>
                    <div class="stat-count">{{ $threadOverdue }}</div>
                </div>

                <div class="stat-box thread-today"
                    :class="{'selected': selectedStat === 'thread-today'}"
                    @click="setSelectedStat('thread-today'); $wire.dispatch('thread-filter-changed', { value: 'today' })">
                    <div class="stat-info">
                        <div class="stat-label">Due Today</div>
                    </div>
                    <div class="stat-count">{{ $threadDueToday }}</div>
                </div>

                <div class="stat-box thread-upcoming"
                    :class="{'selected': selectedStat === 'thread-upcoming'}"
                    @click="setSelectedStat('thread-upcoming'); $wire.dispatch('thread-filter-changed', { value: 'upcoming' })">
                    <div class="stat-info">
                        <div class="stat-label">Upcoming</div>
                    </div>
                    <div class="stat-count">{{ $threadUpcoming }}</div>
                </div>
            </div>

            <!-- PROJECT FOLLOW UP Sub-tabs -->
            <div class="category-container" x-show="selectedGroup === 'follow-up'" x-transition>
                <div class="stat-box follow-up-today"
                    :class="{'selected': selectedStat === 'follow-up-today'}"
                    @click="setSelectedStat('follow-up-today')">
                    <div class="stat-info">
                        <div class="stat-label">Today</div>
                    </div>
                    <div class="stat-count">{{ $followUpToday }}</div>
                </div>

                <div class="stat-box follow-up-overdue"
                    :class="{'selected': selectedStat === 'follow-up-overdue'}"
                    @click="setSelectedStat('follow-up-overdue')">
                    <div class="stat-info">
                        <div class="stat-label">Overdue</div>
                    </div>
                    <div class="stat-count">{{ $followUpOverdue }}</div>
                </div>

                <div class="stat-box follow-up-overdue"
                    :class="{'selected': selectedStat === 'follow-up-future'}"
                    @click="setSelectedStat('follow-up-future')">
                    <div class="stat-info">
                        <div class="stat-label">Next Follow Up</div>
                    </div>
                    <div class="stat-count">{{ $followUpFuture }}</div>
                </div>

                <div class="stat-box follow-up-future"
                    :class="{'selected': selectedStat === 'follow-up-all'}"
                    @click="setSelectedStat('follow-up-all')">
                    <div class="stat-info">
                        <div class="stat-label">All Follow Up</div>
                    </div>
                    <div class="stat-count">{{ $followUpAll }}</div>
                </div>
            </div>

            <!-- PROJECT CLOSING Sub-tabs -->
            <div class="category-container" x-show="selectedGroup === 'project-closing'" x-transition>
                <div class="stat-box request-pending"
                    :class="{'selected': selectedStat === 'project-closing-new'}"
                    @click="setSelectedStat('project-closing-new')">
                    <div class="stat-info">
                        <div class="stat-label">New</div>
                    </div>
                    <div class="stat-count">{{ $projectClosingNew }}</div>
                </div>

                <div class="stat-box request-approved"
                    :class="{'selected': selectedStat === 'project-closing-approved'}"
                    @click="setSelectedStat('project-closing-approved')">
                    <div class="stat-info">
                        <div class="stat-label">Approved</div>
                    </div>
                    <div class="stat-count">{{ $projectClosingApproved }}</div>
                </div>
            </div>

            <!-- TICKETING SYSTEM Sub-tabs -->
            <div class="category-container" x-show="selectedGroup === 'ticketing'" x-transition>
                <div class="stat-box ticketing-today"
                    :class="{'selected': selectedStat === 'internal-today'}"
                    @click="setSelectedStat('internal-today')">
                    <div class="stat-info">
                        <div class="stat-label">Internal Today</div>
                    </div>
                    <div class="stat-count">{{ $internalTicketsToday ?? 0 }}</div>
                </div>

                <div class="stat-box ticketing-overdue"
                    :class="{'selected': selectedStat === 'internal-overdue'}"
                    @click="setSelectedStat('internal-overdue')">
                    <div class="stat-info">
                        <div class="stat-label">Internal Overdue</div>
                    </div>
                    <div class="stat-count">{{ $internalTicketsOverdue ?? 0 }}</div>
                </div>

                <div class="stat-box ticketing-today"
                    :class="{'selected': selectedStat === 'external-today'}"
                    @click="setSelectedStat('external-today')">
                    <div class="stat-info">
                        <div class="stat-label">External Today</div>
                    </div>
                    <div class="stat-count">{{ $externalTicketsToday ?? 0 }}</div>
                </div>

                <div class="stat-box ticketing-overdue"
                    :class="{'selected': selectedStat === 'external-overdue'}"
                    @click="setSelectedStat('external-overdue')">
                    <div class="stat-info">
                        <div class="stat-label">External Overdue</div>
                    </div>
                    <div class="stat-count">{{ $externalTicketsOverdue ?? 0 }}</div>
                </div>
            </div>

            <!-- NEW REQUEST Sub-tabs -->
            <div class="category-container" x-show="selectedGroup === 'new-request'" x-transition>
                <div class="stat-box customization-pending"
                    :class="{'selected': selectedStat === 'customization-pending'}"
                    @click="setSelectedStat('customization-pending')">
                    <div class="stat-info">
                        <div class="stat-label">Customization | Pending</div>
                    </div>
                    <div class="stat-count">{{ $customizationPending }}</div>
                </div>

                <div class="stat-box customization-completed"
                    :class="{'selected': selectedStat === 'customization-completed'}"
                    @click="setSelectedStat('customization-completed')">
                    <div class="stat-info">
                        <div class="stat-label">Customization | Completed</div>
                    </div>
                    <div class="stat-count">{{ $customizationCompleted }}</div>
                </div>

                <div class="stat-box enhancement-pending"
                    :class="{'selected': selectedStat === 'enhancement-pending'}"
                    @click="setSelectedStat('enhancement-pending')">
                    <div class="stat-info">
                        <div class="stat-label">Enhancement | Pending</div>
                    </div>
                    <div class="stat-count">{{ $enhancementPending }}</div>
                </div>

                <div class="stat-box enhancement-completed"
                    :class="{'selected': selectedStat === 'enhancement-completed'}"
                    @click="setSelectedStat('enhancement-completed')">
                    <div class="stat-info">
                        <div class="stat-label">Enhancement | Completed</div>
                    </div>
                    <div class="stat-count">{{ $enhancementCompleted }}</div>
                </div>
            </div>

            <!--PROJECT FOLLOW UP Sub-tabs -->
            <div class="category-container" x-show="selectedGroup === 'no-respond'" x-transition>
                <div class="stat-box follow-up-none"
                    :class="{'selected': selectedStat === 'follow-up-none'}"
                    @click="setSelectedStat('follow-up-none')">
                    <div class="stat-info">
                        <div class="stat-label">None</div>
                    </div>
                    <div class="stat-count">{{ $followUpNone }}</div>
                </div>

                <div class="stat-box follow-up-1"
                    :class="{'selected': selectedStat === 'follow-up-1'}"
                    @click="setSelectedStat('follow-up-1')">
                    <div class="stat-info">
                        <div class="stat-label">Follow-up 1</div>
                    </div>
                    <div class="stat-count">{{ $followUp1 }}</div>
                </div>

                <div class="stat-box follow-up-2"
                    :class="{'selected': selectedStat === 'follow-up-2'}"
                    @click="setSelectedStat('follow-up-2')">
                    <div class="stat-info">
                        <div class="stat-label">Follow-up 2</div>
                    </div>
                    <div class="stat-count">{{ $followUp2 }}</div>
                </div>

                <div class="stat-box follow-up-3"
                    :class="{'selected': selectedStat === 'follow-up-3'}"
                    @click="setSelectedStat('follow-up-3')">
                    <div class="stat-info">
                        <div class="stat-label">Follow-up 3</div>
                    </div>
                    <div class="stat-count">{{ $followUp3 }}</div>
                </div>

                <div class="stat-box follow-up-4"
                    :class="{'selected': selectedStat === 'follow-up-4'}"
                    @click="setSelectedStat('follow-up-4')">
                    <div class="stat-info">
                        <div class="stat-label">Follow-up 4</div>
                    </div>
                    <div class="stat-count">{{ $followUp4 }}</div>
                </div>
            </div>

            <!-- Content area for tables -->
            <div class="content-area">
                <!-- Display hint message when nothing is selected -->
                <div class="hint-message" x-show="selectedGroup === null || selectedStat === null" x-transition>
                    <h3 x-text="selectedGroup === null ? 'Select a category to continue' : 'Select a subcategory to view data'"></h3>
                    <p x-text="selectedGroup === null ? 'Click on any of the category boxes to see options' : 'Click on any of the subcategory boxes to display the corresponding information'"></p>
                </div>

                <!-- Content panels for each table -->
                <!-- PROJECT STATUS Tables -->
                <div x-show="selectedStat === 'status-all'" x-transition>
                    <div class="p-4">
                        <livewire:implementer-dashboard.implementer-project-all />
                    </div>
                </div>
                <div x-show="selectedStat === 'status-open'" x-transition>
                    <div class="p-4">
                        <livewire:implementer-dashboard.implementer-project-open />
                    </div>
                </div>
                <div x-show="selectedStat === 'status-closed'" x-transition>
                    <div class="p-4">
                        <livewire:implementer-dashboard.implementer-project-closed />
                    </div>
                </div>
                <div x-show="selectedStat === 'status-delay'" x-transition>
                    <div class="p-4">
                        <livewire:implementer-dashboard.implementer-project-delay />
                    </div>
                </div>
                <div x-show="selectedStat === 'status-inactive'" x-transition>
                    <div class="p-4">
                        <livewire:implementer-dashboard.implementer-project-inactive />
                    </div>
                </div>

                <!-- Sessions Today Table -->
                <div x-show="selectedStat === 'sessions-today'" x-transition>
                    <div class="p-4">
                        <livewire:implementer-dashboard.session-today />
                    </div>
                </div>
                <!-- Sessions Tomorrow Table -->
                <div x-show="selectedStat === 'sessions-tomorrow'" x-transition>
                    <div class="p-4">
                        <livewire:implementer-dashboard.session-tomorrow />
                    </div>
                </div>

                <!-- LICENSE CERTIFICATION Tables -->
                <div x-show="selectedStat === 'license-pending'" x-transition>
                    <div class="p-4">
                        <livewire:implementer-dashboard.implementer-license />
                    </div>
                </div>
                <div x-show="selectedStat === 'license-completed'" x-transition>
                    <div class="p-4">
                        <livewire:implementer-dashboard.implementer-license-completed />
                    </div>
                </div>
                <div x-show="selectedStat === 'migration-pending'" x-transition>
                    <div class="p-4">
                        <livewire:implementer-dashboard.implementer-migration />
                    </div>
                </div>
                <div x-show="selectedStat === 'migration-completed'" x-transition>
                    <div class="p-4">
                        <livewire:implementer-dashboard.implementer-migration-completed />
                    </div>
                </div>

                <!-- TICKET REMINDER Tables -->
                <div x-show="selectedStat === 'ticket-reminder-completed-today'" x-transition>
                    <div class="p-4">
                        <livewire:implementer-dashboard.ticket-reminder-completed-today />
                    </div>
                </div>
                <div x-show="selectedStat === 'ticket-reminder-completed-overdue'" x-transition>
                    <div class="p-4">
                        <livewire:implementer-dashboard.ticket-reminder-completed-overdue />
                    </div>
                </div>
                <div x-show="selectedStat === 'ticket-reminder-all-status'" x-transition>
                    <div class="p-4">
                        <livewire:implementer-dashboard.ticket-reminder-all-status />
                    </div>
                </div>

                <!-- IMPLEMENTER REQUEST Tables -->
                <div x-show="selectedStat === 'request-pending'" x-transition>
                    <div class="p-4">
                        <livewire:implementer-dashboard.implementer-request-pending-approval />
                    </div>
                </div>
                <div x-show="selectedStat === 'request-approved'" x-transition>
                    <div class="p-4">
                        <livewire:implementer-dashboard.implementer-request-approved />
                    </div>
                </div>
                <div x-show="selectedStat === 'request-rejected'" x-transition>
                    <div class="p-4">
                        <livewire:implementer-dashboard.implementer-request-rejected />
                    </div>
                </div>
                <div x-show="selectedStat === 'request-cancelled'" x-transition>
                    <div class="p-4">
                        <livewire:implementer-dashboard.implementer-request-cancelled />
                    </div>
                </div>

                <!-- THREAD Tables (single component, filter-driven by sub-stat clicks) -->
                <div x-show="selectedStat === 'thread-all' || selectedStat === 'thread-overdue'
                          || selectedStat === 'thread-today' || selectedStat === 'thread-upcoming'" x-transition>
                    <div class="p-4">
                        <livewire:implementer-dashboard.implementer-thread-pending-action />
                    </div>
                </div>

                <!-- PROJECT FOLLOW UP Tables -->
                <div x-show="selectedStat === 'follow-up-today'" x-transition>
                    <div class="p-4">
                        <livewire:implementer-dashboard.implementer-follow-up-today />
                    </div>
                </div>
                <div x-show="selectedStat === 'follow-up-overdue'" x-transition>
                    <div class="p-4">
                        <livewire:implementer-dashboard.implementer-follow-up-overdue />
                    </div>
                </div>
                <div x-show="selectedStat === 'follow-up-future'" x-transition>
                    <div class="p-4">
                        <livewire:implementer-dashboard.implementer-follow-up-future />
                    </div>
                </div>
                <div x-show="selectedStat === 'follow-up-all'" x-transition>
                    <div class="p-4">
                        <livewire:implementer-dashboard.implementer-follow-up-all />
                    </div>
                </div>

                <!-- PROJECT CLOSING Tables -->
                <div x-show="selectedStat === 'project-closing-new'" x-transition>
                    <div class="p-4">
                        <livewire:implementer-dashboard.project-closing-new />
                    </div>
                </div>
                <div x-show="selectedStat === 'project-closing-approved'" x-transition>
                    <div class="p-4">
                        <livewire:implementer-dashboard.project-closing-approved />
                    </div>
                </div>

                <!-- TICKETING SYSTEM Tables -->
                <div x-show="selectedStat === 'internal-today'" x-transition>
                    <div class="p-4">
                        {{-- <livewire:implementer-dashboard.internal-tickets-today /> --}}
                    </div>
                </div>
                <div x-show="selectedStat === 'internal-overdue'" x-transition>
                    <div class="p-4">
                        {{-- <livewire:implementer-dashboard.internal-tickets-overdue /> --}}
                    </div>
                </div>
                <div x-show="selectedStat === 'external-today'" x-transition>
                    <div class="p-4">
                        {{-- <livewire:implementer-dashboard.external-tickets-today /> --}}
                    </div>
                </div>
                <div x-show="selectedStat === 'external-overdue'" x-transition>
                    <div class="p-4">
                        {{-- <livewire:implementer-dashboard.external-tickets-overdue /> --}}
                    </div>
                </div>

                <!-- NEW REQUEST Tables -->
                <div x-show="selectedStat === 'customization-pending'" x-transition>
                    <div class="p-4">
                        {{-- <livewire:customization-pending-table /> --}}
                    </div>
                </div>
                <div x-show="selectedStat === 'customization-completed'" x-transition>
                    <div class="p-4">
                        {{-- <livewire:customization-completed-table /> --}}
                    </div>
                </div>
                <div x-show="selectedStat === 'enhancement-pending'" x-transition>
                    <div class="p-4">
                        {{-- <livewire:enhancement-pending-table /> --}}
                    </div>
                </div>
                <div x-show="selectedStat === 'enhancement-completed'" x-transition>
                    <div class="p-4">
                        {{-- <livewire:enhancement-completed-table /> --}}
                    </div>
                </div>

                <!-- PROJECT FOLLOW UP Tables -->
                <div x-show="selectedStat === 'follow-up-none'" x-transition>
                    <div class="p-4">
                        <livewire:implementer-dashboard.project-follow-up-none />
                    </div>
                </div>

                <div x-show="selectedStat === 'follow-up-1'" x-transition>
                    <div class="p-4">
                        <livewire:implementer-dashboard.project-follow-up-one />
                    </div>
                </div>

                <div x-show="selectedStat === 'follow-up-2'" x-transition>
                    <div class="p-4">
                        <livewire:implementer-dashboard.project-follow-up-two />
                    </div>
                </div>

                <div x-show="selectedStat === 'follow-up-3'" x-transition>
                    <div class="p-4">
                        <livewire:implementer-dashboard.project-follow-up-three />
                    </div>
                </div>

                <div x-show="selectedStat === 'follow-up-4'" x-transition>
                    <div class="p-4">
                        <livewire:implementer-dashboard.project-follow-up-four />
                    </div>
                </div>

                <div x-show="selectedStat === 'session-reminder-pending'" x-transition>
                    <div class="p-4">
                        <livewire:implementer-dashboard.implementer-session-pending />
                    </div>
                </div>

                <div x-show="selectedStat === 'session-reminder-future'" x-transition>
                    <div class="p-4">
                        <livewire:implementer-dashboard.implementer-session-future />
                    </div>
                </div>

                <div x-show="selectedStat === 'session-reminder-completed'" x-transition>
                    <div class="p-4">
                        <livewire:implementer-dashboard.implementer-session-completed />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to reset the implementer component
        window.resetImplementer = function() {
            const container = document.getElementById('implementer-container');
            if (container && container.__x) {
                container.__x.$data.selectedGroup = null;
                container.__x.$data.selectedStat = null;
                console.log('Implementer dashboard reset via global function');
            }
        };

        // Listen for our custom reset event
        window.addEventListener('reset-implementer-dashboard', function() {
            window.resetImplementer();
        });
    });
</script>
