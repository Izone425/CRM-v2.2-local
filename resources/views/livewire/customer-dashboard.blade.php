<div class="cdb-root" wire:poll.60s="refresh"
     x-data="{
        mode: (() => {
            try {
                const url = new URL(window.location);
                const q = url.searchParams.get('dashMode');
                if (q === 'implementation' || q === 'support') return q;
                const stored = localStorage.getItem('cdb-mode');
                if (stored === 'implementation' || stored === 'support') return stored;
            } catch (e) {}
            return @js($defaultMode);
        })(),
        setMode(m) {
            this.mode = m;
            try { localStorage.setItem('cdb-mode', m); } catch (e) {}
            this.$nextTick(() => {
                window.dispatchEvent(new CustomEvent('cdb-mode-changed', { detail: { mode: m } }));
            });
        }
     }"
     x-init="$nextTick(() => window.dispatchEvent(new CustomEvent('cdb-mode-changed', { detail: { mode: mode } })))">
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
            --cdb-shadow-lg: 0 1px 2px rgba(15, 23, 42, 0.04), 0 8px 24px -12px rgba(15, 23, 42, 0.10);
            /* Extended palette - Dabang-inspired tints, TimeTec-aligned hues */
            --cdb-sky: #00a4e0;
            --cdb-sky-bg: #e0f4fc;
            --cdb-sky-soft: #ecf6fd;
            --cdb-coral: #fb7185;
            --cdb-coral-bg: #ffe4e6;
            --cdb-coral-soft: #fff1f2;
            --cdb-amber: #f59e0b;
            --cdb-amber-bg: #fef3c7;
            --cdb-amber-soft: #fffbeb;
            --cdb-mint: #10b981;
            --cdb-mint-bg: #d1fae5;
            --cdb-mint-soft: #ecfdf5;
            --cdb-lavender: #a78bfa;
            --cdb-lavender-bg: #ede9fe;
            --cdb-lavender-soft: #f5f3ff;
            font-family: 'Poppins', sans-serif;
            color: var(--cdb-text);
        }

        /* Greeting */
        .cdb-greeting {
            display: flex;
            align-items: center;
            justify-content: flex-end;
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

        /* ====== STAT STRIP (Dabang-inspired colorful summary cards) ====== */
        .cdb-stat-strip {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-bottom: 12px;
        }
        @media (max-width: 1023px) {
            .cdb-stat-strip { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 599px) {
            .cdb-stat-strip { grid-template-columns: 1fr; }
        }
        .cdb-stat-card {
            position: relative;
            padding: 9px 12px;
            border-radius: 10px;
            background: var(--cdb-surface);
            border: 1px solid var(--cdb-border);
            box-shadow: var(--cdb-shadow-lg);
            overflow: hidden;
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s ease;
            opacity: 0;
            animation: cdb-fade-up 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            /* Horizontal layout: icon on left, content stack on right */
            display: grid;
            grid-template-columns: auto 1fr;
            column-gap: 10px;
            align-items: center;
        }
        .cdb-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04), 0 16px 32px -12px rgba(15, 23, 42, 0.18);
        }
        .cdb-stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, var(--cdb-tint, var(--cdb-sky-bg)) 0%, rgba(255, 255, 255, 0) 65%);
            pointer-events: none;
            z-index: 0;
        }
        .cdb-stat-card > * { position: relative; z-index: 1; }
        .cdb-stat-card--sky { --cdb-tint: var(--cdb-sky-bg); --cdb-accent-color: var(--cdb-sky); }
        .cdb-stat-card--coral { --cdb-tint: var(--cdb-coral-bg); --cdb-accent-color: var(--cdb-coral); }
        .cdb-stat-card--amber { --cdb-tint: var(--cdb-amber-bg); --cdb-accent-color: var(--cdb-amber); }
        .cdb-stat-card--mint { --cdb-tint: var(--cdb-mint-bg); --cdb-accent-color: var(--cdb-mint); }
        .cdb-stat-card--lavender { --cdb-tint: var(--cdb-lavender-bg); --cdb-accent-color: var(--cdb-lavender); }
        .cdb-stat-card:nth-child(1) { animation-delay: 60ms; }
        .cdb-stat-card:nth-child(2) { animation-delay: 120ms; }
        .cdb-stat-card:nth-child(3) { animation-delay: 180ms; }
        .cdb-stat-card:nth-child(4) { animation-delay: 240ms; }
        @keyframes cdb-fade-up {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .cdb-stat-icon {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background: var(--cdb-tint, var(--cdb-sky-bg));
            color: var(--cdb-accent-color, var(--cdb-sky));
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            margin-bottom: 0;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
            flex-shrink: 0;
            grid-row: 1 / span 3;
            grid-column: 1;
            align-self: center;
        }
        .cdb-stat-label {
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--cdb-text-secondary);
            margin-bottom: 1px;
            line-height: 1.2;
            grid-column: 2;
            grid-row: 1;
        }
        .cdb-stat-value {
            font-size: 18px;
            font-weight: 700;
            color: var(--cdb-accent-dark);
            line-height: 1.05;
            letter-spacing: -0.02em;
            grid-column: 2;
            grid-row: 2;
        }
        .cdb-stat-value-suffix {
            font-size: 11px;
            font-weight: 600;
            color: var(--cdb-text-secondary);
            margin-left: 2px;
        }
        .cdb-stat-meta {
            font-size: 9px;
            color: var(--cdb-text-secondary);
            margin-top: 2px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            flex-wrap: wrap;
            grid-column: 2;
            grid-row: 3;
        }
        .cdb-stat-meta-pill {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            padding: 1px 7px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.7);
            color: var(--cdb-accent-color, var(--cdb-sky));
            font-weight: 600;
            font-size: 9px;
            border: 1px solid rgba(15, 23, 42, 0.05);
            line-height: 1.4;
        }

        /* ====== CHART ROW ====== */
        .cdb-chart-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-bottom: 16px;
        }
        @media (max-width: 1279px) {
            .cdb-chart-row { grid-template-columns: 1fr 1fr; }
            .cdb-chart-row > :nth-child(3) { grid-column: span 2; }
        }
        @media (max-width: 767px) {
            .cdb-chart-row { grid-template-columns: 1fr; }
            .cdb-chart-row > :nth-child(3) { grid-column: span 1; }
        }
        .cdb-chart-card {
            background: var(--cdb-surface);
            border: 1px solid var(--cdb-border);
            border-radius: 14px;
            box-shadow: var(--cdb-shadow-lg);
            padding: 16px 18px 12px;
            display: flex;
            flex-direction: column;
            min-height: 300px;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            opacity: 0;
            animation: cdb-fade-up 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .cdb-chart-card:nth-child(1) { animation-delay: 280ms; }
        .cdb-chart-card:nth-child(2) { animation-delay: 340ms; }
        .cdb-chart-card:nth-child(3) { animation-delay: 400ms; }
        .cdb-chart-card:hover {
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04), 0 16px 28px -12px rgba(15, 23, 42, 0.14);
        }
        .cdb-chart-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 6px;
        }
        .cdb-chart-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--cdb-accent-dark);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            letter-spacing: -0.01em;
        }
        .cdb-chart-title .cdb-chart-title-chip {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: var(--cdb-tint, var(--cdb-sky-bg));
            color: var(--cdb-accent-color, var(--cdb-sky));
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }
        .cdb-chart-card--tickets { --cdb-tint: var(--cdb-coral-bg); --cdb-accent-color: var(--cdb-coral); }
        .cdb-chart-card--migration { --cdb-tint: var(--cdb-amber-bg); --cdb-accent-color: var(--cdb-amber); }
        .cdb-chart-card--activity { --cdb-tint: var(--cdb-sky-bg); --cdb-accent-color: var(--cdb-sky); }
        .cdb-chart-sub {
            font-size: 10px;
            color: var(--cdb-text-muted);
            margin-bottom: 4px;
        }
        .cdb-chart-host {
            flex: 1;
            min-height: 220px;
            position: relative;
        }
        .cdb-chart-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 220px;
            color: var(--cdb-text-muted);
            font-size: 12px;
            text-align: center;
            padding: 12px;
        }
        .cdb-chart-empty i {
            font-size: 26px;
            margin-bottom: 8px;
            color: var(--cdb-text-muted);
            opacity: 0.55;
        }
        .cdb-chart-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 6px 12px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed var(--cdb-border);
        }
        .cdb-chart-legend-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 10px;
            color: var(--cdb-text-secondary);
            font-weight: 500;
        }
        .cdb-chart-legend-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
        }

        /* ====== CARD POLISH (apply to existing cards) ====== */
        .cdb-card {
            border-radius: 12px;
            box-shadow: var(--cdb-shadow-lg);
        }
        .cdb-journey,
        .cdb-hero-side,
        .cdb-quick-actions {
            border-radius: 12px;
            box-shadow: var(--cdb-shadow-lg);
        }
        .cdb-journey {
            background: linear-gradient(135deg, #ffffff 0%, var(--cdb-sky-soft) 100%);
        }
        /* Richer journey track connectors */
        .cdb-track-node-done + .cdb-track-node::before,
        .cdb-track-node-current + .cdb-track-node::before {
            background: linear-gradient(90deg, var(--cdb-sky) 0%, var(--cdb-mint) 100%);
        }
        .cdb-track-node-done .cdb-track-dot {
            background: linear-gradient(135deg, var(--cdb-sky) 0%, var(--cdb-mint) 100%);
            border-color: transparent;
            box-shadow: 0 4px 10px -4px rgba(0, 164, 224, 0.55);
        }
        .cdb-track-node-current .cdb-track-dot {
            box-shadow: 0 0 0 0 rgba(251, 113, 133, 0.55);
            border-color: var(--cdb-coral);
            color: var(--cdb-coral);
            animation: cdb-pulse-ring-coral 2s infinite cubic-bezier(0.66, 0, 0, 1);
        }
        @keyframes cdb-pulse-ring-coral {
            0% { box-shadow: 0 0 0 0 rgba(251, 113, 133, 0.55); }
            70% { box-shadow: 0 0 0 12px rgba(251, 113, 133, 0); }
            100% { box-shadow: 0 0 0 0 rgba(251, 113, 133, 0); }
        }
        /* Tinted icon chips on existing card headers */
        .cdb-card-title i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            border-radius: 7px;
            background: var(--cdb-sky-bg);
            color: var(--cdb-sky);
            font-size: 11px;
        }
        .cdb-card-accent-success .cdb-card-title i {
            background: var(--cdb-mint-bg); color: var(--cdb-mint);
        }
        .cdb-card-accent-warning .cdb-card-title i {
            background: var(--cdb-amber-bg); color: var(--cdb-amber);
        }
        .cdb-card-accent-danger .cdb-card-title i {
            background: var(--cdb-coral-bg); color: var(--cdb-coral);
        }
        /* Refresh greeting pill - subtle gradient */
        .cdb-stage-pill {
            background: linear-gradient(135deg, var(--cdb-sky-bg) 0%, var(--cdb-lavender-bg) 100%);
            border-color: rgba(0, 164, 224, 0.2);
        }
        /* Quick action buttons - lift on hover with color */
        .cdb-qa-btn:hover i { color: var(--cdb-sky); }

        /* ====== MODE TOGGLE (segmented control) ====== */
        .cdb-mode-toggle {
            display: inline-flex;
            align-items: center;
            padding: 4px;
            background: rgba(15, 23, 42, 0.04);
            border: 1px solid var(--cdb-border);
            border-radius: 999px;
            gap: 0;
            position: relative;
        }
        .cdb-mode-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border: none;
            background: transparent;
            color: var(--cdb-text-secondary);
            font-size: 11px;
            font-weight: 600;
            border-radius: 999px;
            cursor: pointer;
            transition: color 0.2s ease, background 0.2s ease, transform 0.2s ease;
            font-family: 'Poppins', sans-serif;
            letter-spacing: 0.01em;
            white-space: nowrap;
        }
        .cdb-mode-btn i {
            font-size: 11px;
            transition: transform 0.25s ease;
        }
        .cdb-mode-btn:hover { color: var(--cdb-accent-dark); }
        .cdb-mode-btn[aria-pressed="true"] {
            color: #ffffff;
            box-shadow: 0 4px 12px -4px rgba(0, 60, 117, 0.3);
        }
        .cdb-mode-btn--impl[aria-pressed="true"] {
            background: linear-gradient(135deg, var(--cdb-sky) 0%, var(--cdb-mint) 110%);
        }
        .cdb-mode-btn--support[aria-pressed="true"] {
            background: linear-gradient(135deg, var(--cdb-coral) 0%, var(--cdb-amber) 110%);
        }
        .cdb-mode-btn[aria-pressed="true"] i { transform: scale(1.1); }
        .cdb-mode-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            margin-left: 4px;
            background: rgba(255,255,255,0.95);
            color: var(--cdb-coral);
            border-radius: 999px;
            font-size: 9px;
            font-weight: 700;
        }
        .cdb-mode-btn--support:not([aria-pressed="true"]) .cdb-mode-badge {
            background: var(--cdb-coral-bg);
            color: var(--cdb-coral);
        }

        /* Hide x-cloak-marked elements until Alpine initializes (prevents both-panels flash) */
        [x-cloak] { display: none !important; }

        /* ====== PANEL TRANSITIONS ====== */
        .cdb-panel {
            display: block;
        }
        .cdb-panel[hidden] { display: none !important; }
        @keyframes cdb-panel-in {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .cdb-panel--active {
            animation: cdb-panel-in 0.32s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        /* Mode banner above each panel */
        .cdb-mode-banner {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            margin-bottom: 14px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            position: relative;
            overflow: hidden;
        }
        .cdb-mode-banner::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, var(--cdb-banner-from, var(--cdb-sky-bg)) 0%, transparent 80%);
            opacity: 0.6;
        }
        .cdb-mode-banner > * { position: relative; z-index: 1; }
        .cdb-mode-banner--impl {
            --cdb-banner-from: var(--cdb-sky-bg);
            color: var(--cdb-accent-dark);
            border: 1px solid #c8e7fa;
            background: linear-gradient(90deg, #ffffff 0%, var(--cdb-sky-soft) 100%);
        }
        .cdb-mode-banner--support {
            --cdb-banner-from: var(--cdb-coral-bg);
            color: #9f1239;
            border: 1px solid #fecdd3;
            background: linear-gradient(90deg, #ffffff 0%, var(--cdb-coral-soft) 100%);
        }
        .cdb-mode-banner-icon {
            width: 28px; height: 28px;
            border-radius: 8px;
            display: inline-flex; align-items: center; justify-content: center;
            background: rgba(255, 255, 255, 0.85);
            font-size: 13px;
        }
        .cdb-mode-banner--impl .cdb-mode-banner-icon { color: var(--cdb-sky); }
        .cdb-mode-banner--support .cdb-mode-banner-icon { color: var(--cdb-coral); }
        .cdb-mode-banner-text { flex: 1; }
        .cdb-mode-banner-meta {
            font-weight: 500;
            text-transform: none;
            color: var(--cdb-text-secondary);
            font-size: 10px;
            letter-spacing: 0;
            margin-top: 2px;
        }

        /* Open Tickets hero card (Support mode) */
        .cdb-tickets-hero {
            display: grid;
            grid-template-columns: 1.4fr 1fr 1fr;
            gap: 0;
            padding: 18px 22px;
            background: linear-gradient(135deg, #ffffff 0%, var(--cdb-coral-soft) 100%);
            border: 1px solid var(--cdb-border);
            border-radius: 14px;
            box-shadow: var(--cdb-shadow-lg);
            margin-bottom: 14px;
            position: relative;
            overflow: hidden;
        }
        .cdb-tickets-hero::after {
            content: '';
            position: absolute;
            right: -40px; top: -40px;
            width: 180px; height: 180px;
            background: radial-gradient(circle, var(--cdb-coral-bg) 0%, transparent 70%);
            opacity: 0.5;
        }
        @media (max-width: 1023px) {
            .cdb-tickets-hero { grid-template-columns: 1fr; gap: 16px; }
        }
        .cdb-tickets-hero-cell {
            position: relative;
            z-index: 1;
            padding-right: 16px;
        }
        .cdb-tickets-hero-cell + .cdb-tickets-hero-cell {
            border-left: 1px dashed var(--cdb-border);
            padding-left: 22px;
        }
        @media (max-width: 1023px) {
            .cdb-tickets-hero-cell + .cdb-tickets-hero-cell {
                border-left: none;
                border-top: 1px dashed var(--cdb-border);
                padding-left: 0;
                padding-top: 14px;
            }
        }
        .cdb-tickets-hero-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--cdb-text-secondary);
            margin-bottom: 6px;
        }
        .cdb-tickets-hero-value {
            font-size: 30px;
            font-weight: 700;
            color: var(--cdb-accent-dark);
            line-height: 1;
            letter-spacing: -0.02em;
        }
        .cdb-tickets-hero-value-suffix {
            font-size: 14px;
            color: var(--cdb-text-secondary);
            font-weight: 500;
            margin-left: 4px;
        }
        .cdb-tickets-hero-meta {
            font-size: 11px;
            color: var(--cdb-text-secondary);
            margin-top: 6px;
        }
        .cdb-tickets-hero-cta {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 10px;
            padding: 8px 14px;
            background: linear-gradient(135deg, var(--cdb-coral) 0%, #f43f5e 100%);
            color: #fff;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 6px 16px -6px rgba(244, 63, 94, 0.5);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .cdb-tickets-hero-cta:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 22px -8px rgba(244, 63, 94, 0.55);
        }
        .cdb-sla-health {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
            margin-top: 6px;
        }
        .cdb-sla-health--good { background: var(--cdb-mint-bg); color: #047857; }
        .cdb-sla-health--ok { background: var(--cdb-amber-bg); color: #b45309; }
        .cdb-sla-health--bad { background: var(--cdb-coral-bg); color: #9f1239; }
        .cdb-sla-health--unknown { background: #f1f5f9; color: var(--cdb-text-muted); }

        /* ====== FRESHDESK-STYLE SNAPSHOT CARDS (Support panel) ====== */
        .cdb-snapshot-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 14px;
        }
        @media (max-width: 1023px) { .cdb-snapshot-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 599px)  { .cdb-snapshot-grid { grid-template-columns: 1fr; } }

        .cdb-snapshot-card {
            background: var(--cdb-surface);
            border: 1px solid var(--cdb-border);
            border-top: 3px solid var(--cdb-snapshot-color, var(--cdb-sky));
            border-radius: 10px;
            padding: 12px 14px;
            box-shadow: var(--cdb-shadow-lg);
            display: flex;
            align-items: center;
            gap: 12px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            min-height: 60px;
        }
        .cdb-snapshot-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04), 0 12px 24px -10px rgba(15, 23, 42, 0.12);
        }
        .cdb-snapshot-card--sky    { --cdb-snapshot-color: var(--cdb-sky); }
        .cdb-snapshot-card--amber  { --cdb-snapshot-color: var(--cdb-amber); }
        .cdb-snapshot-card--mint   { --cdb-snapshot-color: var(--cdb-mint); }
        .cdb-snapshot-card--coral  { --cdb-snapshot-color: var(--cdb-coral); }

        .cdb-snapshot-value {
            font-size: 26px;
            font-weight: 700;
            line-height: 1;
            color: var(--cdb-snapshot-color, var(--cdb-accent-dark));
            letter-spacing: -0.02em;
            min-width: 36px;
            font-variant-numeric: tabular-nums;
        }
        .cdb-snapshot-meta {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }
        .cdb-snapshot-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--cdb-text);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            line-height: 1.2;
        }
        .cdb-snapshot-sub  {
            font-size: 9px;
            color: var(--cdb-text-muted);
            margin-top: 2px;
        }

        /* ====== DATE-RANGE DROPDOWN (Daily Activity chart head) ====== */
        .cdb-range-dropdown {
            position: relative;
            display: inline-block;
        }
        .cdb-range-trigger {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 10px;
            background: var(--cdb-sky-soft);
            border: 1px solid #c8e7fa;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 600;
            color: var(--cdb-accent-dark);
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            letter-spacing: 0.02em;
            transition: background 0.15s ease, border-color 0.15s ease, color 0.15s ease;
            white-space: nowrap;
        }
        .cdb-range-trigger:hover {
            background: var(--cdb-sky-bg);
            border-color: var(--cdb-sky);
            color: var(--cdb-sky);
        }
        .cdb-range-trigger i.fa-calendar-days { font-size: 10px; }
        .cdb-range-trigger i.fa-chevron-down {
            font-size: 8px;
            transition: transform 0.2s ease;
            opacity: 0.6;
        }
        .cdb-range-trigger[aria-expanded="true"] i.fa-chevron-down { transform: rotate(180deg); }

        .cdb-range-menu {
            position: absolute;
            right: 0;
            top: calc(100% + 6px);
            min-width: 200px;
            background: var(--cdb-surface);
            border: 1px solid var(--cdb-border);
            border-radius: 10px;
            box-shadow: 0 10px 32px -8px rgba(15, 23, 42, 0.2);
            z-index: 50;
            overflow: hidden;
            padding: 4px 0;
        }
        .cdb-range-section {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--cdb-text-muted);
            padding: 6px 12px 4px;
        }
        .cdb-range-divider {
            height: 1px;
            background: var(--cdb-border);
            margin: 4px 0;
        }
        .cdb-range-option {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            width: 100%;
            padding: 7px 12px;
            border: none;
            background: transparent;
            text-align: left;
            font-size: 11px;
            color: var(--cdb-text);
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            transition: background 0.12s ease, color 0.12s ease;
        }
        .cdb-range-option:hover { background: var(--cdb-sky-soft); color: var(--cdb-sky); }
        .cdb-range-option[aria-current="true"] {
            background: var(--cdb-sky-bg);
            color: var(--cdb-accent-dark);
            font-weight: 600;
        }
        .cdb-range-option[aria-current="true"]::after {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            font-size: 9px;
            color: var(--cdb-sky);
        }

        /* Tighten the two-column secondary row in Support mode */
        .cdb-row-support-2 {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 14px;
            margin-bottom: 16px;
        }
        @media (max-width: 1023px) {
            .cdb-row-support-2 { grid-template-columns: 1fr; }
        }
        .cdb-row-impl-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 16px;
        }
        @media (max-width: 1023px) {
            .cdb-row-impl-2 { grid-template-columns: 1fr; }
        }
        .cdb-row-impl-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-bottom: 16px;
        }
        @media (max-width: 1279px) {
            .cdb-row-impl-3 { grid-template-columns: 1fr 1fr; }
            .cdb-row-impl-3 > :nth-child(3) { grid-column: span 2; }
        }
        @media (max-width: 1023px) {
            .cdb-row-impl-3 { grid-template-columns: 1fr; }
            .cdb-row-impl-3 > :nth-child(3) { grid-column: span 1; }
        }

        /* Greeting layout adjustment to fit toggle */
        .cdb-greeting-controls {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        @media (max-width: 767px) {
            .cdb-greeting-controls {
                width: 100%;
                justify-content: flex-start;
            }
            .cdb-mode-toggle { width: 100%; justify-content: stretch; }
            .cdb-mode-btn { flex: 1; justify-content: center; }
        }

        /* ====== ONE-VIEWPORT GRID (added) ====== */

        /* Hero journey, full-width (overrides the previous 8fr/4fr split) */
        .cdb-hero-solo {
            margin-bottom: 14px;
        }
        .cdb-hero-solo .cdb-journey {
            padding: 16px 22px 18px;
        }

        /* Supporting row: Companion | Action Items | Quick Actions (default 3-col) */
        .cdb-grid-supporting {
            display: grid;
            grid-template-columns: 4fr 5fr 3fr;
            gap: 14px;
            margin-bottom: 16px;
            align-items: stretch;
        }
        .cdb-grid-supporting--2col { grid-template-columns: 5fr 5fr; }
        @media (max-width: 1279px) {
            .cdb-grid-supporting { grid-template-columns: 1fr 1fr; }
            .cdb-grid-supporting > :nth-child(3) { grid-column: span 2; }
            .cdb-grid-supporting--2col { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 1023px) {
            .cdb-grid-supporting { grid-template-columns: 1fr; }
            .cdb-grid-supporting > :nth-child(3) { grid-column: span 1; }
            .cdb-grid-supporting--2col { grid-template-columns: 1fr; }
        }

        /* Vertical Quick Actions stack for the supporting row */
        .cdb-quick-actions--stack {
            grid-template-columns: 1fr;
            gap: 8px;
            padding: 12px;
        }
        .cdb-quick-actions--stack .cdb-qa-btn {
            justify-content: flex-start;
            padding: 9px 12px;
            font-size: 11px;
        }
        @media (max-width: 1023px) {
            .cdb-quick-actions--stack {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* KPI stat-card → tab link (chevron in card footer) */
        .cdb-stat-card { grid-template-rows: auto auto auto auto; }
        .cdb-stat-link {
            grid-column: 1 / -1;
            grid-row: 4;
            margin-top: 6px;
            padding-top: 6px;
            border-top: 1px dashed rgba(15, 23, 42, 0.07);
            font-size: 9px;
            font-weight: 600;
            letter-spacing: 0.04em;
            color: var(--cdb-accent-color, var(--cdb-sky));
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: flex-end;
            gap: 4px;
            cursor: pointer;
            background: transparent;
            border: none;
            font-family: 'Poppins', sans-serif;
            text-transform: uppercase;
            transition: color 0.15s ease, gap 0.15s ease;
            width: 100%;
        }
        .cdb-stat-link:hover {
            color: var(--cdb-accent-dark);
            gap: 7px;
        }
        .cdb-stat-link i { font-size: 8px; }

        /* Whole-card click target on KPI cards */
        .cdb-stat-card[role="link"] { cursor: pointer; }
        .cdb-stat-card[role="link"]:hover .cdb-stat-link { color: var(--cdb-accent-dark); gap: 7px; }

        /* Action Items card body — no internal scroll, just clip overflow gracefully */
        .cdb-supporting-body { padding: 0 14px 14px; flex: 1; overflow: hidden; }

        /* Compact Companion card for the supporting row */
        .cdb-companion-card {
            padding: 14px 16px;
            background: var(--cdb-surface);
            border: 1px solid var(--cdb-border);
            border-radius: 12px;
            box-shadow: var(--cdb-shadow-lg);
            display: flex;
            flex-direction: column;
            min-height: 220px;
        }
        .cdb-companion-card .cdb-companion-title {
            margin-bottom: 10px;
        }

        /* Support Health hero — compact 3-cell band */
        .cdb-support-health {
            display: grid;
            grid-template-columns: 1.2fr 1fr 1fr;
            gap: 0;
            padding: 14px 20px;
            background: linear-gradient(135deg, #ffffff 0%, var(--cdb-coral-soft) 100%);
            border: 1px solid var(--cdb-border);
            border-radius: 12px;
            box-shadow: var(--cdb-shadow-lg);
            margin-bottom: 14px;
            position: relative;
            overflow: hidden;
        }
        .cdb-support-health::after {
            content: '';
            position: absolute;
            right: -36px; top: -36px;
            width: 160px; height: 160px;
            background: radial-gradient(circle, var(--cdb-coral-bg) 0%, transparent 70%);
            opacity: 0.45;
        }
        @media (max-width: 1023px) {
            .cdb-support-health { grid-template-columns: 1fr; gap: 14px; }
        }
        .cdb-support-health-cell {
            position: relative;
            z-index: 1;
            padding-right: 16px;
        }
        .cdb-support-health-cell + .cdb-support-health-cell {
            border-left: 1px dashed var(--cdb-border);
            padding-left: 22px;
        }
        @media (max-width: 1023px) {
            .cdb-support-health-cell + .cdb-support-health-cell {
                border-left: none;
                border-top: 1px dashed var(--cdb-border);
                padding-left: 0;
                padding-top: 12px;
            }
        }
        .cdb-support-health-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--cdb-text-secondary);
            margin-bottom: 4px;
        }
        .cdb-support-health-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--cdb-accent-dark);
            line-height: 1;
            letter-spacing: -0.02em;
        }
        .cdb-support-health-value-suffix {
            font-size: 12px;
            color: var(--cdb-text-secondary);
            font-weight: 500;
            margin-left: 4px;
        }
        .cdb-support-health-meta {
            font-size: 11px;
            color: var(--cdb-text-secondary);
            margin-top: 4px;
        }
        .cdb-support-health-cta {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 10px;
            padding: 7px 14px;
            background: linear-gradient(135deg, var(--cdb-coral) 0%, #f43f5e 100%);
            color: #fff;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 6px 16px -6px rgba(244, 63, 94, 0.5);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .cdb-support-health-cta:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 22px -8px rgba(244, 63, 94, 0.55);
        }

        /* ---------- Snapshot row (Pending-Tasks + Building-Management style) ---------- */
        .cdb-snapshot-row {
            display: grid;
            grid-template-columns: 5fr 7fr;
            gap: 14px;
            margin-bottom: 16px;
        }
        @media (max-width: 1023px) {
            .cdb-snapshot-row { grid-template-columns: 1fr; }
        }

        /* ---------- Pending-Tasks-style "Quick Actions" card ---------- */
        .cdb-pt-card {
            background: white;
            border-radius: 14px;
            border: 1px solid var(--cdb-border);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .cdb-pt-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 16px;
            background: #fff7ed;
            border-bottom: 1px solid #fde0c4;
        }
        .cdb-pt-icon {
            width: 32px; height: 32px;
            border-radius: 9px;
            background: linear-gradient(135deg, #f59e0b, #ea580c);
            display: inline-flex; align-items: center; justify-content: center;
            color: white; font-size: 14px;
            box-shadow: 0 4px 10px rgba(234, 88, 12, 0.28);
        }
        .cdb-pt-title { font-weight: 600; font-size: 14px; color: var(--cdb-text); margin: 0; }
        .cdb-pt-total { margin-left: auto; font-size: 12px; color: #ea580c; font-weight: 500; }
        .cdb-pt-body { padding: 6px 0; max-height: 280px; overflow-y: auto; }
        .cdb-pt-row {
            display: grid;
            grid-template-columns: 100px 1fr auto;
            align-items: center;
            gap: 14px;
            padding: 10px 16px;
            border-left: 4px solid var(--cat-color, #94a3b8);
            text-decoration: none;
            color: var(--cdb-text);
            transition: background 0.15s ease, transform 0.15s ease;
        }
        .cdb-pt-row + .cdb-pt-row { border-top: 1px solid #f1f5f9; }
        .cdb-pt-row:hover { background: #f8fafc; transform: translateX(2px); }
        .cdb-pt-cat { font-size: 12px; font-weight: 600; color: var(--cat-color, #94a3b8); }
        .cdb-pt-text { font-size: 13px; color: var(--cdb-text); }
        .cdb-pt-chev { color: #cbd5e1; font-size: 12px; }

        /* ---------- Building-Management-style "Implementation Snapshot" card ---------- */
        .cdb-bm-card {
            background: white;
            border-radius: 14px;
            border: 1px solid var(--cdb-border);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .cdb-bm-header {
            display: flex; align-items: center; gap: 10px;
            padding: 14px 16px;
            background: #eef2ff;
            border-bottom: 1px solid #c7d2fe;
        }
        .cdb-bm-icon {
            width: 32px; height: 32px;
            border-radius: 9px;
            background: linear-gradient(135deg, #6366f1, #4338ca);
            display: inline-flex; align-items: center; justify-content: center;
            color: white; font-size: 14px;
            box-shadow: 0 4px 10px rgba(67, 56, 202, 0.28);
        }
        .cdb-bm-title { font-weight: 600; font-size: 14px; color: var(--cdb-text); margin: 0; }
        .cdb-bm-tiles { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; padding: 14px 16px 10px; }
        .cdb-bm-tile {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            padding: 14px 6px;
            border: 1px solid #dbeafe;
            border-radius: 10px;
            background: white;
            text-decoration: none;
            color: var(--cdb-text);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .cdb-bm-tile:hover { transform: translateY(-1px); box-shadow: 0 6px 16px -8px rgba(67, 56, 202, 0.25); }
        .cdb-bm-tile--active { background: #dbeafe; border-color: #93c5fd; }
        .cdb-bm-num { font-size: 22px; font-weight: 700; color: #1e3a8a; line-height: 1.1; }
        .cdb-bm-tile--active .cdb-bm-num { color: #1d4ed8; }
        .cdb-bm-tile-lbl { font-size: 11px; color: var(--cdb-text-secondary); margin-top: 4px; text-align: center; }
        .cdb-bm-chart { padding: 0 16px 14px; }
        .cdb-bm-chart-plot { position: relative; padding-left: 28px; }
        .cdb-bm-chart svg { display: block; width: 100%; height: 80px; }
        .cdb-bm-y-axis {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            font-size: 9px;
            line-height: 1;
            color: #94a3b8;
            text-align: right;
            padding: 1px 6px 1px 0;
            box-sizing: border-box;
        }
        .cdb-bm-chart-axis { display: flex; justify-content: space-between; font-size: 10px; color: #94a3b8; margin-top: 4px; padding-left: 28px; }

        @media (max-width: 767px) {
            .cdb-bm-tiles { grid-template-columns: repeat(2, 1fr); }
            .cdb-pt-row { grid-template-columns: 80px 1fr auto; gap: 10px; }
        }
    </style>

    {{-- Greeting strip with Mode Toggle --}}
    <div class="cdb-greeting">
        <div class="cdb-greeting-controls">
            <div class="cdb-mode-toggle" role="tablist" aria-label="Dashboard view mode">
                <button type="button"
                        class="cdb-mode-btn cdb-mode-btn--impl"
                        role="tab"
                        :aria-pressed="mode === 'implementation'"
                        :aria-selected="mode === 'implementation'"
                        @click="setMode('implementation')">
                    <i class="fas fa-bullseye-arrow" style="display:none;"></i>
                    <i class="fas fa-rocket"></i>
                    <span>Implementation</span>
                </button>
                <button type="button"
                        class="cdb-mode-btn cdb-mode-btn--support"
                        role="tab"
                        :aria-pressed="mode === 'support'"
                        :aria-selected="mode === 'support'"
                        @click="setMode('support')">
                    <i class="fas fa-headset"></i>
                    <span>Support</span>
                    @if($ticketsTotal > 0)
                        <span class="cdb-mode-badge">{{ $ticketsTotal }}</span>
                    @endif
                </button>
            </div>
            @php
                $stagePillLabel = match($journeyStage) {
                    'pre_kickoff' => 'Kick-Off',
                    'kickoff_done', 'training' => 'Training',
                    'data_migration' => 'Data Migration',
                    'first_review' => 'First Review',
                    'pre_go_live' => 'Final Review',
                    'live', 'support_only' => 'Live',
                    default => str_replace('_', ' ', $journeyStage),
                };
            @endphp
            <span class="cdb-stage-pill">
                <i class="fas fa-circle" style="font-size: 6px;"></i>
                {{ $stagePillLabel }}
            </span>
            <button type="button" class="cdb-refresh-btn" wire:click="refresh" wire:loading.attr="disabled">
                <i class="fas fa-arrows-rotate" wire:loading.class="fa-spin" wire:target="refresh"></i>
                <span wire:loading.remove wire:target="refresh">Refresh</span>
                <span wire:loading wire:target="refresh">Refreshing</span>
            </button>
        </div>
    </div>

    @php
        $progressPct = (int) ($progressSummary['overallProgress'] ?? 0);
        $migrationPct = (int) ($migrationCounts['percent'] ?? 0);
        $healthLabel = match($ticketsHealthState) {
            'overdue' => 'Overdue',
            'at_risk' => 'At risk',
            default => 'On track',
        };

        // Implementation stats
        $dtgl = $implStats['days_to_go_live'] ?? null;

        // Implementer-thread stats (lives under Implementation mode now)
        $slaPct = $threadStats['sla_health_pct'] ?? null;
        $avgResolve = $threadStats['avg_resolve_days'] ?? null;
        $tickets30d = $threadStats['tickets_30d'] ?? 0;
        $slaClass = $slaPct === null ? 'unknown' : ($slaPct >= 90 ? 'good' : ($slaPct >= 70 ? 'ok' : 'bad'));
        $slaWord = $slaPct === null ? 'No data yet' : ($slaPct >= 90 ? 'Excellent' : ($slaPct >= 70 ? 'Stable' : 'Needs attention'));

        // Support thread stats (separate `support_threads` table — Support mode)
        $supOpen = $supportThreadStats['open_count'] ?? 0;
        $supTotal = $supportThreadStats['total_count'] ?? 0;

        // Action items: Implementation gets the combined list (rejected migrations + tickets needing reply), max 4
        $combinedActionItems = array_slice($actionItems, 0, 4);

        // Quick actions split by mode
        $implQuickActions = array_values(array_filter($quickActions, fn($a) =>
            in_array($a['key'], ['create_ticket', 'book_session', 'upload_migration', 'view_project', 'handover_doc', 'browse_webinars'], true)
        ));
        $supportQuickActions = array_values(array_filter($quickActions, fn($a) =>
            in_array($a['key'], ['create_ticket', 'browse_webinars'], true)
        ));

        // Action items filtered for the Support panel (tickets only — rejected migrations are an Implementation concern)
        $supportActionItems = array_values(array_filter($actionItems, fn($i) => ($i['type'] ?? '') === 'ticket'));
        $supportActionTotal = count($supportActionItems);
    @endphp

    {{-- ════════════════════════════════ IMPLEMENTATION PANEL ════════════════════════════════ --}}
    <div class="cdb-panel" x-show="mode === 'implementation'" x-cloak data-panel-mode="implementation"
         :class="mode === 'implementation' ? 'cdb-panel--active' : ''">

        {{-- HERO: Full-width Implementation Journey track --}}
        <div class="cdb-hero-solo">
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
                @endphp
                @if($currentNode)
                    <div class="cdb-track-current-caption">
                        <i class="fas fa-location-arrow" style="margin-right: 6px;"></i>
                        Currently in <strong>{{ $currentNode['label'] }}</strong>
                        @if($hasProjectPlan && $progressPct > 0)
                            — {{ $progressPct }}% complete overall
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- SNAPSHOT ROW — Quick Actions list (left) + Implementation Snapshot tiles + sparkline (right) --}}
        <div class="cdb-snapshot-row">
            <section class="cdb-pt-card">
                <header class="cdb-pt-header">
                    <span class="cdb-pt-icon"><i class="fas fa-clipboard-list"></i></span>
                    <h3 class="cdb-pt-title">Quick Actions</h3>
                </header>
                <div class="cdb-pt-body">
                    @foreach($implQuickActions as $action)
                        <a href="{{ $action['url'] }}"
                           @if(str_starts_with($action['url'], '?tab=')) onclick="event.preventDefault(); switchTab('{{ substr($action['url'], 5) }}');" @endif
                           class="cdb-pt-row" style="--cat-color: {{ $action['color'] }};">
                            <span class="cdb-pt-cat">{{ $action['category'] }}</span>
                            <span class="cdb-pt-text">{{ $action['label'] }}</span>
                            <i class="fas fa-chevron-right cdb-pt-chev"></i>
                        </a>
                    @endforeach
                </div>
            </section>

            <section class="cdb-bm-card">
                <header class="cdb-bm-header">
                    <span class="cdb-bm-icon"><i class="fas fa-chart-bar"></i></span>
                    <h3 class="cdb-bm-title">Implementation Snapshot</h3>
                </header>
                <div class="cdb-bm-tiles">
                    <a href="?tab=calendar" onclick="event.preventDefault(); switchTab('calendar');" class="cdb-bm-tile">
                        <span class="cdb-bm-num">{{ $dtgl !== null ? abs($dtgl) : '—' }}</span>
                        <span class="cdb-bm-tile-lbl">{{ $dtgl !== null && $dtgl < 0 ? 'Days Live' : 'Days to Go-Live' }}</span>
                    </a>
                    <a href="?tab=project" onclick="event.preventDefault(); switchTab('project');" class="cdb-bm-tile">
                        <span class="cdb-bm-num">{{ $progressPct }}%</span>
                        <span class="cdb-bm-tile-lbl">Project Progress</span>
                    </a>
                    <a href="?tab=dataMigration" onclick="event.preventDefault(); switchTab('dataMigration');" class="cdb-bm-tile">
                        <span class="cdb-bm-num">{{ $migrationPct }}%</span>
                        <span class="cdb-bm-tile-lbl">Migration Progress</span>
                    </a>
                    <a href="?tab=impThread" onclick="event.preventDefault(); switchTab('impThread');" class="cdb-bm-tile cdb-bm-tile--active">
                        <span class="cdb-bm-num">{{ $ticketsTotal }}</span>
                        <span class="cdb-bm-tile-lbl">Implementer Threads</span>
                    </a>
                </div>
                <div class="cdb-bm-chart" aria-hidden="true">
                    <div class="cdb-bm-chart-plot">
                        <div class="cdb-bm-y-axis">
                            <span>100</span>
                            <span>50</span>
                            <span>0</span>
                        </div>
                        <svg viewBox="0 0 600 80" preserveAspectRatio="none">
                            <defs>
                                <linearGradient id="cdbBmFill" x1="0" x2="0" y1="0" y2="1">
                                    <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.32"/>
                                    <stop offset="100%" stop-color="#3b82f6" stop-opacity="0"/>
                                </linearGradient>
                            </defs>
                            <g stroke="#e2e8f0" fill="none" vector-effect="non-scaling-stroke">
                                <line x1="0" y1="4"  x2="600" y2="4"  stroke-width="1"/>
                                <line x1="0" y1="22" x2="600" y2="22" stroke-width="1"/>
                                <line x1="0" y1="40" x2="600" y2="40" stroke-width="1"/>
                                <line x1="0" y1="58" x2="600" y2="58" stroke-width="1"/>
                            </g>
                            <line x1="0" y1="76" x2="600" y2="76" stroke="#cbd5e1" stroke-width="1" vector-effect="non-scaling-stroke"/>
                            <path d="{{ $this->sparkPaths['area'] }}" fill="url(#cdbBmFill)"/>
                            <path d="{{ $this->sparkPaths['line'] }}" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linejoin="round" vector-effect="non-scaling-stroke"/>
                        </svg>
                    </div>
                    <div class="cdb-bm-chart-axis">
                        @foreach($progressSpark['labels'] as $i => $lbl)
                            @if($i === 0 || $i === 3 || $i === 7)
                                <span>{{ $lbl }}</span>
                            @endif
                        @endforeach
                    </div>
                </div>
            </section>
        </div>

    </div>

    {{-- ════════════════════════════════ SUPPORT PANEL ════════════════════════════════ --}}
    <div class="cdb-panel" x-show="mode === 'support'" x-cloak data-panel-mode="support"
         :class="mode === 'support' ? 'cdb-panel--active' : ''">

        {{-- HERO: Support Health (Open Threads / SLA / Avg Resolution) --}}
        <div class="cdb-support-health">
            <div class="cdb-support-health-cell">
                <div class="cdb-support-health-label">Open Threads</div>
                <div class="cdb-support-health-value">{{ $ticketsTotal }}</div>
                <div class="cdb-support-health-meta">{{ $healthLabel }} on SLA</div>
                <a href="?tab=impThread" onclick="event.preventDefault(); switchTab('impThread');" class="cdb-support-health-cta">
                    <i class="fas fa-circle-plus"></i> New Implementer Thread
                </a>
            </div>
            <div class="cdb-support-health-cell">
                <div class="cdb-support-health-label">SLA Health</div>
                <div class="cdb-support-health-value">
                    @if($slaPct === null)—@else{{ $slaPct }}<span class="cdb-support-health-value-suffix">%</span>@endif
                </div>
                <span class="cdb-sla-health cdb-sla-health--{{ $slaClass }}">
                    <i class="fas fa-circle" style="font-size:6px;"></i> {{ $slaWord }}
                </span>
            </div>
            <div class="cdb-support-health-cell">
                <div class="cdb-support-health-label">Avg Resolution</div>
                <div class="cdb-support-health-value">
                    @if($avgResolve === null)—@else{{ $avgResolve }}<span class="cdb-support-health-value-suffix">{{ $avgResolve == 1.0 ? 'day' : 'days' }}</span>@endif
                </div>
                <div class="cdb-support-health-meta">
                    {{ $tickets30d }} new thread{{ $tickets30d === 1 ? '' : 's' }} · last 30 days
                </div>
            </div>
        </div>

        {{-- KPI STRIP — Support thread status buckets --}}
        <div class="cdb-stat-strip">
            <div class="cdb-stat-card cdb-stat-card--lavender">
                <div class="cdb-stat-icon"><i class="fas fa-hourglass-half"></i></div>
                <div class="cdb-stat-label">Waiting Reply</div>
                <div class="cdb-stat-value">{{ $supportStatusCounts['waiting_reply'] }}</div>
                <div class="cdb-stat-meta">
                    @if($supportStatusCounts['waiting_reply'] === 0)
                        <span>No threads waiting</span>
                    @else
                        <span class="cdb-stat-meta-pill"><i class="fas fa-circle" style="font-size:6px;"></i> Pending</span>
                        <span>awaiting response</span>
                    @endif
                </div>
            </div>

            <div class="cdb-stat-card cdb-stat-card--amber">
                <div class="cdb-stat-icon"><i class="fas fa-bolt"></i></div>
                <div class="cdb-stat-label">In Progress</div>
                <div class="cdb-stat-value">{{ $supportStatusCounts['in_progress'] }}</div>
                <div class="cdb-stat-meta">
                    @if($supportStatusCounts['in_progress'] === 0)
                        <span>Nothing being worked</span>
                    @else
                        <span class="cdb-stat-meta-pill"><i class="fas fa-circle" style="font-size:6px;"></i> Active</span>
                        <span>handled by support</span>
                    @endif
                </div>
            </div>

            <div class="cdb-stat-card cdb-stat-card--sky">
                <div class="cdb-stat-icon"><i class="fas fa-envelope-open"></i></div>
                <div class="cdb-stat-label">Open</div>
                <div class="cdb-stat-value">{{ $supportStatusCounts['open'] }}</div>
                <div class="cdb-stat-meta">
                    @if($supportStatusCounts['open'] === 0)
                        <span>Nothing unassigned</span>
                    @else
                        <span class="cdb-stat-meta-pill"><i class="fas fa-circle" style="font-size:6px;"></i> New</span>
                        <span>needs first response</span>
                    @endif
                </div>
            </div>

            <div class="cdb-stat-card cdb-stat-card--mint">
                <div class="cdb-stat-icon"><i class="fas fa-circle-check"></i></div>
                <div class="cdb-stat-label">Closed</div>
                <div class="cdb-stat-value">{{ $supportStatusCounts['closed'] }}</div>
                <div class="cdb-stat-meta">
                    @if($supportStatusCounts['closed'] === 0)
                        <span>Nothing closed yet</span>
                    @else
                        <span class="cdb-stat-meta-pill">Lifetime</span>
                        <span>resolved or done</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- SUPPORTING ROW: Recent Threads / Action Items / Quick Actions --}}
        <div class="cdb-grid-supporting">
            {{-- Recent Support Threads (top 4) --}}
            <div class="cdb-card cdb-card-accent {{ $supOpen > 0 ? '' : 'cdb-card-accent-neutral' }}">
                <div class="cdb-card-header">
                    <span class="cdb-card-title">
                        <i class="fas fa-headset"></i> Recent Support Threads
                    </span>
                    @if($supTotal > 0)
                        <span class="cdb-count-badge">{{ $supOpen }} open</span>
                    @endif
                </div>
                <div class="cdb-supporting-body">
                    @if(count($supportThreads) === 0)
                        <div class="cdb-empty">
                            <div class="cdb-empty-icon cdb-empty-neutral"><i class="fas fa-comment-dots"></i></div>
                            <div class="cdb-empty-text">No support threads yet</div>
                            <div class="cdb-empty-subtext">Open a thread when you need help post-go-live.</div>
                        </div>
                    @else
                        @foreach(array_slice($supportThreads, 0, 4) as $thread)
                            <a href="/customer/implementer-tickets" class="cdb-ticket-row">
                                <div class="cdb-ticket-top">
                                    <span class="cdb-ticket-number">{{ $thread['number'] }}</span>
                                    <span class="cdb-sla-pill cdb-sla-{{ in_array($thread['status'], ['closed','resolved']) ? 'resolved' : 'on_track' }}">
                                        {{ ucfirst($thread['status']) }}
                                    </span>
                                </div>
                                <div class="cdb-ticket-subject">{{ $thread['subject'] }}</div>
                                <div class="cdb-ticket-meta">
                                    @if($thread['module'])
                                        <span><i class="fas fa-cube"></i> {{ $thread['module'] }}</span>
                                    @endif
                                    @if($thread['updated'])
                                        <span><i class="far fa-clock"></i> {{ $thread['updated'] }}</span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- Action Items (Support panel: tickets needing reply only) --}}
            <div class="cdb-card cdb-card-accent {{ $supportActionTotal === 0 ? 'cdb-card-accent-success' : 'cdb-card-accent-warning' }}">
                <div class="cdb-card-header">
                    <span class="cdb-card-title">
                        <i class="fas fa-bolt"></i> Action Items
                    </span>
                    @if($supportActionTotal > 0)
                        <span class="cdb-count-badge">{{ $supportActionTotal }}</span>
                    @endif
                </div>
                <div class="cdb-supporting-body">
                    @if($supportActionTotal === 0)
                        <div class="cdb-empty">
                            <div class="cdb-empty-icon"><i class="fas fa-check"></i></div>
                            <div class="cdb-empty-text">All caught up</div>
                            <div class="cdb-empty-subtext">No tickets waiting on your reply.</div>
                        </div>
                    @else
                        <ul class="cdb-list">
                            @foreach(array_slice($supportActionItems, 0, 4) as $item)
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

            {{-- Quick Actions (vertical stack) --}}
            @if(count($supportQuickActions) > 0)
                <div class="cdb-quick-actions cdb-quick-actions--stack">
                    @foreach($supportQuickActions as $action)
                        <a href="{{ $action['url'] }}" class="cdb-qa-btn">
                            <i class="fas {{ $action['icon'] }}"></i>
                            <span>{{ $action['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
