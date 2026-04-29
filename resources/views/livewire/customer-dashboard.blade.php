<div class="cdb-root" wire:poll.60s="refresh">
    <style>
        .cdb-root {
            --cdb-primary: #00a4e0;
            --cdb-accent-dark: #003c75;
            --cdb-accent-mid: #1a6dd4;
            --cdb-border: #e5e7eb;
            --cdb-text: #1f2937;
            --cdb-text-secondary: #6b7280;
            --cdb-text-muted: #9ca3af;
            --cdb-surface: #ffffff;
            --cdb-page-bg: #f8fafc;
            --cdb-success: #10b981;
            --cdb-warning: #f59e0b;
            --cdb-danger: #ef4444;
            --cdb-info-bg: #ecf6fd;
            --cdb-success-bg: #ecfdf5;
            --cdb-warning-bg: #fffbeb;
            --cdb-danger-bg: #fef2f2;
            --cdb-shadow-sm: 0 1px 2px rgba(15, 23, 42, 0.04);
            --cdb-shadow-md: 0 4px 12px rgba(15, 23, 42, 0.06);
            font-family: 'Poppins', sans-serif;
            color: var(--cdb-text);
        }

        /* Greeting */
        .cdb-greeting {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }
        .cdb-greeting-text h1 {
            font-size: 18px;
            font-weight: 600;
            color: var(--cdb-accent-dark);
            line-height: 1.25;
        }
        .cdb-greeting-text p {
            font-size: 12px;
            color: var(--cdb-text-secondary);
            margin-top: 2px;
        }
        .cdb-stage-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 9px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            background: var(--cdb-info-bg);
            color: var(--cdb-accent-dark);
            border: 1px solid #c8e7fa;
        }
        .cdb-refresh-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 500;
            color: var(--cdb-text-secondary);
            background: transparent;
            border: 1px solid var(--cdb-border);
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .cdb-refresh-btn:hover {
            color: var(--cdb-primary);
            border-color: var(--cdb-primary);
            background: var(--cdb-info-bg);
        }

        /* Card primitives */
        .cdb-card {
            background: var(--cdb-surface);
            border: 1px solid var(--cdb-border);
            border-radius: 10px;
            box-shadow: var(--cdb-shadow-sm);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .cdb-card.cdb-card-hoverable:hover {
            transform: translateY(-2px);
            box-shadow: var(--cdb-shadow-md);
        }
        .cdb-card-accent {
            border-top: 2px solid var(--cdb-primary);
        }
        .cdb-card-accent-success { border-top-color: var(--cdb-success); }
        .cdb-card-accent-warning { border-top-color: var(--cdb-warning); }
        .cdb-card-accent-danger { border-top-color: var(--cdb-danger); }
        .cdb-card-accent-neutral { border-top-color: var(--cdb-border); }

        .cdb-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 14px 16px 10px;
        }
        .cdb-card-title {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--cdb-text-secondary);
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }
        .cdb-card-title i {
            color: var(--cdb-primary);
            font-size: 12px;
        }
        .cdb-card-action {
            font-size: 10px;
            font-weight: 600;
            color: var(--cdb-primary);
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 3px 7px;
            border-radius: 5px;
            transition: background 0.15s ease;
            text-decoration: none;
        }
        .cdb-card-action:hover {
            background: var(--cdb-info-bg);
        }
        .cdb-card-body {
            padding: 0 16px 16px;
            flex: 1;
        }

        /* Hero band */
        .cdb-hero {
            display: grid;
            grid-template-columns: 8fr 4fr;
            gap: 14px;
            margin-bottom: 16px;
        }
        @media (max-width: 1279px) {
            .cdb-hero { grid-template-columns: 1fr; }
        }

        /* Journey Track */
        .cdb-journey {
            padding: 18px 20px;
            background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            border: 1px solid var(--cdb-border);
            border-radius: 10px;
            box-shadow: var(--cdb-shadow-sm);
            position: relative;
            overflow: hidden;
        }
        .cdb-journey-title {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--cdb-text-secondary);
            margin-bottom: 12px;
        }
        .cdb-track {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 0;
            margin-top: 8px;
            position: relative;
        }
        .cdb-track-node {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            text-decoration: none;
            color: inherit;
            transition: transform 0.2s ease;
        }
        .cdb-track-node:not(.cdb-track-node-upcoming):hover {
            transform: translateY(-2px);
        }
        .cdb-track-node + .cdb-track-node::before {
            content: '';
            position: absolute;
            top: 18px;
            right: 50%;
            width: 100%;
            height: 2px;
            background: var(--cdb-border);
            z-index: 0;
        }
        .cdb-track-node-done + .cdb-track-node::before,
        .cdb-track-node-current + .cdb-track-node::before {
            height: 2px;
            background: linear-gradient(90deg, var(--cdb-accent-dark) 0%, var(--cdb-primary) 100%);
            top: 18px;
        }
        .cdb-track-dot {
            width: 36px;
            height: 36px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            background: var(--cdb-surface);
            border: 2px solid var(--cdb-border);
            color: var(--cdb-text-muted);
            position: relative;
            z-index: 1;
            transition: all 0.3s ease;
        }
        .cdb-track-node-done .cdb-track-dot {
            background: var(--cdb-primary);
            border-color: var(--cdb-primary);
            color: #fff;
        }
        .cdb-track-node-current .cdb-track-dot {
            background: var(--cdb-surface);
            border-color: var(--cdb-primary);
            color: var(--cdb-primary);
            box-shadow: 0 0 0 0 rgba(0, 164, 224, 0.5);
            animation: cdb-pulse-ring 2s infinite cubic-bezier(0.66, 0, 0, 1);
        }
        @keyframes cdb-pulse-ring {
            0% {
                box-shadow: 0 0 0 0 rgba(0, 164, 224, 0.5);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(0, 164, 224, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(0, 164, 224, 0);
            }
        }
        .cdb-track-label {
            font-size: 10px;
            font-weight: 600;
            margin-top: 6px;
            text-align: center;
            color: var(--cdb-text-secondary);
            line-height: 1.3;
        }
        .cdb-track-node-current .cdb-track-label,
        .cdb-track-node-done .cdb-track-label {
            color: var(--cdb-text);
        }
        .cdb-track-date {
            font-size: 9px;
            color: var(--cdb-text-muted);
            margin-top: 2px;
            min-height: 11px;
        }
        .cdb-track-current-caption {
            margin-top: 14px;
            padding: 8px 12px;
            background: var(--cdb-info-bg);
            border-left: 3px solid var(--cdb-primary);
            border-radius: 5px;
            font-size: 12px;
            color: var(--cdb-accent-dark);
            font-weight: 500;
        }

        /* Hero Companion */
        .cdb-hero-side {
            padding: 16px 18px;
            background: var(--cdb-surface);
            border: 1px solid var(--cdb-border);
            border-radius: 10px;
            box-shadow: var(--cdb-shadow-sm);
            display: flex;
            flex-direction: column;
            min-height: 180px;
        }
        .cdb-companion-title {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--cdb-text-secondary);
            margin-bottom: 10px;
        }

        /* Next Session */
        .cdb-session {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            flex: 1;
        }
        .cdb-session-date {
            min-width: 54px;
            text-align: center;
            padding: 8px 6px;
            background: linear-gradient(135deg, var(--cdb-accent-dark), var(--cdb-primary));
            color: #fff;
            border-radius: 8px;
            line-height: 1;
            box-shadow: 0 3px 10px rgba(0, 164, 224, 0.22);
        }
        .cdb-session-day {
            font-size: 22px;
            font-weight: 700;
            display: block;
        }
        .cdb-session-month {
            font-size: 9px;
            font-weight: 600;
            letter-spacing: 0.1em;
            margin-top: 2px;
            opacity: 0.95;
        }
        .cdb-session-info {
            flex: 1;
            min-width: 0;
        }
        .cdb-session-label {
            font-size: 12px;
            color: var(--cdb-text-secondary);
        }
        .cdb-session-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--cdb-text);
            margin: 3px 0 5px;
            line-height: 1.3;
        }
        .cdb-session-time {
            font-size: 11px;
            color: var(--cdb-text-secondary);
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 3px;
        }
        .cdb-session-implementer {
            font-size: 11px;
            color: var(--cdb-text-secondary);
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .cdb-session-implementer i {
            color: var(--cdb-text-muted);
            font-size: 10px;
        }
        .cdb-join-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-top: 10px;
            padding: 6px 12px;
            background: var(--cdb-primary);
            color: #fff;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .cdb-join-btn:hover {
            background: var(--cdb-accent-mid);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(26, 109, 212, 0.25);
        }

        /* Welcome / Live Status */
        .cdb-welcome-step {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 6px 0;
            font-size: 12px;
            color: var(--cdb-text-secondary);
        }
        .cdb-welcome-step i {
            width: 24px;
            height: 24px;
            border-radius: 7px;
            background: var(--cdb-info-bg);
            color: var(--cdb-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            flex-shrink: 0;
        }
        .cdb-live-stat {
            display: flex;
            align-items: baseline;
            gap: 7px;
            margin: 10px 0 12px;
        }
        .cdb-live-stat-value {
            font-size: 26px;
            font-weight: 700;
            color: var(--cdb-accent-dark);
            line-height: 1;
        }
        .cdb-live-stat-label {
            font-size: 12px;
            color: var(--cdb-text-secondary);
        }
        .cdb-live-modules {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }
        .cdb-live-module {
            padding: 3px 9px;
            background: var(--cdb-success-bg);
            border: 1px solid #b9ecd6;
            border-radius: 999px;
            color: #047857;
            font-size: 10px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .cdb-no-session {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 18px;
            color: var(--cdb-text-muted);
            text-align: center;
            flex: 1;
        }
        .cdb-no-session i {
            font-size: 24px;
            margin-bottom: 6px;
        }
        .cdb-no-session-cta {
            margin-top: 6px;
            color: var(--cdb-primary);
            font-weight: 600;
            font-size: 12px;
            cursor: pointer;
            background: none;
            border: none;
            text-decoration: none;
        }

        /* Rows */
        .cdb-row {
            display: grid;
            gap: 14px;
            margin-bottom: 16px;
        }
        .cdb-row-primary {
            grid-template-columns: 5fr 4fr 3fr;
        }
        .cdb-row-secondary {
            grid-template-columns: repeat(3, 1fr);
        }
        @media (max-width: 1279px) {
            .cdb-row-primary { grid-template-columns: 1fr 1fr; }
            .cdb-row-primary > :nth-child(3) { grid-column: span 2; }
            .cdb-row-secondary { grid-template-columns: 1fr 1fr; }
            .cdb-row-secondary > :nth-child(3) { grid-column: span 2; }
        }
        @media (max-width: 1023px) {
            .cdb-row-primary, .cdb-row-secondary { grid-template-columns: 1fr; }
            .cdb-row-primary > :nth-child(3),
            .cdb-row-secondary > :nth-child(3) { grid-column: span 1; }
        }

        /* Action list */
        .cdb-list { list-style: none; padding: 0; margin: 0; }
        .cdb-list-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 7px;
            text-decoration: none;
            color: inherit;
            transition: background 0.15s ease;
            border-left: 3px solid transparent;
            cursor: pointer;
        }
        .cdb-list-item + .cdb-list-item {
            margin-top: 3px;
        }
        .cdb-list-item:hover {
            background: var(--cdb-info-bg);
        }
        .cdb-list-item.cdb-urgent {
            border-left-color: var(--cdb-danger);
            background: var(--cdb-danger-bg);
        }
        .cdb-list-item.cdb-urgent:hover {
            background: #fde7e7;
        }
        .cdb-list-icon {
            width: 30px;
            height: 30px;
            border-radius: 7px;
            background: var(--cdb-info-bg);
            color: var(--cdb-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 12px;
        }
        .cdb-urgent .cdb-list-icon {
            background: #fbe6e6;
            color: var(--cdb-danger);
        }
        .cdb-list-content { flex: 1; min-width: 0; }
        .cdb-list-title {
            font-size: 12px;
            font-weight: 600;
            color: var(--cdb-text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .cdb-list-subtitle {
            font-size: 11px;
            color: var(--cdb-text-secondary);
            margin-top: 1px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .cdb-list-age {
            font-size: 10px;
            color: var(--cdb-text-muted);
            white-space: nowrap;
            margin-left: 6px;
        }

        .cdb-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 22px 12px;
            color: var(--cdb-text-muted);
            text-align: center;
            min-height: 120px;
        }
        .cdb-empty-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--cdb-success-bg);
            color: var(--cdb-success);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            margin-bottom: 8px;
        }
        .cdb-empty-icon.cdb-empty-neutral {
            background: #f1f5f9;
            color: var(--cdb-text-muted);
        }
        .cdb-empty-icon.cdb-empty-locked {
            background: #f1f5f9;
            color: var(--cdb-text-muted);
        }
        .cdb-empty-text {
            font-size: 12px;
            font-weight: 500;
            color: var(--cdb-text-secondary);
        }
        .cdb-empty-subtext {
            font-size: 11px;
            color: var(--cdb-text-muted);
            margin-top: 3px;
        }

        /* Badge in card header */
        .cdb-count-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 7px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
            background: var(--cdb-info-bg);
            color: var(--cdb-primary);
        }
        .cdb-count-badge.cdb-count-urgent {
            background: var(--cdb-danger-bg);
            color: var(--cdb-danger);
        }

        /* Progress gauge */
        .cdb-gauge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }
        .cdb-gauge-svg {
            width: 78px;
            height: 78px;
            transform: rotate(-90deg);
        }
        .cdb-gauge-track {
            stroke: #e5edf5;
            fill: none;
            stroke-width: 7;
        }
        .cdb-gauge-fill {
            stroke: var(--cdb-primary);
            fill: none;
            stroke-width: 7;
            stroke-linecap: round;
            transition: stroke-dashoffset 0.6s ease;
        }
        .cdb-gauge-text {
            position: relative;
            margin-top: -78px;
            height: 78px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            pointer-events: none;
        }
        .cdb-gauge-value {
            font-size: 18px;
            font-weight: 700;
            color: var(--cdb-accent-dark);
            line-height: 1;
        }
        .cdb-gauge-suffix {
            font-size: 9px;
            color: var(--cdb-text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-top: 2px;
        }
        .cdb-gauge-meta {
            font-size: 10px;
            color: var(--cdb-text-secondary);
        }

        .cdb-modules { margin-top: 10px; }
        .cdb-module-row {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 4px 0;
            font-size: 11px;
        }
        .cdb-module-name {
            flex: 1;
            color: var(--cdb-text-secondary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .cdb-module-bar {
            width: 70px;
            height: 4px;
            background: #e5edf5;
            border-radius: 999px;
            overflow: hidden;
            flex-shrink: 0;
        }
        .cdb-module-bar-fill {
            height: 100%;
            background: var(--cdb-primary);
            border-radius: 999px;
            transition: width 0.6s ease;
        }
        .cdb-module-count {
            font-size: 10px;
            color: var(--cdb-text-muted);
            white-space: nowrap;
            min-width: 32px;
            text-align: right;
        }

        /* Tickets list */
        .cdb-ticket-row {
            display: flex;
            flex-direction: column;
            gap: 5px;
            padding: 9px 12px;
            border-radius: 7px;
            text-decoration: none;
            color: inherit;
            transition: background 0.15s ease;
        }
        .cdb-ticket-row:hover {
            background: var(--cdb-info-bg);
        }
        .cdb-ticket-row + .cdb-ticket-row {
            margin-top: 3px;
        }
        .cdb-ticket-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 7px;
        }
        .cdb-ticket-number {
            font-family: 'JetBrains Mono', 'Menlo', monospace;
            font-size: 10px;
            font-weight: 700;
            color: var(--cdb-accent-dark);
            background: var(--cdb-info-bg);
            padding: 2px 7px;
            border-radius: 4px;
            white-space: nowrap;
        }
        .cdb-sla-pill {
            font-size: 9px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 999px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .cdb-sla-on_track { background: var(--cdb-success-bg); color: #047857; }
        .cdb-sla-at_risk { background: var(--cdb-warning-bg); color: #b45309; }
        .cdb-sla-overdue { background: var(--cdb-danger-bg); color: var(--cdb-danger); }
        .cdb-sla-resolved { background: #f1f5f9; color: var(--cdb-text-muted); }
        .cdb-ticket-subject {
            font-size: 12px;
            color: var(--cdb-text);
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .cdb-ticket-meta {
            font-size: 10px;
            color: var(--cdb-text-muted);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .cdb-ticket-meta i { font-size: 9px; }

        /* Migration tiles */
        .cdb-tiles {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 7px;
            margin-bottom: 10px;
        }
        .cdb-tile {
            padding: 10px 8px;
            border-radius: 7px;
            text-align: center;
            border: 1px solid var(--cdb-border);
            background: var(--cdb-surface);
            text-decoration: none;
            color: inherit;
            transition: all 0.15s ease;
        }
        .cdb-tile:hover { transform: translateY(-1px); }
        .cdb-tile-value {
            font-size: 17px;
            font-weight: 700;
            color: var(--cdb-text);
            line-height: 1;
        }
        .cdb-tile-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--cdb-text-secondary);
            margin-top: 3px;
            font-weight: 600;
        }
        .cdb-tile-pending { background: #f8fafc; }
        .cdb-tile-submitted .cdb-tile-value { color: var(--cdb-primary); }
        .cdb-tile-approved { background: var(--cdb-success-bg); }
        .cdb-tile-approved .cdb-tile-value { color: #047857; }
        .cdb-tile-rejected { background: var(--cdb-danger-bg); }
        .cdb-tile-rejected .cdb-tile-value { color: var(--cdb-danger); }
        .cdb-progress-line {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 11px;
            color: var(--cdb-text-secondary);
            margin-bottom: 5px;
        }
        .cdb-progress-bar {
            height: 5px;
            background: #e5edf5;
            border-radius: 999px;
            overflow: hidden;
        }
        .cdb-progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--cdb-accent-mid), var(--cdb-primary));
            border-radius: 999px;
            transition: width 0.6s ease;
        }

        /* Activity */
        .cdb-activity-row {
            display: flex;
            align-items: flex-start;
            gap: 9px;
            padding: 7px 10px 7px 8px;
            border-radius: 6px;
            border-left: 3px solid transparent;
            text-decoration: none;
            color: inherit;
            transition: background 0.15s ease;
            cursor: pointer;
        }
        .cdb-activity-row:hover {
            background: var(--cdb-info-bg);
        }
        .cdb-activity-row.cdb-activity-unread {
            border-left-color: var(--cdb-primary);
            background: var(--cdb-info-bg);
        }
        .cdb-activity-icon {
            width: 24px;
            height: 24px;
            border-radius: 7px;
            background: #f1f5f9;
            color: var(--cdb-text-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            flex-shrink: 0;
            margin-top: 2px;
        }
        .cdb-activity-unread .cdb-activity-icon {
            background: var(--cdb-info-bg);
            color: var(--cdb-primary);
        }
        .cdb-activity-content { flex: 1; min-width: 0; }
        .cdb-activity-message {
            font-size: 11px;
            color: var(--cdb-text);
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .cdb-activity-time {
            font-size: 9px;
            color: var(--cdb-text-muted);
            margin-top: 2px;
        }

        /* Resources */
        .cdb-resource-row {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 8px 10px;
            border-radius: 7px;
            text-decoration: none;
            color: inherit;
            transition: background 0.15s ease;
        }
        .cdb-resource-row + .cdb-resource-row { margin-top: 3px; }
        .cdb-resource-row:hover { background: var(--cdb-info-bg); }
        .cdb-resource-icon {
            width: 30px; height: 30px; border-radius: 7px;
            background: var(--cdb-info-bg); color: var(--cdb-primary);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; font-size: 12px;
        }
        .cdb-resource-icon.cdb-resource-video {
            background: #fef2f2; color: var(--cdb-danger);
        }
        .cdb-resource-content { flex: 1; min-width: 0; }
        .cdb-resource-title {
            font-size: 11px; font-weight: 600; color: var(--cdb-text);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .cdb-resource-meta {
            font-size: 9px; color: var(--cdb-text-muted);
            margin-top: 2px;
        }
        .cdb-resource-action {
            color: var(--cdb-text-muted); font-size: 11px; flex-shrink: 0;
        }

        /* Quick Actions */
        .cdb-quick-actions {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            padding: 12px;
            background: var(--cdb-surface);
            border: 1px solid var(--cdb-border);
            border-radius: 10px;
            box-shadow: var(--cdb-shadow-sm);
        }
        @media (max-width: 1023px) {
            .cdb-quick-actions { overflow-x: auto; grid-template-columns: repeat(5, minmax(140px, 1fr)); }
        }
        .cdb-qa-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 9px 12px;
            background: var(--cdb-surface);
            border: 1px solid var(--cdb-border);
            border-radius: 8px;
            color: var(--cdb-text);
            font-size: 11px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            width: 100%;
        }
        .cdb-qa-btn:hover {
            background: var(--cdb-info-bg);
            border-color: var(--cdb-primary);
            color: var(--cdb-primary);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 164, 224, 0.15);
        }
        .cdb-qa-btn i {
            color: var(--cdb-primary);
            font-size: 12px;
        }
        .cdb-qa-btn:hover i {
            transform: scale(1.05);
        }

        /* Orientation tip */
        .cdb-tip {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 8px 12px;
            margin-bottom: 14px;
            background: var(--cdb-info-bg);
            border: 1px solid #c8e7fa;
            border-radius: 8px;
            font-size: 11px;
            color: var(--cdb-accent-dark);
        }
        .cdb-tip-text { display: inline-flex; align-items: center; gap: 7px; }
        .cdb-tip-text i { color: var(--cdb-primary); }
        .cdb-tip-dismiss {
            background: transparent; border: none; cursor: pointer;
            color: var(--cdb-text-secondary); font-size: 12px; padding: 3px;
        }
        .cdb-tip-dismiss:hover { color: var(--cdb-danger); }
    </style>

    {{-- Orientation tip (one-time, dismissible) --}}
    <div class="cdb-tip" x-data="{ show: !localStorage.getItem('cdbDismissedTip') }" x-show="show" style="display: none;">
        <span class="cdb-tip-text">
            <i class="fas fa-lightbulb"></i>
            Looking for the calendar? It's still in the Implementation menu under <strong>Meeting Schedule</strong>.
        </span>
        <button class="cdb-tip-dismiss" @click="localStorage.setItem('cdbDismissedTip', '1'); show = false" title="Dismiss">
            <i class="fas fa-xmark"></i>
        </button>
    </div>

    {{-- Greeting strip --}}
    <div class="cdb-greeting">
        <div class="cdb-greeting-text">
            <h1>{{ $greetingTime }}, {{ $customerName }}</h1>
            <p>{{ $stageCaption }}</p>
        </div>
        <div style="display: inline-flex; align-items: center; gap: 10px;">
            <span class="cdb-stage-pill">
                <i class="fas fa-circle" style="font-size: 6px;"></i>
                {{ str_replace('_', ' ', $journeyStage) }}
            </span>
            <button type="button" class="cdb-refresh-btn" wire:click="refresh" wire:loading.attr="disabled">
                <i class="fas fa-arrows-rotate" wire:loading.class="fa-spin" wire:target="refresh"></i>
                <span wire:loading.remove wire:target="refresh">Refresh</span>
                <span wire:loading wire:target="refresh">Refreshing</span>
            </button>
        </div>
    </div>

    {{-- HERO BAND --}}
    <div class="cdb-hero">
        {{-- Journey Track --}}
        <div class="cdb-journey">
            <div class="cdb-journey-title">Implementation Journey</div>
            <div class="cdb-track">
                @foreach($journeyNodes as $node)
                    <a href="{{ $node['deepLink'] }}"
                       class="cdb-track-node cdb-track-node-{{ $node['status'] }}"
                       title="{{ $node['label'] }}">
                        <div class="cdb-track-dot">
                            @if($node['status'] === 'done')
                                <i class="fas fa-check"></i>
                            @else
                                <i class="fas {{ $node['icon'] }}"></i>
                            @endif
                        </div>
                        <div class="cdb-track-label">{{ $node['label'] }}</div>
                        <div class="cdb-track-date">{{ $node['date'] ?? '' }}</div>
                    </a>
                @endforeach
            </div>
            @php
                $currentNode = collect($journeyNodes)->firstWhere('status', 'current');
                $overall = (int)($progressSummary['overallProgress'] ?? 0);
            @endphp
            @if($currentNode)
                <div class="cdb-track-current-caption">
                    <i class="fas fa-location-arrow" style="margin-right: 6px;"></i>
                    Currently in <strong>{{ $currentNode['label'] }}</strong>
                    @if($hasProjectPlan && $overall > 0)
                        — {{ $overall }}% complete overall
                    @endif
                </div>
            @endif
        </div>

        {{-- Hero Companion --}}
        <div class="cdb-hero-side">
            @if($heroCompanion && $heroCompanion['type'] === 'next_session')
                <div class="cdb-companion-title">Next Session</div>
                <div class="cdb-session">
                    <div class="cdb-session-date">
                        <span class="cdb-session-day">{{ $heroCompanion['day'] }}</span>
                        <span class="cdb-session-month">{{ $heroCompanion['month'] }}</span>
                    </div>
                    <div class="cdb-session-info">
                        <div class="cdb-session-label">{{ $heroCompanion['weekday'] }}</div>
                        <div class="cdb-session-name">{{ $heroCompanion['session'] }}</div>
                        @if(!empty($heroCompanion['timeRange']))
                            <div class="cdb-session-time">
                                <i class="far fa-clock"></i> {{ $heroCompanion['timeRange'] }}
                            </div>
                        @endif
                        <div class="cdb-session-implementer">
                            <i class="fas fa-user"></i> {{ $heroCompanion['implementer'] }}
                        </div>
                        @if(!empty($heroCompanion['meetingLink']))
                            <a href="{{ $heroCompanion['meetingLink'] }}" target="_blank" rel="noopener" class="cdb-join-btn">
                                <i class="fas fa-video"></i> Join Meeting
                            </a>
                        @endif
                    </div>
                </div>
            @elseif($heroCompanion && $heroCompanion['type'] === 'welcome')
                <div class="cdb-companion-title">Getting Started</div>
                <p style="font-size: 13px; color: var(--cdb-text-secondary); margin-bottom: 8px;">
                    Welcome aboard. While we get your kick-off lined up, here's what to expect:
                </p>
                @foreach($heroCompanion['steps'] as $step)
                    <div class="cdb-welcome-step">
                        <i class="fas {{ $step['icon'] }}"></i>
                        {{ $step['label'] }}
                    </div>
                @endforeach
            @elseif($heroCompanion && $heroCompanion['type'] === 'live_status')
                <div class="cdb-companion-title">Live Status</div>
                <div class="cdb-live-stat">
                    <span class="cdb-live-stat-value">{{ $heroCompanion['daysLive'] }}</span>
                    <span class="cdb-live-stat-label">days live{{ $heroCompanion['projectCode'] ? ' · ' . $heroCompanion['projectCode'] : '' }}</span>
                </div>
                @if(count($heroCompanion['modules']) > 0)
                    <div style="font-size: 11px; color: var(--cdb-text-secondary); margin-bottom: 6px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em;">Active modules</div>
                    <div class="cdb-live-modules">
                        @foreach($heroCompanion['modules'] as $mod)
                            <span class="cdb-live-module">
                                <i class="fas fa-check" style="font-size: 8px;"></i>
                                {{ $mod['label'] }}
                            </span>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="cdb-companion-title">Schedule</div>
                <div class="cdb-no-session">
                    <i class="far fa-calendar"></i>
                    <div style="font-size: 13px; color: var(--cdb-text-secondary); font-weight: 500;">No upcoming sessions</div>
                    <button type="button" class="cdb-no-session-cta" onclick="switchTab('calendar')">Open calendar →</button>
                </div>
            @endif
        </div>
    </div>

    {{-- PRIMARY ROW --}}
    <div class="cdb-row cdb-row-primary">
        {{-- Action Required --}}
        <div class="cdb-card cdb-card-accent {{ $actionItemsTotal === 0 ? 'cdb-card-accent-success' : (collect($actionItems)->contains('urgent', true) ? 'cdb-card-accent-warning' : 'cdb-card-accent') }}">
            <div class="cdb-card-header">
                <span class="cdb-card-title">
                    <i class="fas fa-bolt"></i> Action Required
                </span>
                @if($actionItemsTotal > 0)
                    <span class="cdb-count-badge {{ collect($actionItems)->contains('urgent', true) ? 'cdb-count-urgent' : '' }}">{{ $actionItemsTotal }}</span>
                @endif
            </div>
            <div class="cdb-card-body">
                @if($actionItemsTotal === 0)
                    <div class="cdb-empty">
                        <div class="cdb-empty-icon"><i class="fas fa-check"></i></div>
                        <div class="cdb-empty-text">You're all caught up</div>
                        <div class="cdb-empty-subtext">Nothing waiting on you right now.</div>
                    </div>
                @else
                    <ul class="cdb-list">
                        @foreach($actionItems as $item)
                            <li>
                                <a href="{{ $item['url'] }}" class="cdb-list-item {{ $item['urgent'] ? 'cdb-urgent' : '' }}">
                                    <div class="cdb-list-icon"><i class="fas {{ $item['icon'] }}"></i></div>
                                    <div class="cdb-list-content">
                                        <div class="cdb-list-title">{{ $item['title'] }}</div>
                                        @if(!empty($item['subtitle']))
                                            <div class="cdb-list-subtitle">{{ $item['subtitle'] }}</div>
                                        @endif
                                    </div>
                                    @if(!empty($item['age']))
                                        <span class="cdb-list-age">{{ $item['age'] }}</span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        {{-- Implementation Progress --}}
        <div class="cdb-card cdb-card-accent">
            <div class="cdb-card-header">
                <span class="cdb-card-title">
                    <i class="fas fa-chart-line"></i> Implementation Progress
                </span>
                @if($hasProjectPlan)
                    <button type="button" class="cdb-card-action" onclick="switchTab('project')">View plan →</button>
                @endif
            </div>
            <div class="cdb-card-body">
                @if($hasProjectPlan)
                    @php
                        $pct = (int)($progressSummary['overallProgress'] ?? 0);
                        $circumference = 2 * pi() * 40;
                        $offset = $circumference * (1 - $pct / 100);
                    @endphp
                    <div class="cdb-gauge">
                        <div style="position: relative;">
                            <svg class="cdb-gauge-svg" viewBox="0 0 96 96">
                                <circle class="cdb-gauge-track" cx="48" cy="48" r="40"/>
                                <circle class="cdb-gauge-fill" cx="48" cy="48" r="40"
                                        stroke-dasharray="{{ number_format($circumference, 2, '.', '') }}"
                                        stroke-dashoffset="{{ number_format($offset, 2, '.', '') }}"/>
                            </svg>
                            <div class="cdb-gauge-text">
                                <div class="cdb-gauge-value">{{ $pct }}%</div>
                                <div class="cdb-gauge-suffix">overall</div>
                            </div>
                        </div>
                        <div class="cdb-gauge-meta">
                            <div style="font-size: 12px; color: var(--cdb-text); font-weight: 600;">
                                {{ $progressSummary['completedTasks'] ?? 0 }} / {{ $progressSummary['totalTasks'] ?? 0 }}
                                <span style="color: var(--cdb-text-secondary); font-weight: 400;">tasks done</span>
                            </div>
                            <div style="font-size: 11px; color: var(--cdb-text-muted); margin-top: 2px;">
                                Across {{ count($progressSummary['modules'] ?? []) }} modules
                            </div>
                        </div>
                    </div>
                    @if(!empty($progressSummary['modules']))
                        <div class="cdb-modules">
                            @foreach($progressSummary['modules'] as $mod)
                                <div class="cdb-module-row">
                                    <span class="cdb-module-name">{{ $mod['name'] }}</span>
                                    <div class="cdb-module-bar">
                                        <div class="cdb-module-bar-fill" style="width: {{ $mod['progress'] }}%;"></div>
                                    </div>
                                    <span class="cdb-module-count">{{ $mod['completed'] }}/{{ $mod['total'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="cdb-empty">
                        <div class="cdb-empty-icon cdb-empty-locked"><i class="fas fa-lock"></i></div>
                        <div class="cdb-empty-text">Project plan not available yet</div>
                        <div class="cdb-empty-subtext">Your plan will appear here after kick-off.</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Open Tickets --}}
        <div class="cdb-card cdb-card-accent {{ $ticketsHealthState === 'overdue' ? 'cdb-card-accent-danger' : ($ticketsHealthState === 'at_risk' ? 'cdb-card-accent-warning' : 'cdb-card-accent-success') }}">
            <div class="cdb-card-header">
                <span class="cdb-card-title">
                    <i class="fas fa-life-ring"></i> Open Tickets
                </span>
                <span class="cdb-count-badge {{ $ticketsHealthState === 'overdue' ? 'cdb-count-urgent' : '' }}">{{ $ticketsTotal }}</span>
            </div>
            <div class="cdb-card-body">
                @if($ticketsTotal === 0)
                    <div class="cdb-empty">
                        <div class="cdb-empty-icon"><i class="fas fa-check"></i></div>
                        <div class="cdb-empty-text">No open tickets</div>
                        <div class="cdb-empty-subtext">All clear — no support items in flight.</div>
                    </div>
                @else
                    @foreach($tickets as $ticket)
                        <a href="{{ $ticket['url'] }}" class="cdb-ticket-row">
                            <div class="cdb-ticket-top">
                                <span class="cdb-ticket-number">{{ $ticket['number'] }}</span>
                                <span class="cdb-sla-pill cdb-sla-{{ $ticket['sla'] }}">{{ $ticket['sla'] === 'on_track' ? 'On track' : ($ticket['sla'] === 'at_risk' ? 'At risk' : ucfirst($ticket['sla'])) }}</span>
                            </div>
                            <div class="cdb-ticket-subject">{{ $ticket['subject'] }}</div>
                            <div class="cdb-ticket-meta">
                                <span><i class="far fa-clock"></i> {{ $ticket['timeRemaining'] }}</span>
                                <span><i class="far fa-comment"></i> {{ $ticket['replyCount'] }}</span>
                            </div>
                        </a>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    {{-- SECONDARY ROW --}}
    <div class="cdb-row cdb-row-secondary">
        {{-- Data Migration Status --}}
        <div class="cdb-card cdb-card-accent">
            <div class="cdb-card-header">
                <span class="cdb-card-title">
                    <i class="fas fa-database"></i> Data Migration
                </span>
                <button type="button" class="cdb-card-action" onclick="switchTab('dataMigration')">Open →</button>
            </div>
            <div class="cdb-card-body">
                @if($migrationCounts['total'] === 0)
                    <div class="cdb-empty">
                        <div class="cdb-empty-icon cdb-empty-neutral"><i class="fas fa-database"></i></div>
                        <div class="cdb-empty-text">No migration files yet</div>
                        <div class="cdb-empty-subtext">Migration starts after kick-off.</div>
                    </div>
                @else
                    <div class="cdb-tiles">
                        <a href="?tab=dataMigration" class="cdb-tile cdb-tile-pending">
                            <div class="cdb-tile-value">{{ $migrationCounts['pending'] }}</div>
                            <div class="cdb-tile-label">Pending</div>
                        </a>
                        <a href="?tab=dataMigration" class="cdb-tile cdb-tile-submitted">
                            <div class="cdb-tile-value">{{ $migrationCounts['submitted'] }}</div>
                            <div class="cdb-tile-label">Submitted</div>
                        </a>
                        <a href="?tab=dataMigration" class="cdb-tile cdb-tile-approved">
                            <div class="cdb-tile-value">{{ $migrationCounts['approved'] }}</div>
                            <div class="cdb-tile-label">Approved</div>
                        </a>
                        <a href="?tab=dataMigration" class="cdb-tile cdb-tile-rejected">
                            <div class="cdb-tile-value">{{ $migrationCounts['rejected'] }}</div>
                            <div class="cdb-tile-label">Rejected</div>
                        </a>
                    </div>
                    <div class="cdb-progress-line">
                        <span>{{ $migrationCounts['completed'] }} of {{ $migrationCounts['total'] }} approved</span>
                        <span style="font-weight: 600; color: var(--cdb-accent-dark);">{{ $migrationCounts['percent'] }}%</span>
                    </div>
                    <div class="cdb-progress-bar">
                        <div class="cdb-progress-bar-fill" style="width: {{ $migrationCounts['percent'] }}%;"></div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="cdb-card cdb-card-accent">
            <div class="cdb-card-header">
                <span class="cdb-card-title">
                    <i class="fas fa-bell"></i> Recent Activity
                    @if($unreadNotifications > 0)
                        <span class="cdb-count-badge">{{ $unreadNotifications }}</span>
                    @endif
                </span>
                @if($unreadNotifications > 0)
                    <button type="button" class="cdb-card-action" wire:click="markAllActivityRead">Mark all read</button>
                @endif
            </div>
            <div class="cdb-card-body">
                @if(count($recentActivity) === 0)
                    <div class="cdb-empty">
                        <div class="cdb-empty-icon cdb-empty-neutral"><i class="fas fa-bell-slash"></i></div>
                        <div class="cdb-empty-text">No recent activity</div>
                        <div class="cdb-empty-subtext">You'll see updates here as they happen.</div>
                    </div>
                @else
                    @foreach($recentActivity as $activity)
                        @if(!empty($activity['url']))
                            <button type="button"
                                    class="cdb-activity-row {{ $activity['unread'] ? 'cdb-activity-unread' : '' }}"
                                    style="width: 100%; border: none; background: transparent; text-align: left;"
                                    wire:click="openActivity('{{ $activity['id'] }}')">
                                <div class="cdb-activity-icon"><i class="fas {{ $activity['icon'] }}"></i></div>
                                <div class="cdb-activity-content">
                                    <div class="cdb-activity-message">{{ $activity['message'] }}</div>
                                    <div class="cdb-activity-time">{{ $activity['time'] }}</div>
                                </div>
                            </button>
                        @else
                            <button type="button"
                                    class="cdb-activity-row {{ $activity['unread'] ? 'cdb-activity-unread' : '' }}"
                                    style="width: 100%; border: none; background: transparent; text-align: left;"
                                    wire:click="markActivityRead('{{ $activity['id'] }}')">
                                <div class="cdb-activity-icon"><i class="fas {{ $activity['icon'] }}"></i></div>
                                <div class="cdb-activity-content">
                                    <div class="cdb-activity-message">{{ $activity['message'] }}</div>
                                    <div class="cdb-activity-time">{{ $activity['time'] }}</div>
                                </div>
                            </button>
                        @endif
                    @endforeach
                @endif
            </div>
        </div>

        {{-- Resources --}}
        <div class="cdb-card cdb-card-accent">
            <div class="cdb-card-header">
                <span class="cdb-card-title">
                    <i class="fas fa-folder-open"></i> Resources
                </span>
                <button type="button" class="cdb-card-action" onclick="switchTab('softwareHandover')">View all →</button>
            </div>
            <div class="cdb-card-body">
                @if(count($resources) === 0)
                    <div class="cdb-empty">
                        <div class="cdb-empty-icon cdb-empty-neutral"><i class="fas fa-folder-open"></i></div>
                        <div class="cdb-empty-text">No resources yet</div>
                        <div class="cdb-empty-subtext">Handover docs and recordings will appear here.</div>
                    </div>
                @else
                    @foreach($resources as $resource)
                        <a href="{{ $resource['url'] }}"
                           class="cdb-resource-row"
                           {{ $resource['external'] ? 'target=_blank rel=noopener' : '' }}>
                            <div class="cdb-resource-icon {{ $resource['type'] === 'recording' ? 'cdb-resource-video' : '' }}">
                                <i class="fas {{ $resource['icon'] }}"></i>
                            </div>
                            <div class="cdb-resource-content">
                                <div class="cdb-resource-title">{{ $resource['title'] }}</div>
                                <div class="cdb-resource-meta">
                                    {{ $resource['meta'] }}
                                    @if(!empty($resource['updated']))
                                        · {{ $resource['updated'] }}
                                    @endif
                                </div>
                            </div>
                            <div class="cdb-resource-action">
                                <i class="fas {{ $resource['type'] === 'recording' ? 'fa-play' : 'fa-arrow-down' }}"></i>
                            </div>
                        </a>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    {{-- QUICK ACTIONS --}}
    <div class="cdb-quick-actions">
        @foreach($quickActions as $action)
            <a href="{{ $action['url'] }}" class="cdb-qa-btn">
                <i class="fas {{ $action['icon'] }}"></i>
                <span>{{ $action['label'] }}</span>
            </a>
        @endforeach
    </div>
</div>
