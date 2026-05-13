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

    .group-thread { border-left-color: #0d9488; }
    .group-thread .group-count { color: #0d9488; background-color: rgba(13, 148, 136, 0.10); }
    .group-thread.selected { background-color: rgba(13, 148, 136, 0.05); }

    /* Category column styling */
    .category-column {
        padding-right: 10px;
    }

    /* Category container — horizontal filter pill strip */
    .category-container {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding: 4px 10px 14px 0;
        margin-bottom: 14px;
        border-right: none;
        border-bottom: 1px solid #e5e7eb;
        max-height: none;
        overflow: visible;
    }

    /* Stat pill */
    .stat-box {
        --stat-rgb: 100, 116, 139;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 10px 6px 11px;
        min-height: 0;
        width: auto;
        margin: 0;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 999px;
        box-shadow: none;
        cursor: pointer;
        transition: background-color .15s ease, border-color .15s ease, color .15s ease;
        color: #475569;
        font: 500 12.5px/1 Poppins, system-ui, sans-serif;
    }

    .stat-box::before {
        content: '';
        display: inline-block;
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: rgb(var(--stat-rgb));
        flex-shrink: 0;
    }

    .stat-box:hover {
        transform: none;
        border-color: rgba(var(--stat-rgb), 0.45);
        color: rgb(var(--stat-rgb));
        background: rgba(var(--stat-rgb), 0.04);
    }

    .stat-box.selected {
        transform: none;
        background: rgba(var(--stat-rgb), 0.12);
        border-color: rgba(var(--stat-rgb), 0.50);
        color: rgb(var(--stat-rgb));
        font-weight: 600;
        box-shadow: 0 1px 2px rgba(var(--stat-rgb), 0.10);
    }

    .stat-info {
        display: inline-flex;
        align-items: center;
    }

    .stat-label {
        color: inherit;
        font-size: 12.5px;
        font-weight: inherit;
        line-height: 1;
        letter-spacing: 0.01em;
    }

    .stat-count {
        color: rgb(var(--stat-rgb));
        background: rgba(var(--stat-rgb), 0.12);
        font-size: 11.5px;
        font-weight: 700;
        line-height: 1;
        padding: 3px 7px;
        border-radius: 999px;
        margin-left: 2px;
    }

    .stat-box.selected .stat-count {
        background: rgba(var(--stat-rgb), 0.18);
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
        min-height: 60vh;
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

    /* STAT PILL COLORS — each pill class sets --stat-rgb consumed by .stat-box base rule */

    /* Project Status */
    .status-all      { --stat-rgb: 107, 114, 128; }
    .status-open     { --stat-rgb: 37, 99, 235; }
    .status-closed   { --stat-rgb: 16, 185, 129; }
    .status-delay    { --stat-rgb: 245, 158, 11; }
    .status-inactive { --stat-rgb: 239, 68, 68; }

    /* License Certification */
    .license-pending   { --stat-rgb: 139, 92, 246; }
    .license-completed { --stat-rgb: 168, 85, 247; }

    /* Data Migration */
    .migration-pending   { --stat-rgb: 16, 185, 129; }
    .migration-completed { --stat-rgb: 52, 211, 153; }

    /* Follow Up Reminder */
    .follow-up-today   { --stat-rgb: 245, 158, 11; }
    .follow-up-overdue { --stat-rgb: 249, 115, 22; }
    .follow-up-future  { --stat-rgb: 250, 104, 0; }
    .follow-up-none    { --stat-rgb: 100, 116, 139; }
    .follow-up-1       { --stat-rgb: 245, 158, 11; }
    .follow-up-2       { --stat-rgb: 249, 115, 22; }
    .follow-up-3       { --stat-rgb: 239, 68, 68; }
    .follow-up-4       { --stat-rgb: 220, 38, 38; }

    /* Ticketing System */
    .ticketing-today   { --stat-rgb: 236, 72, 153; }
    .ticketing-overdue { --stat-rgb: 217, 70, 239; }

    /* New Request */
    .customization-pending   { --stat-rgb: 6, 182, 212; }
    .customization-completed { --stat-rgb: 14, 165, 233; }
    .enhancement-pending     { --stat-rgb: 2, 132, 199; }
    .enhancement-completed   { --stat-rgb: 3, 105, 161; }

    /* Implementer Request */
    .request-pending   { --stat-rgb: 99, 102, 241; }
    .request-approved  { --stat-rgb: 139, 92, 246; }
    .request-rejected  { --stat-rgb: 239, 68, 68; }
    .request-cancelled { --stat-rgb: 148, 163, 184; }

    /* Thread */
    .thread-all      { --stat-rgb: 20, 184, 166; }
    .thread-overdue  { --stat-rgb: 220, 38, 38; }
    .thread-today    { --stat-rgb: 245, 158, 11; }
    .thread-upcoming { --stat-rgb: 16, 185, 129; }

    /* Preserved: legacy group modifier (still used elsewhere) */
    .group-no-respond { border-left-color: #e11d48; }
    .group-no-respond .group-count { color: #e11d48; background-color: rgba(225, 29, 72, 0.10); }
    .group-no-respond.selected { background-color: rgba(225, 29, 72, 0.05); }
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
            padding: 4px 0 12px;
            margin-bottom: 12px;
            border-bottom: 1px solid #e5e7eb;
        }
    }

    @media (max-width: 768px) {
        .group-column {
            grid-template-columns: repeat(2, 1fr);
        }
        .category-container { gap: 6px; }
        .stat-box { font-size: 12px; padding: 5px 8px 5px 10px; }
        .stat-box:hover,
        .group-box:hover { transform: none; }
        .stat-box.selected,
        .group-box.selected { transform: none; }
    }

    @media (max-width: 640px) {
        .group-column { grid-template-columns: 1fr; }
    }

    /* ───────────────────────────────────────────────────────────────
       Command Center: Today's Focus hero, sparklines, motion, pulse
       ─────────────────────────────────────────────────────────────── */

    /* Atmospheric backdrop */
    .implementer-container {
        background:
            radial-gradient(ellipse 50% 38% at 0% 0%, rgba(0, 80, 181, 0.05), transparent 65%),
            radial-gradient(ellipse 38% 50% at 100% 100%, rgba(0, 164, 224, 0.04), transparent 60%);
        padding: 6px;
        border-radius: 14px;
    }

    /* Hero panel */
    .imp-hero {
        background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 24px 28px;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
        min-height: 60vh;
        display: flex;
        flex-direction: column;
    }
    .imp-hero-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
    }
    .imp-hero-hello {
        font: 600 24px/1.2 Poppins, system-ui, -apple-system, sans-serif;
        color: #0f172a;
        margin: 0 0 4px;
        letter-spacing: -0.01em;
    }
    .imp-hero-date {
        font: 500 13px/1 Poppins, system-ui, sans-serif;
        color: #64748b;
    }
    .imp-hero-meta {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font: 500 11px/1 Poppins, sans-serif;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        padding: 6px 12px;
        background: rgba(16, 185, 129, 0.08);
        border-radius: 999px;
    }
    .imp-hero-pulse {
        width: 8px; height: 8px; border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.6);
        animation: imp-pulse-ring 1.8s infinite cubic-bezier(0.66, 0, 0, 1);
    }
    .imp-hero-meta-label { color: #047857; }

    /* Today strip — 3 tiles */
    .imp-today-strip {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
        margin-top: 22px;
    }
    .imp-today-tile {
        border-radius: 10px;
        padding: 18px 18px 16px;
        cursor: pointer;
        border: 1px solid transparent;
        text-align: left;
        display: flex;
        flex-direction: column;
        gap: 4px;
        transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
        font-family: Poppins, system-ui, sans-serif;
    }
    .imp-today-tile:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
    }
    .imp-today-due      { background: rgba(0, 164, 224, 0.09); color: #075985; }
    .imp-today-due:hover      { border-color: rgba(0, 164, 224, 0.30); }
    .imp-today-overdue  { background: rgba(239, 68, 68, 0.09); color: #991b1b; }
    .imp-today-overdue:hover  { border-color: rgba(239, 68, 68, 0.30); }
    .imp-today-new      { background: rgba(245, 158, 11, 0.09); color: #92400e; }
    .imp-today-new:hover      { border-color: rgba(245, 158, 11, 0.30); }
    .imp-today-count {
        font: 700 30px/1 Poppins, system-ui, sans-serif;
        letter-spacing: -0.02em;
    }
    .imp-today-label {
        font: 600 11px/1 Poppins, sans-serif;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-top: 4px;
    }
    .imp-today-hint {
        font: 400 11.5px/1.3 Poppins, sans-serif;
        opacity: 0.7;
        margin-top: 2px;
    }

    /* Priority list */
    .imp-priority-block { margin-top: 26px; }
    .imp-priority-head {
        font: 600 11px/1 Poppins, sans-serif;
        text-transform: uppercase;
        letter-spacing: 0.10em;
        color: #64748b;
        margin-bottom: 10px;
    }
    .imp-priority-list {
        list-style: none; padding: 0; margin: 0;
        display: flex; flex-direction: column; gap: 8px;
    }
    .imp-priority-item {
        display: flex; align-items: center; gap: 12px;
        padding: 12px 14px;
        border-radius: 8px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        cursor: pointer;
        transition: border-color .15s ease, transform .15s ease, background .15s ease;
        font-family: Poppins, system-ui, sans-serif;
    }
    .imp-priority-item:hover {
        border-color: #0050B5;
        background: #f8fbff;
        transform: translateX(3px);
    }
    .imp-priority-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .imp-priority-high .imp-priority-dot { background: #ef4444; box-shadow: 0 0 0 4px rgba(239,68,68,0.15); }
    .imp-priority-med  .imp-priority-dot { background: #f59e0b; box-shadow: 0 0 0 4px rgba(245,158,11,0.12); }
    .imp-priority-low  .imp-priority-dot { background: #64748b; }
    .imp-priority-count {
        font: 700 15px/1 Poppins, sans-serif;
        color: #0f172a;
        min-width: 24px;
        text-align: right;
    }
    .imp-priority-label {
        font: 500 13.5px/1.3 Poppins, sans-serif;
        color: #1f2937;
        flex: 1;
    }
    .imp-priority-chev {
        font-size: 22px;
        color: #94a3b8;
        line-height: 1;
        transition: transform .15s ease, color .15s ease;
    }
    .imp-priority-item:hover .imp-priority-chev { color: #0050B5; transform: translateX(2px); }

    /* Action Inbox */
    .imp-inbox { margin-top: 26px; }
    .imp-inbox-head {
        display: flex; align-items: baseline; justify-content: space-between;
        margin-bottom: 12px;
        gap: 12px;
    }
    .imp-inbox-head-title {
        font: 600 12px/1 Poppins, sans-serif;
        text-transform: uppercase;
        letter-spacing: 0.10em;
        color: #64748b;
    }
    .imp-inbox-head-sub {
        font: 500 11.5px/1 Poppins, sans-serif;
        color: #94a3b8;
    }

    .imp-inbox-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 6px; }

    .imp-inbox-item {
        display: flex; align-items: center; gap: 14px;
        padding: 12px 14px;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        cursor: pointer;
        transition: border-color .15s ease, transform .15s ease, background .15s ease;
        font-family: Poppins, system-ui, sans-serif;
    }
    .imp-inbox-item:hover {
        border-color: #0050B5;
        background: #f8fbff;
        transform: translateX(3px);
    }
    .imp-inbox-item:focus-visible {
        outline: 2px solid #0050B5;
        outline-offset: 2px;
    }

    .imp-inbox-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .imp-inbox-high .imp-inbox-dot { background: #ef4444; box-shadow: 0 0 0 4px rgba(239,68,68,0.15); }
    .imp-inbox-med  .imp-inbox-dot { background: #f59e0b; box-shadow: 0 0 0 4px rgba(245,158,11,0.12); }
    .imp-inbox-low  .imp-inbox-dot { background: #64748b; }

    .imp-inbox-body { display: flex; flex-direction: column; gap: 4px; flex: 1; min-width: 0; }
    .imp-inbox-title {
        font: 600 13.5px/1.2 Poppins, sans-serif;
        color: #0f172a;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .imp-inbox-meta { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .imp-inbox-type-chip {
        font: 600 10px/1 Poppins, sans-serif;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 3px 7px;
        border-radius: 999px;
        color: var(--imp-type-color, #64748b);
        background: rgba(0,0,0,0.04);
    }
    .imp-inbox-age { font: 500 12px/1.2 Poppins, sans-serif; color: #64748b; }
    .imp-inbox-chev {
        font-size: 20px; line-height: 1; color: #94a3b8;
        transition: transform .15s ease, color .15s ease;
        flex-shrink: 0;
    }
    .imp-inbox-item:hover .imp-inbox-chev { color: #0050B5; transform: translateX(2px); }

    .imp-inbox-footer { margin-top: 14px; text-align: right; }
    .imp-inbox-footer a {
        font: 500 12.5px/1 Poppins, sans-serif;
        color: #0050B5;
        cursor: pointer;
        text-decoration: none;
    }
    .imp-inbox-footer a:hover { text-decoration: underline; }

    @media (max-width: 640px) {
        .imp-inbox-item { padding: 10px 12px; gap: 10px; }
        .imp-inbox-title { font-size: 13px; }
        .imp-inbox-chev { display: none; }
    }

    /* All-clear state */
    .imp-allclear {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 40px 20px;
    }
    .imp-allclear-icon {
        width: 56px; height: 56px;
        border-radius: 50%;
        background: rgba(16, 185, 129, 0.10);
        color: #10b981;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 4px;
    }
    .imp-allclear-icon svg { width: 30px; height: 30px; }
    .imp-allclear-title {
        font: 600 17px/1.2 Poppins, sans-serif;
        color: #0f172a; margin: 0;
    }
    .imp-allclear-sub {
        font: 400 13px/1.4 Poppins, sans-serif;
        color: #64748b; margin: 0;
    }

    /* Tile sparkline (sits behind content) */
    .group-box { position: relative; overflow: hidden; }
    .group-box .group-info,
    .group-box .group-count { position: relative; z-index: 1; }
    .imp-tile-spark {
        position: absolute;
        left: 4px; right: 0; bottom: 0;
        width: calc(100% - 4px);
        height: 22px;
        pointer-events: none;
        z-index: 0;
    }

    /* Hot-pulse dot inside tile */
    .imp-tile-pulse {
        width: 7px; height: 7px;
        border-radius: 50%;
        flex-shrink: 0;
        background: var(--imp-pulse-color, #ef4444);
        animation: imp-pulse-dot 1.8s infinite cubic-bezier(0.66, 0, 0, 1);
        margin-right: 2px;
    }
    @keyframes imp-pulse-dot {
        0%, 100% { transform: scale(1); opacity: 1; box-shadow: 0 0 0 0 var(--imp-pulse-color, #ef4444); }
        50%      { transform: scale(1.15); opacity: 0.85; box-shadow: 0 0 0 5px transparent; }
    }
    @keyframes imp-pulse-ring {
        0%   { box-shadow: 0 0 0 0 currentColor; }
        70%  { box-shadow: 0 0 0 8px transparent; }
        100% { box-shadow: 0 0 0 0 transparent; }
    }

    /* Per-tile accent variables (used by hover glow) */
    .group-project-status      { --imp-accent: 37, 99, 235; }
    .group-license             { --imp-accent: 139, 92, 246; }
    .group-ticketing           { --imp-accent: 236, 72, 153; }
    .group-migration           { --imp-accent: 16, 185, 129; }
    .group-follow-up           { --imp-accent: 245, 158, 11; }
    .group-project-closing     { --imp-accent: 239, 68, 68; }
    .group-implementer-request { --imp-accent: 99, 102, 241; }
    .group-thread              { --imp-accent: 13, 148, 136; }

    /* Hover glow keyed to accent */
    .group-box:hover {
        box-shadow:
            0 2px 6px rgba(0,0,0,0.06),
            0 0 0 1px rgba(var(--imp-accent, 0,80,181), 0.18),
            0 10px 26px rgba(var(--imp-accent, 0,80,181), 0.10);
    }
    .group-box.selected {
        box-shadow:
            0 2px 5px rgba(0,0,0,0.10),
            0 0 0 1px rgba(var(--imp-accent, 0,80,181), 0.22),
            0 12px 30px rgba(var(--imp-accent, 0,80,181), 0.12);
    }

    /* Staggered entrance for hero + tiles */
    .imp-hero {
        animation: imp-fade-up 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
    .group-box {
        animation: imp-fade-up 0.45s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
    .group-box:nth-child(1) { animation-delay: 80ms; }
    .group-box:nth-child(2) { animation-delay: 140ms; }
    .group-box:nth-child(3) { animation-delay: 200ms; }
    .group-box:nth-child(4) { animation-delay: 260ms; }
    .group-box:nth-child(5) { animation-delay: 320ms; }
    .group-box:nth-child(6) { animation-delay: 380ms; }
    .group-box:nth-child(7) { animation-delay: 440ms; }
    .group-box:nth-child(8) { animation-delay: 500ms; }
    @keyframes imp-fade-up {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Keyboard shortcuts overlay */
    .imp-shortcuts-overlay {
        position: fixed; inset: 0;
        background: rgba(15, 23, 42, 0.55);
        display: flex; align-items: center; justify-content: center;
        z-index: 9999;
        backdrop-filter: blur(4px);
    }
    .imp-shortcuts-card {
        background: white;
        border-radius: 14px;
        padding: 28px 32px;
        max-width: 380px;
        width: 90%;
        box-shadow: 0 30px 60px rgba(15, 23, 42, 0.25);
        font-family: Poppins, system-ui, sans-serif;
    }
    .imp-shortcuts-card h3 {
        font-weight: 600; font-size: 16px;
        margin: 0 0 14px; color: #0f172a;
    }
    .imp-shortcuts-card dl {
        display: grid; grid-template-columns: 80px 1fr;
        gap: 10px 16px; margin: 0;
    }
    .imp-shortcuts-card dt {
        font: 600 12px/1 Poppins, sans-serif;
        background: #f1f5f9;
        color: #1e293b;
        padding: 6px 10px;
        border-radius: 6px;
        text-align: center;
        align-self: center;
    }
    .imp-shortcuts-card dd {
        margin: 0;
        font: 500 13px/1.3 Poppins, sans-serif;
        color: #475569;
        align-self: center;
    }
    .imp-shortcuts-hint {
        margin-top: 18px;
        font: 400 11.5px/1.4 Poppins, sans-serif;
        color: #94a3b8;
        text-align: center;
    }

    /* Respect reduced motion */
    @media (prefers-reduced-motion: reduce) {
        .imp-hero, .group-box {
            animation: none !important;
        }
        .imp-tile-pulse, .imp-hero-pulse {
            animation: none !important;
        }
    }

    /* Today strip — responsive */
    @media (max-width: 768px) {
        .imp-today-strip { grid-template-columns: 1fr; }
        .imp-hero { padding: 20px; }
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

    // ── Command Center: hero + sparkline data ───────────────────────────────
    $impHour = (int) now()->format('G');
    $impGreeting = $impHour < 12 ? 'Good morning' : ($impHour < 18 ? 'Good afternoon' : 'Good evening');
    $impFirstName = trim(explode(' ', auth()->user()->name ?? 'there')[0]);

    $impDueTodayTotal = (int) ($followUpToday ?? 0) + (int) ($sessionsToday ?? 0) + (int) ($threadDueToday ?? 0);
    $impOverdueTotal = (int) ($threadOverdue ?? 0) + (int) ($followUpOverdue ?? 0) + (int) ($delayProjects ?? 0);
    $impNewSinceYesterdayTotal = \App\Models\ImplementerTicket::where('created_at', '>=', now()->subDay())->count();

    $impPriorityList = collect([
        ['count' => (int)($threadOverdue ?? 0),    'label' => 'overdue thread'.((int)($threadOverdue ?? 0) === 1 ? '' : 's').' need a reply', 'group' => 'thread',      'stat' => 'thread-overdue',   'severity' => 'high'],
        ['count' => (int)($followUpOverdue ?? 0),  'label' => 'follow-up'.((int)($followUpOverdue ?? 0) === 1 ? '' : 's').' overdue',         'group' => 'follow-up',   'stat' => 'follow-up-overdue','severity' => 'high'],
        ['count' => (int)($delayProjects ?? 0),    'label' => 'delayed project'.((int)($delayProjects ?? 0) === 1 ? '' : 's'),                'group' => 'project-status','stat' => 'status-delay',    'severity' => 'high'],
        ['count' => (int)($followUpToday ?? 0),    'label' => 'follow-up'.((int)($followUpToday ?? 0) === 1 ? '' : 's').' due today',         'group' => 'follow-up',   'stat' => 'follow-up-today',  'severity' => 'med'],
        ['count' => (int)($threadDueToday ?? 0),   'label' => 'thread'.((int)($threadDueToday ?? 0) === 1 ? '' : 's').' due today',           'group' => 'thread',      'stat' => 'thread-today',     'severity' => 'med'],
        ['count' => (int)($sessionsToday ?? 0),    'label' => 'session'.((int)($sessionsToday ?? 0) === 1 ? '' : 's').' scheduled today',     'group' => 'session-reminder','stat' => 'session-pending','severity' => 'med'],
    ])->filter(fn ($i) => $i['count'] > 0)->take(5)->values()->all();

    // Hot-pulse: which categories have any overdue items right now
    $impHasOverdue = [
        'project-status'      => (int) ($delayProjects ?? 0) > 0,
        'license'             => false,
        'ticket-reminder'     => (int) ($ticketCompletedOverdueCount ?? 0) > 0,
        'session-reminder'    => false,
        'follow-up'           => (int) ($followUpOverdue ?? 0) > 0,
        'project-closing'     => false,
        'implementer-request' => false,
        'thread'              => (int) ($threadOverdue ?? 0) > 0,
    ];

    // Sparkline: last 7 days of created_at activity per category (backward-looking workload trend)
    $impSparkClasses = [
        'project-status'      => \App\Models\SoftwareHandover::class,
        'license'             => \App\Models\SoftwareHandover::class,
        'ticket-reminder'     => \App\Models\ImplementerTicket::class,
        'session-reminder'    => \App\Models\ImplementerAppointment::class,
        'follow-up'           => \App\Models\Lead::class,
        'project-closing'     => \App\Models\SoftwareHandover::class,
        'implementer-request' => \App\Models\ImplementerHandoverRequest::class,
        'thread'              => \App\Models\ImplementerTicket::class,
    ];

    $impSparkBuild = function (string $modelClass): array {
        try {
            if (! class_exists($modelClass)) {
                return array_fill(0, 7, 0);
            }
            $start = now()->startOfDay()->subDays(6);
            $rows = $modelClass::query()
                ->where('created_at', '>=', $start)
                ->selectRaw('DATE(created_at) as day, COUNT(*) as n')
                ->groupBy('day')
                ->pluck('n', 'day')
                ->toArray();
            $points = [];
            for ($i = 0; $i < 7; $i++) {
                $d = $start->copy()->addDays($i)->format('Y-m-d');
                $points[] = (int) ($rows[$d] ?? 0);
            }
            return $points;
        } catch (\Throwable $e) {
            return array_fill(0, 7, 0);
        }
    };

    $impSparklines = [];
    foreach ($impSparkClasses as $key => $cls) {
        $impSparklines[$key] = $impSparkBuild($cls);
    }

    // ── Action Inbox: real pending/due items, mixed types, sorted by urgency ──
    $impInboxItems = collect();

    $impSafeLeadUrl = function ($id) {
        try {
            return \App\Filament\Resources\LeadResource::getUrl('view', ['record' => $id]);
        } catch (\Throwable $e) {
            return null;
        }
    };

    try {
        \App\Models\Lead::query()
            ->whereNotNull('follow_up_date')
            ->whereDate('follow_up_date', '<', today())
            ->orderBy('follow_up_date', 'asc')
            ->limit(10)
            ->get(['id', 'name', 'company_name', 'follow_up_date'])
            ->each(function ($lead) use ($impInboxItems, $impSafeLeadUrl) {
                $days = (int) today()->diffInDays(\Carbon\Carbon::parse($lead->follow_up_date));
                $impInboxItems->push([
                    'severity'   => 'high',
                    'priority'   => 100000 + $days,
                    'type'       => 'Follow-up',
                    'type_color' => '#f59e0b',
                    'title'      => $lead->company_name ?: ($lead->name ?: "Lead #{$lead->id}"),
                    'age'        => $days . ' day' . ($days === 1 ? '' : 's') . ' overdue',
                    'group'      => 'follow-up',
                    'stat'       => 'follow-up-overdue',
                    'url'        => $impSafeLeadUrl($lead->id),
                ]);
            });
    } catch (\Throwable $e) { /* defensive */ }

    try {
        \App\Models\Lead::query()
            ->whereDate('follow_up_date', today())
            ->limit(5)
            ->get(['id', 'name', 'company_name', 'follow_up_date'])
            ->each(function ($lead) use ($impInboxItems, $impSafeLeadUrl) {
                $impInboxItems->push([
                    'severity'   => 'med',
                    'priority'   => 50000,
                    'type'       => 'Follow-up',
                    'type_color' => '#f59e0b',
                    'title'      => $lead->company_name ?: ($lead->name ?: "Lead #{$lead->id}"),
                    'age'        => 'due today',
                    'group'      => 'follow-up',
                    'stat'       => 'follow-up-today',
                    'url'        => $impSafeLeadUrl($lead->id),
                ]);
            });
    } catch (\Throwable $e) { /* defensive */ }

    try {
        $threadComp = app(\App\Livewire\ImplementerDashboard\ImplementerThreadPendingAction::class);
        $threadComp->selectedUser = $selectedUser ?? session('selectedUser');
        if (method_exists($threadComp, 'getOverdueTickets')) {
            $overdueTickets = $threadComp->getOverdueTickets();
            if (is_iterable($overdueTickets)) {
                collect($overdueTickets)->take(5)->each(function ($t) use ($impInboxItems) {
                    $impInboxItems->push([
                        'severity'   => 'high',
                        'priority'   => 90000,
                        'type'       => 'Thread',
                        'type_color' => '#0d9488',
                        'title'      => optional($t->customer ?? null)->name ?? ('Ticket ' . ($t->ticket_number ?? '#' . ($t->id ?? '?'))),
                        'age'        => 'thread overdue',
                        'group'      => 'thread',
                        'stat'       => 'thread-overdue',
                        'url'        => null,
                    ]);
                });
            }
        }
    } catch (\Throwable $e) { /* defensive */ }

    $impActionInbox = $impInboxItems
        ->sortByDesc('priority')
        ->take(8)
        ->values()
        ->all();

    // ── Dynamic Today-tile hints ───────────────────────────────────────────
    $impTileHints = [];
    $impTileHints['due_today'] = (function () use ($followUpToday, $sessionsToday, $threadDueToday) {
        $parts = array_filter([
            (int) $followUpToday  > 0 ? $followUpToday  . ' follow-up'  . ((int) $followUpToday  === 1 ? '' : 's') : null,
            (int) $sessionsToday  > 0 ? $sessionsToday  . ' session'    . ((int) $sessionsToday  === 1 ? '' : 's') : null,
            (int) $threadDueToday > 0 ? $threadDueToday . ' thread'     . ((int) $threadDueToday === 1 ? '' : 's') : null,
        ]);
        return $parts ? implode(' · ', $parts) : 'Nothing due today';
    })();
    $impTileHints['overdue'] = (function () use ($followUpOverdue, $threadOverdue, $delayProjects) {
        $parts = array_filter([
            (int) $followUpOverdue > 0 ? $followUpOverdue . ' follow-up' . ((int) $followUpOverdue === 1 ? '' : 's') : null,
            (int) $threadOverdue   > 0 ? $threadOverdue   . ' thread'    . ((int) $threadOverdue   === 1 ? '' : 's') : null,
            (int) $delayProjects   > 0 ? $delayProjects   . ' delayed'   : null,
        ]);
        return $parts ? implode(' · ', $parts) : 'Past due date';
    })();
    $impTileHints['new'] = $impNewSinceYesterdayTotal > 0
        ? $impNewSinceYesterdayTotal . ' ticket' . ($impNewSinceYesterdayTotal === 1 ? '' : 's') . ' in 24h'
        : 'Nothing new';
@endphp

<div id="implementer-container" class="implementer-container"
    x-data="{
        selectedGroup: null,
        selectedStat: null,
        showShortcuts: false,

        setSelectedGroup(value) {
            if (this.selectedGroup === value) {
                this.selectedGroup = null;
                this.selectedStat = null;
            } else {
                this.selectedGroup = value;
                this.selectedStat = null;
            }
            this.syncUrl();
        },

        setSelectedStat(value) {
            if (this.selectedStat === value) {
                this.selectedStat = null;
            } else {
                this.selectedStat = value;
            }
            this.syncUrl();
        },

        syncUrl() {
            try {
                const url = new URL(window.location);
                this.selectedGroup ? url.searchParams.set('group', this.selectedGroup) : url.searchParams.delete('group');
                this.selectedStat ? url.searchParams.set('stat', this.selectedStat) : url.searchParams.delete('stat');
                window.history.replaceState({}, '', url);
            } catch (e) {}
        },

        readUrlState() {
            try {
                const params = new URLSearchParams(window.location.search);
                this.selectedGroup = params.get('group') || null;
                this.selectedStat = params.get('stat') || null;
            } catch (e) {}
        },

        handleKey(e) {
            if (e.target.matches('input, textarea, select, [contenteditable]')) return;
            const map = ['project-status','license','ticket-reminder','session-reminder','follow-up','project-closing','implementer-request','thread'];
            if (/^[1-8]$/.test(e.key)) {
                e.preventDefault();
                this.setSelectedGroup(map[parseInt(e.key) - 1]);
            } else if (e.key === 'Escape') {
                if (this.showShortcuts) { this.showShortcuts = false; return; }
                this.selectedGroup = null;
                this.selectedStat = null;
                this.syncUrl();
            } else if (e.key === '?' && e.shiftKey) {
                e.preventDefault();
                this.showShortcuts = !this.showShortcuts;
            }
        },

        init() {
            this.readUrlState();
            document.addEventListener('keydown', (e) => this.handleKey(e));
            setTimeout(() => { try { window.sessionStorage.setItem('imp-counted-v1', '1'); } catch (e) {} }, 800);
        }
    }"
    x-init="init()">

    <div class="dashboard-layout" wire:poll.300s>
        <!-- Left sidebar with main category groups -->
        <div class="group-column" role="tablist" aria-label="Dashboard categories">
            <!-- NO1 - PROJECT STATUS -->
            <div class="group-box group-project-status"
                :class="{'selected': selectedGroup === 'project-status'}"
                @click="setSelectedGroup('project-status')"
                role="tab"
                :aria-selected="selectedGroup === 'project-status'"
                tabindex="0">
                @include('filament.pages.partials._imp-tile-sparkline', ['points' => $impSparklines['project-status'], 'color' => '#2563eb'])
                <div class="group-info">
                    @if ($impHasOverdue['project-status'])
                        <span class="imp-tile-pulse" style="--imp-pulse-color: #2563eb;" aria-hidden="true"></span>
                    @endif
                    <x-heroicon-o-chart-bar class="group-icon" style="color: #2563eb;" />
                    <div class="group-title">Project Status</div>
                </div>
                <div class="group-count {{ $projectStatusTotal == 0 ? 'is-zero' : '' }}"
                    x-data="{ target: {{ (int)$projectStatusTotal }}, display: window.sessionStorage.getItem('imp-counted-v1') ? {{ (int)$projectStatusTotal }} : 0 }"
                    x-init="if (!window.sessionStorage.getItem('imp-counted-v1')) { const s = performance.now(); const tick = (n) => { const t = Math.min(1, (n - s) / 600); display = Math.round(target * (1 - Math.pow(1 - t, 3))); if (t < 1) requestAnimationFrame(tick); else display = target; }; requestAnimationFrame(tick); }"
                    x-text="display">{{ $projectStatusTotal }}</div>
            </div>

            <!-- NO2 - LICENSE CERTIFICATION -->
            <div class="group-box group-license"
                :class="{'selected': selectedGroup === 'license'}"
                @click="setSelectedGroup('license')"
                role="tab"
                :aria-selected="selectedGroup === 'license'"
                tabindex="0">
                @include('filament.pages.partials._imp-tile-sparkline', ['points' => $impSparklines['license'], 'color' => '#8b5cf6'])
                <div class="group-info">
                    @if ($impHasOverdue['license'])
                        <span class="imp-tile-pulse" style="--imp-pulse-color: #8b5cf6;" aria-hidden="true"></span>
                    @endif
                    <x-heroicon-o-clipboard-document-list class="group-icon" style="color: #8b5cf6;" />
                    <div class="group-title">Project Task</div>
                </div>
                <div class="group-count {{ $licenseTotal == 0 ? 'is-zero' : '' }}"
                    x-data="{ target: {{ (int)$licenseTotal }}, display: window.sessionStorage.getItem('imp-counted-v1') ? {{ (int)$licenseTotal }} : 0 }"
                    x-init="if (!window.sessionStorage.getItem('imp-counted-v1')) { const s = performance.now(); const tick = (n) => { const t = Math.min(1, (n - s) / 600); display = Math.round(target * (1 - Math.pow(1 - t, 3))); if (t < 1) requestAnimationFrame(tick); else display = target; }; requestAnimationFrame(tick); }"
                    x-text="display">{{ $licenseTotal }}</div>
            </div>

            <!-- NO3 - TICKET REMINDER -->
            <div class="group-box group-ticketing"
                :class="{'selected': selectedGroup === 'ticket-reminder'}"
                @click="setSelectedGroup('ticket-reminder')"
                role="tab"
                :aria-selected="selectedGroup === 'ticket-reminder'"
                tabindex="0">
                @include('filament.pages.partials._imp-tile-sparkline', ['points' => $impSparklines['ticket-reminder'], 'color' => '#ec4899'])
                <div class="group-info">
                    @if ($impHasOverdue['ticket-reminder'])
                        <span class="imp-tile-pulse" style="--imp-pulse-color: #ec4899;" aria-hidden="true"></span>
                    @endif
                    <x-heroicon-o-ticket class="group-icon" style="color: #ec4899;" />
                    <div class="group-title">Ticket Reminder</div>
                </div>
                <div class="group-count {{ $ticketReminderTotal == 0 ? 'is-zero' : '' }}"
                    x-data="{ target: {{ (int)$ticketReminderTotal }}, display: window.sessionStorage.getItem('imp-counted-v1') ? {{ (int)$ticketReminderTotal }} : 0 }"
                    x-init="if (!window.sessionStorage.getItem('imp-counted-v1')) { const s = performance.now(); const tick = (n) => { const t = Math.min(1, (n - s) / 600); display = Math.round(target * (1 - Math.pow(1 - t, 3))); if (t < 1) requestAnimationFrame(tick); else display = target; }; requestAnimationFrame(tick); }"
                    x-text="display">{{ $ticketReminderTotal }}</div>
            </div>

            <!-- NO4 - SESSION REMINDER -->
            <div class="group-box group-migration"
                :class="{'selected': selectedGroup === 'session-reminder'}"
                @click="setSelectedGroup('session-reminder')"
                role="tab"
                :aria-selected="selectedGroup === 'session-reminder'"
                tabindex="0">
                @include('filament.pages.partials._imp-tile-sparkline', ['points' => $impSparklines['session-reminder'], 'color' => '#10b981'])
                <div class="group-info">
                    @if ($impHasOverdue['session-reminder'])
                        <span class="imp-tile-pulse" style="--imp-pulse-color: #10b981;" aria-hidden="true"></span>
                    @endif
                    <x-heroicon-o-calendar-days class="group-icon" style="color: #10b981;" />
                    <div class="group-title">Session Reminder</div>
                </div>
                <div class="group-count {{ $sessionReminderTotal == 0 ? 'is-zero' : '' }}"
                    x-data="{ target: {{ (int)$sessionReminderTotal }}, display: window.sessionStorage.getItem('imp-counted-v1') ? {{ (int)$sessionReminderTotal }} : 0 }"
                    x-init="if (!window.sessionStorage.getItem('imp-counted-v1')) { const s = performance.now(); const tick = (n) => { const t = Math.min(1, (n - s) / 600); display = Math.round(target * (1 - Math.pow(1 - t, 3))); if (t < 1) requestAnimationFrame(tick); else display = target; }; requestAnimationFrame(tick); }"
                    x-text="display">{{ $sessionReminderTotal }}</div>
            </div>

            <!-- NO5 - PROJECT FOLLOW UP -->
            <div class="group-box group-follow-up"
                :class="{'selected': selectedGroup === 'follow-up'}"
                @click="setSelectedGroup('follow-up')"
                role="tab"
                :aria-selected="selectedGroup === 'follow-up'"
                tabindex="0">
                @include('filament.pages.partials._imp-tile-sparkline', ['points' => $impSparklines['follow-up'], 'color' => '#f59e0b'])
                <div class="group-info">
                    @if ($impHasOverdue['follow-up'])
                        <span class="imp-tile-pulse" style="--imp-pulse-color: #f59e0b;" aria-hidden="true"></span>
                    @endif
                    <x-heroicon-o-bell-alert class="group-icon" style="color: #f59e0b;" />
                    <div class="group-title">Follow Up Reminder</div>
                </div>
                <div class="group-count {{ $followUpTotal == 0 ? 'is-zero' : '' }}"
                    x-data="{ target: {{ (int)$followUpTotal }}, display: window.sessionStorage.getItem('imp-counted-v1') ? {{ (int)$followUpTotal }} : 0 }"
                    x-init="if (!window.sessionStorage.getItem('imp-counted-v1')) { const s = performance.now(); const tick = (n) => { const t = Math.min(1, (n - s) / 600); display = Math.round(target * (1 - Math.pow(1 - t, 3))); if (t < 1) requestAnimationFrame(tick); else display = target; }; requestAnimationFrame(tick); }"
                    x-text="display">{{ $followUpTotal }}</div>
            </div>

            <!-- NO6 - PROJECT CLOSING -->
            <div class="group-box group-project-closing"
                :class="{'selected': selectedGroup === 'project-closing'}"
                @click="setSelectedGroup('project-closing')"
                role="tab"
                :aria-selected="selectedGroup === 'project-closing'"
                tabindex="0">
                @include('filament.pages.partials._imp-tile-sparkline', ['points' => $impSparklines['project-closing'], 'color' => '#ef4444'])
                <div class="group-info">
                    @if ($impHasOverdue['project-closing'])
                        <span class="imp-tile-pulse" style="--imp-pulse-color: #ef4444;" aria-hidden="true"></span>
                    @endif
                    <x-heroicon-o-check-circle class="group-icon" style="color: #ef4444;" />
                    <div class="group-title">Project Closing</div>
                </div>
                <div class="group-count {{ $projectClosingTotal == 0 ? 'is-zero' : '' }}"
                    x-data="{ target: {{ (int)$projectClosingTotal }}, display: window.sessionStorage.getItem('imp-counted-v1') ? {{ (int)$projectClosingTotal }} : 0 }"
                    x-init="if (!window.sessionStorage.getItem('imp-counted-v1')) { const s = performance.now(); const tick = (n) => { const t = Math.min(1, (n - s) / 600); display = Math.round(target * (1 - Math.pow(1 - t, 3))); if (t < 1) requestAnimationFrame(tick); else display = target; }; requestAnimationFrame(tick); }"
                    x-text="display">{{ $projectClosingTotal }}</div>
            </div>

            <!-- NO7 - IMPLEMENTER REQUEST -->
            <div class="group-box group-implementer-request"
                :class="{'selected': selectedGroup === 'implementer-request'}"
                @click="setSelectedGroup('implementer-request')"
                role="tab"
                :aria-selected="selectedGroup === 'implementer-request'"
                tabindex="0">
                @include('filament.pages.partials._imp-tile-sparkline', ['points' => $impSparklines['implementer-request'], 'color' => '#6366f1'])
                <div class="group-info">
                    @if ($impHasOverdue['implementer-request'])
                        <span class="imp-tile-pulse" style="--imp-pulse-color: #6366f1;" aria-hidden="true"></span>
                    @endif
                    <x-heroicon-o-inbox-arrow-down class="group-icon" style="color: #6366f1;" />
                    <div class="group-title">Session Request</div>
                </div>
                <div class="group-count {{ $implementerRequestTotal == 0 ? 'is-zero' : '' }}"
                    x-data="{ target: {{ (int)$implementerRequestTotal }}, display: window.sessionStorage.getItem('imp-counted-v1') ? {{ (int)$implementerRequestTotal }} : 0 }"
                    x-init="if (!window.sessionStorage.getItem('imp-counted-v1')) { const s = performance.now(); const tick = (n) => { const t = Math.min(1, (n - s) / 600); display = Math.round(target * (1 - Math.pow(1 - t, 3))); if (t < 1) requestAnimationFrame(tick); else display = target; }; requestAnimationFrame(tick); }"
                    x-text="display">{{ $implementerRequestTotal }}</div>
            </div>

            <!-- NO8 - THREAD -->
            <div class="group-box group-thread"
                :class="{'selected': selectedGroup === 'thread'}"
                @click="setSelectedGroup('thread')"
                role="tab"
                :aria-selected="selectedGroup === 'thread'"
                tabindex="0">
                @include('filament.pages.partials._imp-tile-sparkline', ['points' => $impSparklines['thread'], 'color' => '#0d9488'])
                <div class="group-info">
                    @if ($impHasOverdue['thread'])
                        <span class="imp-tile-pulse" style="--imp-pulse-color: #0d9488;" aria-hidden="true"></span>
                    @endif
                    <x-heroicon-o-chat-bubble-left-right class="group-icon" style="color: #0d9488;" />
                    <div class="group-title">Thread</div>
                </div>
                <div class="group-count {{ $threadTotal == 0 ? 'is-zero' : '' }}"
                    x-data="{ target: {{ (int)$threadTotal }}, display: window.sessionStorage.getItem('imp-counted-v1') ? {{ (int)$threadTotal }} : 0 }"
                    x-init="if (!window.sessionStorage.getItem('imp-counted-v1')) { const s = performance.now(); const tick = (n) => { const t = Math.min(1, (n - s) / 600); display = Math.round(target * (1 - Math.pow(1 - t, 3))); if (t < 1) requestAnimationFrame(tick); else display = target; }; requestAnimationFrame(tick); }"
                    x-text="display">{{ $threadTotal }}</div>
            </div>

        </div>

        <!-- Right content column -->
        <div class="content-column" role="region" aria-label="Selected category content">
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
            <div class="category-container" x-show="selectedGroup === 'implementer-request'" x-transition>
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
            <div class="category-container" x-show="selectedGroup === 'thread'" x-transition>
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
                @include('filament.pages.partials._imp-today-focus')

                <div class="hint-message" x-show="selectedGroup !== null && selectedStat === null" x-transition>
                    <h3>Select a subcategory to view data</h3>
                    <p>Click on any of the subcategory boxes to display the corresponding information</p>
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

    <!-- Keyboard shortcuts overlay -->
    <div class="imp-shortcuts-overlay" x-show="showShortcuts" x-cloak
        @click.self="showShortcuts = false"
        x-transition.opacity>
        <div class="imp-shortcuts-card" role="dialog" aria-label="Keyboard shortcuts">
            <h3>Keyboard shortcuts</h3>
            <dl>
                <dt>1 – 8</dt><dd>Jump to category</dd>
                <dt>Esc</dt><dd>Clear selection / close overlay</dd>
                <dt>Shift + ?</dt><dd>Toggle this overlay</dd>
            </dl>
            <p class="imp-shortcuts-hint">Press Esc or click outside to dismiss</p>
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
